<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSimulacionRequest;
use App\Models\SimulacionCredito;
use App\Services\CalculadoraCredito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SimulacionCreditoController extends Controller
{
    /**
     * Muestra el simulador de créditos.
     *
     * Obtiene únicamente la información del usuario autenticado
     * necesaria para mostrar su ingreso mensual y sus simulaciones.
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        $ingresoMensual = $usuario->ingreso_mensual;

        $simulaciones = $usuario->simulaciones()
            ->latest()
            ->get();

        return view('app.creditos.simulador', compact(
            'ingresoMensual',
            'simulaciones'
        ));
    }

    /**
     * Guarda una nueva simulación de crédito.
     *
     * Los valores financieros se recalculan en el servidor mediante
     * CalculadoraCredito antes de almacenarlos.
     */
    public function store(
        StoreSimulacionRequest $request,
        CalculadoraCredito $calculadora
    ) {
        $usuario = $request->user();

        $datos = $request->validated();

        $resultado = $calculadora->calcular(
            (float) $datos['monto'],
            (float) $datos['tasa'],
            (int) $datos['plazo']
        );

        $usuario->simulaciones()->create([
            'nombre' => $datos['nombre'],
            'monto' => (int) $datos['monto'],
            'tasa_mensual' => $datos['tasa'],
            'plazo_meses' => (int) $datos['plazo'],
            'cuota' => $resultado['cuota'],
            'total_pagar' => $resultado['total'],
            'intereses' => $resultado['intereses'],
        ]);

        return redirect()
            ->route('creditos.simulador')
            ->with('exito', 'Simulación guardada correctamente.');
    }
    public function historial(Request $request)
{
    $usuario = $request->user();

    $simulaciones = SimulacionCredito::delUsuario($usuario->id)
        ->latest()
        ->get();

    return view('app.creditos.historial', compact('simulaciones'));
}

public function destroy(Request $request, SimulacionCredito $simulacion)
{
    Gate::authorize('delete', $simulacion);

    $simulacion->delete();

    return redirect()
        ->route('creditos.historial')
        ->with('exito', 'Simulación eliminada correctamente.');
}
}
