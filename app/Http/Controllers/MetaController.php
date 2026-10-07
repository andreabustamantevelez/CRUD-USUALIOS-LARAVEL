<?php

namespace App\Http\Controllers;

use App\Http\Requests\Metas\InvitarMetaRequest;
use App\Http\Requests\Metas\StoreMetaRequest;
use App\Models\Meta;
use App\Models\ParticipacionMeta;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Metas compartidas (solo familiar; grupo D de rutas). La lógica vive en ServicioMetas.
 * PRIVACIDAD: aquí NUNCA se cargan los aportes del otro; show() solo trae los del usuario actual
 * y el total conjunto sale de una suma en SQL.
 */
class MetaController extends Controller
{
    public function __construct(private ServicioMetas $metas)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Meta::class);
        $yo = $request->user();

        $metas = Meta::query()
            ->delParticipante($yo->id)
            ->withSum('aportes', 'monto')                     // total conjunto, calculado en SQL
            ->with(['participaciones' => fn ($q) => $q->whereIn('estado_invitacion', ['aceptada', 'pendiente']),
                    'participaciones.usuario:id,nombre'])    // solo NOMBRES, nunca aportes
            ->latest()
            ->get();

        $invitaciones = ParticipacionMeta::query()
            ->where('user_id', $yo->id)
            ->where('estado_invitacion', 'pendiente')
            ->whereHas('meta', fn ($q) => $q->where('estado', 'activa'))
            ->count();

        return view('app.metas.index', ['metas' => $metas, 'invitaciones' => $invitaciones]);
    }

    public function store(StoreMetaRequest $request): RedirectResponse
    {
        $meta = $this->metas->crear($request->user(), $request->validated());

        return redirect()->route('metas.show', $meta)->with('exito', 'Meta creada');
    }

    public function show(Request $request, Meta $meta): View
    {
        Gate::authorize('view', $meta);
        $yo = $request->user();

        $avance = $this->metas->avance($meta);

        return view('app.metas.show', [
            'meta'          => $meta->load('creador:id,nombre'),
            'avance'        => $avance,
            // Solo los aportes de quien mira. Los del otro no se consultan.
            'aportes'       => $meta->aportesDelUsuario($yo->id)->latest('fecha')->latest('id')->get(),
            'miTotal'       => (int) $meta->aportesDelUsuario($yo->id)->sum('monto'),
            'participantes' => $meta->participaciones()
                ->whereIn('estado_invitacion', ['aceptada', 'pendiente'])
                ->with('usuario:id,nombre')->get(),
            'puedeAportar'   => $yo->can('aportar', $meta),
            'puedeInvitar'   => $yo->can('invitar', $meta),
            'puedeFinalizar' => $yo->can('finalizar', $meta) && $meta->objetivoAlcanzado(),
            'puedeAbandonar' => $yo->can('abandonar', $meta),
        ]);
    }

    public function invitar(InvitarMetaRequest $request, Meta $meta): RedirectResponse
    {
        $this->metas->invitar($meta, $request->validated('correo'));

        return redirect()->route('metas.show', $meta)->with('exito', 'Invitación enviada');
    }

    public function finalizar(Request $request, Meta $meta): RedirectResponse
    {
        Gate::authorize('finalizar', $meta);
        $this->metas->finalizar($meta, $request->user());

        return redirect()->route('metas.show', $meta)->with('exito', 'Meta finalizada');
    }

    public function abandonar(Request $request, Meta $meta): RedirectResponse
    {
        Gate::authorize('abandonar', $meta);
        $this->metas->abandonar($meta, $request->user());

        return redirect()->route('metas.index')->with('exito', 'Meta abandonada');
    }
}
