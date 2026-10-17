<?php

namespace Tests\Feature;

use App\Models\Meta;
use App\Models\ParticipacionMeta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas del cambio de tipo de cuenta desde Ajustes (contrato C7).
 * Ejecutar: php artisan test --filter=AjustesTest
 */
class AjustesTest extends TestCase
{
    use RefreshDatabase;

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
            'meta_id' => $meta->id, 'user_id' => $u->id,
            'estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString(),
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
            'meta_id' => $meta->id, 'user_id' => $u->id,
            'estado_invitacion' => 'aceptada', 'fecha_union' => now()->toDateString(),
        ]);

        $this->actingAs($u)
            ->put(route('ajustes.tipo-cuenta.update'), ['tipo_cuenta' => 'estandar'])
            ->assertRedirect(route('ajustes.tipo-cuenta'));
        $this->assertSame('estandar', $u->fresh()->rol);
    }

    public function test_tipo_cuenta_lo_puede_ver_cualquier_rol_con_sesion(): void
    {
        $usuarios = [
            User::factory()->create(),                       // estandar (estado por defecto)
            User::factory()->familiar()->create(),
            User::factory()->administrador()->create(),
        ];

        foreach ($usuarios as $u) {
            $this->actingAs($u)->get(route('ajustes.tipo-cuenta'))->assertOk();
        }
    }

    public function test_perfil_actualiza_nombre_correo_e_ingreso_con_puntos(): void
{
    $usuario = User::factory()->create([
        'ingreso_mensual' => 1500000,
    ]);

    $this->actingAs($usuario)
        ->put(route('ajustes.perfil.update'), [
            'nombre' => 'Valeria Prueba',
            'email' => $usuario->email,
            'ingreso_mensual' => '3.000.000',
        ])
        ->assertRedirect(route('ajustes.perfil'))
        ->assertSessionHas('exito');

    $this->assertSame('Valeria Prueba', $usuario->fresh()->nombre);
    $this->assertSame(3000000, $usuario->fresh()->ingreso_mensual);
}

    public function test_perfil_permite_dejar_ingreso_vacio(): void
    {
    $usuario = User::factory()->create([
        'ingreso_mensual' => 1500000,
    ]);

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

    $this->assertTrue(
        \Illuminate\Support\Facades\Hash::check(
            'password',
            $usuario->fresh()->password
        )
    );
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

    $this->assertTrue(
        \Illuminate\Support\Facades\Hash::check(
            'NuevaClave123',
            $usuario->fresh()->password
        )
    );
}

    public function test_perfil_muestra_ingreso_mensual_cero(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 0,
        ]);

        $this->actingAs($usuario)
            ->get(route('ajustes.perfil'))
            ->assertOk()
            ->assertSee('value="0"', false);
    }

    public function test_perfil_rechaza_ingreso_con_separadores_invalidos(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 1500000,
        ]);

        $this->actingAs($usuario)
            ->put(route('ajustes.perfil.update'), [
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'ingreso_mensual' => '3.00.000',
            ])
            ->assertSessionHasErrors('ingreso_mensual');

        $this->assertSame(
            1500000,
            $usuario->fresh()->ingreso_mensual
        );
    }

    public function test_eliminar_cuenta_rechaza_contrasena_incorrecta(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->delete(route('ajustes.eliminar-cuenta.destroy'), [
                'contrasena_actual' => 'incorrecta',
                'confirmar_eliminacion' => '1',
            ])
            ->assertSessionHasErrors('contrasena_actual');

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
        ]);
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

    $this->assertDatabaseMissing('users', [
        'id' => $usuario->id,
    ]);

    $this->assertGuest();
}

public function test_eliminar_cuenta_protege_al_ultimo_administrador_activo(): void
{
    $administrador = User::factory()->administrador()->create([
        'activo' => true,
    ]);

    $this->actingAs($administrador)
        ->delete(route('ajustes.eliminar-cuenta.destroy'), [
            'contrasena_actual' => 'password',
            'confirmar_eliminacion' => '1',
        ])
        ->assertSessionHasErrors('cuenta');

    $this->assertDatabaseHas('users', [
        'id' => $administrador->id,
        'rol' => 'administrador',
        'activo' => true,
    ]);

    $this->assertAuthenticatedAs($administrador);
}

}