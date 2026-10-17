<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

Route::prefix('ajustes')->name('ajustes.')->group(function () {
    Route::get('/perfil', [AjustesController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [AjustesController::class, 'actualizarPerfil'])
        ->name('perfil.update');

    Route::get('/tipo-cuenta', [AjustesController::class, 'tipoCuenta'])
        ->name('tipo-cuenta');

    Route::put('/tipo-cuenta', [AjustesController::class, 'cambiarTipo'])
        ->name('tipo-cuenta.update');

    Route::get('/eliminar-cuenta', [AjustesController::class, 'eliminarCuenta'])
        ->name('eliminar-cuenta');

    Route::delete('/eliminar-cuenta', [AjustesController::class, 'destruirCuenta'])
        ->name('eliminar-cuenta.destroy');

    Route::get('/confirmar-correo/{token}', [AjustesController::class, 'confirmarCorreo'])
        ->name('confirmar-correo');

    Route::post('/confirmar-correo/reenviar', [AjustesController::class, 'reenviarConfirmacionCorreo'])
        ->name('confirmar-correo.reenviar');
});