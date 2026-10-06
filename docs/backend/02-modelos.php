<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/02-modelos.php
 * ESTADO  : HECHO (2026-10-05, Juan) - tarea J2. Los modelos REALES están en app/Models/.
 * MODELOS (clase -> tabla) Y RELACIONES
 *   User -> users: tiene muchos Gasto, Categoria (propias), GastoFijo, SimulacionCredito, Informe,
 *     Notificacion. Pertenece a muchas Meta por ParticipacionMeta.
 *   Categoria -> categorias: user_id NULL = del sistema (esDelSistema()). Constante Categoria::BLOQUES
 *     (C2). Tiene muchos Gasto.
 *   Gasto -> gastos: pertenece a User y Categoria. scopeDelUsuario($userId) y fecha/monto con cast.
 *   GastoFijo -> gastos_fijos: pertenece a User y Categoria; tiene muchos PagoGastoFijo.
 *   PagoGastoFijo -> pagos_gasto_fijo: pertenece a GastoFijo y User.
 *   SimulacionCredito -> simulaciones_credito: pertenece a User.
 *   Informe -> informes: pertenece a User; tiene muchos DetalleInforme.
 *   DetalleInforme -> detalles_informe (sin timestamps): pertenece a Informe.
 *   Meta -> metas: creador (User), tiene muchas ParticipacionMeta y AporteMeta. totalConjunto().
 *   ParticipacionMeta -> participaciones_meta · AporteMeta -> aportes_meta (privacidad, RQNF-03).
 *   Notificacion -> notificaciones: pertenece a User; opcional PagoGastoFijo y Meta.
 * REGLAS
 *   - Todos tienen HasFactory (fábricas en database/factories). El User tiene $fillable SIN rol ni
 *     activo (no se pueden asignar desde un formulario); los demás usan $guarded = [] y los controladores
 *     SIEMPRE usan $request->validated().
 *   - Toda consulta de datos financieros parte del usuario autenticado (Gasto::delUsuario(auth()->id())).
 *     Falta el "alcance global" automático: lo hace J4.
 *   - Pruebas de la base: tests/Feature/BaseDeDatosTest.php (php artisan test --filter=BaseDeDatosTest).
 * ==============================================================================
 */
