<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/04-middleware.php  (los archivos reales están en app/Http/Middleware/)
 * ESTADO  : HECHO (J4, 2026-10-06, Juan).
 * QUÉ ES  : Middleware propios del proyecto.
 *
 * 1. CuentaActiva ..... alias 'cuenta.activa'  (app/Http/Middleware/CuentaActiva.php)
 *    - Si users.activo = false: cierra la sesión, la invalida, regenera el token y redirige a
 *      'login' con el error "Tu cuenta está suspendida. Comunícate con el administrador."
 *      (en peticiones JSON responde 403).
 *    - Surte efecto en la SIGUIENTE petición, aunque la persona ya estuviera dentro.
 *
 * 2. RolRequerido ..... alias 'rol'  (app/Http/Middleware/RolRequerido.php)
 *    - Recibe uno o varios roles: rol:familiar · rol:estandar,familiar · rol:administrador.
 *    - Si el rol del usuario no está en la lista: 403 "No tienes permiso para esta sección."
 *
 * REGISTRO DE LOS ALIAS (bootstrap/app.php; las líneas 'use' van ARRIBA del archivo):
 *    $middleware->alias([
 *        'cuenta.activa' => CuentaActiva::class,
 *        'rol'           => RolRequerido::class,
 *    ]);
 *
 * DÓNDE SE USAN (routes/web.php):
 *    Route::middleware(['auth', 'cuenta.activa'])->group(...)            // B a E
 *      B  ajustes ............................................. todos los roles con sesión
 *      C  ->middleware('rol:estandar,familiar') ............... panel, gastos, categorías, gastos fijos,
 *                                                               notificaciones, créditos, informes
 *      D  ->middleware('rol:familiar') ........................ metas
 *      E  ->middleware('rol:administrador') + prefijo /admin .. usuarios, categorías, estadísticas
 *
 * REGLAS:
 *   - Ocultar un enlace del menú NO protege: la protección real es este middleware + las Policies.
 *   - Nada de closures como middleware en grupos de rutas ('Closure could not be converted to
 *     string'): siempre clases con alias.
 *   - Ruta nueva: ponla dentro del grupo que le toca; no declares permisos suelto en el archivo del módulo.
 *
 * CÓMO SE COMPRUEBA: tests/Feature/PermisosTest.php (matriz rol x ruta, acciones POST/PUT/DELETE,
 *   sin sesión, cambio de rol, cuenta suspendida con sesión abierta). Agrega tu ruta a la lista de su grupo.
 * ==============================================================================
 */
