<?php

namespace Tests\Unit;

use App\Services\CalculadoraCredito;
use PHPUnit\Framework\TestCase;

class CalculadoraCreditoTest extends TestCase
{
    public function test_calcula_cuota_total_e_intereses(): void
    {
        $calculadora = new CalculadoraCredito();

        $resultado = $calculadora->calcular(1000000, 2, 12);
        $this->assertEquals(94560, $resultado['cuota']);
        $this->assertEquals(1134715, $resultado['total']);
        $this->assertEquals(134715, $resultado['intereses']); 
    }

    public function test_genera_la_tabla_de_amortizacion_completa(): void
    {
        $calculadora = new CalculadoraCredito();

        $resultado = $calculadora->calcular(1000000, 2, 12);

        $this->assertCount(12, $resultado['amortizacion']);
        $this->assertSame(1, $resultado['amortizacion'][0]['periodo']);
        $this->assertSame(12, $resultado['amortizacion'][11]['periodo']);
    }

    public function test_calcula_un_credito_sin_intereses(): void
    {
        $calculadora = new CalculadoraCredito();

        $resultado = $calculadora->calcular(1200000, 0, 12);

        $this->assertEquals(100000, $resultado['cuota']);
        $this->assertEquals(1200000, $resultado['total']);
        $this->assertEquals(0, $resultado['intereses']);
    }
}