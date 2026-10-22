<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\AlertaSeguridad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * QUÉ HACE : iniciar y cerrar sesión (rutas login, login.entrar y logout).
 * QUIÉN LO USA : routes/modulos/auth.php.
 * DEPENDE DE : LoginRequest (validación + límite de intentos).
 * Redirección: administrador -> admin.panel; estandar/familiar -> panel.
 *              Si la persona intentó entrar antes a una URL protegida, vuelve
 *              a esa URL (intended).
 *
 * 2026-10-22 (J9):
 *   - Fix: admin redirige a admin.panel (antes admin.estadisticas).
 *   - Registra last_login_ip/at y notifica si la IP es nueva.
 */
class LoginController extends Controller
{
    public function create(): View
    {
        return view('publico.iniciar-sesion');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->autenticar();
        $request->session()->regenerate();

        $usuario = Auth::user();
        $eraIpNueva = $usuario->esIpNueva($request->ip());

        // Guarda la IP/fecha del login exitoso (forceFill interno).
        $usuario->registrarLogin($request->ip(), $request->userAgent());

        // Notifica solo si NO es el primer login y la IP es distinta.
        if ($eraIpNueva) {
            try {
                $usuario->notify(new AlertaSeguridad('login.new_ip', [
                    'fecha'      => now()->format('Y-m-d H:i'),
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]));
            } catch (\Throwable $e) {
                // No bloquear el login por un fallo de SMTP.
                Log::warning('No se pudo enviar alerta de seguridad login.new_ip', [
                    'user_id' => $usuario->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return redirect()->intended($usuario->rutaPanel());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}