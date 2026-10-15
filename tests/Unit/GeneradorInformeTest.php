<?php

namespace Tests\Unit;

use App\Services\GeneradorInforme;
use PHPUnit\Framework\TestCase;

class GeneradorInformeTest extends TestCase
{
    public function test_calcula_correctamente_la_regla_50_30_20(): void
    {
        $generador = new GeneradorInforme();

        $informe = $generador->generar(3000000, [
            'necesidades' => 1500000,
            'discrecionales' => 900000,
            'ahorro' => 600000,
        ]);

        $this->assertSame(3000000, $informe['ingreso']);
        $this->assertSame(3000000, $informe['total_gastado']);
        $this->assertSame(0, $informe['saldo_disponible']);

        $this->assertSame(
            50.0,
            $informe['bloques']['necesidades']['porcentaje_real']
        );

        $this->assertSame(
            30.0,
            $informe['bloques']['discrecionales']['porcentaje_real']
        );

        $this->assertSame(
            20.0,
            $informe['bloques']['ahorro']['porcentaje_real']
        );
    }

    public function test_identifica_cuando_las_necesidades_superan_el_50_por_ciento(): void
    {
        $generador = new GeneradorInforme();

        $informe = $generador->generar(3000000, [
            'necesidades' => 1800000,
            'discrecionales' => 700000,
            'ahorro' => 200000,
        ]);

        $necesidades = $informe['bloques']['necesidades'];

        $this->assertSame(60.0, $necesidades['porcentaje_real']);
        $this->assertSame('excede', $necesidades['estado']);
        $this->assertSame(300000, $necesidades['diferencia_monto']);

        $this->assertSame(2700000, $informe['total_gastado']);
        $this->assertSame(300000, $informe['saldo_disponible']);
    }

    public function test_calcula_correctamente_cuando_no_hay_gastos(): void
    {
        $generador = new GeneradorInforme();

        $informe = $generador->generar(2000000, []);

        $this->assertSame(0, $informe['total_gastado']);
        $this->assertSame(2000000, $informe['saldo_disponible']);
        $this->assertTrue($informe['sin_gastos']);
        $this->assertTrue($informe['porcentajes_disponibles']);
    }

    public function test_no_divide_por_cero_si_el_ingreso_es_cero(): void
    {
        $generador = new GeneradorInforme();

        $informe = $generador->generar(0, [
            'necesidades' => 100000,
            'discrecionales' => 50000,
            'ahorro' => 0,
        ]);

        $this->assertFalse($informe['porcentajes_disponibles']);
        $this->assertNull(
            $informe['bloques']['necesidades']['porcentaje_real']
        );
        $this->assertSame(150000, $informe['total_gastado']);
        $this->assertSame(-150000, $informe['saldo_disponible']);
        $this->assertStringContainsString(
            'Ajustes',
            $informe['mensaje_general']
        );
    }

    public function test_incluye_los_gastos_sin_clasificar_en_el_total(): void
    {
        $generador = new GeneradorInforme();

        $informe = $generador->generar(1000000, [
            'necesidades' => 300000,
            'sin_clasificar' => 100000,
        ]);

        $this->assertSame(400000, $informe['total_gastado']);
        $this->assertSame(600000, $informe['saldo_disponible']);
        $this->assertSame(100000, $informe['monto_sin_clasificar']);
        $this->assertSame(10.0, $informe['porcentaje_sin_clasificar']);
    }
}
