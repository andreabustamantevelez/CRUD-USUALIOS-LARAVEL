<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestablecerContrasenaRequest;
use App\Http\Requests\Auth\SolicitarEnlaceRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * QUÉ HACE : recuperar y restablecer la contraseña con el "Password Broker" de Laravel.
 *            Rutas: password.request, password.email, password.reset, password.update.
 * QUIÉN LO USA : routes/modulos/auth.php (middleware guest; password.email lleva throttle).
 * DEPENDE DE : tabla password_reset_tokens (J2), notificación RestablecerContrasena.
 * REGLAS:
 *   - Mensaje NEUTRO al pedir el enlace: no revela si el correo existe.
 *   - El enlace sirve una sola vez y vence a los 60 min (config/auth.php, passwords.users.expire).
 *   - En desarrollo (MAIL_MAILER=log) el enlace aparece en storage/logs/laravel.log.
 */
class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('publico.recuperar-contrasena');
    }

    public function enviarEnlace(SolicitarEnlaceRequest $request): RedirectResponse
    {
        // Se ignora el resultado a propósito: exista o no el correo, la respuesta es la misma.
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
                // El cast 'hashed' del modelo guarda la contraseña con hash.
                $usuario->forceFill([
                    'password'       => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($usuario));
            }
        );

        if ($estado === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('estado', 'Tu contraseña fue actualizada. Ya puedes iniciar sesión.');
        }

        return back()
            ->withInput($request->only('correo'))
            ->withErrors(['correo' => 'El enlace no es válido o ya venció. Solicita uno nuevo.']);
    }
}
