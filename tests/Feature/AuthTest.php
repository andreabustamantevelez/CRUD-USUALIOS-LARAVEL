<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\RestablecerContrasena;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Pruebas de J3: registro, login, límite de intentos, cierre de sesión, recuperación de
 * contraseña y protección de las rutas privadas. Los usuarios se crean con UserFactory.
 * Ejecutar: php artisan test --filter=AuthTest
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function datosRegistro(array $cambios = []): array
    {
        return array_merge([
            'nombre'                => 'Ana Pérez',
            'identificacion'        => '1020304050',
            'correo'                => 'ana@correo.com',
            'password'              => 'Clave2026*',
            'password_confirmation' => 'Clave2026*',
            'tipo_cuenta'           => 'estandar',
            'consentimiento'        => '1',
        ], $cambios);
    }

    // ------------------------------------------------------------------ REGISTRO

    public function test_registro_valido_crea_usuario_estandar_con_sesion(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro())
            ->assertRedirect(route('panel'))
            ->assertSessionHas('exito', 'Registro exitoso');

        $usuario = User::where('email', 'ana@correo.com')->firstOrFail();
        $this->assertSame('estandar', $usuario->rol);
        $this->assertTrue($usuario->activo);
        $this->assertSame('1020304050', $usuario->cedula);
        $this->assertNotNull($usuario->consentimiento_datos_at);
        $this->assertStringStartsWith('$2y$', $usuario->password);   // contraseña con hash
        $this->assertNotSame('Clave2026*', $usuario->password);
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_registro_familiar_guarda_rol_familiar(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['tipo_cuenta' => 'familiar']));

        $this->assertSame('familiar', User::firstWhere('email', 'ana@correo.com')->rol);
    }

    public function test_el_correo_se_guarda_en_minusculas(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['correo' => 'Ana@Correo.COM']));

        $this->assertDatabaseHas('users', ['email' => 'ana@correo.com']);
    }

    public function test_no_se_puede_registrar_como_administrador(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['tipo_cuenta' => 'administrador']))
            ->assertSessionHasErrors('tipo_cuenta');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_campos_extra_rol_y_activo_se_ignoran(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['rol' => 'administrador', 'activo' => 0]));

        $usuario = User::firstWhere('email', 'ana@correo.com');
        $this->assertSame('estandar', $usuario->rol);
        $this->assertTrue($usuario->activo);
    }

    public function test_registro_con_correo_repetido(): void
    {
        User::factory()->create(['email' => 'ana@correo.com']);

        $this->from(route('registro'))
            ->post(route('registro.guardar'), $this->datosRegistro(['identificacion' => '999888777']))
            ->assertRedirect(route('registro'))
            ->assertSessionHasErrors(['correo' => 'Este correo ya se encuentra registrado']);

        $this->assertGuest();
    }

    public function test_registro_con_cedula_repetida(): void
    {
        User::factory()->create(['cedula' => '1020304050']);

        $this->post(route('registro.guardar'), $this->datosRegistro())
            ->assertSessionHasErrors('identificacion');
    }

    public function test_registro_con_cedula_que_no_es_numerica(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['identificacion' => '10203abc']))
            ->assertSessionHasErrors('identificacion');
    }

    public function test_registro_con_contrasena_corta(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['password' => 'Ab1*', 'password_confirmation' => 'Ab1*']))
            ->assertSessionHasErrors('password');
    }

    public function test_registro_con_contrasenas_distintas(): void
    {
        $this->post(route('registro.guardar'), $this->datosRegistro(['password_confirmation' => 'OtraClave2026*']))
            ->assertSessionHasErrors('password');
    }

    public function test_registro_sin_consentimiento(): void
    {
        $datos = $this->datosRegistro();
        unset($datos['consentimiento']);

        $this->post(route('registro.guardar'), $datos)->assertSessionHasErrors('consentimiento');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_tras_un_error_se_conservan_los_datos_menos_la_contrasena(): void
    {
        User::factory()->create(['email' => 'ana@correo.com']);

        $this->post(route('registro.guardar'), $this->datosRegistro(['identificacion' => '999888777']));

        // Laravel guarda lo escrito en la sesión (_old_input) para que old() lo repinte.
        // La contraseña NUNCA se devuelve al formulario.
        $this->assertSame('Ana Pérez', session('_old_input.nombre'));
        $this->assertSame('ana@correo.com', session('_old_input.correo'));
        $this->assertNull(session('_old_input.password'));
    }

    // ------------------------------------------------------------------ LOGIN

    public function test_login_correcto_de_usuario_estandar_va_al_panel(): void
    {
        $usuario = User::factory()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'Clave2026*'])
            ->assertRedirect(route('panel'));

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_login_del_administrador_va_a_estadisticas(): void
    {
        User::factory()->administrador()->create(['email' => 'admin@correo.com', 'password' => 'Clave2026*']);

        $this->post(route('login.entrar'), ['correo' => 'admin@correo.com', 'password' => 'Clave2026*'])
            ->assertRedirect(route('admin.estadisticas'));
    }

    public function test_login_vuelve_a_la_url_protegida_que_se_intento_abrir(): void
    {
        User::factory()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $this->get(route('gastos.index'))->assertRedirect(route('login'));

        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'Clave2026*'])
            ->assertRedirect(route('gastos.index'));
    }

    public function test_login_con_contrasena_incorrecta_da_mensaje_generico(): void
    {
        User::factory()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'incorrecta'])
            ->assertSessionHasErrors(['correo' => 'Usuario o contraseña incorrectos']);

        $this->assertGuest();
    }

    public function test_login_con_correo_inexistente_da_el_mismo_mensaje(): void
    {
        $this->post(route('login.entrar'), ['correo' => 'nadie@correo.com', 'password' => 'Clave2026*'])
            ->assertSessionHasErrors(['correo' => 'Usuario o contraseña incorrectos']);
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        User::factory()->inactivo()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'Clave2026*'])
            ->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    public function test_el_inactivo_con_contrasena_mala_recibe_el_mensaje_generico(): void
    {
        // Así nadie puede descubrir que la cuenta existe y está suspendida.
        User::factory()->inactivo()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'mala'])
            ->assertSessionHasErrors(['correo' => 'Usuario o contraseña incorrectos']);
    }

    public function test_despues_de_5_intentos_fallidos_se_bloquea_aunque_la_clave_sea_correcta(): void
    {
        User::factory()->create(['email' => 'ana@correo.com', 'password' => 'Clave2026*']);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'mala']);
        }

        $respuesta = $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'Clave2026*']);

        $respuesta->assertSessionHasErrors('correo');
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('correo'));
        $this->assertGuest();
    }

    public function test_login_no_funciona_con_una_sesion_ya_iniciada(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get(route('login'))->assertRedirect();
    }

    // ------------------------------------------------------------------ LOGOUT Y RUTAS PRIVADAS

    public function test_cerrar_sesion_por_post(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->post(route('logout'))->assertRedirect(route('inicio'));

        $this->assertGuest();
    }

    public function test_cerrar_sesion_por_get_no_existe(): void
    {
        $this->actingAs(User::factory()->create())->get('/cerrar-sesion')->assertStatus(405);
    }

    public function test_las_rutas_privadas_redirigen_al_login_sin_sesion(): void
    {
        foreach (['panel', 'gastos.index', 'categorias.index', 'gastos-fijos.index', 'creditos.simulador',
                  'informes.actual', 'metas.index', 'ajustes.perfil', 'admin.usuarios'] as $ruta) {
            $this->get(route($ruta))->assertRedirect(route('login'));
        }
    }

    public function test_un_usuario_con_sesion_ve_el_panel(): void
    {
        $this->actingAs(User::factory()->create())->get(route('panel'))->assertOk();
    }

    // ------------------------------------------------------------------ RECUPERAR CONTRASEÑA

    public function test_pedir_enlace_envia_la_notificacion_y_da_mensaje_neutro(): void
    {
        Notification::fake();
        $usuario = User::factory()->create(['email' => 'ana@correo.com']);

        $this->post(route('password.email'), ['correo' => 'ana@correo.com'])
            ->assertSessionHas('estado');

        Notification::assertSentTo($usuario, RestablecerContrasena::class);
    }

    public function test_pedir_enlace_con_correo_inexistente_da_el_mismo_mensaje_y_no_envia_nada(): void
    {
        Notification::fake();

        $this->post(route('password.email'), ['correo' => 'nadie@correo.com'])
            ->assertSessionHas('estado')
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_restablecer_con_token_valido_cambia_la_contrasena(): void
    {
        $usuario = User::factory()->create(['email' => 'ana@correo.com', 'password' => 'ClaveVieja2026*']);
        $token = Password::broker()->createToken($usuario);

        $this->post(route('password.update'), [
            'token' => $token, 'correo' => 'ana@correo.com',
            'password' => 'ClaveNueva2026*', 'password_confirmation' => 'ClaveNueva2026*',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('ClaveNueva2026*', $usuario->fresh()->password));
        $this->post(route('login.entrar'), ['correo' => 'ana@correo.com', 'password' => 'ClaveNueva2026*'])
            ->assertRedirect(route('panel'));
    }

    public function test_el_token_solo_sirve_una_vez(): void
    {
        $usuario = User::factory()->create(['email' => 'ana@correo.com']);
        $token = Password::broker()->createToken($usuario);
        $datos = ['token' => $token, 'correo' => 'ana@correo.com',
                  'password' => 'ClaveNueva2026*', 'password_confirmation' => 'ClaveNueva2026*'];

        $this->post(route('password.update'), $datos)->assertRedirect(route('login'));
        $this->post(route('password.update'), $datos)->assertSessionHasErrors('correo');
    }

    public function test_el_token_vencido_no_sirve(): void
    {
        $usuario = User::factory()->create(['email' => 'ana@correo.com']);
        $token = Password::broker()->createToken($usuario);

        $this->travel(61)->minutes();

        $this->post(route('password.update'), [
            'token' => $token, 'correo' => 'ana@correo.com',
            'password' => 'ClaveNueva2026*', 'password_confirmation' => 'ClaveNueva2026*',
        ])->assertSessionHasErrors('correo');
    }

    public function test_restablecer_con_token_falso_falla(): void
    {
        $usuario = User::factory()->create(['email' => 'ana@correo.com', 'password' => 'ClaveVieja2026*']);

        $this->post(route('password.update'), [
            'token' => 'token-inventado', 'correo' => 'ana@correo.com',
            'password' => 'ClaveNueva2026*', 'password_confirmation' => 'ClaveNueva2026*',
        ])->assertSessionHasErrors('correo');

        $this->assertTrue(Hash::check('ClaveVieja2026*', $usuario->fresh()->password));
    }

    public function test_el_formulario_de_restablecer_muestra_el_token(): void
    {
        $this->get(route('password.reset', ['token' => 'abc123']))->assertOk()->assertSee('abc123');
    }
}
