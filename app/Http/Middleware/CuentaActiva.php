<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * QUÉ HACE : expulsa a quien tenga la cuenta suspendida (users.activo = false), aunque ya tuviera la
 *            sesión abierta. Cierra la sesión y lo manda al login con un aviso.
 * QUIÉN LO USA : alias 'cuenta.activa' (bootstrap/app.php), en el grupo privado de routes/web.php.
 * DEPENDE DE : users.activo (J2) y 'auth' (corre antes).
 * EJEMPLO : el administrador suspende a Ana mientras ella navega; en su SIGUIENTE petición vuelve al
 *           login con "Tu cuenta está suspendida. Comunícate con el administrador."
 */
class CuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && ! $usuario->activo) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                abort(403, 'Tu cuenta está suspendida.');
            }

            return redirect()->route('login')->withErrors([
                'correo' => 'Tu cuenta está suspendida. Comunícate con el administrador.',
            ]);
        }

        return $next($request);
    }
}
