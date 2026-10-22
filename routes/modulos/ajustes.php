<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/ajustes.php — DUEÑO: Valeria (V4).
 *
 * BUGFIX 2026-10-21:
 *   La ruta de reenviar confirmación tenía el prefijo 'ajustes' dos veces
 *   (el del grupo + uno manual), generando /ajustes/ajustes/confirmar-correo/reenviar.
 *   Se quitó el 'ajustes/' manual del path y el 'ajustes.' manual del nombre.
 *   URL final: /ajustes/confirmar-correo/reenviar
 *   Nombre final: ajustes.confirmar-correo.reenviar
 */

Route::prefix('ajustes')->name('ajustes.')->group(function () {
    // Cualquier rol con sesión (incluye administrador)
    Route::get('/perfil', [AjustesController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [AjustesController::class, 'actualizarPerfil'])
        ->name('perfil.update');

    Route::get('/confirmar-correo/{token}', [AjustesController::class, 'confirmarCorreo'])
        ->name('confirmar-correo');

    Route::post('/confirmar-correo/reenviar', [AjustesController::class, 'reenviarConfirmacionCorreo'])
        ->middleware('throttle:1,1')
        ->name('confirmar-correo.reenviar');

    // Solo estandar y familiar: el administrador no tiene finanzas personales
    // (decisión #20 y #43). Su cuenta se gestiona desde /admin/usuarios.
    Route::middleware('rol:estandar,familiar')->group(function () {
        Route::get('/tipo-cuenta', [AjustesController::class, 'tipoCuenta'])
            ->name('tipo-cuenta');

        Route::put('/tipo-cuenta', [AjustesController::class, 'cambiarTipo'])
            ->name('tipo-cuenta.update');

        Route::get('/eliminar-cuenta', [AjustesController::class, 'eliminarCuenta'])
            ->name('eliminar-cuenta');

        Route::delete('/eliminar-cuenta', [AjustesController::class, 'destruirCuenta'])
            ->name('eliminar-cuenta.destroy');
    });
});