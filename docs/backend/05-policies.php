<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/05-policies.php  (SOLO PLAN: el archivo real se crea en app/Policies/...)
 * QUÉ ES  : Policies: quién puede ver/editar cada registro. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - GastoPolicy, CategoriaPolicy, GastoFijoPolicy, SimulacionCreditoPolicy, NotificacionPolicy,
 *     InformePolicy: solo el dueño.
 *   - MetaPolicy: solo participantes. Aportes: solo ver los propios.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Llamar $this->authorize() en cada controlador.
 *   2. Ejemplo textual: GastoPolicy@update permite editar solo si gasto.user_id es el del
 *      usuario autenticado.
 * ==============================================================================
 */
