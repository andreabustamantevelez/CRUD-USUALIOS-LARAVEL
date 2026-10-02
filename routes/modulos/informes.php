<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/informes.php — DUEÑA: Valeria (V3).
 * Nombres: informes.actual, informes.historial
 * MAQUETA: cambia por InformeController.
 */

Route::view('/informes', 'app.informes.actual')->name('informes.actual');
Route::view('/informes/historial', 'app.informes.historial')->name('informes.historial');
