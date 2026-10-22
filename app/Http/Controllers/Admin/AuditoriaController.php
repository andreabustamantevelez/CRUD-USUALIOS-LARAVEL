<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Vista de solo lectura de la tabla auditorias.
 * Filtros: acción, admin, rango de fechas.
 * Sin escritura: la tabla solo recibe INSERT desde Auditoria::registrar().
 */
class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'accion' => ['nullable', 'string', 'max:50'],
            'admin'  => ['nullable', 'integer', 'exists:users,id'],
            'desde'  => ['nullable', 'date'],
            'hasta'  => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $auditorias = Auditoria::query()
            ->with('user:id,nombre,email,rol')
            ->when($filtros['accion'] ?? null, fn ($q, $a) => $q->where('accion', 'like', "{$a}%"))
            ->when($filtros['admin'] ?? null, fn ($q, $u) => $q->where('user_id', $u))
            ->when($filtros['desde'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($filtros['hasta'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        // Listas para los selects de filtro.
        $acciones = Auditoria::query()
            ->select('accion')
            ->distinct()
            ->orderBy('accion')
            ->pluck('accion');

        $admins = User::where('rol', 'administrador')
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('admin.auditoria', compact('auditorias', 'acciones', 'admins', 'filtros'));
    }
}