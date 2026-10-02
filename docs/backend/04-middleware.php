<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/04-middleware.php  (SOLO PLAN: el archivo real se crea en app/Http/Middleware/...)
 * QUÉ ES  : Middleware propios. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - cuenta.activa: rechaza usuarios suspendidos (cierra su sesión).
 *   - rol: recibe uno o varios roles permitidos (estandar, familiar, administrador) y responde 403
 *     si no coincide.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Registrar ambos con alias en bootstrap/app.php (o Kernel según la versión de Laravel).
 *   2. Ocultar un enlace en el menú NO protege: la protección real es este middleware + las
 *      Policies.
 * ==============================================================================
 */
