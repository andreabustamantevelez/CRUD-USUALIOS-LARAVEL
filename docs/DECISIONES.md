# DECISIONES.md — Cuentas Claras

Registro de decisiones del equipo. Cada una lleva fecha, qué se decidió y por qué.
**Lo decidido no se reabre sin un motivo nuevo.** Para cambiar una decisión: agregar una entrada
nueva que diga cuál reemplaza y por qué (no borrar la anterior).

Equipo: Juan (J), Nikol (N), Valeria (V).

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
| Página 403 | Por ahora la estándar de Laravel | Una pantalla con el diseño de la app queda como mejora |

## Decisiones tomadas al hacer J5 — Metas compartidas (2026-10-07, Juan)

| # | Tema | Qué se decidió | Por qué |
|---|------|----------------|---------|
| 7 | Fecha límite de la meta | Opcional. Si se escribe, debe ser hoy o futura. La meta **no** se cierra sola al vencer | Evita metas que cambian de estado sin que nadie haga nada |
| 8 | Gastos ligados a meta | **No** en la primera versión. Los aportes son lo único que mueve el avance | Alcance; ligar gastos exige otro flujo y más pruebas |
| 18 | Si alguien abandona | La meta pasa a **cancelada para las dos personas**; los aportes se conservan como historial; se notifica a la otra. Quien abandonó ya no la ve; la otra la ve en solo lectura. Si una cuenta se elimina, sus filas se borran en cascada (migración J2) | Con solo dos participantes, una meta compartida con una persona deja de ser compartida |
| — | Quién invita | Solo quien creó la meta, con la meta activa | `metas.creador_id` (J2) |
| — | Máximo 2 participantes | Cuentan **aceptados + pendientes** (la invitación reserva el cupo); al rechazar se libera | Evita invitar a tres personas a la vez |
| — | Reinvitar | Quien rechazó o abandonó puede ser invitado otra vez: se reactiva su misma fila (`unique(meta_id, user_id)`) | No se pueden duplicar filas |
| — | Finalizar | Solo si el total conjunto alcanzó el objetivo; cualquier participante puede hacerlo. Pasa a `completada` | Jira J5-B: "Finalizar (al llegar al objetivo)" |
| — | Privacidad de aportes | La vista y el controlador solo consultan los aportes del usuario actual; el total sale de una suma en SQL; `AporteMetaPolicy` = solo el dueño; nadie edita ni borra aportes | RQNF-03 |
| — | Respuesta a ajenos | 403 en todas las rutas de una meta ajena y al responder una invitación ajena | Igual que J4 |
| — | Invitar por | Correo (`correo`). Un usuario suspendido o administrador se trata como "no existe" | El correo es lo único que una persona conoce de otra |
| — | Notificaciones de metas | Versión mínima dentro de `ServicioMetas::notificar()` (tipos `invitacion_meta`, `respuesta_meta`, `meta_finalizada`, `meta_abandonada`). Cuando exista el servicio de J6 solo se cambia ese método | J6 aún no está |
| C6 | `tieneMetasActivas($usuario)` | En `ServicioMetas`; `Meta::aportesDelUsuario($id, $desde, $hasta)` para dashboard e informes | Contrato con Valeria (cambio a estándar) y con el panel |

## Otras decisiones cerradas

| # | Decisión | Fecha | Por qué |
|---|----------|-------|---------|
| 4 | Crédito: solo simulaciones (no créditos reales) | 2026-10-05 | Alcance de la primera versión |
| 11 | El rol "soporte" no se construye | — | Alcance (roles: estándar, familiar, administrador) |
| 19 | **Modo demo: DESCARTADO** | — | Ya no se va a usar; se eliminó de código y documentos |
| 22 | Moneda: pesos colombianos (COP), enteros | — | — |

## Pendientes (se cierran antes de su módulo)

- J6: #5 alertas de cuotas de crédito (ya no aplica: no hay `cuota_credito`) · #16 repetición de alertas.
- N2: #17 eliminar categoría con gastos (propuesta: pedir a qué categoría moverlos).
- N3: #15 estado "vencido".
- V1: #3 semáforo (hasta 20 % saludable, 20–30 % moderado, más de 30 % riesgo) · #13 tasa mensual vencida, sistema francés, redondeo final a pesos enteros.
- J5: confirmar los textos exactos de los mensajes de invitación contra la especificación (están en `ServicioMetas::MSG_*`).
- Mejora opcional: página 403 con el diseño de la app.
- J7: #20 finanzas del administrador · #23 columna `categorias.activa` (decidir antes de J7).
- N5/J9: #21 cifras de la landing y textos legales.
