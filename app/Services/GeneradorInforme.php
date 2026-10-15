<?php

namespace App\Services;

class GeneradorInforme
{
    /**
     * Porcentajes recomendados para la regla 50/30/20.
     */
    private const BLOQUES = [
        'necesidades' => [
            'nombre' => 'Necesidades',
            'recomendado' => 50,
        ],
        'discrecionales' => [
            'nombre' => 'Gastos discrecionales',
            'recomendado' => 30,
        ],
        'ahorro' => [
            'nombre' => 'Ahorro',
            'recomendado' => 20,
        ],
    ];

    /**
     * Genera los cálculos del informe a partir del ingreso y los totales.
     *
     * @param int $ingreso Ingreso mensual del usuario.
     * @param array $totalesPorBloque Totales por bloque de gastos.
     */
    public function generar(int $ingreso, array $totalesPorBloque): array
    {
        $totalGastado = 0;
        $bloques = [];

        foreach (self::BLOQUES as $clave => $configuracion) {
            $monto = max(0, (int) ($totalesPorBloque[$clave] ?? 0));
            $totalGastado += $monto;

            $porcentajeReal = $ingreso > 0
                ? round(($monto / $ingreso) * 100, 2)
                : null;

            $porcentajeRecomendado = $configuracion['recomendado'];

            $montoRecomendado = $ingreso > 0
                ? (int) round($ingreso * $porcentajeRecomendado / 100)
                : null;

            $diferenciaPorcentual = $porcentajeReal !== null
                ? round($porcentajeReal - $porcentajeRecomendado, 2)
                : null;

            $diferenciaMonto = $montoRecomendado !== null
                ? $monto - $montoRecomendado
                : null;

            $estado = $porcentajeReal === null
    ? 'sin_calcular'
    : ($porcentajeReal > $porcentajeRecomendado
        ? 'excede'
        : 'dentro');

            $mensaje = $this->generarMensaje(
                $clave,
                $porcentajeReal,
                $porcentajeRecomendado,
                $diferenciaMonto
            );

            $bloques[$clave] = [
                'nombre' => $configuracion['nombre'],
                'monto_real' => $monto,
                'porcentaje_real' => $porcentajeReal,
                'porcentaje_recomendado' => $porcentajeRecomendado,
                'monto_recomendado' => $montoRecomendado,
                'diferencia_porcentual' => $diferenciaPorcentual,
                'diferencia_monto' => $diferenciaMonto,
                'estado' => $estado,
                'mensaje' => $mensaje,
            ];
        }

        $montoSinClasificar = max(
            0,
            (int) ($totalesPorBloque['sin_clasificar'] ?? 0)
        );

        $totalGastado += $montoSinClasificar;

        return [
            'ingreso' => max(0, $ingreso),
            'total_gastado' => $totalGastado,
            'saldo_disponible' => max(0, $ingreso) - $totalGastado,
            'porcentajes_disponibles' => $ingreso > 0,
            'sin_gastos' => $totalGastado === 0,
            'monto_sin_clasificar' => $montoSinClasificar,
            'porcentaje_sin_clasificar' => $ingreso > 0
                ? round(($montoSinClasificar / $ingreso) * 100, 2)
                : null,
            'mensaje_general' => $ingreso <= 0
                ? 'Registra tu ingreso mensual en Ajustes para ver tu informe.'
                : ($totalGastado === 0
                    ? 'Todavía no tienes gastos registrados para este periodo.'
                    : null),
            'bloques' => $bloques,
        ];
    }

    private function generarMensaje(
        string $bloque,
        ?float $porcentajeReal,
        int $porcentajeRecomendado,
        ?int $diferenciaMonto
    ): string {
        if ($porcentajeReal === null) {
            return 'Registra tu ingreso mensual para calcular este porcentaje.';
        }

        if ($bloque === 'ahorro' && $porcentajeReal < $porcentajeRecomendado) {
            return 'Estás por debajo del porcentaje recomendado para el ahorro.';
        }

        if ($porcentajeReal > $porcentajeRecomendado) {
            return match ($bloque) {
                'necesidades' => 'Tus necesidades superan el 50 % recomendado; revisa servicios y arriendo.',
                'discrecionales' => 'Tus gastos discrecionales superan el 30 % recomendado; revisa tus gastos opcionales.',
                'ahorro' => 'Tu ahorro supera el 20 % recomendado.',
                default => 'Este bloque supera el porcentaje recomendado.',
            };
        }

        if ($bloque === 'ahorro') {
            return 'Tu ahorro está dentro del porcentaje recomendado.';
        }

        return 'Tus gastos están dentro del porcentaje recomendado.';
    }
}
