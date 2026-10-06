<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * QUÉ HACE : valida el login (POST /iniciar-sesion, ruta login.entrar) y hace el intento de
 *            autenticación con límite de intentos (5 por minuto por correo + IP).
 * QUIÉN LO USA : LoginController@store.
 * DEPENDE DE : users.email, users.password, users.activo.
 * REGLAS:
 *   - Error de credenciales SIEMPRE igual: "Usuario o contraseña incorrectos" (no dice cuál falló).
 *   - Cuenta suspendida (activo = false): solo se informa DESPUÉS de acertar la contraseña, así
 *     nadie puede descubrir qué correos existen.
 *   - El 6.º intento fallido en un minuto se bloquea aunque la contraseña sea correcta.
 */
class LoginRequest extends FormRequest
{
    public const MAX_INTENTOS = 5;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['correo' => mb_strtolower(trim((string) $this->input('correo')))]);
    }

    public function rules(): array
    {
        return [
            'correo'   => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required'   => 'Escribe tu correo electrónico.',
            'correo.email'      => 'Escribe un correo válido.',
            'password.required' => 'Escribe tu contraseña.',
        ];
    }

    /** Intenta iniciar sesión o lanza ValidationException con el mensaje adecuado. */
    public function autenticar(): void
    {
        $this->asegurarSinBloqueo();

        $credenciales = ['email' => $this->input('correo'), 'password' => $this->input('password')];

        if (! Auth::attempt($credenciales, $this->boolean('recordarme'))) {
            RateLimiter::hit($this->claveLimite(), 60);

            throw ValidationException::withMessages([
                'correo' => 'Usuario o contraseña incorrectos',
            ]);
        }

        if (! Auth::user()->activo) {
            Auth::logout();
            RateLimiter::hit($this->claveLimite(), 60);

            throw ValidationException::withMessages([
                'correo' => 'Tu cuenta está suspendida. Comunícate con el administrador.',
            ]);
        }

        RateLimiter::clear($this->claveLimite());
    }

    private function asegurarSinBloqueo(): void
    {
        if (! RateLimiter::tooManyAttempts($this->claveLimite(), self::MAX_INTENTOS)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->claveLimite());

        throw ValidationException::withMessages([
            'correo' => "Demasiados intentos fallidos. Intenta de nuevo en {$segundos} segundos.",
        ])->status(429);
    }

    private function claveLimite(): string
    {
        return Str::transliterate(Str::lower($this->input('correo')).'|'.$this->ip());
    }
}
