<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/gastos.php — DUEÑO: Nikol (N1 gastos, N2 categorías).
 * Nombres: gastos.index/create/store/update/destroy · categorias.index/store/update/destroy
 * MAQUETA: cambia cada closure por GastoController / CategoriaController.
 * Regla: segmentos fijos (/gastos/nuevo) ANTES de los de parámetro (/gastos/{gasto}).
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

// N1 - Gastos
Route::view('/gastos', 'app.gastos.index')->name('gastos.index');
Route::view('/gastos/nuevo', 'app.gastos.index')->name('gastos.create');
Route::post('/gastos', $pendiente)->name('gastos.store');
Route::put('/gastos/{gasto}', $pendiente)->name('gastos.update');
Route::delete('/gastos/{gasto}', $pendiente)->name('gastos.destroy');

// N2 - Categorías
Route::view('/categorias', 'app.categorias.index')->name('categorias.index');
Route::post('/categorias', $pendiente)->name('categorias.store');
Route::put('/categorias/{categoria}', $pendiente)->name('categorias.update');
Route::delete('/categorias/{categoria}', $pendiente)->name('categorias.destroy');
