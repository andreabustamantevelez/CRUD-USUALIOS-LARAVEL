<?php

namespace App\Http\Controllers;

use App\Models\ParticipacionMeta;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Invitaciones recibidas: listar, aceptar y rechazar. Solo la persona invitada (ParticipacionMetaPolicy). */
class InvitacionMetaController extends Controller
{
    public function __construct(private ServicioMetas $metas)
    {
    }

    public function index(Request $request): View
    {
        $invitaciones = ParticipacionMeta::query()
            ->where('user_id', $request->user()->id)
            ->where('estado_invitacion', 'pendiente')
            ->whereHas('meta', fn ($q) => $q->where('estado', 'activa'))
            ->with(['meta:id,nombre,monto_objetivo,fecha_limite,creador_id', 'meta.creador:id,nombre'])
            ->latest()
            ->get();

        return view('app.metas.invitaciones', ['invitaciones' => $invitaciones]);
    }

    public function aceptar(Request $request, ParticipacionMeta $participacion): RedirectResponse
    {
        Gate::authorize('aceptar', $participacion);
        $this->metas->aceptar($participacion);

        return redirect()->route('metas.show', $participacion->meta_id)->with('exito', 'Invitación aceptada');
    }

    public function rechazar(Request $request, ParticipacionMeta $participacion): RedirectResponse
    {
        Gate::authorize('rechazar', $participacion);
        $this->metas->rechazar($participacion);

        return redirect()->route('metas.invitaciones')->with('exito', 'Invitación rechazada');
    }
}
