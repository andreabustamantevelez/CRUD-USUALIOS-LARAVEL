<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\Informe;
use App\Models\Notificacion;
use App\Models\SimulacionCredito;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Pruebas de J4 (parte 2): cada persona solo ve y cambia SUS datos (Policies y scope delUsuario).
 * Para un modelo nuevo con dueño: crea su Policy y súmalo a datosPorModelo().
 * Ejecutar: php artisan test --filter=AislamientoTest
 */
class AislamientoTest extends TestCase
{
    use RefreshDatabase;

    /** Crea un registro de cada modelo con dueño, perteneciente a $usuario. */
    private function registrosDe(User $usuario): array
    {
        return [
            'Gasto'             => Gasto::factory()->create(['user_id' => $usuario->id]),
            'GastoFijo'         => GastoFijo::factory()->create(['user_id' => $usuario->id]),
            'SimulacionCredito' => SimulacionCredito::factory()->create(['user_id' => $usuario->id]),
            'Notificacion'      => Notificacion::factory()->create(['user_id' => $usuario->id]),
            'Informe'           => Informe::create(['user_id' => $usuario->id, 'anio' => 2026, 'mes' => 9,
                                                    'ingreso' => 0, 'total_gastado' => 0]),
        ];
    }

    public function test_el_dueno_puede_ver_y_los_ajenos_no(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->familiar()->create();

        foreach ($this->registrosDe($a) as $nombre => $registro) {
            $this->assertTrue($a->can('view', $registro), "{$nombre}: el dueño debe poder ver");
            $this->assertFalse($b->can('view', $registro), "{$nombre}: un ajeno NO debe poder ver");
        }
    }

    public function test_el_dueno_edita_y_borra_y_el_ajeno_no(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $registros = $this->registrosDe($a);

        // Modelos que se pueden editar y borrar.
        foreach (['Gasto', 'GastoFijo', 'Notificacion'] as $nombre) {
            $this->assertTrue($a->can('update', $registros[$nombre]), "{$nombre}: dueño edita");
            $this->assertTrue($a->can('delete', $registros[$nombre]), "{$nombre}: dueño borra");
            $this->assertFalse($b->can('update', $registros[$nombre]), "{$nombre}: ajeno no edita");
            $this->assertFalse($b->can('delete', $registros[$nombre]), "{$nombre}: ajeno no borra");
        }
    }

    public function test_reglas_especiales_de_simulaciones_informes_y_gastos_fijos(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $r = $this->registrosDe($a);

        // La simulación se borra pero no se edita.
        $this->assertTrue($a->can('delete', $r['SimulacionCredito']));
        $this->assertFalse($a->can('update', $r['SimulacionCredito']));
        $this->assertFalse($b->can('delete', $r['SimulacionCredito']));

        // El informe guardado no se edita ni se borra a mano.
        $this->assertTrue($a->can('view', $r['Informe']));
        $this->assertFalse($a->can('update', $r['Informe']));
        $this->assertFalse($a->can('delete', $r['Informe']));

        // Pagar un gasto fijo: solo el dueño.
        $this->assertTrue($a->can('pagar', $r['GastoFijo']));
        $this->assertFalse($b->can('pagar', $r['GastoFijo']));
    }

    public function test_el_administrador_nunca_toca_datos_financieros(): void
    {
        $dueno = User::factory()->create();
        $admin = User::factory()->administrador()->create();

        foreach ($this->registrosDe($dueno) as $nombre => $registro) {
            foreach (['view', 'update', 'delete'] as $accion) {
                $this->assertFalse($admin->can($accion, $registro), "{$nombre}: el administrador no puede {$accion}");
            }
        }
        $this->assertFalse($admin->can('viewAny', Gasto::class));
        $this->assertFalse($admin->can('create', Gasto::class));
    }

    // ------------------------------------------------------------------ CATEGORÍAS

    public function test_categorias_propias_del_sistema_y_ajenas(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $admin = User::factory()->administrador()->create();

        $propia = Categoria::factory()->create(['user_id' => $a->id]);
        $sistema = Categoria::factory()->delSistema()->create();

        // Propia: el dueño todo; el ajeno nada.
        $this->assertTrue($a->can('usar', $propia));
        $this->assertTrue($a->can('update', $propia));
        $this->assertTrue($a->can('delete', $propia));
        $this->assertFalse($b->can('view', $propia));
        $this->assertFalse($b->can('usar', $propia));
        $this->assertFalse($b->can('update', $propia));
        $this->assertFalse($b->can('delete', $propia));

        // Del sistema: todos la ven y la usan; nadie desde la zona del usuario la edita ni borra.
        $this->assertTrue($a->can('view', $sistema));
        $this->assertTrue($a->can('usar', $sistema));
        $this->assertFalse($a->can('update', $sistema));
        $this->assertFalse($a->can('delete', $sistema));

        // Administrador: gestiona las del sistema, no ve las de las personas y no las usa en gastos.
        $this->assertTrue($admin->can('update', $sistema));
        $this->assertFalse($admin->can('delete', $sistema));
        $this->assertFalse($admin->can('update', $propia));
        $this->assertFalse($admin->can('view', $propia));
        $this->assertFalse($admin->can('usar', $sistema));
    }

    // ------------------------------------------------------------------ SCOPE EN LISTADOS

    public function test_los_listados_con_delUsuario_no_incluyen_filas_ajenas(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        Gasto::factory()->count(3)->create(['user_id' => $a->id]);
        Gasto::factory()->count(2)->create(['user_id' => $b->id]);

        $this->assertCount(3, Gasto::delUsuario($a->id)->get());
        $this->assertCount(2, Gasto::delUsuario($b->id)->get());
        $this->assertSame(0, Gasto::delUsuario($a->id)->where('user_id', $b->id)->count());

        // También en totales: la suma de A no incluye los gastos de B.
        $this->assertSame(
            (int) Gasto::where('user_id', $a->id)->sum('monto'),
            (int) Gasto::delUsuario($a->id)->sum('monto')
        );
    }

    public function test_el_scope_existe_en_todos_los_modelos_con_dueno(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->registrosDe($a);
        $this->registrosDe($b);

        foreach ([Gasto::class, GastoFijo::class, SimulacionCredito::class, Notificacion::class, Informe::class] as $clase) {
            /** @var Model $clase */
            $this->assertSame(1, $clase::delUsuario($a->id)->count(), "{$clase}: debe devolver solo 1 fila de A");
        }
    }

    // ------------------------------------------------------------------ DE PUNTA A PUNTA (HTTP)

    /**
     * Las rutas reales de gastos todavía son "maqueta". Para probar el 403 de verdad se registra una
     * ruta de prueba con el mismo patrón que usarán los controladores: binding + authorize.
     */
    private function rutaDePrueba(): void
    {
        Route::middleware(['web', 'auth', 'cuenta.activa'])
            ->put('/__prueba/gastos/{gasto}', function (Gasto $gasto) {
                Gate::authorize('update', $gasto);
                $gasto->update(['descripcion' => 'cambiado']);

                return response('ok');
            });
    }

    public function test_un_ajeno_recibe_403_y_el_gasto_no_cambia(): void
    {
        $this->rutaDePrueba();
        $a = User::factory()->create();
        $b = User::factory()->create();
        $gasto = Gasto::factory()->create(['user_id' => $a->id, 'descripcion' => 'original']);

        $this->actingAs($b)->put("/__prueba/gastos/{$gasto->id}")->assertForbidden();

        $this->assertSame('original', $gasto->fresh()->descripcion);
    }

    public function test_el_dueno_si_puede_cambiar_su_gasto(): void
    {
        $this->rutaDePrueba();
        $a = User::factory()->create();
        $gasto = Gasto::factory()->create(['user_id' => $a->id, 'descripcion' => 'original']);

        $this->actingAs($a)->put("/__prueba/gastos/{$gasto->id}")->assertOk();

        $this->assertSame('cambiado', $gasto->fresh()->descripcion);
    }

    public function test_el_administrador_recibe_403_aunque_el_gasto_exista(): void
    {
        $this->rutaDePrueba();
        $gasto = Gasto::factory()->create(['descripcion' => 'original']);
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->put("/__prueba/gastos/{$gasto->id}")->assertForbidden();
    }
}
