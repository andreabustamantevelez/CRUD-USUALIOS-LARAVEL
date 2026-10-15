
<?php

use App\Http\Controllers\AjustesController;
use Illuminate\Support\Facades\Route;

$pendiente = fn () => back()->with(
    'exito',
    'Esta acción aún no está implementada.'
);

Route::prefix('ajustes')->name('ajustes.')->group(function () use ($pendiente) {
    Route::get('/perfil', [AjustesController::class, 'perfil'])
        ->name('perfil');

    Route::put('/perfil', [AjustesController::class, 'actualizarPerfil'])
        ->name('perfil.update');

    Route::view('/tipo-cuenta', 'app.ajustes.tipo-cuenta')
        ->name('tipo-cuenta');

    Route::put('/tipo-cuenta', $pendiente)
        ->name('tipo-cuenta.update');

    Route::view('/eliminar-cuenta', 'app.ajustes.eliminar-cuenta')
        ->name('eliminar-cuenta');

    Route::delete('/eliminar-cuenta', $pendiente)
        ->name('eliminar-cuenta.destroy');
});
