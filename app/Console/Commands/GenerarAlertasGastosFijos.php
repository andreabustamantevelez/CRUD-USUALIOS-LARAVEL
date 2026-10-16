<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\PagoGastoFijo;
use App\Models\User;
use App\Services\ServicioGastosFijos;
use App\Services\ServicioNotificaciones;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Qué hace: recorre los usuarios con finanzas, genera los pagos del mes,
 *           marca vencidos, y crea alertas "por vencer" (3 días o menos)
 *           y "vencido". Es idempotente: correrlo dos veces el mismo día
 *           no duplica notificaciones.
 * Quién lo usa: el programador diario (routes/console.php) y las pruebas.
 * Dueño: Juan (J6).
 * Ejemplo: php artisan alertas:gastos-fijos
 *          php artisan alertas:gastos-fijos --fecha=2026-10-27
 */
class GenerarAlertasGastosFijos extends Command
{
    protected $signature = 'alertas:gastos-fijos
                            {--fecha= : Fecha YYYY-MM-DD a simular (opcional)}';

    protected $description = 'Genera las alertas diarias de gastos fijos (por vencer y vencidos).';

    public function handle(
        ServicioGastosFijos $gastosFijos,
        ServicioNotificaciones $notificaciones
    ): int {
        $fecha = $this->option('fecha')
            ? Carbon::createFromFormat('Y-m-d', (string) $this->option('fecha'))->startOfDay()
            : Carbon::today();

        $this->info("Generando alertas de gastos fijos para {$fecha->toDateString()}.");

        $nuevasPorVencer = 0;
        $nuevasVencidas = 0;

        User::query()
            ->where('activo', true)
            ->whereIn('rol', ['estandar', 'familiar'])
            ->orderBy('id')
            ->chunkById(100, function ($usuarios) use (
                $fecha,
                $gastosFijos,
                $notificaciones,
                &$nuevasPorVencer,
                &$nuevasVencidas
            ) {
                foreach ($usuarios as $usuario) {
                    $nuevasPorVencer += $this->alertarPorVencer(
                        $usuario,
                        $fecha,
                        $gastosFijos,
                        $notificaciones
                    );

                    $nuevasVencidas += $this->alertarVencidos(
                        $usuario,
                        $fecha,
                        $gastosFijos,
                        $notificaciones
                    );
                }
            });

        $this->info("Nuevas por vencer: {$nuevasPorVencer}. Nuevas vencidas: {$nuevasVencidas}.");

        return self::SUCCESS;
    }

    private function alertarPorVencer(
        User $usuario,
        Carbon $fecha,
        ServicioGastosFijos $gastosFijos,
        ServicioNotificaciones $notificaciones
    ): int {
        $creadas = 0;

        foreach ($gastosFijos->proximosAVencer($usuario, $fecha, 3) as $pago) {
            $clave = $this->clavePorVencer($pago);

            $yaExistiaPendiente = Notificacion::delUsuario((int) $usuario->id)
                ->where('clave_unica', $clave)
                ->where('estado', ServicioNotificaciones::ESTADO_PENDIENTE)
                ->exists();

            $notificaciones->crear(
                $usuario,
                ServicioNotificaciones::TIPO_POR_VENCER,
                sprintf(
                    'Tu gasto fijo "%s" vence el %s.',
                    $pago->gastoFijo?->nombre ?? 'sin nombre',
                    $pago->fecha_vencimiento->format('d/m/Y')
                ),
                [
                    'pago_gasto_fijo_id' => $pago->id,
                    'clave_unica' => $clave,
                ]
            );

            if (! $yaExistiaPendiente) {
                $creadas++;
            }
        }

        return $creadas;
    }

    private function alertarVencidos(
        User $usuario,
        Carbon $fecha,
        ServicioGastosFijos $gastosFijos,
        ServicioNotificaciones $notificaciones
    ): int {
        $creadas = 0;

        foreach ($gastosFijos->vencidos($usuario, $fecha) as $pago) {
            // Si quedaba una alerta "por vencer" pendiente para este pago,
            // la cancelamos: ya no aplica.
            Notificacion::delUsuario((int) $usuario->id)
                ->where('pago_gasto_fijo_id', $pago->id)
                ->where('tipo', ServicioNotificaciones::TIPO_POR_VENCER)
                ->where('estado', ServicioNotificaciones::ESTADO_PENDIENTE)
                ->update(['estado' => ServicioNotificaciones::ESTADO_CANCELADA]);

            $clave = $this->claveVencido($pago);

            $yaExistiaPendiente = Notificacion::delUsuario((int) $usuario->id)
                ->where('clave_unica', $clave)
                ->where('estado', ServicioNotificaciones::ESTADO_PENDIENTE)
                ->exists();

            $notificaciones->crear(
                $usuario,
                ServicioNotificaciones::TIPO_VENCIDO,
                sprintf(
                    'Tu gasto fijo "%s" está vencido desde el %s.',
                    $pago->gastoFijo?->nombre ?? 'sin nombre',
                    $pago->fecha_vencimiento->format('d/m/Y')
                ),
                [
                    'pago_gasto_fijo_id' => $pago->id,
                    'clave_unica' => $clave,
                ]
            );

            if (! $yaExistiaPendiente) {
                $creadas++;
            }
        }

        return $creadas;
    }

    private function clavePorVencer(PagoGastoFijo $pago): string
    {
        return 'por_vencer:' . $pago->id;
    }

    private function claveVencido(PagoGastoFijo $pago): string
    {
        return 'vencido:' . $pago->id;
    }
}