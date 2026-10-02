<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/03-migraciones.php  (SOLO PLAN: el archivo real se crea en database/migrations/...)
 * QUÉ ES  : Tablas y columnas propuestas (corrigen los 14 huecos de la especificación). Vacío a
 *           propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - usuarios: id, nombre, identificacion (decisión #1), correo único, password, rol
 *     (estandar|familiar|administrador), estado (activo|suspendido), ingreso_mensual (nullable),
 *     consentimiento_datos_at, timestamps
 *   - categorias_gasto: id, nombre, bloque (necesidades|discrecionales|ahorro), peso_recomendado,
 *     usuario_id (NULL = global), activa · único (usuario_id, nombre)
 *   - gastos: id, usuario_id, categoria_id, monto (entero en pesos), fecha, descripcion
 *   - gastos_fijos: id, usuario_id, categoria_id, nombre, monto, dia_pago, descripcion,
 *     periodicidad (mensual), activo
 *   - pagos_gasto_fijo: id, gasto_fijo_id, periodo, fecha_vencimiento, estado
 *     (pendiente|pagado|vencido), fecha_pago · único (gasto_fijo_id, periodo)
 *   - notificaciones: id, usuario_id, tipo, mensaje, estado (pendiente|leida), pago_gasto_fijo_id
 *     (nullable), timestamps
 *   - simulaciones_credito: id, usuario_id, nombre, monto, tasa, plazo, cuota, total, intereses,
 *     timestamps
 *   - metas_ahorro: id, creador_id, nombre, monto_objetivo, monto_actual, estado, fecha_limite ·
 *     participaciones_meta: id, meta_id, usuario_id, estado_invitacion, fecha_union (máx. 2 por
 *     meta) · aportes_meta: id, meta_id, usuario_id, monto, fecha
 *   - informes_mensuales: id, usuario_id, periodo · detalle_informe: id, informe_id, categoria_id
 *     o bloque, monto, porcentaje_real, porcentaje_recomendado, excede
 * QUÉ HAY QUE HACER (en orden):
 *   1. Cerrar las decisiones de la guía (sección 12) ANTES de crear estas migraciones.
 *   2. Dinero = enteros (pesos), nunca flotantes. Llaves foráneas con borrado en cascada para
 *      poder eliminar una cuenta completa.
 *   3. Índices: (usuario_id, fecha) en gastos; (usuario_id, estado) en notificaciones.
 * ==============================================================================
 */
