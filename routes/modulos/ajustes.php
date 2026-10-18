<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajustes')->name('ajustes.')->group(function () {
    // Cualquier rol con sesión (incluye administrador)
    Route::get('/perfil', [AjustesController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [AjustesController::class, 'actualizarPerfil'])
        ->name('perfil.update');

    Route::get('/confirmar-correo/{token}', [AjustesController::class, 'confirmarCorreo'])
        ->name('confirmar-correo');

    Route::post('/confirmar-correo/reenviar', [AjustesController::class, 'reenviarConfirmacionCorreo'])
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