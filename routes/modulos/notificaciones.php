<?php

use App\Http\Controllers\NotificacionController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/notificaciones.php — DUEÑO: Nikol (N4).
 * Centro de notificaciones.
 */

Route::get('/notificaciones', [
    NotificacionController::class,
    'index',
])->name('notificaciones.index');

Route::post('/notificaciones/leer', [
    NotificacionController::class,
    'leer',
])->name('notificaciones.leer');

Route::post('/notificaciones/leer-todas', [
    NotificacionController::class,
    'leerTodas',
])->name('notificaciones.leerTodas');
