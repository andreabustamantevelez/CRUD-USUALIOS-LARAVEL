<?php

namespace App\Http\Controllers;

use App\Http\Requests\Metas\StoreAporteMetaRequest;
use App\Models\Meta;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;

/** Registrar un aporte propio a una meta. La autorización está en StoreAporteMetaRequest (MetaPolicy@aportar). */
class AporteMetaController extends Controller
{
    public function store(StoreAporteMetaRequest $request, Meta $meta, ServicioMetas $metas): RedirectResponse
    {
        $metas->aportar($meta, $request->user(), (int) $request->validated('monto'), $request->validated('fecha'));

        return redirect()->route('metas.show', $meta)->with('exito', 'Aporte registrado');
    }
}
