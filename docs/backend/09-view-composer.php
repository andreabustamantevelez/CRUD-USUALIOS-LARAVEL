<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/09-view-composer.php  (SOLO PLAN: el archivo real se crea en app/Providers/...)
 * QUÉ ES  : View Composer para compartir variables con todas las vistas. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - $notificacionesPendientes (conteo de notificaciones pendientes del usuario) para la campana
 *     de <x-topbar />.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Registrarlo en AppServiceProvider y limitarlo a las plantillas layouts.app y layouts.admin.
 * ==============================================================================
 */
