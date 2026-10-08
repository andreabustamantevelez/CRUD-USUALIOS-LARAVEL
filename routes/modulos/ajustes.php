<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/ajustes.php — DUEÑA: Valeria (V4).
 * Nombres: ajustes.perfil(.update), ajustes.tipo-cuenta(.update), ajustes.eliminar-cuenta(.destroy)
 * J5 ACTUALIZADO (2026-10-08): tipo-cuenta ya tiene controlador real (cambio estandar <-> familiar).
 * Perfil y eliminar-cuenta siguen maqueta hasta que V4 los haga.
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::prefix('ajustes')->name('ajustes.')->group(function () use ($pendiente) {
    Route::get('/perfil', [AjustesController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', $pendiente)->name('perfil.update');

    Route::get('/tipo-cuenta', [AjustesController::class, 'tipoCuenta'])->name('tipo-cuenta');
    Route::put('/tipo-cuenta', [AjustesController::class, 'cambiarTipo'])->name('tipo-cuenta.update');

    Route::get('/eliminar-cuenta', [AjustesController::class, 'eliminarCuenta'])->name('eliminar-cuenta');
    Route::delete('/eliminar-cuenta', $pendiente)->name('eliminar-cuenta.destroy');
});