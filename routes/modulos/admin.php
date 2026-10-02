<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/admin.php — DUEÑO: Juan (J7). Ya viene con prefijo /admin y nombres admin.*
 * (definido en routes/web.php), por eso aquí los nombres NO repiten "admin.".
 * Resultado: admin.usuarios, admin.usuarios.rol/estado/destroy,
 *            admin.categorias(.store/.update/.desactivar), admin.estadisticas
 * MAQUETA: cambia por Admin\UsuarioController, Admin\CategoriaSistemaController, Admin\EstadisticaController.
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::view('/usuarios', 'admin.usuarios')->name('usuarios');
Route::put('/usuarios/{usuario}/rol', $pendiente)->name('usuarios.rol');
Route::put('/usuarios/{usuario}/estado', $pendiente)->name('usuarios.estado');
Route::delete('/usuarios/{usuario}', $pendiente)->name('usuarios.destroy');

Route::view('/categorias', 'admin.categorias')->name('categorias');
Route::post('/categorias', $pendiente)->name('categorias.store');
Route::put('/categorias/{categoria}', $pendiente)->name('categorias.update');
Route::patch('/categorias/{categoria}/desactivar', $pendiente)->name('categorias.desactivar');

Route::view('/estadisticas', 'admin.estadisticas')->name('estadisticas');
