<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/06-servicios.php  (SOLO PLAN: el archivo real se crea en app/Services/...)
 * QUÉ ES  : Servicios con la lógica de negocio. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - CalculadoraCredito: cuota, total, intereses y tabla francesa (con pruebas contra cálculo
 *     manual).
 *   - GeneradorInforme: agrupa gastos, calcula % real vs recomendado y guarda el detalle.
 *   - ServicioMetas: invitar (3 validaciones con mensajes exactos), aceptar, rechazar, aportar,
 *     avance, finalizar, abandonar.
 *   - ServicioGastosFijos: crear periodo, marcar pagado (cancela alerta), vencimientos.
 *   - ServicioCuentas: cambiar tipo de cuenta, eliminar cuenta, impedir quedarse sin
 *     administrador.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Cada servicio tiene pruebas automáticas (tests/Feature).
 * ==============================================================================
 */
