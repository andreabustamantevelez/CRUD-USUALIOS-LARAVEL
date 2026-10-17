<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdatePerfilRequest;
use App\Services\ServicioCuentas;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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


    public function actualizarPerfil(UpdatePerfilRequest $request): RedirectResponse
{
    $usuario = $request->user();

    $datos = $request->validated();

    $usuario->nombre = $datos['nombre'];
    $usuario->email = $datos['email'];
    $usuario->ingreso_mensual = $datos['ingreso_mensual'] ?? null;

    if (!empty($datos['nueva_contrasena'])) {
        $usuario->password = Hash::make($datos['nueva_contrasena']);
    }

    $usuario->save();

    return redirect()
        ->route('ajustes.perfil')
        ->with('exito', 'Tu perfil se actualizó correctamente.');
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
public function destruirCuenta(Request $request): RedirectResponse
{
    $request->validate([
        'contrasena_actual' => ['required', 'current_password'],
        'confirmar_eliminacion' => ['required', 'accepted'],
    ], [
        'contrasena_actual.required' => 'Ingresa tu contraseña actual.',
        'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',
        'confirmar_eliminacion.required' => 'Debes confirmar que deseas eliminar tu cuenta.',
        'confirmar_eliminacion.accepted' => 'Debes confirmar que deseas eliminar tu cuenta.',
    ]);

    $usuario = $request->user();

    $this->cuentas->validarEliminacionCuenta($usuario);

    Auth::logout();

    $this->cuentas->eliminarCuenta($usuario);

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('inicio')
        ->with('exito', 'Tu cuenta se eliminó correctamente.');
}

}