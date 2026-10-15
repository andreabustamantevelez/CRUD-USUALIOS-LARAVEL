<?php

namespace App\Http\Controllers;

use App\Services\ServicioCuentas;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AjustesController extends Controller
{
    public function __construct(
        private ServicioCuentas $cuentas,
        private ServicioMetas $metas,
    ) {
    }

    public function perfil(Request $request): View
    {
        return view('app.ajustes.perfil', [
            'usuario' => $request->user(),
        ]);
    }

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $usuario = $request->user();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'ingreso_mensual' => [
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
        ]);

        $usuario->nombre = $datos['nombre'];
        $usuario->email = $datos['email'];
        $usuario->ingreso_mensual = $datos['ingreso_mensual'];
        $usuario->save();

        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', 'Tu perfil se actualizo correctamente.');
    }

    public function tipoCuenta(Request $request): View
    {
        $usuario = $request->user();

        return view('app.ajustes.tipo-cuenta', [
            'usuario' => $usuario,
            'tieneMetasCompartidasActivas' =>
                $this->metas->tieneMetasCompartidasActivas($usuario),
        ]);
    }

    public function cambiarTipo(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tipo_cuenta' => ['required', 'in:estandar,familiar'],
        ], [
            'tipo_cuenta.required' => 'Selecciona un tipo de cuenta.',
            'tipo_cuenta.in' => 'Selecciona un tipo de cuenta valido.',
        ]);

        $this->cuentas->cambiarTipo(
            $request->user(),
            $datos['tipo_cuenta']
        );

        return redirect()
            ->route('ajustes.tipo-cuenta')
            ->with('exito', 'Tipo de cuenta actualizado.');
    }

    public function eliminarCuenta(Request $request): View
    {
        return view('app.ajustes.eliminar-cuenta', [
            'usuario' => $request->user(),
        ]);
    }
}