<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * QUÉ HACE : deja pasar solo a los roles indicados; a los demás les responde 403.
 * QUIÉN LO USA : alias 'rol' (bootstrap/app.php). Ejemplos: ->middleware('rol:familiar'),
 *                ->middleware('rol:estandar,familiar'), ->middleware('rol:administrador').
 * DEPENDE DE : users.rol y 'auth' (corre antes).
 * DECISIÓN : se responde 403 (no 404) cuando el recurso existe pero la persona no puede verlo.
 * Ocultar un enlace del menú NO protege: la protección real es este middleware + las Policies.
 */
class RolRequerido
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        abort_unless(
            $usuario && in_array($usuario->rol, $roles, true),
            403,
            'No tienes permiso para esta sección.'
        );

        return $next($request);
    }
}
