<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/05-policies.php  (los archivos reales están en app/Policies/)
 * ESTADO  : HECHO (J4, 2026-10-06, Juan), excepto MetaPolicy (la hace J5).
 * QUÉ ES  : Policies: quién puede ver, editar y borrar cada registro.
 *
 * POLICIES (Laravel las encuentra solas por el nombre: App\Models\Gasto -> App\Policies\GastoPolicy)
 *   PoliticaDeDueno ........... regla base: solo el dueño (user_id) ve, edita y borra. El
 *                               administrador siempre queda en false (before).
 *   GastoPolicy, GastoFijoPolicy, NotificacionPolicy ... solo el dueño. GastoFijoPolicy agrega 'pagar'.
 *   SimulacionCreditoPolicy ... dueño ve y borra; NO se edita (update = false).
 *   InformePolicy ............. dueño ve; NO se edita ni se borra a mano (update y delete = false).
 *   CategoriaPolicy ........... propias: ver, usar, editar, borrar · del sistema: ver y usar todos,
 *                               nadie las edita desde la zona del usuario · ajenas: nada ·
 *                               administrador: ve y edita solo las del sistema; borrar = false (#23).
 *   MetaPolicy ................ PENDIENTE (J5): solo participantes; de los aportes solo ver los propios.
 *
 * SCOPE PARA LISTADOS (trait App\Models\Concerns\PerteneceAUsuario):
 *   Gasto::delUsuario(auth()->id())   // existe en Gasto, GastoFijo, PagoGastoFijo, SimulacionCredito,
 *                                     // Informe y Notificacion. NUNCA Gasto::all().
 *
 * USO EN UN CONTROLADOR:
 *   $this->authorize('viewAny', Gasto::class);        // antes de listar
 *   $gastos = Gasto::delUsuario(auth()->id())->latest('fecha')->paginate(15);
 *   $this->authorize('update', $gasto);               // 403 si el gasto es de otra persona
 *   $this->authorize('usar', $categoria);             // al guardar un gasto: la categoría debe ser propia o del sistema
 *   $this->authorize('pagar', $gastoFijo);            // ruta gastos-fijos.pagar
 *
 * REGLAS:
 *   - 403 (no 404) cuando el recurso existe pero es ajeno.
 *   - Nunca tomes el id del usuario de un formulario: sale de auth().
 *   - Un @can en la vista NO protege; la protección va en el controlador.
 *   - Olvidar el scope en totales y contadores es una fuga silenciosa: úsalo también ahí.
 *
 * CÓMO SE COMPRUEBA: tests/Feature/AislamientoTest.php (dueño vs ajeno por modelo, administrador,
 *   categorías, listados 3 vs 2 y una ruta de prueba con binding + authorize). Modelo nuevo con
 *   dueño: crea su Policy y súmalo a esa prueba.
 * ==============================================================================
 */
