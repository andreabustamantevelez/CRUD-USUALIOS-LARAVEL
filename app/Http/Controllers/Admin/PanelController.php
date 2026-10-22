<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\User;

/**
 * PanelController — dashboard principal del administrador.
 *
 * REGLA DE ORO (§11): el admin NUNCA ve datos financieros. Solo COUNT y
 * agregados. Nada de gastos, ingresos, informes, metas ni simulaciones.
 *
 * Solo lectura. No modifica nada.
 */
class PanelController extends Controller
{
    public function index()
    {
        // ---- KPIs de usuarios (solo COUNT) ----
        $totalUsuarios       = User::count();
        $usuariosActivos     = User::where('activo', true)->count();
        $usuariosSuspendidos = User::where('activo', false)->count();

        // Distribución por rol en UNA sola consulta.
        $conteoPorRol = User::query()
            ->selectRaw('rol, COUNT(*) as total')
            ->groupBy('rol')
            ->pluck('total', 'rol')
            ->toArray();

        $porRol = [
            'estandar'      => (int) ($conteoPorRol['estandar']      ?? 0),
            'familiar'      => (int) ($conteoPorRol['familiar']      ?? 0),
            'administrador' => (int) ($conteoPorRol['administrador'] ?? 0),
        ];

        // ---- KPIs de categorías del sistema (solo COUNT) ----
        $totalCategoriasSistema     = Categoria::whereNull('user_id')->count();
        $categoriasSistemaActivas   = Categoria::whereNull('user_id')->where('activa', true)->count();
        $categoriasSistemaInactivas = Categoria::whereNull('user_id')->where('activa', false)->count();

        // ---- Últimos usuarios registrados (sin datos financieros) ----
        // Solo nombre, correo, rol, activo y alta. Nunca ingreso_mensual.
        $ultimosUsuarios = User::query()
            ->select(['id', 'nombre', 'email', 'rol', 'activo', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // ---- Últimas categorías del sistema modificadas ----
        $ultimasCategorias = Categoria::query()
            ->whereNull('user_id')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get(['id', 'nombre', 'bloque', 'icono', 'activa', 'updated_at']);

        return view('admin.panel', [
            'totalUsuarios'              => $totalUsuarios,
            'usuariosActivos'            => $usuariosActivos,
            'usuariosSuspendidos'        => $usuariosSuspendidos,
            'porRol'                     => $porRol,
            'totalCategoriasSistema'     => $totalCategoriasSistema,
            'categoriasSistemaActivas'   => $categoriasSistemaActivas,
            'categoriasSistemaInactivas' => $categoriasSistemaInactivas,
            'ultimosUsuarios'            => $ultimosUsuarios,
            'ultimasCategorias'          => $ultimasCategorias,
        ]);
    }
}