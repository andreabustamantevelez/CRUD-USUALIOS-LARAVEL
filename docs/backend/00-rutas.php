<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a routes/ (reemplazaría tu web.php).

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/00-rutas.php
 * QUÉ ES  : Disposición de las rutas: qué archivo las define, en qué grupo y con qué middleware.
 *           Plan sin código; el archivo real es routes/web.php (índice) + routes/modulos/*.php.
 * ACTUALIZADO 2026-10-16 (J7):
 *   - Conteo real: 62 rutas. El "60" de versiones anteriores era obsoleto.
 *   - GRUPO D eliminado (2026-10-08): las 10 rutas de metas pasaron al GRUPO C.
 *   - Añadidas informes.guardar (POST) e informes.ver (GET) que agregó V3.
 *   - Las 9 rutas de /admin (GRUPO E) ya apuntan a controladores reales (J7).
 *   - Metas ya no son "solo familiar"; ahora estandar y familiar entran (Grupo C).
 * Verificación: php artisan route:list --except-vendor => "Showing [62] routes".
 * ==============================================================================
 *
 * GRUPO A - PÚBLICAS (sin sesión) [middleware 'guest' en login/registro/recuperar]
 * -------------------------------------------------------------------------
 *   GET    /iniciar-sesion                          login                    (auth.php, J3)
 *   POST   /iniciar-sesion                          login.entrar             (auth.php, J3)
 *   GET    /crear-usuario                           registro                 (auth.php, J3)
 *   POST   /crear-usuario                           registro.guardar         (auth.php, J3)
 *   GET    /recuperar-contrasena                    password.request         (auth.php, J3)
 *   POST   /recuperar-contrasena                    password.email           (auth.php, J3)
 *   GET    /restablecer-contrasena/{token}          password.reset           (auth.php, J3)
 *   POST   /restablecer-contrasena                  password.update          (auth.php, J3)
 *   GET    /                                        inicio                   (publico.php, N5)
 *   GET    /politica-de-privacidad                  politica                 (publico.php, N5)
 *   GET    /terminos                                terminos                 (publico.php, N5)
 *
 * GRUPO B - PRIVADAS, todos los roles con sesión [auth + cuenta.activa]
 * -------------------------------------------------------------------------
 *   POST   /cerrar-sesion                           logout                   (auth.php, J3)
 *   GET    /ajustes/perfil                          ajustes.perfil           (ajustes.php, V4)
 *   PUT    /ajustes/perfil                          ajustes.perfil.update    (ajustes.php, V4)
 *   GET    /ajustes/tipo-cuenta                     ajustes.tipo-cuenta      (ajustes.php, V4)
 *   PUT    /ajustes/tipo-cuenta                     ajustes.tipo-cuenta.update (ajustes.php, V4)
 *   GET    /ajustes/eliminar-cuenta                 ajustes.eliminar-cuenta  (ajustes.php, V4)
 *   DELETE /ajustes/eliminar-cuenta                 ajustes.eliminar-cuenta.destroy (ajustes.php, V4)
 *   GET  /ajustes/confirmar-correo/{token}   ajustes.confirmar-correo
 *   POST /ajustes/confirmar-correo/reenviar  ajustes.confirmar-correo.reenviar
 *
 * GRUPO C - ESTANDAR Y FAMILIAR [auth + cuenta.activa + rol:estandar,familiar]
 * -------------------------------------------------------------------------
 *   GET    /panel                                   panel                    (panel.php, J8)
 *   GET    /gastos                                  gastos.index             (gastos.php, N1)
 *   GET    /gastos/nuevo                            gastos.create            (gastos.php, N1)
 *   POST   /gastos                                  gastos.store             (gastos.php, N1)
 *   PUT    /gastos/{gasto}                          gastos.update            (gastos.php, N1)
 *   DELETE /gastos/{gasto}                          gastos.destroy           (gastos.php, N1)
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
 *   GET    /admin/usuarios                          admin.usuarios           (admin.php, J7)
 *   PUT    /admin/usuarios/{usuario}/rol            admin.usuarios.rol       (admin.php, J7)
 *   PUT    /admin/usuarios/{usuario}/estado         admin.usuarios.estado    (admin.php, J7)
 *   DELETE /admin/usuarios/{usuario}                admin.usuarios.destroy   (admin.php, J7)
 *   GET    /admin/categorias                        admin.categorias         (admin.php, J7)
 *   POST   /admin/categorias                        admin.categorias.store   (admin.php, J7)
 *   PUT    /admin/categorias/{categoria}            admin.categorias.update  (admin.php, J7)
 *   PATCH  /admin/categorias/{categoria}/desactivar admin.categorias.desactivar (admin.php, J7)
 *   GET    /admin/estadisticas                      admin.estadisticas       (admin.php, J7)
 *
 * TOTAL: 62 rutas (11 + 7 + 35 + 0 + 9).
 *
 * QUÉ HAY QUE HACER (en orden):
 *   1. routes/web.php solo incluye archivos de routes/modulos/ dentro de grupos con middleware
 *      (Juan).
 *   2. Cada persona escribe SOLO sus archivos de módulo con los nombres de esta lista.
 *   3. Segmentos fijos antes que los de parámetro (/gastos/nuevo antes de /gastos/{gasto};
 *      /metas/invitaciones antes de /metas/{meta}).
 *   4. Verificar con el listado de rutas de Laravel que no falte ni sobre ninguna:
 *      php artisan route:list --except-vendor  =>  "Showing [62] routes".
 *
 * CÓMO HACERLO / NOTAS:
 *   - Detalle completo y reglas: Disposicion_de_Rutas_Cuentas_Claras.txt
 *   - GRUPOS DE MIDDLEWARE (definidos en routes/web.php):
 *       A  Públicas ............. sin sesión; 'guest' en login/registro/recuperar.
 *       B  Con sesión ........... auth + cuenta.activa (cualquier rol).
 *       C  Estándar y familiar .. auth + cuenta.activa + rol:estandar,familiar.
 *       D  *** ELIMINADO *** (2026-10-08).
 *       E  Solo administrador ... auth + cuenta.activa + rol:administrador,
 *                                 prefijo /admin y nombres admin.*.
 * ==============================================================================
 */