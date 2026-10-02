<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/07-alertas-programadas.php  (SOLO PLAN: el archivo real se crea en app/Console/...)
 * QUÉ ES  : Tarea programada diaria de alertas (RQF-26, RQNF-05). Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - Cada día: 1) crear el pago del mes de cada gasto fijo; 2) marcar vencidos; 3) generar
 *     notificación a los que vencen en 3 días o menos y no están pagados; 4) no duplicar.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Registrar la tarea en el programador de Laravel.
 *   2. En el servidor de producción debe correr el programador cada minuto (cron); sin eso NO hay
 *      alertas.
 *   3. Debe ser idempotente: correrla dos veces el mismo día no duplica alertas.
 * ==============================================================================
 */
