<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de J4 (parte 1): qué rol entra a qué grupo de rutas y expulsión de cuentas suspendidas.
 * Para agregar una ruta nueva a la matriz, súmala a la lista de su grupo.
 * Ejecutar: php artisan test --filter=PermisosTest
 */
class PermisosTest extends TestCase
{
    use RefreshDatabase;

    // Rutas GET sin parámetros, por grupo.
    private const GRUPO_B = ['ajustes.perfil', 'ajustes.tipo-cuenta', 'ajustes.eliminar-cuenta'];
    private const GRUPO_C = ['panel', 'gastos.index', 'categorias.index', 'gastos-fijos.index', 'notificaciones.index',
                             'creditos.simulador', 'creditos.historial', 'informes.actual', 'informes.historial',
                             'metas.index', 'metas.invitaciones'];
    // El GRUPO_D desapareció: metas pasó al Grupo C.
    private const GRUPO_E = ['admin.usuarios', 'admin.categorias', 'admin.estadisticas'];

    private function usuarioDeRol(string $rol): User
    {
        return match ($rol) {
            'familiar'      => User::factory()->familiar()->create(),
            'administrador' => User::factory()->administrador()->create(),
            default         => User::factory()->create(),
        };
    }

    /** Comprueba una ruta para un rol y devuelve un texto de error si el resultado no es el esperado. */
    private function revisar(string $rol, string $ruta, int $esperado): ?string
    {
        $recibido = $this->actingAs($this->usuarioDeRol($rol))->get(route($ruta))->getStatusCode();

        return $recibido === $esperado ? null : "{$rol} en {$ruta}: esperado {$esperado}, recibido {$recibido}";
    }

    private function revisarMatriz(array $rutas, array $esperadoPorRol): void
    {
        $fallos = [];
        foreach ($esperadoPorRol as $rol => $codigo) {
            foreach ($rutas as $ruta) {
                if ($error = $this->revisar($rol, $ruta, $codigo)) {
                    $fallos[] = $error;
                }
            }
        }
        $this->assertSame([], $fallos, implode("\n", $fallos));
    }

    public function test_grupo_b_todos_los_roles_con_sesion_entran(): void
    {
        $this->revisarMatriz(self::GRUPO_B, ['estandar' => 200, 'familiar' => 200, 'administrador' => 200]);
    }

    public function test_grupo_c_estandar_y_familiar_entran_el_administrador_recibe_403(): void
    {
        $this->revisarMatriz(self::GRUPO_C, ['estandar' => 200, 'familiar' => 200, 'administrador' => 403]);
    }

    public function test_grupo_e_admin_solo_administrador(): void
    {
        $this->revisarMatriz(self::GRUPO_E, ['estandar' => 403, 'familiar' => 403, 'administrador' => 200]);
    }

    public function test_las_acciones_que_modifican_tambien_estan_protegidas(): void
    {
        $estandar = User::factory()->create();
        $admin = User::factory()->administrador()->create();

        // El administrador sigue bloqueado en rutas financieras.
        $this->actingAs($admin)->post(route('gastos.store'))->assertForbidden();
        $this->actingAs($admin)->post(route('creditos.guardar'))->assertForbidden();
        $this->actingAs($admin)->post(route('metas.store'))->assertForbidden();

        // El estándar no entra a administración.
        $this->actingAs($estandar)->put(route('admin.usuarios.rol', 1))->assertForbidden();
        $this->actingAs($estandar)->delete(route('admin.usuarios.destroy', 1))->assertForbidden();

        // El estándar SÍ entra a metas pero no puede crear una COMPARTIDA:
        // la validación lo devuelve con error (302 con errores), no 403.
        $this->actingAs($estandar)->post(route('metas.store'), [
            'compartida' => '1', 'nombre' => 'X', 'monto_objetivo' => '1000', 'correo' => 'a@a.com',
        ])->assertSessionHasErrors('compartida');
    }

    public function test_sin_sesion_todo_lo_privado_redirige_al_login(): void
    {
        foreach (array_merge(self::GRUPO_B, self::GRUPO_C, self::GRUPO_E) as $ruta) {
            $this->get(route($ruta))->assertRedirect(route('login'));
        }
    }

    public function test_cambiar_el_rol_surte_efecto_en_la_siguiente_peticion(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario)->get(route('gastos.index'))->assertOk();

        $usuario->rol = 'administrador';
        $usuario->save();

        $this->actingAs($usuario->fresh())->get(route('gastos.index'))->assertForbidden();
    }

    // ------------------------------------------------------------------ CUENTA SUSPENDIDA

    public function test_cuenta_suspendida_con_sesion_abierta_es_expulsada(): void
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario)->get(route('panel'))->assertOk();

        $usuario->activo = false;
        $usuario->save();

        $this->actingAs($usuario->fresh())->get(route('panel'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    public function test_el_administrador_suspendido_tambien_es_expulsado(): void
    {
        $admin = User::factory()->administrador()->inactivo()->create();

        $this->actingAs($admin)->get(route('admin.usuarios'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_cuenta_suspendida_en_una_peticion_que_pide_json_recibe_403(): void
    {
        $usuario = User::factory()->inactivo()->create();

        $this->actingAs($usuario)->getJson(route('panel'))->assertForbidden();
    }
}
