<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/08-validaciones.php  (SOLO PLAN: el archivo real se crea en app/Http/Requests/...)
 * QUÉ ES  : FormRequests de validación. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - StoreRegistroRequest, LoginRequest, StoreGastoRequest, StoreCategoriaRequest,
 *     StoreGastoFijoRequest, StoreSimulacionRequest, StoreMetaRequest, InvitarMetaRequest,
 *     StoreAporteRequest, UpdatePerfilRequest
 * QUÉ HAY QUE HACER (en orden):
 *   1. Usar los textos de error de la especificación (ej. "Este correo ya se encuentra
 *      registrado").
 *   2. Los nombres de campo deben coincidir con los name= de las vistas.
 * ==============================================================================
 */
