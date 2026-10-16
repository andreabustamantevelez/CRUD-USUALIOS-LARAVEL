<?php

namespace App\Services;

use App\Models\GastoFijo;
use App\Models\PagoGastoFijo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class ServicioGastosFijos
{
    /**
     * Calcula la fecha de vencimiento del gasto fijo para un mes.
     * Si el día configurado no existe, utiliza el último día del mes.
     */
    public function fechaVencimiento(GastoFijo $gastoFijo, Carbon $fecha): Carbon
    {
        $inicioMes = $fecha->copy()->startOfMonth();

        $ultimoDia = $inicioMes->daysInMonth;

        $dia = min((int) $gastoFijo->dia_vencimiento, $ultimoDia);

        return $inicioMes->day($dia)->startOfDay();
    }

    /**
     * Crea los pagos del periodo para los gastos fijos activos.
     * La restricción única de la base de datos evita duplicados.
     */
    public function generarPagosDelPeriodo(User $usuario, Carbon $fecha): Collection
    {
        return DB::transaction(function () use ($usuario, $fecha) {
            $periodo = $fecha->format('Y-m');

            $gastosFijos = GastoFijo::delUsuario((int) $usuario->id)
                ->where('activo', true)
                ->get();

            foreach ($gastosFijos as $gastoFijo) {
                PagoGastoFijo::firstOrCreate(
                    [
                        'gasto_fijo_id' => $gastoFijo->id,
                        'periodo' => $periodo,
                    ],
                    [
                        'user_id' => $usuario->id,
                        'fecha_vencimiento' => $this->fechaVencimiento(
                            $gastoFijo,
                            $fecha
                        )->toDateString(),
                        'estado' => 'pendiente',
                        'fecha_pago' => null,
                        'monto' => $gastoFijo->monto,
                    ]
                );
            }

            return PagoGastoFijo::delUsuario((int) $usuario->id)
                ->where('periodo', $periodo)
                ->with('gastoFijo')
                ->get();
        });
    }

    /**
     * Actualiza a vencidos los pagos pendientes cuya fecha ya pasó.
     */
    public function marcarVencidos(User $usuario, Carbon $fecha): int
    {
        return PagoGastoFijo::delUsuario((int) $usuario->id)
            ->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', $fecha->toDateString())
            ->update(['estado' => 'vencido']);
    }

    /**
     * Devuelve los pagos pendientes del periodo indicado.
     */
    public function pendientesDelPeriodo(User $usuario, Carbon $fecha): Collection
    {
        $this->generarPagosDelPeriodo($usuario, $fecha);
        $this->marcarVencidos($usuario, $fecha);

        return PagoGastoFijo::delUsuario((int) $usuario->id)
            ->where('periodo', $fecha->format('Y-m'))
            ->where('estado', 'pendiente')
            ->with('gastoFijo.categoria')
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    /**
     * Devuelve pagos no realizados que vencen entre hoy y los próximos
     * días indicados, inclusive.
     */
    public function proximosAVencer(
        User $usuario,
        Carbon $fecha,
        int $dias = 3
    ): Collection {
        if ($dias < 0) {
            throw new \InvalidArgumentException(
                'Los días no pueden ser negativos.'
            );
        }

        $this->generarPagosDelPeriodo($usuario, $fecha);
        $this->marcarVencidos($usuario, $fecha);

        $hoy = $fecha->toDateString();
        $limite = $fecha->copy()->addDays($dias)->toDateString();

        return PagoGastoFijo::delUsuario((int) $usuario->id)
            ->where('estado', 'pendiente')
            ->whereDate('fecha_vencimiento', '>=', $hoy)
            ->whereDate('fecha_vencimiento', '<=', $limite)
            ->with('gastoFijo.categoria')
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    /**
     * Devuelve pagos vencidos y todavía no pagados.
     */
    public function vencidos(User $usuario, Carbon $fecha): Collection
    {
        $this->generarPagosDelPeriodo($usuario, $fecha);
        $this->marcarVencidos($usuario, $fecha);

        return PagoGastoFijo::delUsuario((int) $usuario->id)
            ->where('estado', 'vencido')
            ->with('gastoFijo.categoria')
            ->orderBy('fecha_vencimiento')
            ->get();
    }

    /**
     * Indica si el gasto fijo está pagado en un año y mes específicos.
     */
    public function estaPagado(
        GastoFijo $gastoFijo,
        int $anio,
        int $mes
    ): bool {
        if ($mes < 1 || $mes > 12) {
            throw new \InvalidArgumentException(
                'El mes debe estar entre 1 y 12.'
            );
        }

        return PagoGastoFijo::delUsuario((int) $gastoFijo->user_id)
            ->where('gasto_fijo_id', $gastoFijo->id)
            ->where('periodo', sprintf('%04d-%02d', $anio, $mes))
            ->where('estado', 'pagado')
            ->exists();
    }
    /**
     * Marca como pagado el gasto fijo correspondiente al periodo.
     * Si el pago ya existe, lo reutiliza y no crea duplicados.
     */
    public function marcarPagado(
        User $usuario,
        GastoFijo $gastoFijo,
        Carbon $fecha
    ): PagoGastoFijo {
        if ((int) $gastoFijo->user_id !== (int) $usuario->id) {
            throw new AuthorizationException(
                'No tienes permiso para pagar este gasto fijo.'
            );
        }

        return DB::transaction(function () use ($usuario, $gastoFijo, $fecha) {
            $periodo = $fecha->format('Y-m');

            $pago = PagoGastoFijo::where('user_id', $usuario->id)
                ->where('gasto_fijo_id', $gastoFijo->id)
                ->where('periodo', $periodo)
                ->lockForUpdate()
                ->first();

            if (!$pago) {
                $pago = PagoGastoFijo::create([
                    'user_id' => $usuario->id,
                    'gasto_fijo_id' => $gastoFijo->id,
                    'periodo' => $periodo,
                    'fecha_vencimiento' => $this->fechaVencimiento($gastoFijo, $fecha)->toDateString(),
                    'estado' => 'pagado',
                    'fecha_pago' => $fecha->toDateString(),
                    'monto' => $gastoFijo->monto,
                ]);
            } else {
                $pago->update([
                    'estado' => 'pagado',
                    'fecha_pago' => $fecha->toDateString(),
                ]);
            }

            app(\App\Services\ServicioNotificaciones::class)
                ->cancelarPorPago($usuario, $pago);

            return $pago->fresh();
        });
    }
}