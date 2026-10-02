<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/02-modelos.php  (SOLO PLAN: el archivo real se crea en app/Models/...)
 * QUÉ ES  : Modelos Eloquent y sus relaciones. Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - Usuario (tabla usuarios): tiene muchos Gasto, GastoFijo, SimulacionCredito, Notificacion,
 *     InformeMensual; pertenece a muchas MetaAhorro (por ParticipacionMeta).
 *   - CategoriaGasto (usuario_id NULL = global) · Gasto · GastoFijo → tiene muchos PagoGastoFijo ·
 *     Notificacion
 *   - SimulacionCredito · MetaAhorro → tiene muchos ParticipacionMeta y AporteMeta ·
 *     InformeMensual → tiene muchos DetalleInforme
 * QUÉ HAY QUE HACER (en orden):
 *   1. Crear con migración y factory (para pruebas).
 *   2. Agregar un alcance global "del usuario actual" a los modelos financieros para que olvidar
 *      un filtro no filtre datos ajenos.
 *   3. AporteMeta: nunca exponer filas del otro participante (RQNF-03).
 * ==============================================================================
 */
