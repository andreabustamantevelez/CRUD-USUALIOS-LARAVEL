<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\GastoFijo;
use App\Models\Notificacion;
use App\Models\User;
use App\Services\ServicioGastosFijos;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GastoFijoController extends Controller
{
    /**
     * Lista los gastos fijos y el estado de los pagos del mes actual.
     */
    public function index(ServicioGastosFijos $servicio): View
    {
        $usuario = auth()->user();
        $fecha = Carbon::today();
        $periodo = $fecha->format('Y-m');

        // Generar los pagos del mes sin duplicarlos y actualizar vencimientos.
        $servicio->generarPagosDelPeriodo($usuario, $fecha);
        $servicio->marcarVencidos($usuario, $fecha);

        // Consultar exclusivamente los gastos fijos del usuario autenticado.
        $gastosFijos = GastoFijo::delUsuario((int) $usuario->id)
            ->with([
                'categoria',
                'pagos' => function ($consulta) use ($periodo) {
                    $consulta->where('periodo', $periodo);
                },
            ])
            ->orderBy('dia_vencimiento')
            ->get();

        $pagos = $gastosFijos
            ->pluck('pagos')
            ->flatten();

        $resumen = [
            [
                'titulo' => 'Pagados',
                'cantidad' => $pagos->where('estado', 'pagado')->count(),
                'estado' => 'pagado',
            ],
            [
                'titulo' => 'Próximos y pendientes',
                'cantidad' => $pagos->where('estado', 'pendiente')->count(),
                'estado' => 'pendiente',
            ],
            [
                'titulo' => 'Vencidos',
                'cantidad' => $pagos->where('estado', 'vencido')->count(),
                'estado' => 'vencido',
            ],
        ];

        // Mostrar categorías del sistema y categorías propias.
        $categorias = Categoria::query()
            ->where(function ($consulta) use ($usuario) {
                $consulta->whereNull('user_id')
                    ->orWhere('user_id', $usuario->id);
            })
            ->orderBy('nombre')
            ->get();

        return view('app.gastos-fijos.index', compact(
            'gastosFijos',
            'categorias',
            'resumen',
            'periodo'
        ));
    }

    /**
     * Crea un gasto fijo para el usuario autenticado.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', GastoFijo::class);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'monto' => ['required', 'integer', 'min:1'],
            'dia_vencimiento' => ['required', 'integer', 'between:1,31'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'periodicidad' => ['required', 'in:mensual'],
            'descripcion' => ['nullable', 'string', 'max:100'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $categoria = Categoria::findOrFail($datos['categoria_id']);
        $this->authorize('usar', $categoria);

        $datos['user_id'] = auth()->id();
        $datos['activo'] = $request->boolean('activo', true);

        GastoFijo::create($datos);

        return redirect()
            ->route('gastos-fijos.index')
            ->with('exito', 'Gasto fijo creado correctamente.');
    }

    /**
     * Actualiza un gasto fijo propio y cancela sus notificaciones pendientes.
     */
    public function update(
        Request $request,
        GastoFijo $gastoFijo
    ): RedirectResponse {
        $this->authorize('update', $gastoFijo);

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'monto' => ['required', 'integer', 'min:1'],
            'dia_vencimiento' => ['required', 'integer', 'between:1,31'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'periodicidad' => ['required', 'in:mensual'],
            'descripcion' => ['nullable', 'string', 'max:100'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $categoria = Categoria::findOrFail($datos['categoria_id']);
        $this->authorize('usar', $categoria);

        $datos['activo'] = $request->boolean('activo');

        DB::transaction(function () use ($gastoFijo, $datos) {
            $this->cancelarNotificacionesPendientes($gastoFijo);

            $gastoFijo->update($datos);
        });

        return redirect()
            ->route('gastos-fijos.index')
            ->with('exito', 'Gasto fijo actualizado correctamente.');
    }

    /**
     * Elimina un gasto fijo propio y cancela sus notificaciones pendientes.
     */
    public function destroy(GastoFijo $gastoFijo): RedirectResponse
    {
        $this->authorize('delete', $gastoFijo);

        DB::transaction(function () use ($gastoFijo) {
            $this->cancelarNotificacionesPendientes($gastoFijo);

            $gastoFijo->delete();
        });

        return redirect()
            ->route('gastos-fijos.index')
            ->with('exito', 'Gasto fijo eliminado correctamente.');
    }

    /**
     * Marca como pagado el gasto fijo del periodo actual.
     */
    public function pagar(
        GastoFijo $gastoFijo,
        ServicioGastosFijos $servicio
    ): RedirectResponse {
        $this->authorize('pagar', $gastoFijo);

        $servicio->marcarPagado(
            auth()->user(),
            $gastoFijo,
            Carbon::today()
        );

        return redirect()
            ->route('gastos-fijos.index')
            ->with('exito', 'Gasto fijo pagado');
    }

    /**
     * Cancela las alertas pendientes asociadas a los pagos del gasto fijo.
     */
    private function cancelarNotificacionesPendientes(
        GastoFijo $gastoFijo
    ): void {
        $idsPagos = $gastoFijo->pagos()->pluck('id');

        if ($idsPagos->isEmpty()) {
            return;
        }

        Notificacion::delUsuario((int) $gastoFijo->user_id)
            ->whereIn('pago_gasto_fijo_id', $idsPagos)
            ->where('estado', 'pendiente')
            ->update(['estado' => 'cancelada']);
    }
}
