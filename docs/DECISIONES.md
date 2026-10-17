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
| Quién entra a qué | B (ajustes): todos · C (gastos, categorías, gastos fijos, notificaciones, créditos, informes, panel): estandar y familiar · D (metas): familiar · E (/admin): administrador | Matriz del documento de arquitectura, probada en PermisosTest |
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

---

## Decisiones del 2026-10-16 — J7 Administración (#42..#44)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 42 | Tinte del administrador | Core Blue (#2563EB claro / #93b4ff oscuro). Se reutiliza el azul de "Necesidades" del gráfico 50/30/20 | No introducir un color nuevo al sistema |
| 43 | ¿El admin puede eliminarse a sí mismo? | No desde el panel de administración. Sí desde Ajustes si hay otro admin activo | El panel es para gestionar a otros; Ajustes es para la propia cuenta |
| 44 | Registro de acciones del admin (auditoría) | **No implementado** en esta versión ([PROPUESTA]) | Requiere tabla nueva y decisión de alcance; se hace en una PR aparte si se decide |

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

- **N1/N2 (Nikol)**: filtrar por `Categoria::activas()` al registrar/listar categorías. Hoy si el admin desactiva una, se filtra solo en el panel admin; el módulo del usuario todavía no.
- **N5 / J9 (Nikol + Juan)**: #21 cifras de la landing y textos legales. Validar las promesas (AES-256, "cero reportes", cifras de usuarios) o corregir el texto.
- **V1**: #3 semáforo (hasta 20 % saludable, 20–30 % moderado, más de 30 % riesgo) · #13 tasa mensual vencida, sistema francés, redondeo final a pesos enteros. Ya implementado; queda como referencia.
- **V2 (Valeria)**: historial de simulaciones sigue como maqueta.
- **J5**: confirmar los textos exactos de los mensajes de invitación contra la especificación (están en `ServicioMetas::MSG_*`).
- **J8**: `TODO(J8)` en `DashboardController` — reemplazar `whereYear`/`whereMonth` por el scope `delPeriodo` de C1 cuando Nikol lo agregue.
- **J9**: `docs/backend/00-rutas.php` debe actualizarse a 64 rutas (agregar las 2 nuevas de `ajustes.confirmar-correo`). Además, configurar SMTP real (hoy `MAIL_MAILER=log` en desarrollo).
- **J9**: revisión de seguridad integral y despliegue. Ver `docs/juan/J9_Checklist_Previo.txt`.
- **Mejora opcional**: si se decide implementar el registro de acciones del administrador (auditoría), se hace en una PR aparte (#44).

---

## Notas de cierre

- Todas las decisiones con número (`#N`) están referenciadas en `Arquitectura_Cuentas_Claras.txt` sección 14.
- Las decisiones de la sesión del 2026-10-17 (V4, verificación de correo, rediseño de reset) cierran el alcance funcional del producto para la primera versión. Lo que queda es pulir (N1, N2, V2, V5), la landing (N5) y el despliegue (J9).
- Antes de reabrir una decisión, revisar la sección "Pendientes" y el documento de arquitectura; muchas veces lo que parece una decisión nueva ya está resuelto con la misma respuesta.