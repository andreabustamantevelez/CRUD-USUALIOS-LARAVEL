<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Notifications\AlertaSeguridad;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * 2026-10-22 (J9): notifica login.blocked cuando el rate limit se dispara.
 *                  Usa Cache para no enviar más de 1 correo por hora por usuario.
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

        $this->notificarBloqueo();

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->claveLimite());

        throw ValidationException::withMessages([
            'correo' => "Demasiados intentos fallidos. Intenta de nuevo en {$segundos} segundos.",
        ])->status(429);
    }

    /**
     * Notifica al dueño del correo que hubo intentos de login bloqueados.
     * Solo si el correo existe (nunca revelar si existe o no hacia afuera).
     * Máximo 1 correo por hora por usuario (Cache).
     */
    private function notificarBloqueo(): void
    {
        $usuario = User::where('email', $this->input('correo'))->first();
        if (! $usuario) {
            return;
        }

        $cacheKey = 'login-bloqueado:' . $usuario->id;
        if (Cache::has($cacheKey)) {
            return;
        }

        try {
            $usuario->notify(new AlertaSeguridad('login.blocked', [
                'fecha'    => now()->format('Y-m-d H:i'),
                'ip'       => $this->ip(),
                'intentos' => self::MAX_INTENTOS,
            ]));

            Cache::put($cacheKey, true, now()->addHour());
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar alerta login.blocked', [
                'user_id' => $usuario->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    private function claveLimite(): string
    {
        return Str::transliterate(Str::lower($this->input('correo')).'|'.$this->ip());
    }
}