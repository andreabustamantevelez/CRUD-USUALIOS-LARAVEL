<?php

// ESTE ARCHIVO ES SOLO DOCUMENTACIÓN. No lo copies a app/, routes/ ni database/ (en migrations/ haría fallar "php artisan migrate").

/*
 * ==============================================================================
 * ARCHIVO : docs/backend/01-controladores.php  (SOLO PLAN: el archivo real se crea en app/Http/Controllers/...)
 * QUÉ ES  : Lista de controladores que hay que crear (uno por módulo) y qué hace cada método.
 *           Vacío a propósito.
 * DATOS QUE LLEGAN DEL CONTROLADOR:
 *   - Auth\LoginController: create, store, destroy · Auth\RegistroController: create, store ·
 *     Auth\PasswordController (recuperar y restablecer)
 *   - DashboardController@index · GastoController (index, store, update, destroy) ·
 *     CategoriaController · GastoFijoController (+ pagar) · NotificacionController (index, leer)
 *   - SimulacionCreditoController (index, store, destroy, historial) · MetaController (index,
 *     store, show, finalizar, abandonar) · AporteMetaController@store · InvitacionMetaController
 *     (index, aceptar, rechazar)
 *   - InformeController (actual, historial) · AjustesController (perfil, actualizarPerfil,
 *     tipoCuenta, cambiarTipo, eliminar, destruir)
 *   - Admin\UsuarioController · Admin\CategoriaSistemaController · Admin\EstadisticaController
 * QUÉ HAY QUE HACER (en orden):
 *   1. Cada controlador: validar con un FormRequest, delegar la lógica a un servicio y devolver la
 *      vista o una redirección con mensaje.
 *   2. Nunca consultar sin filtrar por el usuario autenticado.
 * CÓMO HACERLO / NOTAS:
 *   - Ejemplo textual: GastoController@store valida, crea el gasto del usuario y redirige a
 *     gastos.index con el mensaje "Gasto guardado".
 * ==============================================================================
 */
