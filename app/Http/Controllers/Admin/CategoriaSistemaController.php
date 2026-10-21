<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoriaSistemaRequest;
use App\Http\Requests\Admin\UpdateCategoriaSistemaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Qué hace: CRUD de categorías del sistema (user_id = null).
 *           El admin nunca toca categorías personales de otros usuarios.
 *           Las categorías del sistema se DESACTIVAN (activa=false), no se borran.
 * Quién lo usa: rutas admin.categorias.* (rol:administrador).
 * Dueño: Juan (J7).
 */
class CategoriaSistemaController extends Controller
{
    public function index(Request $request): View
    {
        $categorias = Categoria::query()
            ->whereNull('user_id')
            ->withCount(['gastos', 'gastosFijos'])
            ->orderBy('bloque')
            ->orderBy('nombre')
            ->get();

        $resumen = [
            'total' => $categorias->count(),
            'activas' => $categorias->where('activa', true)->count(),
            'inactivas' => $categorias->where('activa', false)->count(),
        ];

        // Definición de los bloques del 50/30/20, con descripción y porcentaje
        // objetivo. Se muestra en la cabecera de cada grupo.
        $bloques = [
            'necesidades' => [
                'nombre' => 'Necesidades',
                'porcentaje' => 50,
                'descripcion' => 'Gastos esenciales: arriendo, servicios públicos, mercado, transporte, salud, educación.',
                'color' => 'var(--need)',
                'bg' => 'var(--need-bg)',
            ],
            'discrecionales' => [
                'nombre' => 'Gastos discrecionales',
                'porcentaje' => 30,
                'descripcion' => 'Gastos opcionales: restaurantes, entretenimiento, ropa, suscripciones, viajes.',
                'color' => 'var(--want)',
                'bg' => 'var(--want-bg)',
            ],
            'ahorro' => [
                'nombre' => 'Ahorro',
                'porcentaje' => 20,
                'descripcion' => 'Ahorro, inversión o pago extra de deudas.',
                'color' => 'var(--save)',
                'bg' => 'var(--save-bg)',
            ],
        ];

        return view('admin.categorias', compact('categorias', 'resumen', 'bloques'));
    }

    public function store(StoreCategoriaSistemaRequest $request): RedirectResponse
    {
        Categoria::create([
            'user_id' => null,
            'nombre' => $request->validated('nombre'),
            'bloque' => $request->validated('bloque'),
            'peso_recomendado' => $request->validated('peso_recomendado'),
            'icono' => $request->validated('icono'),
            'activa' => true,
        ]);

        return back()->with('exito', 'Categoría creada');
    }

    public function update(UpdateCategoriaSistemaRequest $request, Categoria $categoria): RedirectResponse
    {
        abort_unless($categoria->user_id === null, 403);

        $categoria->update([
            'nombre' => $request->validated('nombre'),
            'bloque' => $request->validated('bloque'),
            'peso_recomendado' => $request->validated('peso_recomendado'),
            'icono' => $request->validated('icono'),
        ]);

        return back()->with('exito', 'Categoría actualizada');
    }

    public function desactivar(Request $request, Categoria $categoria): RedirectResponse
    {
        abort_unless($categoria->user_id === null, 403);

        $categoria->update(['activa' => ! $categoria->activa]);

        return back()->with('exito', $categoria->activa ? 'Categoría activada' : 'Categoría desactivada');
    }
}
