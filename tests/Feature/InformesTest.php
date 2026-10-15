<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\Informe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_puede_consultar_el_informe_de_otro(): void
    {
        $usuarioA = User::factory()->create(['ingreso_mensual' => 3000000]);
        $usuarioB = User::factory()->create(['ingreso_mensual' => 2500000]);

        $informe = Informe::create([
            'user_id' => $usuarioA->id,
            'anio' => 2026,
            'mes' => 9,
            'ingreso' => 3000000,
            'total_gastado' => 500000,
        ]);

        $this->actingAs($usuarioB)
            ->get(route('informes.ver', $informe->id))
            ->assertNotFound();
    }

    public function test_el_informe_guardado_no_cambia_al_editar_un_gasto(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Arriendo',
            'bloque' => 'necesidades',
        ]);

        $gasto = Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 1200000,
            'fecha' => '2026-09-10',
        ]);

        $this->actingAs($usuario)
            ->post(route('informes.guardar'), [
                'mes' => '2026-09',
            ])
            ->assertRedirect()
            ->assertSessionHas('exito');

        $informe = Informe::where('user_id', $usuario->id)
            ->where('anio', 2026)
            ->where('mes', 9)
            ->firstOrFail();

        $this->assertSame(1200000, (int) $informe->total_gastado);

        $this->assertDatabaseHas('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'bloque' => 'necesidades',
            'monto_gastado' => 1200000,
        ]);

        // Simulamos que el usuario modifica el gasto después de guardar.
        $gasto->update(['monto' => 500000]);

        // El informe histórico debe conservar los valores originales.
        $informe->refresh();

        $this->assertSame(1200000, (int) $informe->total_gastado);

        $this->assertDatabaseHas('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'monto_gastado' => 1200000,
        ]);

        $this->assertDatabaseMissing('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'monto_gastado' => 500000,
        ]);

        // El usuario debe poder abrir su fotografía histórica.
        $this->get(route('informes.ver', $informe->id))
            ->assertOk()
            ->assertSee('Consulta de un informe guardado')
            ->assertSee('1.200.000');
    }

    public function test_el_informe_muestra_un_estado_vacio_si_no_hay_gastos(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Aún no tienes gastos en este mes');
    }

    public function test_el_informe_indica_cuando_falta_registrar_el_ingreso(): void
    {
        $usuario = User::factory()->sinIngreso()->create();

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Registra tu ingreso mensual');
    }
}
