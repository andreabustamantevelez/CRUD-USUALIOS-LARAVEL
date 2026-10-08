<?php

use Illuminate\Support\Facades\Route;

/*
 * ==============================================================================
 * routes/web.php  —  ÍNDICE DE RUTAS (solo Juan lo modifica)
 * Cada módulo vive en routes/modulos/<modulo>.php y lo escribe su dueño.
 * Los NOMBRES de ruta salen de docs/backend/00-rutas.php y NO se cambian.
 *
 * J3 HECHO (2026-10-06): login real. Todo el bloque privado exige 'auth' (sin sesión -> /iniciar-sesion).
 * J4 HECHO (2026-10-06): 'cuenta.activa' expulsa cuentas suspendidas; 'rol:...' limita cada grupo:
 *     B  todos los roles con sesión ......... ajustes
 *     C  estandar y familiar ................ panel, gastos, categorías, gastos fijos, notificaciones,
 *                                             créditos, informes y METAS (individuales para estandar,
 *                                             compartidas para familiar)
 *     E  solo administrador (prefijo /admin)  usuarios, categorías del sistema, estadísticas
 * J5 ACTUALIZADO (2026-10-08): metas pasaron al Grupo C. El estándar puede tener metas individuales
 *     y recibir invitaciones (con aviso de cambiar a familiar para aceptar). El familiar sigue
 *     pudiendo crear metas compartidas.
 * ==============================================================================
 */

// GRUPO A - PÚBLICAS
require __DIR__.'/modulos/publico.php';   // Nikol (N5)
require __DIR__.'/modulos/auth.php';      // Juan  (J3)

// GRUPOS B a E - PRIVADAS
Route::middleware(['auth', 'cuenta.activa'])->group(function () {

    // GRUPO B - todos los roles con sesión
    require __DIR__.'/modulos/ajustes.php';          // Valeria (V4)

    // GRUPO C - estándar y familiar
    Route::middleware('rol:estandar,familiar')->group(function () {
        require __DIR__.'/modulos/panel.php';            // Juan    (J8)
        require __DIR__.'/modulos/gastos.php';           // Nikol   (N1, N2)
        require __DIR__.'/modulos/gastos-fijos.php';     // Nikol   (N3)
        require __DIR__.'/modulos/notificaciones.php';   // Nikol   (N4)
        require __DIR__.'/modulos/creditos.php';         // Valeria (V1, V2)
        require __DIR__.'/modulos/informes.php';         // Valeria (V3)
        require __DIR__.'/modulos/metas.php';            // Juan    (J5) - individuales para todos
    });

    // GRUPO D - solo familiar
    // Route::middleware('rol:familiar')->group(function () {
    //     require __DIR__.'/modulos/metas.php';            // Juan (J5)
    // });


    // GRUPO E - solo administrador
    Route::middleware('rol:administrador')->prefix('admin')->name('admin.')->group(function () {
        require __DIR__.'/modulos/admin.php';            // Juan (J7)
    });
});