<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/notificaciones.php — DUEÑO: Nikol (N4).
 * Nombres: notificaciones.index, notificaciones.leer
 * MAQUETA: cambia por NotificacionController.
 */

Route::view('/notificaciones', 'app.notificaciones.index')->name('notificaciones.index');
Route::post('/notificaciones/leer', fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada'))->name('notificaciones.leer');
