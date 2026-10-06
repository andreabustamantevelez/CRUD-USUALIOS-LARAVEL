<?php

namespace App\Services;

/**
 * Calculadora de créditos con sistema de amortización francés.
 *
 * Se encarga de calcular la cuota mensual, el total a pagar,
 * los intereses y la tabla de amortización de una simulación.
 *
 * Es utilizada por SimulacionCreditoController.
 *
 * No depende de la base de datos.
 */
class CalculadoraCredito
{
    /**
     * Calcula una simulación de crédito.
     *
     * @param float $monto Monto solicitado.
     * @param float $tasaMensual Tasa de interés mensual en porcentaje.
     * @param int $plazo Plazo en meses.
     * @return array
     */
    public function calcular(
        float $monto,
        float $tasaMensual,
        int $plazo
    ): array {
        $tasa = $tasaMensual / 100;

        if ($tasa === 0.0) {
            $cuota = $monto / $plazo;
        } else {
            $cuota = $monto
                * ($tasa / (1 - pow(1 + $tasa, -$plazo)));
        }

        $total = $cuota * $plazo;
        $intereses = $total - $monto;

        $saldo = $monto;
        $amortizacion = [];

        for ($periodo = 1; $periodo <= $plazo; $periodo++) {
            $interesesPeriodo = $saldo * $tasa;
            $capitalPeriodo = $cuota - $interesesPeriodo;
            $saldoFinal = $saldo - $capitalPeriodo;

            if ($saldoFinal < 0) {
                $saldoFinal = 0;
            }

            $amortizacion[] = [
                'periodo' => $periodo,
                'cuota' => $cuota,
                'intereses' => $interesesPeriodo,
                'capital' => $capitalPeriodo,
                'saldo' => $saldoFinal,
            ];

            $saldo = $saldoFinal;
        }

        return [
            'cuota' => round($cuota),
            'total' => round($total),
            'intereses' => round($intereses),
            'amortizacion' => $amortizacion,
        ];
    }
}