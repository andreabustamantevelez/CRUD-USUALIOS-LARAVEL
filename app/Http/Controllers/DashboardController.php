<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\Meta;
use App\Models\Notificacion;
use App\Models\PagoGastoFijo;
use App\Services\GeneradorInforme;
use App\Services\ServicioGastosFijos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Qué hace: arma la pantalla principal (panel) del usuario estándar o familiar.
 *           Métricas del mes, distribución 50/30/20, próximos vencimientos,
 *           últimos gastos, notificaciones recientes y tarjeta de metas si es familiar.
 * Quién lo usa: ruta 'panel' (routes/modulos/panel.php).
 * Dependencias: C1 (gastos por periodo) — hoy con whereYear/whereMonth directo,
 *               C3 (ingreso mensual), C4 (notificaciones), C6 (metas activas),
 *               GeneradorInforme (Valeria) y ServicioGastosFijos (Nikol).
 * Dueño: Juan (J8).
 */
class DashboardController extends Controller
{
    public function index(
        Request $request,
        GeneradorInforme $generador,
        ServicioGastosFijos $gastosFijos
    ): View {
        $usuario = $request->user();
        $hoy = Carbon::today();

        // Refresca pagos del mes y vencidos: el dashboard siempre ve datos al día.
        $gastosFijos->generarPagosDelPeriodo($usuario, $hoy);
        $gastosFijos->marcarVencidos($usuario, $hoy);

        // TODO(J8): reemplazar por Gasto::delPeriodo($anio, $mes) cuando Nikol agregue
        // el scope del contrato C1. Mientras tanto, whereYear + whereMonth directo.
        $totalGastadoMes = (int) Gasto::delUsuario((int) $usuario->id)
            ->whereYear('fecha', $hoy->year)
            ->whereMonth('fecha', $hoy->month)
            ->sum('monto');

        $ingreso = (int) ($usuario->ingreso_mensual ?? 0);
        $disponible = $ingreso - $totalGastadoMes;

        $fijosPendientes = PagoGastoFijo::delUsuario((int) $usuario->id)
            ->where('periodo', $hoy->format('Y-m'))
            ->whereIn('estado', ['pendiente', 'vencido'])
            ->count();

        $metricas = [
            [
                'clave' => 'ingreso',
                'titulo' => 'Ingreso del mes',
                'valor' => $ingreso,
                'es_dinero' => true,
            ],
            [
                'clave' => 'gastos',
                'titulo' => 'Gastos del mes',
                'valor' => $totalGastadoMes,
                'es_dinero' => true,
            ],
            [
                'clave' => 'disponible',
                'titulo' => 'Disponible del mes',
                'valor' => $disponible,
                'es_dinero' => true,
            ],
            [
                'clave' => 'fijos_pendientes',
                'titulo' => 'Gastos fijos pendientes',
                'valor' => $fijosPendientes,
                'es_dinero' => false,
            ],
        ];

        $totalesPorBloque = $this->totalesPorBloque($usuario, $hoy);
        $calculos = $generador->generar($ingreso, $totalesPorBloque);
        $distribucion = $calculos['bloques'];

        $proximos = $gastosFijos->proximosAVencer($usuario, $hoy, 3)->take(5);

        $ultimosGastos = Gasto::delUsuario((int) $usuario->id)
            ->with('categoria')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $notificaciones = Notificacion::delUsuario((int) $usuario->id)
            ->where('estado', 'pendiente')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $metas = collect();
        if ($usuario->rol === 'familiar') {
            $metas = Meta::query()
                ->where('estado', 'activa')
                ->delParticipante((int) $usuario->id)
                ->withSum('aportes', 'monto')
                ->orderByDesc('created_at')
                ->take(3)
                ->get();
        }

        $esUsuarioNuevo = $ingreso === 0
            && $usuario->gastos()->count() === 0
            && $usuario->gastosFijos()->count() === 0;

        return view('app.dashboard', compact(
            'metricas',
            'distribucion',
            'proximos',
            'ultimosGastos',
            'notificaciones',
            'metas',
            'esUsuarioNuevo',
        ));
    }

    /**
     * Totales del mes por bloque 50/30/20. Los gastos sin categoría (o con categoría
     * sin bloque) caen en "sin_clasificar".
     *
     * @return array{necesidades:int, discrecionales:int, ahorro:int, sin_clasificar:int}
     */
    private function totalesPorBloque($usuario, Carbon $fecha): array
    {
        $totales = [
            'necesidades' => 0,
            'discrecionales' => 0,
            'ahorro' => 0,
            'sin_clasificar' => 0,
        ];

        $gastos = Gasto::delUsuario((int) $usuario->id)
            ->whereYear('fecha', $fecha->year)
            ->whereMonth('fecha', $fecha->month)
            ->with('categoria')
            ->get();

        foreach ($gastos as $gasto) {
            $bloque = $gasto->categoria && in_array(
                $gasto->categoria->bloque,
                Categoria::BLOQUES,
                true
            ) ? $gasto->categoria->bloque : 'sin_clasificar';

            $totales[$bloque] += (int) $gasto->monto;
        }

        return $totales;
    }
}