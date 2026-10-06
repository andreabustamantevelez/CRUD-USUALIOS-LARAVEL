<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/03-migraciones.php
 * ESTADO  : HECHO (2026-10-05, Juan) - tarea J2. Las migraciones REALES están en database/migrations/.
 *           Este archivo es ahora el DICCIONARIO DE DATOS: lo que existe de verdad. Si cambias una tabla,
 *           crea una migración NUEVA (nunca edites una que ya esté en main) y actualiza este archivo.
 * REGLAS GENERALES
 *   - Dinero = enteros en pesos COP (unsignedBigInteger). Nunca decimales. Solo tasa_mensual usa decimal(5,2).
 *   - Llaves foráneas: al borrar un usuario se borran sus datos (cascade). Una categoría con gastos NO se
 *     puede borrar (restrict): primero se mueven los gastos (decisión #17).
 *   - Estados son texto validado en la aplicación (no enum).
 *   - Fechas de creación/edición = created_at / updated_at en todas las tablas (salvo detalles_informe).
 *
 * TABLAS Y COLUMNAS (llave primaria = id en todas)
 *   users: nombre, cedula (único), email (único), password (hash), rol (estandar|familiar|administrador),
 *     activo (boolean; false = cuenta suspendida), ingreso_mensual (nullable; vacío = no registrado, C3),
 *     consentimiento_datos_at (nullable; migración 000009), remember_token, timestamps
 *   categorias: user_id (NULL = del sistema), nombre, bloque (necesidades|discrecionales|ahorro, C2),
 *     peso_recomendado (nullable), timestamps · nombre único por usuario SE VALIDA EN LA APLICACIÓN
 *   gastos: user_id, categoria_id (restrict), monto, fecha, descripcion (nullable), timestamps ·
 *     índice (user_id, fecha)
 *   gastos_fijos: user_id, categoria_id (restrict), nombre, monto, dia_vencimiento (1-31),
 *     periodicidad ('mensual'), descripcion (nullable), activo, timestamps
 *   pagos_gasto_fijo: user_id, gasto_fijo_id, periodo ('AAAA-MM'), fecha_vencimiento,
 *     estado (pendiente|pagado|vencido), fecha_pago (nullable), monto, timestamps ·
 *     ÚNICO (gasto_fijo_id, periodo)
 *   simulaciones_credito: user_id, nombre, monto, tasa_mensual (%), plazo_meses, cuota, total_pagar,
 *     intereses, timestamps (created_at = fecha de la simulación)
 *   informes: user_id, anio, mes, ingreso, total_gastado, timestamps · ÚNICO (user_id, anio, mes)
 *     (es una FOTO del mes: no cambia si luego se editan gastos)
 *   detalles_informe (sin timestamps): informe_id, categoria_id (nullable; null si la categoría se
 *     eliminó), categoria_nombre, bloque, monto_gastado, porcentaje_real (decimal 5,2),
 *     porcentaje_recomendado, excede (boolean)
 *   metas: creador_id, nombre, monto_objetivo, fecha_limite (nullable), estado (activa|completada|
 *     cancelada), timestamps · NO guarda monto_actual: el avance se calcula con Meta::totalConjunto()
 *   participaciones_meta: meta_id, user_id, estado_invitacion (pendiente|aceptada|rechazada|abandonada),
 *     fecha_union (nullable), timestamps · ÚNICO (meta_id, user_id) · máximo 2 participantes por meta
 *     (se valida en la aplicación)
 *   aportes_meta: meta_id, user_id, monto, fecha, timestamps · índice (meta_id, user_id)
 *     PRIVACIDAD (RQNF-03): nunca enviar a una vista los aportes del otro participante.
 *   notificaciones: user_id, tipo, mensaje (255), estado (pendiente|leida|cancelada),
 *     pago_gasto_fijo_id (nullable), meta_id (nullable), clave_unica (nullable), timestamps ·
 *     ÚNICO (user_id, clave_unica) para que el comando diario (J6) no duplique alertas ·
 *     clave sugerida: 'por_vencer:{pago_id}:{periodo}'
 *   Tablas de Laravel: sessions, password_reset_tokens, cache, cache_locks, jobs, job_batches,
 *     failed_jobs, migrations.
 *
 * DIFERENCIAS CON EL PLAN ORIGINAL (decididas el 2026-10-05; ver DECISIONES.md)
 *   - ELIMINADAS: la tabla saldo (#14: el "disponible del mes" se calcula) y cuota_credito (#4: el
 *     crédito es solo simulación).
 *   - Nombres: usuarios -> users, categorias_gasto -> categorias, metas_ahorro -> metas,
 *     informes_mensuales -> informes, detalle_informe -> detalles_informe, usuario_id -> user_id,
 *     identificacion -> cedula, correo -> email, estado (activo|suspendido) -> activo (boolean),
 *     dia_pago -> dia_vencimiento, tasa/plazo/total -> tasa_mensual/plazo_meses/total_pagar.
 *   - Se quitó categorias.activa y metas.monto_actual; se agregaron pagos_gasto_fijo.gasto_fijo_id y
 *     monto, informes.id/anio/mes/ingreso/total_gastado y notificaciones.clave_unica.
 * ==============================================================================
 */
