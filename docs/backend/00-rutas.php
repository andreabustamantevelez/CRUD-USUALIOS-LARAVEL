<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a routes/ (reemplazaría tu web.php).

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/00-rutas.php
 * QUÉ ES  : Disposición de las rutas: qué archivo las define, en qué grupo y con qué middleware.
 *           Plan sin código; el archivo real es routes/web.php (índice) + routes/modulos/*.php.
 *
 * ACTUALIZADO 2026-10-22:
 *   - Conteo real: 68 rutas (11 + 9 + 37 + 0 + 11) + Route::fallback().
 *     `php artisan route:list --except-vendor` muestra "Showing [69] routes"
 *     porque cuenta el fallback como una línea más.
 *   - GRUPO E pasa de 10 a 11 rutas: se agrega admin.auditoria (GET /admin/auditoria).
 *   - GRUPO C pasa de 35 a 37 rutas: se documentan gastos.edit y
 *     notificaciones.leerTodas (existían desde N1 y N4, no estaban documentadas).
 *   - Se agregan anotaciones de throttle en las rutas que lo llevan:
 *       registro.guardar, password.email, password.update,
 *       ajustes.confirmar-correo.reenviar, admin.usuarios.rol.
 *   - Se agregan anotaciones de re-auth (current_password) en:
 *       admin.usuarios.destroy, admin.categorias.desactivar.
 *   - Nota PENDIENTE J9.2: 2FA agrega 6 rutas nuevas cuando se implemente
 *     (ajustes.seguridad.*, login.2fa, login.2fa.verify). No se listan como
 *     existentes todavía. Total pasará a 74 rutas.
 *   - Decisión #61 (auditoría) y #63 (re-auth) implementadas en esta sesión.
 *     Ver DECISIONES.md.
 *
 * ACTUALIZACIÓN 2026-10-21:
 *   - Conteo anterior: 65 rutas. Se agregó admin.panel (GET /admin/panel).
 *   - GRUPO E pasó de 9 a 10 rutas.
 *   - El header "Ir a mi panel" apunta a admin.panel si rol=administrador,
 *     a panel si estandar|familiar (fix del bug de 404 en admin).
 *   - Decisión #60 (dashboard del administrador).
 *
 * ACTUALIZACIÓN 2026-10-16 (J7):
 *   - Conteo anterior: 62 rutas (obsoleto; era 64 tras V4 y verificación de correo).
 *   - GRUPO D eliminado (2026-10-08): las 10 rutas de metas pasaron al GRUPO C.
 *   - Añadidas informes.guardar (POST) e informes.ver (GET) que agregó V3.
 *   - Las 9 rutas de /admin (GRUPO E) ya apuntan a controladores reales (J7).
 *   - Metas ya no son "solo familiar"; ahora estandar y familiar entran (Grupo C).
 *
 * Verificación: php artisan route:list --except-vendor => "Showing [69] routes"
 * (68 rutas con nombre + 1 fallback sin nombre).
 * ==============================================================================
 *
 * ANOTACIONES DE ESTA VERSIÓN:
 *   · (throttle:N,M)   = middleware throttle:N peticiones por M minutos.
 *   · (reauth)         = el FormRequest exige `current_password`.
 *   · (auditada)       = la acción deja registro en la tabla `auditorias`.
 *   · (seguridad)      = envía notificación AlertaSeguridad al usuario afectado.
 *   · [NUEVA 2026-10-22] = ruta agregada en esta sesión.
 *   · [DOC 2026-10-22]   = ruta ya existía, ahora documentada.
 *   · [PENDIENTE J9.2]   = ruta planificada, no implementada todavía.
 *
 * GRUPO A - PÚBLICAS (sin sesión) [middleware 'guest' en login/registro/recuperar]
 * -------------------------------------------------------------------------
 *   GET    /iniciar-sesion                          login                    (auth.php, J3)
 *   POST   /iniciar-sesion                          login.entrar             (auth.php, J3)
 *   GET    /crear-usuario                           registro                 (auth.php, J3)
 *   POST   /crear-usuario                           registro.guardar         (auth.php, J3)  (throttle:5,1)
 *   GET    /recuperar-contrasena                    password.request         (auth.php, J3)
 *   POST   /recuperar-contrasena                    password.email           (auth.php, J3)  (throttle:5,1)
 *   GET    /restablecer-contrasena/{token}          password.reset           (auth.php, J3)
 *   POST   /restablecer-contrasena                  password.update          (auth.php, J3)  (throttle:5,1)
 *   GET    /                                        inicio                   (publico.php, N5)
 *   GET    /politica-de-privacidad                  politica                 (publico.php, N5)
 *   GET    /terminos                                terminos                 (publico.php, N5)
 *
 * GRUPO B - PRIVADAS, todos los roles con sesión [auth + cuenta.activa]
 * -------------------------------------------------------------------------
 *   POST   /cerrar-sesion                           logout                   (auth.php, J3)
 *   GET    /ajustes/perfil                          ajustes.perfil           (ajustes.php, V4)
 *   PUT    /ajustes/perfil                          ajustes.perfil.update    (ajustes.php, V4)
 *   GET    /ajustes/confirmar-correo/{token}        ajustes.confirmar-correo (ajustes.php, V4)  (seguridad: email.changed al correo VIEJO)
 *   POST   /ajustes/confirmar-correo/reenviar       ajustes.confirmar-correo.reenviar (ajustes.php, V4)  (throttle:1,1)
 *
 * GRUPO B FINANCIERO - [auth + cuenta.activa + rol:estandar,familiar]
 * -------------------------------------------------------------------------
 *   GET    /ajustes/tipo-cuenta                     ajustes.tipo-cuenta      (ajustes.php, V4)
 *   PUT    /ajustes/tipo-cuenta                     ajustes.tipo-cuenta.update (ajustes.php, V4)
 *   GET    /ajustes/eliminar-cuenta                 ajustes.eliminar-cuenta  (ajustes.php, V4)
 *   DELETE /ajustes/eliminar-cuenta                 ajustes.eliminar-cuenta.destroy (ajustes.php, V4)
 *
 * GRUPO C - ESTANDAR Y FAMILIAR [auth + cuenta.activa + rol:estandar,familiar]
 * -------------------------------------------------------------------------
 *   GET    /panel                                   panel                    (panel.php, J8)
 *   GET    /gastos                                  gastos.index             (gastos.php, N1)
 *   GET    /gastos/nuevo                            gastos.create            (gastos.php, N1)
 *   POST   /gastos                                  gastos.store             (gastos.php, N1)
 *   PUT    /gastos/{gasto}                          gastos.update            (gastos.php, N1)
 *   DELETE /gastos/{gasto}                          gastos.destroy           (gastos.php, N1)
 *   GET    /gastos/{gasto}/editar                   gastos.edit              (gastos.php, N1)  [DOC 2026-10-22]
 *   GET    /categorias                              categorias.index         (gastos.php, N2)
 *   POST   /categorias                              categorias.store         (gastos.php, N2)
 *   PUT    /categorias/{categoria}                  categorias.update        (gastos.php, N2)
 *   DELETE /categorias/{categoria}                  categorias.destroy       (gastos.php, N2)
 *   GET    /gastos-fijos                            gastos-fijos.index       (gastos-fijos.php, N3)
 *   POST   /gastos-fijos                            gastos-fijos.store       (gastos-fijos.php, N3)
 *   PUT    /gastos-fijos/{gastoFijo}                gastos-fijos.update      (gastos-fijos.php, N3)
 *   DELETE /gastos-fijos/{gastoFijo}                gastos-fijos.destroy     (gastos-fijos.php, N3)
 *   POST   /gastos-fijos/{gastoFijo}/pagar          gastos-fijos.pagar       (gastos-fijos.php, N3)
 *   GET    /notificaciones                          notificaciones.index     (notificaciones.php, N4)
 *   POST   /notificaciones/leer                     notificaciones.leer      (notificaciones.php, N4)
 *   POST   /notificaciones/leer-todas               notificaciones.leerTodas (notificaciones.php, N4)  [DOC 2026-10-22]
 *   GET    /creditos/simulador                      creditos.simulador       (creditos.php, V1)
 *   POST   /creditos                                creditos.guardar         (creditos.php, V1)
 *   GET    /creditos/historial                      creditos.historial       (creditos.php, V2)
 *   DELETE /creditos/{simulacion}                   creditos.eliminar        (creditos.php, V2)
 *   GET    /informes                                informes.actual          (informes.php, V3)
 *   POST   /informes/guardar                        informes.guardar         (informes.php, V3)
 *   GET    /informes/historial                      informes.historial       (informes.php, V3)
 *   GET    /informes/historial/{id}                 informes.ver             (informes.php, V3)
 *   GET    /metas                                   metas.index              (metas.php, J5)
 *   POST   /metas                                   metas.store              (metas.php, J5)
 *   GET    /metas/invitaciones                      metas.invitaciones       (metas.php, J5)
 *   POST   /metas/invitaciones/{participacion}/aceptar  metas.aceptar        (metas.php, J5)
 *   POST   /metas/invitaciones/{participacion}/rechazar metas.rechazar       (metas.php, J5)
 *   GET    /metas/{meta}                            metas.show               (metas.php, J5)
 *   POST   /metas/{meta}/invitar                    metas.invitar            (metas.php, J5)
 *   POST   /metas/{meta}/aportes                    metas.aportes            (metas.php, J5)
 *   POST   /metas/{meta}/finalizar                  metas.finalizar          (metas.php, J5)
 *   POST   /metas/{meta}/abandonar                  metas.abandonar          (metas.php, J5)
 *
 * GRUPO D - SOLO FAMILIAR *** ELIMINADO el 2026-10-08 ***
 * Las 10 rutas de metas pasaron al GRUPO C para que el estándar también pueda
 * crear metas individuales y recibir invitaciones.
 *
 * GRUPO E - SOLO ADMINISTRADOR, prefijo /admin y nombres admin.*
 *           [auth + cuenta.activa + rol:administrador]
 * -------------------------------------------------------------------------
 *   GET    /admin/panel                             admin.panel              (admin.php, 2026-10-21)
 *   GET    /admin/usuarios                          admin.usuarios           (admin.php, J7)
 *   PUT    /admin/usuarios/{usuario}/rol            admin.usuarios.rol       (admin.php, J7)  (throttle:30,1) (auditada)
 *   PUT    /admin/usuarios/{usuario}/estado         admin.usuarios.estado    (admin.php, J7)  (auditada) (seguridad: account.suspended)
 *   DELETE /admin/usuarios/{usuario}                admin.usuarios.destroy   (admin.php, J7)  (reauth) (auditada)
 *   GET    /admin/categorias                        admin.categorias         (admin.php, J7)
 *   POST   /admin/categorias                        admin.categorias.store   (admin.php, J7)  (auditada)
 *   PUT    /admin/categorias/{categoria}            admin.categorias.update  (admin.php, J7)  (auditada)
 *   PATCH  /admin/categorias/{categoria}/desactivar admin.categorias.desactivar (admin.php, J7)  (reauth) (auditada)
 *   GET    /admin/estadisticas                      admin.estadisticas       (admin.php, J7)
 *   GET    /admin/auditoria                         admin.auditoria          (admin.php, 2026-10-22)  [NUEVA 2026-10-22]
 *
 * RUTAS PLANIFICADAS (PENDIENTE J9.2)
 * -------------------------------------------------------------------------
 *   2FA (TOTP). No implementadas todavía; se agregan cuando el entorno local
 *   permita instalar pragmarx/google2fa + bacon/bacon-qr-code. Ver decisión #67.
 *
 *   GET    /iniciar-sesion/2fa                     login.2fa                [PENDIENTE J9.2]
 *   POST   /iniciar-sesion/2fa                     login.2fa.verify         [PENDIENTE J9.2]
 *   GET    /ajustes/seguridad                      ajustes.seguridad.index  [PENDIENTE J9.2]
 *   GET    /ajustes/seguridad/2fa/activar          ajustes.seguridad.2fa.enable  [PENDIENTE J9.2]
 *   POST   /ajustes/seguridad/2fa/confirmar        ajustes.seguridad.2fa.confirm [PENDIENTE J9.2]
 *   DELETE /ajustes/seguridad/2fa                  ajustes.seguridad.2fa.disable [PENDIENTE J9.2]
 *
 * TOTAL ACTUAL: 68 rutas reales (11 A + 5 B + 4 B.F + 37 C + 0 D + 11 E)
 *               + Route::fallback() = 69 líneas en route:list.
 * TOTAL PLANIFICADO (cuando cierre J9.2): 74 rutas reales + 1 fallback = 75 líneas.
 *
 * QUÉ HAY QUE HACER (en orden):
 *   1. routes/web.php solo incluye archivos de routes/modulos/ dentro de grupos con middleware
 *      (Juan).
 *   2. Cada persona escribe SOLO sus archivos de módulo con los nombres de esta lista.
 *   3. Segmentos fijos antes que los de parámetro (/gastos/nuevo antes de /gastos/{gasto};
 *      /metas/invitaciones antes de /metas/{meta}; /admin/panel antes de /admin/usuarios
 *      no importa el orden pero se documenta así).
 *   4. Verificar con el listado de rutas de Laravel que no falte ni sobre ninguna:
 *      php artisan route:list --except-vendor  =>  "Showing [69] routes".
 *
 * CÓMO HACERLO / NOTAS:
 *   - Detalle completo y reglas: Disposicion_de_Rutas_Cuentas_Claras.txt
 *   - GRUPOS DE MIDDLEWARE (definidos en routes/web.php):
 *       A   Públicas ............. sin sesión; 'guest' en login/registro/recuperar.
 *       B   Con sesión ........... auth + cuenta.activa (cualquier rol).
 *       B F Financiero ........... auth + cuenta.activa + rol:estandar,familiar.
 *       C   Estándar y familiar .. auth + cuenta.activa + rol:estandar,familiar.
 *       D   *** ELIMINADO *** (2026-10-08).
 *       E   Solo administrador ... auth + cuenta.activa + rol:administrador,
 *                                  prefijo /admin y nombres admin.*.
 *   - Middleware GLOBAL (bootstrap/app.php):
 *       SecurityHeaders::class → aplica X-Frame-Options, nosniff,
 *       Referrer-Policy, Permissions-Policy, CSP, HSTS (prod+HTTPS).
 *   - redirectUsersTo (bootstrap/app.php): admin → admin.panel, resto → panel.
 *   - Throttles inline (2026-10-22):
 *       registro.guardar (5,1), password.email (5,1), password.update (5,1),
 *       ajustes.confirmar-correo.reenviar (1,1), admin.usuarios.rol (30,1).
 *   - Re-auth (2026-10-22): admin.usuarios.destroy y
 *       admin.categorias.desactivar usan FormRequest con `current_password`.
 *   - Auditoría (2026-10-22): toda mutación del admin registra en la tabla
 *       `auditorias` (decisión #61). La vista de auditoría es solo lectura.
 *
 * CHANGELOG RUTAS:
 *   2026-10-22: +1 = 68 (admin.auditoria). Se documentan 2 rutas que ya existían
 *               (gastos.edit, notificaciones.leerTodas). Se anotan throttles,
 *               re-auth, auditoría y notificaciones de seguridad. Total del
 *               grupo C: 35 → 37. Total del grupo E: 10 → 11.
 *               PENDIENTE J9.2: +6 rutas de 2FA (total proyectado: 74 rutas).
 *   2026-10-21: +1 = 65 (admin.panel). Grupo E: 9 → 10.
 *   2026-10-20: 64 (docs desactualizado: decía 62; +2 confirmar-correo de V4).
 *   2026-10-19: 62 (docs desactualizado: no incluía las 2 de confirmar-correo).
 *   2026-10-17: +2 = 62 (ajustes.confirmar-correo, ajustes.confirmar-correo.reenviar).
 *   2026-10-08: metas de Grupo D a Grupo C. Grupo D eliminado.
 * ==============================================================================
 */