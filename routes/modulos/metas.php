<?php

use App\Http\Controllers\AporteMetaController;
use App\Http\Controllers\InvitacionMetaController;
use App\Http\Controllers\MetaController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/metas.php — DUEÑO: Juan (J5). Solo familiar (grupo D en routes/web.php).
 * J5 HECHO: ya no es maqueta. Los NOMBRES de ruta no cambian.
 * Regla: /metas/invitaciones ANTES de /metas/{meta}. Los {meta}/{participacion} solo aceptan números.
 */

Route::get('/metas', [MetaController::class, 'index'])->name('metas.index');
Route::post('/metas', [MetaController::class, 'store'])->name('metas.store');

Route::get('/metas/invitaciones', [InvitacionMetaController::class, 'index'])->name('metas.invitaciones');
Route::post('/metas/invitaciones/{participacion}/aceptar', [InvitacionMetaController::class, 'aceptar'])
    ->whereNumber('participacion')->name('metas.aceptar');
Route::post('/metas/invitaciones/{participacion}/rechazar', [InvitacionMetaController::class, 'rechazar'])
    ->whereNumber('participacion')->name('metas.rechazar');

Route::get('/metas/{meta}', [MetaController::class, 'show'])->whereNumber('meta')->name('metas.show');
Route::post('/metas/{meta}/invitar', [MetaController::class, 'invitar'])->whereNumber('meta')->name('metas.invitar');
Route::post('/metas/{meta}/aportes', [AporteMetaController::class, 'store'])->whereNumber('meta')->name('metas.aportes');
Route::post('/metas/{meta}/finalizar', [MetaController::class, 'finalizar'])->whereNumber('meta')->name('metas.finalizar');
Route::post('/metas/{meta}/abandonar', [MetaController::class, 'abandonar'])->whereNumber('meta')->name('metas.abandonar');
