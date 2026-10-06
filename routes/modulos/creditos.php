<?php

use App\Http\Controllers\SimulacionCreditoController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/creditos.php — DUEÑA: Valeria (V1 simulador, V2 historial).
 * Nombres: creditos.simulador, creditos.guardar, creditos.historial, creditos.eliminar
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::get(
    '/creditos/simulador',
    [SimulacionCreditoController::class, 'index']
)->name('creditos.simulador');

Route::post(
    '/creditos',
    [SimulacionCreditoController::class, 'store']
)->name('creditos.guardar');

Route::view('/creditos/historial', 'app.creditos.historial')
    ->name('creditos.historial');

Route::delete('/creditos/{simulacion}', $pendiente)
    ->name('creditos.eliminar');