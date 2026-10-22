# DECISIONES.md — Cuentas Claras

Registro de decisiones del equipo. Cada una lleva fecha, qué se decidió y por qué.
**Lo decidido no se reabre sin un motivo nuevo.** Para cambiar una decisión: agregar una entrada
nueva que diga cuál reemplaza y por qué (no borrar la anterior).

Equipo: Juan (J), Nikol (N), Valeria (V).

---

## Decisiones que afectan las tablas (cerradas el 2026-10-05, al hacer J2)

| # | Decisión | Qué se decidió | Por qué | Lleva |
|---|----------|----------------|---------|-------|
| 1 | Identificación (cédula) | Se guarda: `users.cedula`, única | El diseño del registro ya la trae | Juan |
| 2 | Ingreso mensual | `users.ingreso_mensual` (pesos enteros, puede estar vacío); se edita en Ajustes > Perfil (contrato C3) | Un solo lugar; simulador, informes y dashboard lo leen | Juan |
| 6 | Periodicidad de gastos fijos | Solo `mensual` (`gastos_fijos.periodicidad`) | Alcance de la primera versión | Nikol |
| 9 | Pago ligado a su gasto fijo | `pagos_gasto_fijo.gasto_fijo_id`, único con `periodo` | Sin la clave no se sabe de qué gasto es cada pago, ni se evita pagar dos veces el mismo mes | Nikol |
| 10 | Categorías del sistema vs. propias | `categorias.user_id` vacío = del sistema | Una sola tabla | Juan |
| 12 | Bloques 50/30/20 | `categorias.bloque`: `necesidades`, `discrecionales`, `ahorro`. "Excede" = % real mayor que el recomendado | Valeria necesita agrupar los gastos por bloque (contrato C2) | Valeria |
| 14 | Saldo | **NO se guarda.** Se calcula el "disponible del mes" = ingreso − gastos. **Se eliminó la tabla `saldo` del diagrama original.** | Un saldo guardado se desactualiza y puede contradecir los gastos | Juan |

---

## Decisiones tomadas al corregir el diagrama de base de datos (2026-10-05, Juan)

| Cambio | Qué se hizo | Por qué |
|--------|-------------|---------|
| Tabla `cuota_credito` | **Eliminada**, junto con su referencia en `notificacion` | Decisión #4: el crédito es solo **simulación**. Un crédito real con cuotas y alertas de cuotas no está en el alcance. Si el equipo la quiere, hay que cambiar esta decisión y agregar tareas |
| Tabla `credito` | Ahora `simulaciones_credito`; se quitó `cupo_credito` y se agregaron `cuota`, `total_pagar` e `intereses` (los calcula el servidor) | Ninguna pantalla usa el cupo; el historial muestra cuota, total e intereses |
| Dinero | Enteros en pesos COP (`unsignedBigInteger`). Solo `tasa_mensual` es decimal | SQLite guarda decimales como números de coma flotante y pueden dar errores de redondeo |
| `meta_ahorro.monto_actual` | **Eliminado.** El avance se calcula con `Meta::totalConjunto()` | No se desincroniza y protege la privacidad de los aportes |
| `metas.creador_id` | Agregado | Solo el creador invita |
| `informe_mensual` | Ahora `informes` con `id`, `anio`, `mes`, `ingreso`, `total_gastado`; único por (usuario, año, mes) | La llave primaria era solo la fecha y dos usuarios chocaban |
| `detalle_informe` | Ahora `detalles_informe`, con `categoria_nombre` y `bloque` guardados | El historial no cambia si luego se renombra o elimina la categoría |
| `notificacion.clave_unica` | Agregada, única por usuario | El comando diario (J6) no duplica alertas |
| Nombres | `usuarios`→`users`, `correo`→`email`, `contraseña`→`password`, `usuario_id`→`user_id` | El sistema de login de Laravel espera esos nombres. En pantalla se sigue viendo "Correo" y "Contraseña" |
| Estado del usuario | `activo` (boolean) en lugar de un texto activo/suspendido | `activo = false` es cuenta suspendida |
| Consentimiento | `users.consentimiento_datos_at` (migración 000009) | El registro exige guardar la fecha de aceptación |
| Categorías: nombre único | Se valida en la aplicación (sin distinguir mayúsculas) | SQLite distingue mayúsculas y trata los vacíos como distintos en un índice único |

---

## Decisiones tomadas al hacer J3 — Autenticación (2026-10-06, Juan)

| Tema | Qué se decidió | Por qué |
|------|----------------|---------|
| Nombres de campos | Los formularios mantienen `correo` e `identificacion`; el controlador los traduce a las columnas `email` y `cedula` | El diseño y los nombres de campo no se cambian; las columnas siguen lo que espera el login de Laravel |
| Rol al registrarse | Sale de la tarjeta del registro (`tipo_cuenta`): solo `estandar` o `familiar`. **`administrador` nunca se acepta desde un formulario**; el primer administrador lo crea el seeder o una persona con acceso a la base | La cabecera de la vista ya definía `rol = tipo_cuenta`; la tarjeta de Jira decía "siempre estándar" y se dejó la de la vista |
| Asignar rol y activo | Se asignan a mano en el controlador (no son `fillable`) | Evita que alguien mande `rol=administrador` en un formulario |
| Contraseña | Mínimo 8 caracteres, máximo 72, con confirmación. El medidor visual del formulario es solo una ayuda | Lo pide la tarjeta de J3; 72 es el límite real de bcrypt |
| Mensaje de error de login | Siempre "Usuario o contraseña incorrectos" | No revelar si el correo existe |
| Cuenta suspendida | Se avisa "Tu cuenta está suspendida…" **solo si la contraseña era correcta** | Mensaje claro para el dueño sin delatar qué correos existen |
| Límite de intentos | 5 fallos por minuto por correo + IP en el login (en `LoginRequest`); 5 peticiones por minuto en recuperar y restablecer (`throttle:5,1`) | Protege contra adivinar contraseñas |
| Recuperar contraseña | Mensaje neutro; enlace de un solo uso que vence a los 60 min; correo en español (`RestablecerContrasena`) | No revelar correos; el correo por defecto de Laravel viene en inglés |
| Login con cédula (#1) | **No se hizo.** Se entra solo con correo | Se deja para decidir después si realmente se necesita |
| "Recordarme" | El código lo soporta (campo `recordarme`) pero la vista no tiene casilla | Cambiar el diseño no estaba en el alcance |
| `UsuarioMaqueta` | Eliminado. Todo el bloque privado de `routes/web.php` exige `auth` | Ya hay sesión real |
| Hueco temporal | Hasta J4 no existían `cuenta.activa` ni `rol:…`. **Cerrado en J4 (2026-10-06)** | Ver la sección de J4 |

---

## Decisiones tomadas al hacer J4 — Roles, permisos y aislamiento (2026-10-06, Juan)

| Tema | Qué se decidió | Por qué |
|------|----------------|---------|
| Rol que no corresponde | Responde **403** (no 404 ni redirección) | El recurso existe pero la persona no puede verlo; 403 es explícito y fácil de probar |
| Dato ajeno | Responde **403** (no 404) | Igual que arriba; la prueba comprueba además que el dato no cambia |
| Quién entra a qué | B (ajustes): todos · C (gastos, categorías, gastos fijos, notificaciones, créditos, informes, panel, metas): estandar y familiar · E (/admin): administrador | Matriz del documento de arquitectura, probada en PermisosTest. Las metas están en el Grupo C desde la reapertura de J5 (2026-10-08) |
| Administrador y datos financieros | Bloqueado también **dentro de las Policies** (`before` devuelve false) | Defensa extra por si algún día se afloja una ruta (decisión #20) |
| Cuenta suspendida | `cuenta.activa` la expulsa en la **siguiente petición**, aunque ya tuviera sesión | Suspender debe surtir efecto de inmediato |
| Global scope | **No se usa.** Se usa el scope explícito `delUsuario` y las relaciones del usuario | Un global scope esconde errores y estorba a las consultas del administrador y de los servicios |
| Simulaciones de crédito | Se ven y se **borran**, pero no se editan | Se calcula otra y se guarda como nueva |
| Informes guardados | Solo se ven; no se editan ni se borran a mano | En meses cerrados no se recalculan (regla de informes) |
| Categorías | Propias: todo. Del sistema: las ven y usan todos, nadie las edita desde la zona del usuario. Habilidad `usar` al guardar un gasto | Evita ligar un gasto a una categoría ajena mandando otro id |
| Administrador y categorías | Gestiona (ve y edita) solo las del sistema; no ve las de las personas; **borrar queda en false** | Depende de la decisión #23 (desactivar, no borrar) |
| MetaPolicy | Hecha en J5 (2026-10-07) | Ver la sección de J5 |
| Página 403 | **Personalizada desde el 2026-10-16** | Ver la sección de pulido (#47) |

---

## Decisiones tomadas al hacer J5 — Metas compartidas (2026-10-07, Juan)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 7 | Fecha límite de la meta | Opcional. Si se escribe, debe ser hoy o futura. La meta **no** se cierra sola al vencer | Evita metas que cambian de estado sin que nadie haga nada |
| 8 | Gastos ligados a meta | **No** en la primera versión. Los aportes son lo único que mueve el avance | Alcance; ligar gastos exige otro flujo y más pruebas |
| 18 | Si alguien abandona | La meta pasa a **cancelada para las dos personas**; los aportes se conservan como historial; se notifica a la otra. Quien abandonó ya no la ve; la otra la ve en solo lectura. Si una cuenta se elimina, sus filas se borran en cascada (migración J2) | Con solo dos participantes, una meta compartida con una persona deja de ser compartida |
| — | Metas individuales o compartidas | La persona decide al crear (casilla "Compartir…"). Individual: sin invitados, privada y con los mismos aportes/avance/finalizar; "abandonar" = "Cancelar meta". Columna nueva `metas.compartida` (default true) en migración nueva. Por ahora solo el rol familiar accede a metas (también las individuales) | Aclaración del 2026-10-07; el estándar sigue sin metas |
| — | Quién invita | Solo quien creó la meta, con la meta compartida y activa | `metas.creador_id` (J2) |
| — | Máximo 2 participantes | Cuentan **aceptados + pendientes** (la invitación reserva el cupo); al rechazar se libera | Evita invitar a tres personas a la vez |
| — | Reinvitar | Quien rechazó o abandonó puede ser invitado otra vez: se reactiva su misma fila (`unique(meta_id, user_id)`) | No se pueden duplicar filas |
| — | Finalizar | Solo si el total conjunto alcanzó el objetivo; cualquier participante puede hacerlo. Pasa a `completada` | Jira J5-B: "Finalizar (al llegar al objetivo)" |
| — | Privacidad de aportes | La vista y el controlador solo consultan los aportes del usuario actual; el total sale de una suma en SQL; `AporteMetaPolicy` = solo el dueño; nadie edita ni borra aportes | RQNF-03 |
| — | Respuesta a ajenos | 403 en todas las rutas de una meta ajena y al responder una invitación ajena | Igual que J4 |
| — | Invitar por | Correo (`correo`). Un usuario suspendido o administrador se trata como "no existe" | El correo es lo único que una persona conoce de otra |
| — | Notificaciones de metas | **Delegadas en `ServicioNotificaciones` (C4) desde el 2026-10-16** | Antes era versión mínima dentro de `ServicioMetas::notificar()` |
| C6 | `tieneMetasActivas($usuario)` | En `ServicioMetas`; `Meta::aportesDelUsuario($id, $desde, $hasta)` para dashboard e informes | Contrato con Valeria (cambio a estándar) y con el panel |

---

## Decisiones al reabrir J5 — Metas para estándar y cambio de tipo (2026-10-08, Juan)

| Tema | Qué se decidió | Por qué |
|------|----------------|---------|
| Metas y rol estándar | **Metas al Grupo C** (estandar + familiar). El estándar solo puede crear metas INDIVIDUALES; el familiar sigue siendo el único que puede crear compartidas o aceptar invitaciones a compartidas | El estándar también ahorra; las individuales no rompen la privacidad |
| Invitar a un estándar | Se permite + notificación especial `invitacion_meta_requiere_familiar` que lo manda a Ajustes | No cerrar la puerta de entrada; el estándar puede subir a familiar |
| Aceptar siendo estándar | `ServicioMetas::aceptar()` lanza ValidationException con mensaje: "Para aceptar esta invitación debes cambiar tu tipo de cuenta a familiar en Ajustes." | Bloqueo amable con instrucción |
| Ajustes · tipo de cuenta | `ServicioCuentas::cambiarTipo` (contrato C7 parcial, luego completo en V4) | Una sola vía; no cierra sesión |
| Familiar → estándar | Bloqueado si tiene metas COMPARTIDAS activas. Regla en `ServicioMetas::tieneMetasCompartidasActivas` | Sería abandonar metas ajenas sin aviso |
| Policy de metas | `before` solo bloquea al administrador; el resto del control lo hace el servicio | El estándar no debe recibir 403 seco |
| Grupo D de rutas | Eliminado. Las 10 rutas de metas pasaron al Grupo C | Coherencia con la nueva matriz de roles |
| Sidebar | "Metas" visible para estandar y familiar | Refleja la nueva matriz |
| `ServicioCuentas::cambiarTipo` | Contrato C7 parcial: solo cambiar tipo | Eliminar cuenta llega en J8a |

---

## Decisiones del 2026-10-14 — Refinamiento visual (#24..#30)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 24 | Tinte por tipo de cuenta | El color de marca cambia (verde ↔ morado) según `users.rol`. Se expone como `<html data-account-type="...">` | Comunicar el rol; el usuario siente que su cuenta es distinta |
| 25 | Píldora de acciones del topbar | Clase `.topbar-acciones` agrupa el botón de tema y el menú de usuario | Un solo bloque visual |
| 26 | Transiciones | View Transitions API + fade-in + fuente fallback con `display=swap` | Navegación fluida sin depender de JS en la mayoría de casos |
| 27 | Material Symbols | `display=block` con fallback a los 800 ms | Evita el flash de iconos sin cargar |
| 28 | Botones de gastos | Migrados a clases del sistema (`btn btn-primary`, `btn btn-secondary`) | Los estilos inline rompen el tinte y el dark mode |
| 29 | Contenido bajo el topbar fijo | `pt-16` en el layout `app` | El topbar es `fixed`; sin padding el contenido queda tapado |
| 30 | Tinte y peligro | El tinte NUNCA se aplica a estados de error ni al gráfico 50/30/20 | Rojo = peligro siempre; el gráfico tiene sus colores propios por bloque |

---

## Decisiones del 2026-10-16 — J6 Alertas y servicio de notificaciones (#31..#34)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 31 | Hora del comando diario | 07:00, con `--fecha=YYYY-MM-DD` para simular | Antes de la jornada laboral; la simulación permite probar sin esperar |
| 32 | Idempotencia | Por `(user_id, clave_unica)`. Si la alerta estaba cancelada, se reactiva | El comando puede correr varias veces sin duplicar |
| 33 | Vencido vs por vencer | Un pago vencido cancela la alerta `por_vencer` pendiente y crea una `vencido` | Evita dos notificaciones contradictorias del mismo pago |
| 34 | A quién ignora el comando | Administradores y suspendidos | El admin no tiene finanzas; el suspendido no debe recibir nada |

---

## Decisiones del 2026-10-16 — J8a Servicio de cuentas: eliminar cuenta (#35..#36)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 35 | Eliminar cuenta con metas compartidas activas | Las metas se cancelan y se avisa al otro. Si el que se va es el creador, `metas.creador_id` se transfiere al otro antes de borrar | La FK tiene `ON DELETE CASCADE`; sin la transferencia, la meta se borraría y el otro perdería el historial |
| 36 | Último administrador activo | No se puede eliminar. `ValidationException` con mensaje claro | El sistema nunca queda sin admin activo |

---

## Decisiones del 2026-10-16 — J8 Dashboard (#37..#41)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 37 | Refresco de pagos | El dashboard llama a `ServicioGastosFijos` antes de calcular métricas | Los pagos del mes pueden no existir todavía al entrar |
| 38 | Scope del periodo | Se usa `whereYear` + `whereMonth` directo hasta que exista el scope `delPeriodo` (C1 de Nikol) | No bloquear J8 por C1; el TODO queda marcado |
| 39 | Tarjeta de metas | Solo se dibuja si el rol es familiar | El estándar no ve la tarjeta (aunque puede tener metas individuales, no están en el alcance del dashboard) |
| 40 | Casts de Gasto | `fecha => date`, `monto => integer` | Formateo consistente en toda la app |
| 41 | Vista del dashboard | Se traslada el `code.html` a Blade tal cual | El diseño no se cambia; solo se conectan datos |

**#38 CERRADA el 2026-10-22**: `Gasto::delPeriodo($anio, $mes)` implementado (C1). `DashboardController` lo usa. `TODO(J8)` cerrado. Ver #61.

---

## Decisiones del 2026-10-16 — J7 Administración (#42..#44)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 42 | Tinte del administrador | Core Blue (#2563EB claro / #93b4ff oscuro). Se reutiliza el azul de "Necesidades" del gráfico 50/30/20 | No introducir un color nuevo al sistema |
| 43 | ¿El admin puede eliminarse a sí mismo? | No desde el panel de administración. Sí desde Ajustes si hay otro admin activo | El panel es para gestionar a otros; Ajustes es para la propia cuenta |
| 44 | Registro de acciones del admin (auditoría) | **No implementado** en esta versión ([PROPUESTA]) | Requiere tabla nueva y decisión de alcance; se hace en una PR aparte si se decide |

**#44 CERRADA el 2026-10-22**: auditoría implementada. Ver #61.

---

## Decisiones del 2026-10-16 — Pulido (#45..#48)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 45 | Logo | SVG inline en `components/logo.blade.php`, tokens `--logo-*` en `theme.css` bloque 10. Reemplaza todas las imágenes externas de `lh3.googleusercontent.com` | El logo externo depende de Google; el SVG se adapta al tema y al rol |
| 46 | Skip link | Universal desde `base.blade.php`; el script inline pone `id="contenido"` al primer `<main>` | Una sola línea en un solo archivo, sin tocar cada layout |
| 47 | Página 403 | Personalizada en `errors/403.blade.php` con el diseño del sistema. Layout `publico` (no requiere sesión) | Coherencia visual; antes era la de Laravel |
| 48 | Selects nativos | `appearance:none` + flecha SVG propia + `color-scheme:dark` para el popup nativo | El popup nativo lo dibuja el SO; no se puede controlar al 100% sin componente custom |

---

## Decisiones del 2026-10-17 — V4, verificación de correo y rediseño de reset (#49..#52)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 49 | Verificación de cambio de correo | Token de un solo uso: `Str::random(64)` plano → guardado como SHA-256 en `users.email_token`. Vencimiento en `users.pending_email_expires_at = now()->addHour()` (1 hora). Se envía al NUEVO correo con `Notification::route('mail', $pending_email)`. El correo actual sigue activo hasta confirmar. Al confirmar se cierra la sesión. El cambio requiere contraseña actual | Verifica la titularidad del NUEVO correo (no del actual). El hash protege contra fuga de la BD. La contraseña actual protege ante sesión robada. El logout evita que un atacante con sesión siga dentro |
| 50 | Rediseño del correo de restablecer contraseña | Mismo layout visual que `confirmar-cambio-correo`. Vencimiento reducido de 60 a 30 minutos, configurable con `AUTH_PASSWORD_RESET_EXPIRE`. 30 minutos es el estándar de la industria (GitHub, GitLab, Stripe, Auth0) | Consistencia visual + seguridad. 60 minutos es demasiado para un enlace que puede reenviarse o quedar en spam. 30 es suficiente para abrir el correo y completar el formulario |
| 51 | Envío de notificaciones a destinatarios no autenticados | `Notification::route('mail', $destinatario)->notify(...)` cuando el destinatario NO es el usuario autenticado. Específicamente `ConfirmarCambioCorreo` se envía al `pending_email`. El resto de notificaciones internas (alertas, invitaciones) siguen con `$usuario->notify(...)` | `$usuario->notify(...)` usa `$usuario->email` (el actual); el correo de verificación debe ir al nuevo |
| 52 | Bug de `cycleRememberToken` al eliminar cuenta | Si `Auth::logout()` va después de `$usuario->delete()`, Laravel hace `$user->save()` en `cycleRememberToken()`. Como `$user->exists` quedó en `false`, hace INSERT y re-crea al usuario. Fix: `$usuario->setRememberToken(null)` antes de `Auth::logout()`. Aplica también al confirmar cambio de correo | Evita re-crear la cuenta recién eliminada. Bug sutil que no aparece en tests si el factory no genera `remember_token` |

---

## Decisiones del 2026-10-19 — Restricción de Ajustes, página 404, sesión, íconos y botones (#53..#57)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 53 | Restricción de Ajustes por rol | El administrador solo accede a `ajustes.perfil` y `ajustes.confirmar-correo`. Los endpoints de tipo-cuenta y eliminar-cuenta (GET y POST/PUT/DELETE) requieren rol estandar o familiar; el admin recibe 403. Aplica en `routes/modulos/ajustes.php` (middleware `rol:estandar,familiar`) y en `AjustesController` (`abort_unless` como defensa en profundidad). La vista `_tabs.blade.php` oculta las 2 pestañas al admin | Refuerza #20 (admin sin finanzas personales) y #43 (no puede eliminarse a sí mismo desde Ajustes). Evita que un admin se degrade o se auto-elimine sin pasar por el panel de administración (que sí tiene la lógica de último admin activo) |
| 54 | Página 404 personalizada con `Route::fallback()` | Mismo esqueleto que el 403 (icono `explore_off`, código 404, título, mensaje, botones según rol). El fallback se declara al final del bloque privado de `routes/web.php` para que la vista se renderice con sesión activa y `@auth` funcione. Sin fallback, Laravel responde 404 sin sesión y un usuario logueado vería la versión de invitado. Consecuencia colateral: `GET /cerrar-sesion` (que solo existe como POST) pasa de 405 a 404; se acepta y el test de `AuthTest` ahora verifica además que la sesión NO se cierra | Coherencia visual y mejor UX para un usuario logueado. El test de seguridad se mantiene intacto |
| 55 | Expiración de sesión por inactividad | Middleware `ExpirarSesionPorInactividad` (alias `sesion.inactividad`), aplicado ANTES de `auth` en `routes/web.php`. `LIMITE_MINUTOS = 15`. `SESSION_LIFETIME=480` en `.env` para que Laravel no expire antes que el middleware. Mensaje "Tu sesión expiró por inactividad. Vuelve a iniciar sesión." aparece en el login. Sin expulsión en tiempo real: el servidor no puede empujar un logout al navegador; la sesión se invalida en la SIGUIENTE petición del usuario después de 15 min | 15 minutos es el estándar para apps de finanzas. El orden del array importa: si `auth` va primero, redirige al login y el mensaje nunca aparece |
| 56 | Iconografía por categoría | Componente reutilizable `<x-categoria-icono :categoria="$c" :size="20" />`. Mapea el nombre de la categoría a un SVG propio. Fallback por bloque 50/30/20: tag (necesidades), sparkles (discrecionales), piggy (ahorro). Los colores del ícono NO dependen del rol; dependen del bloque, usando `--need`, `--want`, `--save`. Primera integración: gastos fijos (tarjetas) y categorías (listado) | Cada categoría tiene identidad visual propia sin romper el tinte por rol del resto de la app. El color del bloque ya era familiar (gráfico del informe) |
| 57 | Botones "guardar" / "confirmar" | NO se pintan verdes. Se distinguen con más peso visual (ícono check a la izquierda, font-weight 700, padding ligeramente superior, sombra suave al hover) manteniendo el color de marca del rol (`--accent-*`). Si en el futuro se quiere un verde explícito para acciones de "éxito" (marcar pagado, finalizar meta), se hace como `.btn-success` NUEVO, no reutilizando `.btn-primary` verde | Pintar verde rompe el tinte por rol (#24) y es daltónico-hostile. Rojo y verde son los dos colores que más se confunden en deuteranopia. Regla general: color de marca = acción primaria (depende del rol); rojo = destructivo; neutro = secundario. Nada más |

---

## Decisiones del 2026-10-20 — Ícono personalizable y admin sin ingreso (#58..#59)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 58 | Ícono personalizable por categoría | Nueva columna `categorias.icono` (string 20, nullable). Catálogo `Categoria::ICONOS` (16 claves: home, bolt, cart, car, heart, cap, utensils, film, shirt, refresh, plane, piggy, trending, card, sparkles, tag). El usuario elige ícono al crear/editar en `/categorias`; el admin elige el ícono de las categorías del sistema en `/admin/categorias`. Selector visual con radios estilizados y opción "auto" (varita mágica). Si es `null`, se usa fallback automático. Prioridad al resolver: ícono guardado > nombre mapeado (solo categorías del sistema) > bloque 50/30/20 | Cada categoría tiene identidad visual propia sin romper el fallback automático. El admin puede sobreescribir el ícono por defecto de una categoría del sistema (ej. cambiar "Mercado" de `cart` a `piggy`) |
| 59 | Administrador sin "Ingreso mensual" | Refuerza #20. Se aplica en 3 capas: (1) vista — `@if(!$esAdmin)` oculta el campo en `perfil.blade.php`; (2) `UpdatePerfilRequest` — `Rule::prohibitedIf(rol === 'administrador')` + `prepareForValidation` borra el campo del request si llega; (3) `AjustesController@actualizarPerfil` — si es admin, no asigna `ingreso_mensual` (conserva el valor de BD, que debe ser `null`). El subtítulo del header también es condicional (`@if($esAdmin)`) | El admin no tiene finanzas personales ni dashboard. Aunque el campo no se muestre, alguien podría inyectarlo por POST manipulado. Las 3 capas garantizan que el `ingreso_mensual` del admin siempre sea `null` |

---

## Decisiones del 2026-10-21 — Dashboard del administrador (#60)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 60 | Dashboard del administrador en `/admin/panel` | Nueva ruta `admin.panel` (GET `/admin/panel`) como pantalla principal del admin. Primera ruta del Grupo E. `PanelController` nuevo (separado de `EstadisticaController`, que mantiene su responsabilidad y sus 3 tests). Solo COUNT y agregados, sin datos financieros. El header "Ir a mi panel" apunta a `admin.panel` si rol=administrador, a `panel` si estandar\|familiar. Fix del bug del 404 en `/panel` para admin. `/admin/estadisticas` se mantiene como pantalla de agregados detallados. `docs/backend/00-rutas.php` pasa a 65 rutas (11+9+35+0+10) | El admin no tenía dashboard propio. `/admin/estadisticas` era solo COUNT y no daba una visión general. Un dashboard unifica KPIs, accesos rápidos y últimos movimientos (sin datos financieros) |

---

## Decisiones del 2026-10-22 — J9 Seguridad (#61..#67)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 61 | Auditoría de acciones del administrador (**cierra #44**) | Tabla `auditorias` (`user_id` FK nullOnDelete, `accion`, `modelo`, `modelo_id`, `antes` JSON, `despues` JSON, `ip`, `user_agent`, `timestamps`) + índices `(user_id, created_at)`, `(modelo, modelo_id)`, `accion`. Modelo `Auditoria` (casts JSON, solo INSERT, inmutable). Servicio `App\Services\Auditoria::registrar($accion, $modelo, $modeloId, $antes, $despues)`. `AuditoriaController@index` (solo lectura, filtros por acción/admin/fecha, 30 por página). Ruta `admin.auditoria` + ítem en `sidebar-admin`. `UsuarioController` audita `usuario.rol`, `usuario.suspender`, `usuario.reactivar`, `usuario.eliminar`. `CategoriaSistemaController` audita `categoria.crear`, `categoria.actualizar`, `categoria.desactivar`, `categoria.reactivar`. En `destroy` se captura `$idBorrado` y `$rolBorrado` **antes** del delete. `user_id` es nullable + nullOnDelete para que el histórico sobreviva al borrado del admin. `modelo_id` es `unsignedBigInteger` sin FK (histórico intencional) | Trazabilidad completa de acciones del admin, exigible en escenarios de soporte y compliance. Cerrar una acción sin dejar registro es un riesgo operativo: si mañana un admin suspende a 50 usuarios, no hay forma de saber quién fue. La tabla es inmutable por diseño |
| 62 | Notificaciones de seguridad por correo | `App\Notifications\AlertaSeguridad` (implements `ShouldQueue`). 5 eventos: `password.changed`, `email.changed`, `account.suspended`, `login.new_ip`, `login.blocked`. `email.changed` se envía al correo VIEJO (por si el cambio fue un ataque; el dueño se entera). `login.new_ip` se envía solo si NO es el primer login tras registro (`User::esIpNueva` compara contra `last_login_ip` / `last_login_at`). `login.blocked` se envía desde `LoginRequest::notificarBloqueo()` con Cache 1h por usuario para no spamear. Cada `notify` va en `try/catch` con `Log::warning` para no bloquear el flujo principal si el SMTP falla. Columnas nuevas `users.last_login_ip` (45), `users.last_login_at` (timestamp). `User::registrarLogin($ip, $ua)` las asigna con `forceFill`. `LoginController@store` detecta IP nueva y llama `registrarLogin`. `PasswordResetController@actualizar` notifica `password.changed` tras reset exitoso. `AjustesController@confirmarCorreo` captura `$correoViejo` antes del servicio y notifica al correo viejo | El usuario se entera si alguien le cambió contraseña, correo o intenta entrar. `email.changed` al correo VIEJO es la pieza clave: si un atacante con la sesión abierta cambia el correo, el dueño recibe el aviso en su correo anterior y puede revertirlo. `login.new_ip` da visibilidad de accesos sospechosos. `login.blocked` avisa si alguien está intentando fuerza bruta. Ninguna notificación bloquea la request principal |
| 63 | Re-autenticación para acciones destructivas | `EliminarUsuarioRequest` y `EliminarCategoriaSistemaRequest` con `password` `current_password`. `UsuarioController@destroy` y `CategoriaSistemaController@desactivar` usan estos Requests. **El `authorize()` de `EliminarCategoriaSistemaRequest` valida que la categoría sea del sistema (`user_id === null`) ANTES de la validación de `password`**, para que el 403 ocurra primero (bug detectado y corregido en el mismo J9: antes daba 302 con error de password en vez de 403). El `abort_unless` duplicado en el controlador se elimina: la autorización vive solo en el Request | Si el admin deja la sesión abierta, un tercero no puede ejecutar acciones destructivas sin conocer la contraseña. Es el patrón de GitHub, Stripe y AWS: re-auth para acciones que no se pueden deshacer. El bug del 403 vs 302 es un recordatorio: **la autorización siempre va antes de la validación de reglas** para que los códigos HTTP sean semánticamente correctos |
| 64 | Content-Security-Policy (CSP) | Modo permisivo: `script-src 'self' 'unsafe-inline'`, `style-src 'self' 'unsafe-inline' https://fonts.googleapis.com`, `font-src 'self' https://fonts.gstatic.com data:`, `img-src 'self' data: https:`, `connect-src 'self'`, `form-action 'self'`, `frame-ancestors 'none'`, `base-uri 'self'`, `object-src 'none'`. `upgrade-insecure-requests` solo en HTTPS. HSTS en producción + HTTPS. Todo se aplica desde `App\Http\Middleware\SecurityHeaders`, registrado globalmente en `bootstrap/app.php` | Los 2 scripts inline del tema (`base.blade.php`: bootstrap de tema + marcado de `<main id>`) y los `style=""` dinámicos (barras de progreso) rompen la CSP estricta. Migrar a nonces queda para J9.2. Aun en modo permisivo, la CSP bloquea `<script src="evil.com">`, clickjacking (`frame-ancestors`), secuestro de base href y plugins. Es el 80% del valor con el 0% del riesgo de romper el diseño en producción |
| 65 | Regeneración de sesión en login | Verificado con `test_login_regenera_la_sesion`. La sesión anterior no sirve después del login exitoso. `LoginController@store` ya llamaba `$request->session()->regenerate()`; ahora hay test que lo cubre | Mitiga session fixation attacks. Es una defensa estándar de Laravel que a veces se rompe por accidente al customizar el login. Tener el test evita regresiones |
| 66 | Notificación de bloqueo por intentos fallidos | `LoginRequest::notificarBloqueo()` envía `login.blocked` al usuario. Solo si el correo existe en BD (no revelar existencia). Cache 1h por usuario (`login-bloqueado:{user_id}`) para no spamear si el atacante sigue intentando. Se llama desde `asegurarSinBloqueo()` cuando el `RateLimiter` dispara | Avisar al dueño si alguien intenta entrar por fuerza bruta. El cache de 1h evita que un ataque sostenido genere 100 correos. El hecho de que solo se envíe si el correo existe es deliberado: hacia afuera el mensaje sigue siendo genérico ("Demasiados intentos"), pero el dueño real recibe el aviso |
| 67 | 2FA (TOTP) para usuarios y admin — **PENDIENTE J9.2** | Paquetes `pragmarx/google2fa` + `bacon/bacon-qr-code`. Columnas `users.two_factor_secret` (text, nullable) y `users.two_factor_confirmed_at` (timestamp, nullable). `TwoFactorController` (activar, confirmar, desactivar). Rutas `ajustes.seguridad.{index, 2fa.enable, 2fa.confirm, 2fa.disable}`, `login.2fa`, `login.2fa.verify`. Vistas `app/ajustes/seguridad.blade.php`, `app/ajustes/seguridad-2fa-setup.blade.php`, `publico/verificar-2fa.blade.php`. `LoginController`: si `hasTwoFactorEnabled`, desloguear y pedir código TOTP. Desactivar requiere `current_password`. **Estado**: diseño completo documentado, no implementado por bloqueo de Composer en el entorno local (ver §2 PENDIENTE J9.2 en el documento de arquitectura) | Capa extra de seguridad, especialmente para el admin (que puede suspender/eliminar usuarios). El proyecto actual ya es seguro con contraseña + rate limit + sesión 15 min + notificación de login nuevo, que cubre el 80% del riesgo. 2FA lo lleva al 99%, pero 4 horas de J9 en un entorno con Composer bloqueado no es viable. Se hace en J9.2 cuando el entorno esté sano |

---

## Otras decisiones cerradas

| # | Decisión | Fecha | Por qué |
|---|----------|-------|---------|
| 4 | Crédito: solo simulaciones (no créditos reales) | 2026-10-05 | Alcance de la primera versión |
| 5 | Alertas de cuotas de crédito | 2026-10-16 | **Ya no aplica**: se eliminó `cuota_credito` con la decisión #4 |
| 11 | El rol "soporte" no se construye | — | Alcance (roles: estándar, familiar, administrador) |
| 16 | Repetición de alertas | 2026-10-16 | Una a los 3 días y una de vencido (decisión #33 de J6) |
| 19 | **Modo demo: DESCARTADO** | — | Ya no se va a usar; se eliminó de código y documentos |
| 20 | Finanzas del administrador | 2026-10-16 | **No** en la primera versión (decisión #42 de J7) |
| 22 | Moneda: pesos colombianos (COP), enteros | — | — |
| 23 | Columna `categorias.activa` | 2026-10-16 | Boolean, default true. Índice (user_id, activa). Las categorías del sistema se desactivan, no se borran |

---

## Pendientes (solo lo que sigue realmente abierto)

### J9.2 (Juan) — bloqueado por entorno local, no por diseño

- **2FA (TOTP)**: paquetes `pragmarx/google2fa` + `bacon/bacon-qr-code` no instalados. El
  `composer require` falla en el entorno local (Composer no accesible desde Git Bash). Una
  vez resuelto, se aplica el diseño completo de la decisión #67.
- **`composer audit`**: no se pudo correr. Comando correcto en el entorno local:
  `php /c/Users/juane/.config/herd-lite/bin/composer.phar audit`. Pégame el output y se
  cierra el punto.
- **Grep de logs**: `grep -rn "Log::" app/` para verificar que no haya fugas de tokens ni
  contraseñas en logs. No se pudo correr.
- **Instalar `backup-cuentas-claras.sh`** en el servidor. El script está documentado en
  `docs/juan/J9_Checklist_Previo.txt` (creación, restauración mensual, verificación gzip,
  incidencias) pero no está instalado.

### N5 (Nikol) — en curso desde 2026-10-19

- Landing final y páginas legales. Reemplaza los `Route::view` de `público.php` por vistas
  completas con contenido de marketing y textos legales. Pendiente: validar cifras y
  promesas con el equipo (decisión #21).

### J9 (Juan) — despliegue

- Configurar SMTP real para producción (hoy `MAIL_MAILER=log` en desarrollo).
- Seguir `docs/juan/J9_Checklist_Previo.txt`.
- Ejecutar `docs/juan/deploy.sh` (con `composer audit` obligatorio antes de `composer install`).
- Restaurar un backup en staging (verificación mensual del procedimiento).

### J9 (Juan + Nikol) — contenido

- #21: cifras de la landing y textos legales. Validar las promesas (AES-256, "cero reportes",
  cifras de usuarios) o corregir el texto.

### Docs

- `docs/backend/00-rutas.php`: actualizar a **68 rutas reales** (11+9+37+0+11) + fallback = 69
  líneas mostradas por `php artisan route:list --except-vendor`. Ya incluye `admin.panel` y
  `admin.auditoria`, y las 2 rutas que estaban sin doc (`gastos.edit`,
  `notificaciones.leerTodas`).
- Crear `docs/juan/J9_Seguridad_2026-10-22.txt` con el resumen de los cambios de J9
  (auditoría, notificaciones, CSP, re-auth, throttles) y lo que queda pendiente en J9.2.

### Cerrados en esta sesión (para no confundir con pendientes)

- **#38** (whereYear/whereMonth): cerrado con `Gasto::delPeriodo`. Ver #61.
- **#44** (auditoría): implementada. Ver #61.
- **TODO(J8)** en `DashboardController`: cerrado con `delPeriodo`.
- 3 bugs de tests corregidos:
  - `AuthTest.php` tenía `class UsuariosTest` (35 tests no corrían).
  - `ajustes.php` tenía `/ajustes/ajustes/confirmar-correo/reenviar` (URL duplicada).
  - `CategoriasSistemaTest` daba 302 en vez de 403 (el `abort_unless` estaba en el
    controlador, la validación de password lo interceptaba antes). Movido al
    `authorize()` del FormRequest.

---

## Notas de cierre

- Todas las decisiones con número (`#N`) están referenciadas en
  `Arquitectura_Cuentas_Claras.txt` sección 14.
- Las decisiones de la sesión del 2026-10-17 (V4, verificación de correo, rediseño de reset)
  cerraron el alcance funcional del producto para la primera versión.
- Las decisiones del 2026-10-19 y 2026-10-20 (restricción de Ajustes, 404, sesión,
  iconografía, botones, ícono personalizable, admin sin ingreso) son pulido y consistencia.
- La sesión del 2026-10-21 agregó el dashboard del admin (`/admin/panel`) y cerró el bug del
  header.
- La sesión del 2026-10-22 (J9) agregó seguridad de producción: auditoría, notificaciones de
  seguridad, CSP, re-auth, throttles, notificación de bloqueo, procedimiento de backup y
  `deploy.sh`. También cerró `#38` y `#44`, corrigió 3 bugs de tests, y movió `TODO(J8)` a
  cerrado con `delPeriodo`.
- Antes de reabrir una decisión, revisar la sección "Pendientes" y el documento de
  arquitectura; muchas veces lo que parece una decisión nueva ya está resuelto con la
  misma respuesta.
- **Regla de oro de J9**: cualquier acción nueva del admin que modifique estado DEBE pasar
  por `App\Services\Auditoria::registrar()`. Cualquier notificación de seguridad nueva DEBE
  usar `App\Notifications\AlertaSeguridad`. Cualquier acción destructiva nueva DEBE usar un
  FormRequest con `current_password`.