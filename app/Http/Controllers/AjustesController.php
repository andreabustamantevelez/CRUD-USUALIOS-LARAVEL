<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Services\ServicioCuentas;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AjustesController extends Controller
{
    public function __construct(
        private ServicioCuentas $cuentas,
        private ServicioMetas $metas,
    ) {
    }

    // =========================================================================
    // PERFIL
    // =========================================================================

    public function perfil(Request $request): View
    {
        return view('app.ajustes.perfil', [
            'usuario' => $request->user(),
        ]);
    }

    public function actualizarPerfil(UpdatePerfilRequest $request): RedirectResponse
    {
        $usuario = $request->user();
        $datos   = $request->validated();
        $mensaje = 'Tu perfil se actualizó correctamente.';

        // 1. Datos básicos
        $usuario->nombre          = $datos['nombre'];
        $usuario->ingreso_mensual = $datos['ingreso_mensual'] ?? null;

        // 2. Contraseña (si aplica)
        if (!empty($datos['nueva_contrasena'])) {
            $usuario->password = Hash::make($datos['nueva_contrasena']);
        }

        $usuario->save();

        // 3. Cambio de correo (delegado al servicio, con su propia transacción)
        $emailNuevo = strtolower(trim($datos['email']));
        if ($emailNuevo !== $usuario->email) {
            try {
                $this->cuentas->solicitarCambioCorreo($usuario, $emailNuevo);
                $mensaje = 'Perfil actualizado. Revisa tu nuevo correo para confirmar el cambio.';
            } catch (ValidationException $e) {
                return back()->withErrors($e->errors())->withInput();
            }
        }

        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', $mensaje);
    }

    public function reenviarConfirmacionCorreo(Request $request): RedirectResponse
    {
        try {
            $this->cuentas->reenviarConfirmacionCorreo($request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', 'Te enviamos un nuevo enlace de confirmación.');
    }

    // =========================================================================
    // CONFIRMAR CORREO
    // =========================================================================

    public function confirmarCorreo(Request $request, string $token): RedirectResponse
    {
        try {
            $this->cuentas->confirmarCambioCorreo($request->user(), $token);
        } catch (ValidationException $e) {
            return redirect()
                ->route('ajustes.perfil')
                ->withErrors($e->errors());
        }

        // Cerramos la sesión: se autenticó con el correo viejo.
        // Vaciamos remember_token en memoria para que Auth::logout() no haga
        // $user->save() de un modelo ya eliminado (no aplica aquí porque no
        // eliminamos al usuario, pero es defensivo).
        $request->user()->setRememberToken(null);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('exito', 'Tu correo se actualizó. Inicia sesión con el nuevo.');
    }

    // =========================================================================
    // TIPO DE CUENTA
    // =========================================================================

    public function tipoCuenta(Request $request): View
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $usuario = $request->user();

        return view('app.ajustes.tipo-cuenta', [
            'usuario' => $usuario,
            'tieneMetasCompartidasActivas' =>
                $this->metas->tieneMetasCompartidasActivas($usuario),
        ]);
    }

    public function cambiarTipo(Request $request): RedirectResponse
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $datos = $request->validate([
            'tipo_cuenta' => ['required', 'in:estandar,familiar'],
        ], [
            'tipo_cuenta.required' => 'Selecciona un tipo de cuenta.',
            'tipo_cuenta.in'       => 'Selecciona un tipo de cuenta válido.',
        ]);

        try {
            $this->cuentas->cambiarTipo($request->user(), $datos['tipo_cuenta']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('ajustes.tipo-cuenta')
            ->with('exito', 'Tipo de cuenta actualizado.');
    }

    // =========================================================================
    // ELIMINAR CUENTA
    // =========================================================================

    public function eliminarCuenta(Request $request): View
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        return view('app.ajustes.eliminar-cuenta', [
            'usuario' => $request->user(),
        ]);
    }

    public function destruirCuenta(Request $request): RedirectResponse
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $request->validate([
            'contrasena_actual'     => ['required', 'current_password'],
            'confirmar_eliminacion' => ['required', 'accepted'],
        ], [
            'contrasena_actual.required'         => 'Ingresa tu contraseña actual.',
            'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',
            'confirmar_eliminacion.required'     => 'Debes confirmar que deseas eliminar tu cuenta.',
            'confirmar_eliminacion.accepted'     => 'Debes confirmar que deseas eliminar tu cuenta.',
        ]);

        $usuario = $request->user();

        try {
            $this->cuentas->eliminarCuenta($usuario);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $usuario->setRememberToken(null);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('inicio')
            ->with('exito', 'Tu cuenta se eliminó correctamente.');
    }
}