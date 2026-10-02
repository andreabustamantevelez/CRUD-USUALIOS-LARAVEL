<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Qué hace: simula un usuario con nombre y rol SOLO mientras no exista el inicio de sesión real,
 *           para que el menú lateral y la barra superior (que leen auth()->user()) no fallen.
 * Quién lo usa: routes/web.php, en los grupos de zona privada.
 * De qué depende: nada (no usa base de datos).
 * Ejemplo: /gastos?rol=estandar muestra el menú del usuario estándar; ?rol=familiar o
 *          ?rol=administrador muestran los otros dos.
 * TEMPORAL: Juan (J3) lo elimina junto con su línea en routes/web.php cuando exista el login.
 */
class UsuarioMaqueta
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = new User;
        $usuario->nombre = 'Usuario Demo';
        $usuario->rol = $request->query('rol', 'familiar');
        Auth::setUser($usuario);

        return $next($request);
    }
}
