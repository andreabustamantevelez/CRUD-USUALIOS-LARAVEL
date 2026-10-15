
<?php

use App\Http\Controllers\InformeController;
use Illuminate\Support\Facades\Route;

Route::get('/informes', [InformeController::class, 'mes'])
    ->name('informes.actual');

Route::post('/informes/guardar', [InformeController::class, 'guardar'])
    ->name('informes.guardar');

Route::get('/informes/historial', [InformeController::class, 'historial'])
    ->name('informes.historial');

Route::get('/informes/historial/{id}', [InformeController::class, 'ver'])
    ->whereNumber('id')
    ->name('informes.ver');
