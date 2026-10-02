<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/panel.php — DUEÑO: Juan (J8). Nombre: panel
 * MAQUETA: cambia por [DashboardController::class, 'index'].
 */

Route::view('/panel', 'app.dashboard')->name('panel');
