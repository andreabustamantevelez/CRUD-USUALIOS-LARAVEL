<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Qué hace: conteos agregados para el panel de administración.
 *           NUNCA consulta montos ni datos financieros.
 * Quién lo usa: admin.estadisticas (rol:administrador).
 * Dueño: Juan (J7).
 */
class EstadisticaController extends Controller
{
    public function index(): View
    {
        $estadisticas = [
            'total_usuarios' => User::count(),
            'por_rol' => [
                'estandar' => User::where('rol', 'estandar')->count(),
                'familiar' => User::where('rol', 'familiar')->count(),
                'administrador' => User::where('rol', 'administrador')->count(),
            ],
            'por_estado' => [
                'activos' => User::where('activo', true)->count(),
                'suspendidos' => User::where('activo', false)->count(),
            ],
            'notificaciones_totales' => Notificacion::count(),
            'notificaciones_por_tipo' => Notificacion::query()
                ->select('tipo', DB::raw('COUNT(*) as total'))
                ->groupBy('tipo')
                ->orderByDesc('total')
                ->get()
                ->pluck('total', 'tipo')
                ->all(),
        ];

        return view('admin.estadisticas', compact('estadisticas'));
    }
}