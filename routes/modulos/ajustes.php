<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

$pendiente = fn () => back()->with(
    'exito',
    'Esta accion aun no esta implementada.'
);

Route::prefix('ajustes')->name('ajustes.')->group(function () use ($pendiente) {
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

    Route::delete('/eliminar-cuenta', $pendiente)
        ->name('eliminar-cuenta.destroy');
});