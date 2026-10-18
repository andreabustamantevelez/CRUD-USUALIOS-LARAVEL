<?php

namespace Tests\Feature;

use App\Models\Meta;
use App\Models\ParticipacionMeta;
use App\Models\User;
use App\Notifications\ConfirmarCambioCorreo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Pruebas del perfil, cambio de tipo de cuenta y verificación de correo.
 * Ejecutar: php artisan test --filter=AjustesTest
 */
class AjustesTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // TIPO DE CUENTA (J5 / C7)
    // =========================================================================

    public function test_estandar_puede_cambiar_a_familiar(): void
    {
        $u = User::factory()->create();
        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'familiar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));

        $this->assertSame('familiar', $u->fresh()->rol);
    }

    public function test_familiar_sin_metas_puede_bajar_a_estandar(): void
    {
        $u = User::factory()->familiar()->create();
        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));

        $this->assertSame('estandar', $u->fresh()->rol);
    }

    public function test_familiar_con_meta_compartida_activa_no_puede_bajar(): void
    {
        $u = User::factory()->familiar()->create();
        $meta = Meta::factory()->create(['creador_id' => $u->id, 'compartida' => true, 'estado' => 'activa']);
        ParticipacionMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $u->id,
            'estado_invitacion' => 'aceptada',
            'fecha_union' => now()->toDateString(),
        ]);

        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertSessionHasErrors('tipo_cuenta');
        $this->assertSame('familiar', $u->fresh()->rol);
    }

    public function test_familiar_con_solo_meta_individual_puede_bajar(): void
    {
        $u = User::factory()->familiar()->create();
        $meta = Meta::factory()->create(['creador_id' => $u->id, 'compartida' => false, 'estado' => 'activa']);
        ParticipacionMeta::create([
            'meta_id' => $meta->id,
            'user_id' => $u->id,
            'estado_invitacion' => 'aceptada',
            'fecha_union' => now()->toDateString(),
        ]);

        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));
        $this->assertSame('estandar', $u->fresh()->rol);
    }

    public function test_tipo_cuenta_lo_ven_estandar_y_familiar_pero_no_admin(): void
    {
        $estandar = User::factory()->create();
        $familiar = User::factory()->familiar()->create();
        $admin = User::factory()->administrador()->create();

        $this->actingAs($estandar)->get(route('ajustes.tipo-cuenta'))->assertOk();
        $this->actingAs($familiar)->get(route('ajustes.tipo-cuenta'))->assertOk();
        $this->actingAs($admin)->get(route('ajustes.tipo-cuenta'))->assertForbidden();
    }

    public function test_admin_no_puede_cambiar_tipo_de_cuenta(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertForbidden();

        $this->assertSame('administrador', $admin->fresh()->rol);
    }

    public function test_eliminar_cuenta_lo_ven_estandar_y_familiar_pero_no_admin(): void
    {
        $estandar = User::factory()->create();
        $familiar = User::factory()->familiar()->create();
        $admin = User::factory()->administrador()->create();

        $this->actingAs($estandar)->get(route('ajustes.eliminar-cuenta'))->assertOk();
        $this->actingAs($familiar)->get(route('ajustes.eliminar-cuenta'))->assertOk();
        $this->actingAs($admin)->get(route('ajustes.eliminar-cuenta'))->assertForbidden();
    }

    public function test_admin_no_puede_eliminar_su_cuenta_desde_ajustes(): void
    {
        // Aunque haya otro admin activo, Ajustes bloquea al admin por rol.
        User::factory()->administrador()->create(['activo' => true]);
        $admin = User::factory()->administrador()->create(['activo' => true]);

        $this->actingAs($admin)
            ->delete(route('ajustes.eliminar-cuenta.destroy'), [
                'contrasena_actual' => 'password',
                'confirmar_eliminacion' => '1',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertAuthenticatedAs($admin);
    }

    // =========================================================================
    // PERFIL (V4)
    // =========================================================================

    public function test_perfil_permite_cambiar_solo_datos_sin_pedir_contrasena(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => 'Nombre Nuevo',
                'email' => $usuario->email,
                'ingreso_mensual' => '500.000',
            ])
            ->assertRedirect(route('ajustes.perfil'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Nombre Nuevo', $usuario->fresh()->nombre);
        $this->assertSame(500000, $usuario->fresh()->ingreso_mensual);
    }

    public function test_perfil_permite_dejar_ingreso_vacio(): void
    {
        $usuario = User::factory()->create(['ingreso_mensual' => 1500000]);

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'ingreso_mensual' => '',
            ])
            ->assertRedirect(route('ajustes.perfil'));

        $this->assertNull($usuario->fresh()->ingreso_mensual);
    }

    public function test_perfil_rechaza_correo_duplicado(): void
    {
        $usuario = User::factory()->create();
        $otroUsuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $otroUsuario->email,
                'ingreso_mensual' => $usuario->ingreso_mensual,
                'contrasena_actual' => 'password',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_perfil_rechaza_contrasena_actual_incorrecta(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'ingreso_mensual' => $usuario->ingreso_mensual,
                'contrasena_actual' => 'incorrecta',
                'nueva_contrasena' => 'NuevaClave123',
                'nueva_contrasena_confirmation' => 'NuevaClave123',
            ])
            ->assertSessionHasErrors('contrasena_actual');

        $this->assertTrue(Hash::check('password', $usuario->fresh()->password));
    }

    public function test_perfil_cambia_contrasena_con_datos_correctos(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'ingreso_mensual' => $usuario->ingreso_mensual,
                'contrasena_actual' => 'password',
                'nueva_contrasena' => 'NuevaClave123',
                'nueva_contrasena_confirmation' => 'NuevaClave123',
            ])
            ->assertRedirect(route('ajustes.perfil'));

        $this->assertTrue(Hash::check('NuevaClave123', $usuario->fresh()->password));
    }

    public function test_perfil_muestra_ingreso_mensual_cero(): void
    {
        $usuario = User::factory()->create(['ingreso_mensual' => 0]);

        $this->actingAs($usuario)
            ->get(route('ajustes.perfil'))
            ->assertOk()
            ->assertSee('value="0"', false);
    }

    public function test_perfil_rechaza_ingreso_con_separadores_invalidos(): void
    {
        $usuario = User::factory()->create(['ingreso_mensual' => 1500000]);

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'ingreso_mensual' => '3.00.000',
            ])
            ->assertSessionHasErrors('ingreso_mensual');

        $this->assertSame(1500000, $usuario->fresh()->ingreso_mensual);
    }

    // =========================================================================
    // CAMBIO DE CORREO (V4)
    // =========================================================================

    public function test_cambiar_email_exige_contrasena_actual(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => 'nuevo@correo.com',
                'ingreso_mensual' => $usuario->ingreso_mensual,
                // sin contrasena_actual
            ])
            ->assertSessionHasErrors('contrasena_actual');

        $this->assertSame($usuario->email, $usuario->fresh()->email);
        $this->assertNull($usuario->fresh()->pending_email);
    }

    public function test_cambiar_email_envia_notificacion_al_nuevo_correo(): void
    {
        Notification::fake();

        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => 'nuevo@correo.com',
                'ingreso_mensual' => $usuario->ingreso_mensual,
                'contrasena_actual' => 'password',
            ])
            ->assertRedirect(route('ajustes.perfil'))
            ->assertSessionHas('exito');

        $fresh = $usuario->fresh();
        $this->assertSame($usuario->email, $fresh->email); // el correo no cambia aún
        $this->assertSame('nuevo@correo.com', $fresh->pending_email);
        $this->assertNotNull($fresh->email_token);
        $this->assertNotNull($fresh->pending_email_expires_at);

        Notification::assertSentOnDemand(
            ConfirmarCambioCorreo::class,
            function ($notification, $channels, $notifiable) {
                return $notifiable->routes['mail'] === 'nuevo@correo.com';
            }
        );
    }

    public function test_confirmar_correo_con_token_valido_actualiza_email_y_cierra_sesion(): void
    {
        $usuario = User::factory()->create([
            'pending_email' => 'nuevo@correo.com',
            'email_token' => hash('sha256', 'token-plano-123'),
            'pending_email_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($usuario)
            ->get(route('ajustes.confirmar-correo', ['token' => 'token-plano-123']))
            ->assertRedirect(route('login'))
            ->assertSessionHas('exito');

        $fresh = $usuario->fresh();
        $this->assertSame('nuevo@correo.com', $fresh->email);
        $this->assertNull($fresh->pending_email);
        $this->assertNull($fresh->email_token);
        $this->assertNull($fresh->pending_email_expires_at);

        $this->assertGuest();
    }

    public function test_confirmar_correo_con_token_invalido_falla(): void
    {
        $usuario = User::factory()->create([
            'pending_email' => 'nuevo@correo.com',
            'email_token' => hash('sha256', 'token-correcto'),
            'pending_email_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($usuario)
            ->get(route('ajustes.confirmar-correo', ['token' => 'token-equivocado']))
            ->assertSessionHasErrors('token');

        $this->assertSame($usuario->email, $usuario->fresh()->email);
    }

    public function test_confirmar_correo_con_token_vencido_falla(): void
    {
        $usuario = User::factory()->create([
            'pending_email' => 'nuevo@correo.com',
            'email_token' => hash('sha256', 'token-plano-123'),
            'pending_email_expires_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($usuario)
            ->get(route('ajustes.confirmar-correo', ['token' => 'token-plano-123']))
            ->assertSessionHasErrors('token');

        $this->assertSame($usuario->email, $usuario->fresh()->email);
    }

    public function test_confirmar_correo_con_email_ya_ocupado_falla(): void
    {
        User::factory()->create(['email' => 'nuevo@correo.com']);

        $usuario = User::factory()->create([
            'pending_email' => 'nuevo@correo.com',
            'email_token' => hash('sha256', 'token-plano-123'),
            'pending_email_expires_at' => now()->addHour(),
        ]);

        $this->actingAs($usuario)
            ->get(route('ajustes.confirmar-correo', ['token' => 'token-plano-123']))
            ->assertSessionHasErrors('token');

        $this->assertSame($usuario->email, $usuario->fresh()->email);
    }

    public function test_reenviar_confirmacion_regenera_token_y_reenvia(): void
    {
        Notification::fake();

        $usuario = User::factory()->create([
            'pending_email' => 'nuevo@correo.com',
            'email_token' => hash('sha256', 'token-viejo'),
            'pending_email_expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($usuario)
            ->post(route('ajustes.confirmar-correo.reenviar'))
            ->assertRedirect(route('ajustes.perfil'))
            ->assertSessionHas('exito');

        $fresh = $usuario->fresh();
        $this->assertNotSame(hash('sha256', 'token-viejo'), $fresh->email_token);
        $this->assertTrue($fresh->pending_email_expires_at->isFuture());

        Notification::assertSentOnDemand(ConfirmarCambioCorreo::class);
    }

    // =========================================================================
    // ELIMINAR CUENTA (J8a)
    // =========================================================================

    public function test_eliminar_cuenta_rechaza_contrasena_incorrecta(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->delete(route('ajustes.eliminar-cuenta.destroy'), [
                'contrasena_actual' => 'incorrecta',
                'confirmar_eliminacion' => '1',
            ])
            ->assertSessionHasErrors('contrasena_actual');

        $this->assertDatabaseHas('users', ['id' => $usuario->id]);
    }

    public function test_eliminar_cuenta_elimina_usuario_y_cierra_sesion(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->delete(route('ajustes.eliminar-cuenta.destroy'), [
                'contrasena_actual' => 'password',
                'confirmar_eliminacion' => '1',
            ])
            ->assertRedirect(route('inicio'))
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
        $this->assertGuest();
    }

}