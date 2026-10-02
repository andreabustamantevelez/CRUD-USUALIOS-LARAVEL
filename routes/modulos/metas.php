<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/metas.php — DUEÑO: Juan (J5). Solo familiar.
 * Nombres: metas.index/store/invitaciones/aceptar/rechazar/show/invitar/aportes/finalizar/abandonar
 * MAQUETA: cambia por MetaController / AporteMetaController / InvitacionMetaController.
 * Regla: /metas/invitaciones ANTES de /metas/{meta}.
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::view('/metas', 'app.metas.index')->name('metas.index');
Route::post('/metas', $pendiente)->name('metas.store');

Route::view('/metas/invitaciones', 'app.metas.invitaciones')->name('metas.invitaciones');
Route::post('/metas/invitaciones/{participacion}/aceptar', $pendiente)->name('metas.aceptar');
Route::post('/metas/invitaciones/{participacion}/rechazar', $pendiente)->name('metas.rechazar');

Route::view('/metas/{meta}', 'app.metas.show')->name('metas.show');
Route::post('/metas/{meta}/invitar', $pendiente)->name('metas.invitar');
Route::post('/metas/{meta}/aportes', $pendiente)->name('metas.aportes');
Route::post('/metas/{meta}/finalizar', $pendiente)->name('metas.finalizar');
Route::post('/metas/{meta}/abandonar', $pendiente)->name('metas.abandonar');
