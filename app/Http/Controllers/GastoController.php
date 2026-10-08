<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            auth()->loginUsingId(1);
        }

        $userId = auth()->id();

        $gastos = Gasto::delUsuario($userId)
            ->with('categoria')
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        $categorias = Categoria::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id');
        })->get();

        $totalMes = Gasto::delUsuario($userId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sum('monto');

        return view(
            'app.gastos.index',
            compact('gastos', 'categorias', 'totalMes')
        );
    }

 public function create()
{
    if (!auth()->check()) {
        auth()->loginUsingId(1);
    }

    $userId = auth()->id();

    $categorias = Categoria::where(function ($q) use ($userId) {
        $q->where('user_id', $userId)
          ->orWhereNull('user_id');
    })->get();

    return view('app.gastos.create', compact('categorias'));
}

    public function store(Request $request)
    {
        $request->validate([
            'monto' => 'required|integer|min:1',
            'categoria_id' => 'required|exists:categorias,id',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Gasto::create([
            'user_id' => auth()->id(),
            'categoria_id' => $request->categoria_id,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()
            ->route('gastos.index')
            ->with('exito', 'Gasto guardado correctamente.');
    }

    public function edit(Gasto $gasto)
    {
        if ($gasto->user_id !== auth()->id()) {
            abort(403);
        }

        $categorias = Categoria::where(function ($q) {
            $q->where('user_id', auth()->id())
              ->orWhereNull('user_id');
        })->get();

        return view(
            'app.gastos.edit',
            compact('gasto', 'categorias')
        );
    }

    public function update(Request $request, Gasto $gasto)
    {
        if ($gasto->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'monto' => 'required|integer|min:1',
            'categoria_id' => 'required|exists:categorias,id',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string|max:200',
        ]);

        $gasto->update([
            'categoria_id' => $request->categoria_id,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()
            ->route('gastos.index')
            ->with('exito', 'Gasto actualizado correctamente.');
    }

    public function destroy(Gasto $gasto)
    {
        if ($gasto->user_id !== auth()->id()) {
            abort(403);
        }

        $gasto->delete();

        return redirect()
            ->route('gastos.index')
            ->with('exito', 'Gasto eliminado correctamente.');
    }
}