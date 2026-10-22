<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — cabeceras HTTP de seguridad.
 *
 * - X-Frame-Options: DENY (evita clickjacking).
 * - X-Content-Type-Options: nosniff.
 * - Referrer-Policy: strict-origin-when-cross-origin.
 * - Permissions-Policy: sin cámara, micrófono, geolocalización.
 * - Content-Security-Policy: permisiva (con unsafe-inline) para no romper
 *   los scripts inline del tema ni los style="" dinámicos. Bloquea scripts
 *   externos no autorizados, iframes, plugins y formularios a terceros.
 * - Strict-Transport-Security: solo si APP_ENV=production y HTTPS.
 *
 * 2026-10-22: CSP activada (modo permisivo). Ver J9.2 para migrar a nonces.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Content-Security-Policy.
        // Directivas:
        //   default-src 'self'                  → fallback.
        //   script-src 'self' 'unsafe-inline'   → JS locales + scripts inline del tema.
        //   style-src 'self' 'unsafe-inline' https://fonts.googleapis.com
        //                                       → CSS locales + style="" inline + Google Fonts.
        //   font-src 'self' https://fonts.gstatic.com data:
        //                                       → Material Symbols + Plus Jakarta + data: fonts.
        //   img-src 'self' data: https:         → favicon SVG inline + imágenes locales + https externas.
        //   connect-src 'self'                  → fetch/XHR solo al propio dominio.
        //   form-action 'self'                  → formularios solo al propio dominio.
        //   frame-ancestors 'none'              → no embebible en iframes.
        //   base-uri 'self'                     → no secuestro de base href.
        //   object-src 'none'                   → sin <object>, <embed>, <applet>.
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "object-src 'none'",
        ]);

        // Solo en HTTPS pedimos al navegador que suba recursos HTTP a HTTPS.
        if ($request->secure()) {
            $csp .= '; upgrade-insecure-requests';
        }

        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS: solo si estamos en producción y ya hay HTTPS.
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}