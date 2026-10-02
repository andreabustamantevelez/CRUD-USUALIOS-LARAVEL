<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a routes/ (reemplazaría tu web.php).

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/00-rutas.php
 * QUÉ ES  : Disposición de las rutas: qué archivo las define, en qué grupo y con qué middleware.
 *           Plan sin código; el archivo real es routes/web.php (índice) + routes/modulos/*.php.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - GRUPO A - PÚBLICAS (sin sesión) [sin middleware; las de login/registro/recuperar usan
 *     "guest" desde J3]
 *   -    GET    /iniciar-sesion  →  login  (auth.php, Juan (J3))
 *   -    POST   /iniciar-sesion  →  login.entrar  (auth.php, Juan (J3))
 *   -    GET    /crear-usuario  →  registro  (auth.php, Juan (J3))
 *   -    POST   /crear-usuario  →  registro.guardar  (auth.php, Juan (J3))
 *   -    GET    /recuperar-contrasena  →  password.request  (auth.php, Juan (J3))
 *   -    POST   /recuperar-contrasena  →  password.email  (auth.php, Juan (J3))
 *   -    GET    /restablecer-contrasena/{token}  →  password.reset  (auth.php, Juan (J3))
 *   -    POST   /restablecer-contrasena  →  password.update  (auth.php, Juan (J3))
 *   -    GET    /  →  inicio  (publico.php, Nikol (N5))
 *   -    GET    /politica-de-privacidad  →  politica  (publico.php, Nikol (N5))
 *   -    GET    /terminos  →  terminos  (publico.php, Nikol (N5))
 *   - GRUPO B - PRIVADAS - todos los roles con sesión [auth + cuenta.activa]
 *   -    POST   /cerrar-sesion  →  logout  (auth.php, Juan (J3))
 *   -    GET    /ajustes/perfil  →  ajustes.perfil  (ajustes.php, Valeria (V4))
 *   -    PUT    /ajustes/perfil  →  ajustes.perfil.update  (ajustes.php, Valeria (V4))
 *   -    GET    /ajustes/tipo-cuenta  →  ajustes.tipo-cuenta  (ajustes.php, Valeria (V4))
 *   -    PUT    /ajustes/tipo-cuenta  →  ajustes.tipo-cuenta.update  (ajustes.php, Valeria (V4))
 *   -    GET    /ajustes/eliminar-cuenta  →  ajustes.eliminar-cuenta  (ajustes.php, Valeria (V4))
 *   -    DELETE /ajustes/eliminar-cuenta  →  ajustes.eliminar-cuenta.destroy  (ajustes.php,
 *     Valeria (V4))
 *   - GRUPO C - PRIVADAS - usuario estándar y familiar [auth + cuenta.activa +
 *     rol:estandar,familiar]
 *   -    GET    /panel  →  panel  (panel.php, Juan (J8))
 *   -    GET    /gastos  →  gastos.index  (gastos.php, Nikol (N1))
 *   -    GET    /gastos/nuevo  →  gastos.create  (gastos.php, Nikol (N1))
 *   -    POST   /gastos  →  gastos.store  (gastos.php, Nikol (N1))
 *   -    PUT    /gastos/{gasto}  →  gastos.update  (gastos.php, Nikol (N1))
 *   -    DELETE /gastos/{gasto}  →  gastos.destroy  (gastos.php, Nikol (N1))
 *   -    GET    /categorias  →  categorias.index  (gastos.php, Nikol (N2))
 *   -    POST   /categorias  →  categorias.store  (gastos.php, Nikol (N2))
 *   -    PUT    /categorias/{categoria}  →  categorias.update  (gastos.php, Nikol (N2))
 *   -    DELETE /categorias/{categoria}  →  categorias.destroy  (gastos.php, Nikol (N2))
 *   -    GET    /gastos-fijos  →  gastos-fijos.index  (gastos-fijos.php, Nikol (N3))
 *   -    POST   /gastos-fijos  →  gastos-fijos.store  (gastos-fijos.php, Nikol (N3))
 *   -    PUT    /gastos-fijos/{gastoFijo}  →  gastos-fijos.update  (gastos-fijos.php, Nikol (N3))
 *   -    DELETE /gastos-fijos/{gastoFijo}  →  gastos-fijos.destroy  (gastos-fijos.php, Nikol (N3))
 *   -    POST   /gastos-fijos/{gastoFijo}/pagar  →  gastos-fijos.pagar  (gastos-fijos.php, Nikol
 *     (N3))
 *   -    GET    /notificaciones  →  notificaciones.index  (notificaciones.php, Nikol (N4))
 *   -    POST   /notificaciones/leer  →  notificaciones.leer  (notificaciones.php, Nikol (N4))
 *   -    GET    /creditos/simulador  →  creditos.simulador  (creditos.php, Valeria (V1, V2))
 *   -    POST   /creditos  →  creditos.guardar  (creditos.php, Valeria (V1, V2))
 *   -    GET    /creditos/historial  →  creditos.historial  (creditos.php, Valeria (V1, V2))
 *   -    DELETE /creditos/{simulacion}  →  creditos.eliminar  (creditos.php, Valeria (V1, V2))
 *   -    GET    /informes  →  informes.actual  (informes.php, Valeria (V3))
 *   -    GET    /informes/historial  →  informes.historial  (informes.php, Valeria (V3))
 *   - GRUPO D - SOLO FAMILIAR [auth + cuenta.activa + rol:familiar]
 *   -    GET    /metas  →  metas.index  (metas.php, Juan (J5))
 *   -    POST   /metas  →  metas.store  (metas.php, Juan (J5))
 *   -    GET    /metas/invitaciones  →  metas.invitaciones  (metas.php, Juan (J5))
 *   -    POST   /metas/invitaciones/{participacion}/aceptar  →  metas.aceptar  (metas.php, Juan
 *     (J5))
 *   -    POST   /metas/invitaciones/{participacion}/rechazar  →  metas.rechazar  (metas.php, Juan
 *     (J5))
 *   -    GET    /metas/{meta}  →  metas.show  (metas.php, Juan (J5))
 *   -    POST   /metas/{meta}/invitar  →  metas.invitar  (metas.php, Juan (J5))
 *   -    POST   /metas/{meta}/aportes  →  metas.aportes  (metas.php, Juan (J5))
 *   -    POST   /metas/{meta}/finalizar  →  metas.finalizar  (metas.php, Juan (J5))
 *   -    POST   /metas/{meta}/abandonar  →  metas.abandonar  (metas.php, Juan (J5))
 *   - GRUPO E - SOLO ADMINISTRADOR (prefijo /admin, nombres admin.*) [auth + cuenta.activa +
 *     rol:administrador]
 *   -    GET    /admin/usuarios  →  admin.usuarios  (admin.php, Juan (J7))
 *   -    PUT    /admin/usuarios/{usuario}/rol  →  admin.usuarios.rol  (admin.php, Juan (J7))
 *   -    PUT    /admin/usuarios/{usuario}/estado  →  admin.usuarios.estado  (admin.php, Juan (J7))
 *   -    DELETE /admin/usuarios/{usuario}  →  admin.usuarios.destroy  (admin.php, Juan (J7))
 *   -    GET    /admin/categorias  →  admin.categorias  (admin.php, Juan (J7))
 *   -    POST   /admin/categorias  →  admin.categorias.store  (admin.php, Juan (J7))
 *   -    PUT    /admin/categorias/{categoria}  →  admin.categorias.update  (admin.php, Juan (J7))
 *   -    PATCH  /admin/categorias/{categoria}/desactivar  →  admin.categorias.desactivar
 *     (admin.php, Juan (J7))
 *   -    GET    /admin/estadisticas  →  admin.estadisticas  (admin.php, Juan (J7))
 * QUÉ HAY QUE HACER (en orden):
 *   1. routes/web.php solo incluye archivos de routes/modulos/ dentro de grupos con middleware
 *      (Juan).
 *   2. Cada persona escribe SOLO sus archivos de módulo con los nombres de esta lista.
 *   3. Fase 1 sin middleware; J3 agrega auth; J4 registra "cuenta.activa" y "rol" y los agrega a
 *      los grupos.
 *   4. Segmentos fijos antes que los de parámetro (/gastos/nuevo antes de /gastos/{gasto}).
 *   5. Verificar con el listado de rutas de Laravel que no falte ni sobre ninguna.
 * CÓMO HACERLO / NOTAS:
 *   - Detalle completo y reglas: Disposicion_de_Rutas_Cuentas_Claras.txt
 * ==============================================================================
 */
