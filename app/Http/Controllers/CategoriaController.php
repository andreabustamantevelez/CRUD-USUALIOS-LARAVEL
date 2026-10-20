<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categorias\StoreCategoriaRequest;
use App\Models\Categoria;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $categorias = Categoria::where(function ($query) use ($userId) {
            $query->whereNull('user_id')
                ->orWhere('user_id', $userId);
        })
            ->withCount('gastos')
            ->orderByRaw('user_id IS NOT NULL')
            ->orderBy('nombre')
            ->get();

        $categoriaEditar = null;

        if ($request->filled('editar')) {
            $categoriaEditar = Categoria::where('id', $request->editar)
                ->where('user_id', $userId)
                ->firstOrFail();
        }

        return view('app.categorias.index', compact(
            'categorias',
            'categoriaEditar'
        ));
    }
public function store(StoreCategoriaRequest $request)
{
    Categoria::create([
        'user_id' => auth()->id(),
        'nombre'  => $request->validated()['nombre'],
        'bloque'  => $request->validated()['bloque'],
        'icono'   => $request->validated()['icono'] ?? null,
    ]);

    return redirect()
        ->route('categorias.index')
        ->with('exito', 'Categoría creada correctamente.');
}

    public function update(StoreCategoriaRequest $request, Categoria $categoria)
    {
        if ($categoria->user_id !== auth()->id()) {
            abort(403);
        }

        $categoria->update($request->validated());

        return redirect()
            ->route('categorias.index')
            ->with('exito', 'Categoría actualizada correctamente.');
    }


    public function destroy(Request $request, Categoria $categoria)
    {
        $userId = auth()->id();

        // Solo se pueden eliminar categorías propias.
        if ($categoria->user_id !== $userId) {
            abort(403);
        }

        // Verificar por separado si existen gastos de otros usuarios.
        if ($categoria->gastos()->where('user_id', '!=', $userId)->exists()) {
            abort(403);
        }

        // Contar los gastos propios sin reutilizar consultas filtradas.
        $cantidadGastos = $categoria->gastos()
            ->where('user_id', $userId)
            ->count();

        $destino = null;

        // Si tiene gastos, se debe seleccionar una categoría de destino.
        if ($cantidadGastos > 0) {
            $datos = $request->validate([
                'categoria_destino_id' => [
                    'required',
                    'integer',
                    'exists:categorias,id',
                ],
            ]);

            $destino = Categoria::where('id', $datos['categoria_destino_id'])
                ->where(function ($query) use ($userId) {
                    $query->whereNull('user_id')
                        ->orWhere('user_id', $userId);
                })
                ->first();

            if (!$destino || $destino->id === $categoria->id) {
                return back()->withErrors([
                    'categoria_destino_id' =>
                        'Selecciona una categoría de destino válida.',
                ]);
            }
        }

        DB::transaction(function () use ($categoria, $cantidadGastos, $destino, $userId) {
            if ($cantidadGastos > 0) {
                $categoria->gastos()
                    ->where('user_id', $userId)
                    ->update([
                        'categoria_id' => $destino->id,
                    ]);
            }

            $categoria->delete();
        });


        return redirect()
            ->route('categorias.index')
            ->with('exito', 'Categoría eliminada correctamente.');
    }
}