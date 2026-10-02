<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/ajustes.php — DUEÑA: Valeria (V4).
 * Nombres: ajustes.perfil(.update), ajustes.tipo-cuenta(.update), ajustes.eliminar-cuenta(.destroy)
 * MAQUETA: cambia cada closure por [AjustesController::class, 'metodo'].
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::prefix('ajustes')->name('ajustes.')->group(function () use ($pendiente) {
    Route::view('/perfil', 'app.ajustes.perfil')->name('perfil');
    Route::put('/perfil', $pendiente)->name('perfil.update');

    Route::view('/tipo-cuenta', 'app.ajustes.tipo-cuenta')->name('tipo-cuenta');
    Route::put('/tipo-cuenta', $pendiente)->name('tipo-cuenta.update');

    Route::view('/eliminar-cuenta', 'app.ajustes.eliminar-cuenta')->name('eliminar-cuenta');
    Route::delete('/eliminar-cuenta', $pendiente)->name('eliminar-cuenta.destroy');
});
