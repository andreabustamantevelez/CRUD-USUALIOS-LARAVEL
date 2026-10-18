<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expira la sesión tras N minutos de inactividad.
 *
 * Requiere que SESSION_LIFETIME en .env sea MAYOR que LIMITE_MINUTOS
 * (por ejemplo, SESSION_LIFETIME=480) para que Laravel no expire la sesión
 * antes de que este middleware pueda mostrar el mensaje.
 *
 * Si se usa junto con SESSION_LIFETIME=15, este middleware nunca se ejecuta
 * con la sesión activa (Laravel la expira primero). En ese caso NO se necesita.
 */
class ExpirarSesionPorInactividad
{
    /**
     * Minutos de inactividad antes de cerrar la sesión.
     * Cambiar acá si se quiere otro valor.
     */
    private const LIMITE_MINUTOS = 15;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $ultimaActividad = $request->session()->get('ultima_actividad');

            if ($ultimaActividad && now()->diffInMinutes($ultimaActividad) >= self::LIMITE_MINUTOS) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('estado', 'Tu sesión expiró por inactividad. Vuelve a iniciar sesión.');
            }

            $request->session()->put('ultima_actividad', now());
        }

        return $next($request);
    }
}