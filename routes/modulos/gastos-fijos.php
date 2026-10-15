<?php

use App\Http\Controllers\GastoFijoController;
use Illuminate\Support\Facades\Route;

Route::get('/gastos-fijos', [
    GastoFijoController::class,
    'index',
])->name('gastos-fijos.index');

Route::post('/gastos-fijos', [
    GastoFijoController::class,
    'store',
])->name('gastos-fijos.store');

Route::put('/gastos-fijos/{gastoFijo}', [
    GastoFijoController::class,
    'update',
])->name('gastos-fijos.update');

Route::delete('/gastos-fijos/{gastoFijo}', [
    GastoFijoController::class,
    'destroy',
])->name('gastos-fijos.destroy');

Route::post('/gastos-fijos/{gastoFijo}/pagar', [
    GastoFijoController::class,
    'pagar',
])->name('gastos-fijos.pagar');