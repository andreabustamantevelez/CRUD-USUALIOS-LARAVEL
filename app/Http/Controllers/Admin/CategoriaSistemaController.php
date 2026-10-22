<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoriaSistemaRequest;
use App\Http\Requests\Admin\UpdateCategoriaSistemaRequest;
use App\Models\Categoria;
use App\Services\Auditoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\Admin\EliminarCategoriaSistemaRequest;
/**
 * 2026-10-22 (J9): auditoría en store, update y desactivar.
 *                  desactivar togglea: registra categoria.desactivar o
 *                  categoria.reactivar según el estado anterior.
 */
class CategoriaSistemaController extends Controller
{
    public function __construct(private Auditoria $auditoria)
    {
    }

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
        $categoria = Categoria::create([
            'user_id' => null,
            'nombre' => $request->validated('nombre'),
            'bloque' => $request->validated('bloque'),
            'peso_recomendado' => $request->validated('peso_recomendado'),
            'icono' => $request->validated('icono'),
            'activa' => true,
        ]);

        $this->auditoria->registrar(
            accion: 'categoria.crear',
            modelo: 'Categoria',
            modeloId: $categoria->id,
            antes: null,
            despues: $categoria->only(['nombre', 'bloque', 'icono', 'activa']),
        );

        return back()->with('exito', 'Categoría creada');
    }

    public function update(UpdateCategoriaSistemaRequest $request, Categoria $categoria): RedirectResponse
    {
        abort_unless($categoria->user_id === null, 403);

        $antes = $categoria->only(['nombre', 'bloque', 'peso_recomendado', 'icono']);

        $categoria->update([
            'nombre' => $request->validated('nombre'),
            'bloque' => $request->validated('bloque'),
            'peso_recomendado' => $request->validated('peso_recomendado'),
            'icono' => $request->validated('icono'),
        ]);

        $this->auditoria->registrar(
            accion: 'categoria.actualizar',
            modelo: 'Categoria',
            modeloId: $categoria->id,
            antes: $antes,
            despues: $categoria->fresh()->only(['nombre', 'bloque', 'peso_recomendado', 'icono']),
        );

        return back()->with('exito', 'Categoría actualizada');
    }

    public function desactivar(EliminarCategoriaSistemaRequest $request, Categoria $categoria): RedirectResponse
    {
        $activaAntes = (bool) $categoria->activa;
        $categoria->update(['activa' => !$activaAntes]);

        $accion = $activaAntes ? 'categoria.desactivar' : 'categoria.reactivar';
        $mensaje = $activaAntes ? 'Categoría desactivada' : 'Categoría activada';

        $this->auditoria->registrar(
            accion: $accion,
            modelo: 'Categoria',
            modeloId: $categoria->id,
            antes: ['activa' => $activaAntes],
            despues: ['activa' => !$activaAntes],
        );

        return back()->with('exito', $mensaje);
    }
}