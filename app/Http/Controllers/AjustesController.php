<?php

namespace App\Http\Controllers;

use App\Services\ServicioCuentas;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * QUÉ HACE : pantallas de Ajustes. Por ahora: perfil, tipo de cuenta y eliminar cuenta (placeholder).
 * QUIÉN LO USA : rutas ajustes.* (grupo B, cualquier rol con sesión).
 * DEPENDE DE : ServicioCuentas (cambio de tipo) y ServicioMetas (C6, metas compartidas activas).
 */
class AjustesController extends Controller
{
    public function __construct(
        private ServicioCuentas $cuentas,
        private ServicioMetas $metas,
    ) {
    }

    public function perfil(Request $request): View
    {
        return view('app.ajustes.perfil', ['usuario' => $request->user()]);
    }

    public function tipoCuenta(Request $request): View
    {
        $usuario = $request->user();

        return view('app.ajustes.tipo-cuenta', [
            'usuario'                      => $usuario,
            'tieneMetasCompartidasActivas' => $this->metas->tieneMetasCompartidasActivas($usuario),
        ]);
    }

    public function cambiarTipo(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tipo_cuenta' => ['required', 'in:estandar,familiar'],
        ], [
            'tipo_cuenta.required' => 'Selecciona un tipo de cuenta.',
            'tipo_cuenta.in'       => 'Selecciona un tipo de cuenta válido.',
        ]);

        $this->cuentas->cambiarTipo($request->user(), $datos['tipo_cuenta']);

        return redirect()->route('ajustes.tipo-cuenta')->with('exito', 'Tipo de cuenta actualizado');
    }

    public function eliminarCuenta(Request $request): View
    {
        return view('app.ajustes.eliminar-cuenta', ['usuario' => $request->user()]);
    }
}