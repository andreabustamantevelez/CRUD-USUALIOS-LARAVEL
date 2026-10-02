<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/gastos-fijos.php — DUEÑO: Nikol (N3).
 * Nombres: gastos-fijos.index/store/update/destroy/pagar
 * MAQUETA: cambia cada closure por GastoFijoController.
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::view('/gastos-fijos', 'app.gastos-fijos.index')->name('gastos-fijos.index');
Route::post('/gastos-fijos', $pendiente)->name('gastos-fijos.store');
Route::put('/gastos-fijos/{gastoFijo}', $pendiente)->name('gastos-fijos.update');
Route::delete('/gastos-fijos/{gastoFijo}', $pendiente)->name('gastos-fijos.destroy');
Route::post('/gastos-fijos/{gastoFijo}/pagar', $pendiente)->name('gastos-fijos.pagar');
