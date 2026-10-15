<?php

namespace Tests\Unit;

use App\Models\GastoFijo;
use App\Models\PagoGastoFijo;
use App\Models\User;
use App\Services\ServicioGastosFijos;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicioGastosFijosTest extends TestCase
{
    use RefreshDatabase;

    private ServicioGastosFijos $servicio;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = new ServicioGastosFijos();
    }

    public function test_vencimiento_dia_31_se_ajusta_al_ultimo_dia_de_abril(): void
    {
        $gastoFijo = new GastoFijo(['dia_vencimiento' => 31]);

        $resultado = $this->servicio->fechaVencimiento(
            $gastoFijo,
            Carbon::create(2026, 4, 10)
        );

        $this->assertSame('2026-04-30', $resultado->toDateString());
    }

    public function test_vencimiento_dia_31_se_ajusta_a_febrero_de_2027(): void
    {
        $gastoFijo = new GastoFijo(['dia_vencimiento' => 31]);

        $resultado = $this->servicio->fechaVencimiento(
            $gastoFijo,
            Carbon::create(2027, 2, 10)
        );

        $this->assertSame('2027-02-28', $resultado->toDateString());
    }

    public function test_vencimiento_dia_31_respeta_febrero_bisiesto(): void
    {
        $gastoFijo = new GastoFijo(['dia_vencimiento' => 31]);

        $resultado = $this->servicio->fechaVencimiento(
            $gastoFijo,
            Carbon::create(2028, 2, 10)
        );

        $this->assertSame('2028-02-29', $resultado->toDateString());
    }

    public function test_conserva_el_dia_si_existe_en_el_mes(): void
    {
        $gastoFijo = new GastoFijo(['dia_vencimiento' => 15]);

        $resultado = $this->servicio->fechaVencimiento(
            $gastoFijo,
            Carbon::create(2026, 10, 9)
        );

        $this->assertSame('2026-10-15', $resultado->toDateString());
    }

    public function test_generar_pagos_dos_veces_no_duplica_el_periodo(): void
    {
        $usuario = User::factory()->create();

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'dia_vencimiento' => 31,
            'monto' => 120000,
            'activo' => true,
        ]);

        $fecha = Carbon::create(2026, 10, 9);

        $primera = $this->servicio->generarPagosDelPeriodo(
            $usuario,
            $fecha
        );

        $segunda = $this->servicio->generarPagosDelPeriodo(
            $usuario,
            $fecha
        );

        $this->assertCount(1, $primera);
        $this->assertCount(1, $segunda);
        $this->assertSame(1, PagoGastoFijo::where(
            'gasto_fijo_id',
            $gastoFijo->id
        )->where('periodo', '2026-10')->count());

        $pago = PagoGastoFijo::where('gasto_fijo_id', $gastoFijo->id)
            ->where('periodo', '2026-10')
            ->firstOrFail();

        $this->assertSame(120000, (int) $pago->monto);
        $this->assertSame('2026-10-31', $pago->fecha_vencimiento->toDateString());
    }

    public function test_generar_pagos_no_mezcla_usuarios(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $gastoA = GastoFijo::factory()->create([
            'user_id' => $usuarioA->id,
            'activo' => true,
        ]);

        $gastoB = GastoFijo::factory()->create([
            'user_id' => $usuarioB->id,
            'activo' => true,
        ]);

        $fecha = Carbon::create(2026, 10, 9);

        $pagosA = $this->servicio->generarPagosDelPeriodo(
            $usuarioA,
            $fecha
        );

        $this->assertCount(1, $pagosA);
        $this->assertSame(
            $gastoA->id,
            (int) $pagosA->first()->gasto_fijo_id
        );

        $this->assertDatabaseMissing('pagos_gasto_fijo', [
            'gasto_fijo_id' => $gastoB->id,
            'periodo' => '2026-10',
        ]);
    }

    public function test_no_genera_pagos_para_gastos_fijos_inactivos(): void
    {
        $usuario = User::factory()->create();

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'activo' => false,
        ]);

        $pagos = $this->servicio->generarPagosDelPeriodo(
            $usuario,
            Carbon::create(2026, 10, 9)
        );

        $this->assertCount(0, $pagos);
        $this->assertDatabaseCount('pagos_gasto_fijo', 0);
    }
}