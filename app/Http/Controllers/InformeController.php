<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\Informe;
use App\Models\User;
use App\Services\GeneradorInforme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    /**
     * Muestra el informe calculado para el mes seleccionado.
     */
    public function mes(Request $request, GeneradorInforme $generador)
    {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = $datos['mes'] ?? now()->format('Y-m');
        $fecha = Carbon::createFromFormat('!Y-m', $mes);

        $usuario = $request->user();
        $resultado = $this->calcularMes($usuario, $fecha, $generador);

        $informe = $resultado['calculos']['bloques'];
        $detalle = $resultado['detalle'];

        $informeGuardado = $usuario->informes()
            ->where('anio', $fecha->year)
            ->where('mes', $fecha->month)
            ->exists();

        return view('app.informes.actual', [
            'informe' => $informe,
            'detalle' => $detalle,
            'resumen' => $resultado['calculos'],
            'mesSeleccionado' => $mes,
            'informeGuardado' => $informeGuardado,
        ]);
    }

    /**
     * Guarda una fotografía del informe mensual.
     */
    public function guardar(Request $request, GeneradorInforme $generador)
    {
        $datos = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
        ]);

        $usuario = $request->user();
        $fecha = Carbon::createFromFormat('!Y-m', $datos['mes']);

        if ((int) ($usuario->ingreso_mensual ?? 0) <= 0) {
            return back()->with(
                'error',
                'Registra tu ingreso mensual antes de guardar el informe.'
            );
        }

        $yaExiste = $usuario->informes()
            ->where('anio', $fecha->year)
            ->where('mes', $fecha->month)
            ->exists();

        if ($yaExiste) {
            return redirect()
                ->route('informes.actual', ['mes' => $datos['mes']])
                ->with('error', 'Ya existe un informe guardado para este mes.');
        }

        $resultado = $this->calcularMes($usuario, $fecha, $generador);

        DB::transaction(function () use (
            $usuario,
            $fecha,
            $resultado
        ) {
            $informeGuardado = Informe::create([
                'user_id' => $usuario->id,
                'anio' => $fecha->year,
                'mes' => $fecha->month,
                'ingreso' => $usuario->ingreso_mensual,
                'total_gastado' => $resultado['calculos']['total_gastado'],
            ]);

            foreach ($resultado['detalle'] as $item) {
                $bloque = $item['bloque'];

                $porcentajeRecomendado = match ($bloque) {
                    'necesidades' => 50,
                    'discrecionales' => 30,
                    'ahorro' => 20,
                    default => 0,
                };

                $porcentajeReal = round(
                    ($item['monto_gastado'] / $usuario->ingreso_mensual) * 100,
                    2
                );

                $excede = isset($resultado['calculos']['bloques'][$bloque])
                    && $resultado['calculos']['bloques'][$bloque]['estado'] === 'excede';

                $informeGuardado->detalles()->create([
                    'categoria_id' => $item['categoria_id'],
                    'categoria_nombre' => $item['categoria_nombre'],
                    'bloque' => $bloque,
                    'monto_gastado' => $item['monto_gastado'],
                    'porcentaje_real' => $porcentajeReal,
                    'porcentaje_recomendado' => $porcentajeRecomendado,
                    'excede' => $excede,
                ]);
            }
        });

        return redirect()
            ->route('informes.actual', ['mes' => $datos['mes']])
            ->with('exito', 'El informe mensual se guardó correctamente.');
    }

    /**
     * Muestra el historial del usuario autenticado.
     */
    public function historial(Request $request)
    {
        $informes = $request->user()
            ->informes()
            ->with('detalles')
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->paginate(12);

        return view('app.informes.historial', [
            'informes' => $informes,
        ]);
    }

    /**
     * Abre una fotografía histórica sin recalcularla con los gastos actuales.
     */
    public function ver(
        Request $request,
        int $id,
        GeneradorInforme $generador
    ) {
        $informeGuardado = $request->user()
            ->informes()
            ->with('detalles')
            ->findOrFail($id);

        $totalesPorBloque = [
            'necesidades' => 0,
            'discrecionales' => 0,
            'ahorro' => 0,
            'sin_clasificar' => 0,
        ];

        foreach ($informeGuardado->detalles as $item) {
            $bloque = in_array(
                $item->bloque,
                Categoria::BLOQUES,
                true
            ) ? $item->bloque : 'sin_clasificar';

            $totalesPorBloque[$bloque] += (int) $item->monto_gastado;
        }

        $calculos = $generador->generar(
            (int) $informeGuardado->ingreso,
            $totalesPorBloque
        );

        $detalle = $informeGuardado->detalles->map(function ($item) {
            return [
                'categoria_id' => $item->categoria_id,
                'categoria_nombre' => $item->categoria_nombre,
                'bloque' => $item->bloque,
                'monto_gastado' => (int) $item->monto_gastado,
                'porcentaje_real' => (float) $item->porcentaje_real,
                'porcentaje_recomendado' => (int) $item->porcentaje_recomendado,
                'excede' => (bool) $item->excede,
            ];
        });

        return view('app.informes.actual', [
            'informe' => $calculos['bloques'],
            'detalle' => $detalle,
            'resumen' => $calculos,
            'mesSeleccionado' => sprintf(
                '%04d-%02d',
                $informeGuardado->anio,
                $informeGuardado->mes
            ),
            'informeGuardado' => true,
            'esHistorico' => true,
            'fechaGuardado' => $informeGuardado->created_at,
        ]);
    }

    /**
     * Calcula los gastos del usuario para el mes indicado.
     *
     * No guarda ni modifica registros.
     */
    private function calcularMes(
        User $usuario,
        Carbon $fecha,
        GeneradorInforme $generador
    ): array {
        $inicio = $fecha->copy()->startOfMonth()->toDateString();
        $fin = $fecha->copy()->endOfMonth()->toDateString();

        $gastos = Gasto::query()
            ->where('user_id', $usuario->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->with('categoria')
            ->get();

        $totalesPorBloque = [
            'necesidades' => 0,
            'discrecionales' => 0,
            'ahorro' => 0,
            'sin_clasificar' => 0,
        ];

        $categorias = [];

        foreach ($gastos as $gasto) {
            $categoria = $gasto->categoria;

            $bloque = $categoria && in_array(
                $categoria->bloque,
                Categoria::BLOQUES,
                true
            ) ? $categoria->bloque : 'sin_clasificar';

            $monto = (int) $gasto->monto;
            $totalesPorBloque[$bloque] += $monto;

            $clave = $categoria
                ? (string) $categoria->id
                : 'sin_categoria';

            if (!isset($categorias[$clave])) {
                $categorias[$clave] = [
                    'categoria_id' => $categoria?->id,
                    'categoria_nombre' => $categoria?->nombre
                        ?? 'Sin categoría',
                    'bloque' => $bloque,
                    'monto_gastado' => 0,
                ];
            }

            $categorias[$clave]['monto_gastado'] += $monto;
        }

        $calculos = $generador->generar(
            (int) ($usuario->ingreso_mensual ?? 0),
            $totalesPorBloque
        );

        $detalle = array_values(array_map(
            function (array $item) use ($usuario, $calculos) {
                $ingreso = (int) ($usuario->ingreso_mensual ?? 0);

                $item['porcentaje_real'] = $ingreso > 0
                    ? round(($item['monto_gastado'] / $ingreso) * 100, 2)
                    : null;

                $item['porcentaje_recomendado'] = match ($item['bloque']) {
                    'necesidades' => 50,
                    'discrecionales' => 30,
                    'ahorro' => 20,
                    default => 0,
                };

                $item['excede'] = isset(
                    $calculos['bloques'][$item['bloque']]
                ) && $calculos['bloques'][$item['bloque']]['estado'] === 'excede';

                return $item;
            },
            $categorias
        ));

        return [
            'calculos' => $calculos,
            'detalle' => $detalle,
        ];
    }
}
