<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/panel.php — DUEÑO: Juan (J8). Nombre: panel
 * Grupo C (rol:estandar,familiar).
 */

Route::get('/panel', [DashboardController::class, 'index'])->name('panel');