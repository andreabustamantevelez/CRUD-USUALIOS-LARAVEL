<?php

use App\Http\Controllers\Admin\CategoriaSistemaController;
use App\Http\Controllers\Admin\EstadisticaController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/admin.php — DUEÑO: Juan (J7).
 * Prefijo /admin y nombres admin.* ya puestos en web.php.
 * Middleware: auth + cuenta.activa + rol:administrador.
 */

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
Route::put('/usuarios/{usuario}/rol', [UsuarioController::class, 'cambiarRol'])->name('usuarios.rol');
Route::put('/usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');
Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

Route::get('/categorias', [CategoriaSistemaController::class, 'index'])->name('categorias');
Route::post('/categorias', [CategoriaSistemaController::class, 'store'])->name('categorias.store');
Route::put('/categorias/{categoria}', [CategoriaSistemaController::class, 'update'])->name('categorias.update');
Route::patch('/categorias/{categoria}/desactivar', [CategoriaSistemaController::class, 'desactivar'])->name('categorias.desactivar');

Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas');