<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestablecerContrasenaRequest;
use App\Http\Requests\Auth\SolicitarEnlaceRequest;
use App\Models\User;
use App\Notifications\AlertaSeguridad;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * QUÉ HACE : recuperar y restablecer la contraseña con el "Password Broker" de Laravel.
 *            Rutas: password.request, password.email, password.reset, password.update.
 *
 * 2026-10-22 (J9): notifica password.changed tras cambiar la contraseña.
 */
class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('publico.recuperar-contrasena');
    }

    public function enviarEnlace(SolicitarEnlaceRequest $request): RedirectResponse
    {
        Password::sendResetLink(['email' => $request->validated('correo')]);

        return back()->with('estado', 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.');
    }

    public function edit(string $token): View
    {
        return view('publico.restablecer-contrasena', ['token' => $token]);
    }

    public function actualizar(RestablecerContrasenaRequest $request): RedirectResponse
    {
        $estado = Password::reset(
            [
                'email'    => $request->validated('correo'),
                'password' => $request->validated('password'),
                'token'    => $request->validated('token'),
            ],
            function (User $usuario, string $password) {
                $usuario->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            // Notificar al usuario (al mismo correo, no cambió).
            $usuario = User::where('email', $request->validated('correo'))->first();
            if ($usuario) {
                try {
                    $usuario->notify(new AlertaSeguridad('password.changed', [
                        'fecha' => now()->format('Y-m-d H:i'),
                    ]));
                } catch (\Throwable $e) {
                    Log::warning('No se pudo enviar alerta password.changed', [
                        'user_id' => $usuario->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            return redirect()->route('login')->with('estado', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('correo'))
            ->withErrors(['correo' => 'El enlace no es válido o ya venció. Solicita uno nuevo.']);
    }
}