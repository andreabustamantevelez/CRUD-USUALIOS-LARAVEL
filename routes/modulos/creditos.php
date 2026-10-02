<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/creditos.php — DUEÑA: Valeria (V1 simulador, V2 historial).
 * Nombres: creditos.simulador, creditos.guardar, creditos.historial, creditos.eliminar
 * MAQUETA: cambia por SimulacionCreditoController.
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::view('/creditos/simulador', 'app.creditos.simulador')->name('creditos.simulador');
Route::post('/creditos', $pendiente)->name('creditos.guardar');
Route::view('/creditos/historial', 'app.creditos.historial')->name('creditos.historial');
Route::delete('/creditos/{simulacion}', $pendiente)->name('creditos.eliminar');
