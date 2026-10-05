<?php

namespace Tests\Feature;

use App\Models\AporteMeta;
use App\Models\Categoria;
use App\Models\DetalleInforme;
use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\Informe;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\PagoGastoFijo;
use App\Models\ParticipacionMeta;
use App\Models\SimulacionCredito;
use App\Models\User;
use Database\Seeders\CategoriaSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Pruebas de la BASE DE DATOS (tarea J2).
 * Comprueban que las tablas, columnas, llaves foráneas y reglas de unicidad funcionan.
 * No dependen de usuarios "quemados": cada prueba crea los suyos con las factories.
 * RefreshDatabase: antes de cada prueba deja la base limpia (usa una base en MEMORIA, ver phpunit.xml).
 * Ejecutar:  php artisan test --filter=BaseDeDatosTest
 */
class BaseDeDatosTest extends TestCase
{
    use RefreshDatabase;

    /** Afirma que la operación es RECHAZADA por la base de datos. */
    private function rechaza(callable $operacion, string $mensaje = 'La base debía rechazar esta operación'): void
    {
        try {
            $operacion();
        } catch (QueryException) {
            $this->assertTrue(true);
            return;
        }
        $this->fail($mensaje);
    }

    // ---------------------------------------------------------------- estructura

    public function test_existen_todas_las_tablas(): void
    {
        foreach (['users', 'categorias', 'gastos', 'gastos_fijos', 'pagos_gasto_fijo', 'simulaciones_credito',
                  'informes', 'detalles_informe', 'metas', 'participaciones_meta', 'aportes_meta', 'notificaciones',
                  'sessions', 'password_reset_tokens'] as $tabla) {
            $this->assertTrue(Schema::hasTable($tabla), "Falta la tabla $tabla");
        }
    }

    public function test_no_existen_las_tablas_eliminadas(): void
    {
        $this->assertFalse(Schema::hasTable('saldo'));
        $this->assertFalse(Schema::hasTable('cuota_credito'));
    }

    public function test_columnas_clave_existen(): void
    {
        $esperadas = [
            'users'            => ['nombre', 'cedula', 'email', 'password', 'rol', 'activo', 'ingreso_mensual'],
            'categorias'       => ['user_id', 'nombre', 'bloque'],
            'gastos'           => ['user_id', 'categoria_id', 'monto', 'fecha', 'descripcion'],
            'gastos_fijos'     => ['periodicidad', 'activo', 'dia_vencimiento'],
            'pagos_gasto_fijo' => ['gasto_fijo_id', 'periodo', 'monto'],
            'informes'         => ['user_id', 'anio', 'mes', 'ingreso', 'total_gastado'],
            'metas'            => ['creador_id', 'monto_objetivo', 'estado'],
            'notificaciones'   => ['clave_unica', 'pago_gasto_fijo_id', 'meta_id'],
        ];
        foreach ($esperadas as $tabla => $columnas) {
            $this->assertTrue(Schema::hasColumns($tabla, $columnas), "Faltan columnas en $tabla");
        }
        $this->assertFalse(Schema::hasColumn('metas', 'monto_actual'), 'El avance se calcula, no se guarda');
    }

    // ---------------------------------------------------------------- usuarios

    public function test_correo_y_cedula_son_unicos(): void
    {
        $a = User::factory()->create();
        $this->rechaza(fn () => User::factory()->create(['email' => $a->email]), 'Correo repetido aceptado');
        $this->rechaza(fn () => User::factory()->create(['cedula' => $a->cedula]), 'Cédula repetida aceptada');
    }

    public function test_la_contrasena_se_guarda_con_hash(): void
    {
        $u = User::factory()->create(['password' => 'Clave2026*']);
        $this->assertNotSame('Clave2026*', $u->fresh()->password);
        $this->assertTrue(Hash::check('Clave2026*', $u->fresh()->password));
    }

    public function test_rol_y_activo_no_se_asignan_en_masa(): void
    {
        $u = User::create([
            'nombre' => 'Intruso', 'cedula' => '9999999999', 'email' => 'i@i.com',
            'password' => '12345678', 'rol' => 'administrador', 'activo' => false,
        ]);
        $this->assertSame('estandar', $u->fresh()->rol, 'Alguien pudo ponerse administrador');
        $this->assertTrue($u->fresh()->activo);
    }

    public function test_las_factories_crean_cada_rol(): void
    {
        $this->assertSame('estandar', User::factory()->create()->rol);
        $this->assertSame('familiar', User::factory()->familiar()->create()->rol);
        $this->assertSame('administrador', User::factory()->administrador()->create()->rol);
        $this->assertFalse(User::factory()->inactivo()->create()->activo);
        $this->assertNull(User::factory()->sinIngreso()->create()->ingreso_mensual);
    }

    // ---------------------------------------------------------------- gastos y categorías

    public function test_un_gasto_pertenece_a_su_usuario_y_categoria(): void
    {
        $u = User::factory()->create();
        $c = Categoria::factory()->delSistema()->create(['nombre' => 'Mercado', 'bloque' => 'necesidades']);
        $g = Gasto::factory()->create(['user_id' => $u->id, 'categoria_id' => $c->id, 'monto' => 45000, 'fecha' => '2026-10-14']);

        $this->assertTrue($g->usuario->is($u));
        $this->assertTrue($g->categoria->is($c));
        $this->assertCount(1, $u->gastos);
        $this->assertSame(45000, $g->fresh()->monto);
        $this->assertIsInt($g->fresh()->monto, 'El dinero debe ser entero (pesos)');
    }

    public function test_gasto_con_usuario_inexistente_es_rechazado(): void
    {
        $c = Categoria::factory()->delSistema()->create();
        $this->rechaza(fn () => Gasto::factory()->create(['user_id' => 99999, 'categoria_id' => $c->id]));
    }

    public function test_no_se_puede_borrar_una_categoria_que_tiene_gastos(): void
    {
        $c = Categoria::factory()->delSistema()->create();
        Gasto::factory()->create(['categoria_id' => $c->id]);
        $this->rechaza(fn () => $c->delete(), 'Se borró una categoría con gastos');
    }

    public function test_categoria_sin_dueno_es_del_sistema(): void
    {
        $this->assertTrue(Categoria::factory()->delSistema()->create()->esDelSistema());
        $this->assertFalse(Categoria::factory()->create()->esDelSistema());
    }

    public function test_el_scope_del_usuario_no_mezcla_gastos(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        Gasto::factory()->count(3)->create(['user_id' => $a->id]);
        Gasto::factory()->count(2)->create(['user_id' => $b->id]);
        $this->assertCount(3, Gasto::delUsuario($a->id)->get());
        $this->assertCount(2, Gasto::delUsuario($b->id)->get());
    }

    // ---------------------------------------------------------------- gastos fijos

    public function test_un_gasto_fijo_solo_tiene_un_pago_por_periodo(): void
    {
        $gf = GastoFijo::factory()->create();
        PagoGastoFijo::factory()->create(['gasto_fijo_id' => $gf->id, 'periodo' => '2026-10']);
        $this->rechaza(fn () => PagoGastoFijo::factory()->create(['gasto_fijo_id' => $gf->id, 'periodo' => '2026-10']),
            'Se aceptó un pago duplicado del mismo mes');
        PagoGastoFijo::factory()->create(['gasto_fijo_id' => $gf->id, 'periodo' => '2026-11']);  // otro mes: sí
        $this->assertCount(2, $gf->pagos);
    }

    // ---------------------------------------------------------------- informes

    public function test_dos_usuarios_pueden_tener_informe_del_mismo_mes(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        Informe::create(['user_id' => $a->id, 'anio' => 2026, 'mes' => 10, 'ingreso' => 3000000, 'total_gastado' => 1000000]);
        Informe::create(['user_id' => $b->id, 'anio' => 2026, 'mes' => 10, 'ingreso' => 2000000, 'total_gastado' => 500000]);
        $this->assertSame(2, Informe::count());
    }

    public function test_un_usuario_no_repite_informe_del_mismo_mes_y_el_detalle_se_borra_con_el_informe(): void
    {
        $a = User::factory()->create();
        $i = Informe::create(['user_id' => $a->id, 'anio' => 2026, 'mes' => 10, 'ingreso' => 1, 'total_gastado' => 1]);
        $this->rechaza(fn () => Informe::create(['user_id' => $a->id, 'anio' => 2026, 'mes' => 10, 'ingreso' => 2, 'total_gastado' => 2]));

        DetalleInforme::create(['informe_id' => $i->id, 'categoria_id' => null, 'categoria_nombre' => 'Mercado',
            'bloque' => 'necesidades', 'monto_gastado' => 45000, 'porcentaje_real' => 4.5, 'porcentaje_recomendado' => 50]);
        $this->assertCount(1, $i->detalles);
        $i->delete();
        $this->assertSame(0, DetalleInforme::count());
    }

    // ---------------------------------------------------------------- metas

    public function test_meta_total_conjunto_y_participacion_unica(): void
    {
        $ana  = User::factory()->familiar()->create();
        $luis = User::factory()->familiar()->create();
        $meta = Meta::factory()->create(['creador_id' => $ana->id]);

        ParticipacionMeta::create(['meta_id' => $meta->id, 'user_id' => $luis->id]);
        $this->rechaza(fn () => ParticipacionMeta::create(['meta_id' => $meta->id, 'user_id' => $luis->id]),
            'Se invitó dos veces a la misma persona');

        AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $ana->id,  'monto' => 500000, 'fecha' => '2026-10-14']);
        AporteMeta::create(['meta_id' => $meta->id, 'user_id' => $luis->id, 'monto' => 300000, 'fecha' => '2026-10-15']);
        $this->assertSame(800000, $meta->totalConjunto());
    }

    // ---------------------------------------------------------------- notificaciones

    public function test_notificacion_clave_unica_y_referencias_opcionales(): void
    {
        $u = User::factory()->create();
        Notificacion::factory()->create(['user_id' => $u->id]);   // sin pago ni meta: permitido
        Notificacion::factory()->create(['user_id' => $u->id, 'clave_unica' => 'por_vencer:1:2026-10']);
        $this->rechaza(fn () => Notificacion::factory()->create(['user_id' => $u->id, 'clave_unica' => 'por_vencer:1:2026-10']),
            'El comando diario podría duplicar alertas');
    }

    // ---------------------------------------------------------------- simulaciones

    public function test_simulacion_guarda_cuota_total_e_intereses(): void
    {
        $s = SimulacionCredito::factory()->create();
        $this->assertSame($s->total_pagar - $s->monto, $s->intereses);
    }

    // ---------------------------------------------------------------- borrar usuario

    public function test_al_borrar_un_usuario_se_borran_sus_datos_y_no_los_de_otros(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        Gasto::factory()->count(2)->create(['user_id' => $a->id]);
        Gasto::factory()->count(1)->create(['user_id' => $b->id]);
        GastoFijo::factory()->create(['user_id' => $a->id]);
        SimulacionCredito::factory()->create(['user_id' => $a->id]);
        Notificacion::factory()->create(['user_id' => $a->id]);

        $a->delete();

        $this->assertSame(0, Gasto::where('user_id', $a->id)->count());
        $this->assertSame(0, GastoFijo::where('user_id', $a->id)->count());
        $this->assertSame(0, SimulacionCredito::where('user_id', $a->id)->count());
        $this->assertSame(0, Notificacion::where('user_id', $a->id)->count());
        $this->assertSame(1, Gasto::where('user_id', $b->id)->count(), 'No debe tocar datos de otros');
    }

    // ---------------------------------------------------------------- seeder de categorías

    public function test_seeder_de_categorias_crea_14_validas_y_no_duplica(): void
    {
        $this->seed(CategoriaSeeder::class);
        $this->seed(CategoriaSeeder::class);   // segunda vez: no debe duplicar

        $this->assertSame(14, Categoria::count());
        $this->assertSame(0, Categoria::whereNotNull('user_id')->count());
        $this->assertSame(0, Categoria::whereNotIn('bloque', Categoria::BLOQUES)->count());
        foreach (Categoria::BLOQUES as $bloque) {
            $this->assertGreaterThan(0, Categoria::where('bloque', $bloque)->count(), "Ninguna categoría en $bloque");
        }
    }
}
