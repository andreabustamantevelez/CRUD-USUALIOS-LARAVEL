<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\CategoriaController;

/*
 * routes/modulos/gastos.php — DUEÑO: Nikol (N1 gastos, N2 categorías).
 * Nombres: gastos.index/create/store/update/destroy · categorias.index/store/update/destroy
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

// N1 - Gastos (Conectados al Controlador)
Route::get('/gastos', [GastoController::class, 'index'])->name('gastos.index');

Route::get('/gastos/nuevo', [GastoController::class, 'create'])->name('gastos.create');

Route::get('/gastos/{gasto}/editar', [GastoController::class, 'edit'])->name('gastos.edit');
Route::post('/gastos', [GastoController::class, 'store'])->name('gastos.store');
Route::put('/gastos/{gasto}', [GastoController::class, 'update'])->name('gastos.update');
Route::delete('/gastos/{gasto}', [GastoController::class, 'destroy'])->name('gastos.destroy');

// N2 - Categorías
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');