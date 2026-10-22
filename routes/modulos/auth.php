<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistroController;
use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/auth.php — DUEÑO: Juan (J3). HECHO (2026-10-06, Juan).
 * Nombres (NO se cambian): login, login.entrar, registro, registro.guardar,
 *   password.request, password.email, password.reset, password.update, logout
 *
 * 'guest' = solo para quien NO ha iniciado sesión (si ya tiene sesión, lo manda a su panel).
 * 'throttle:5,1' = máximo 5 peticiones por minuto (registro y recuperación de contraseña).
 * El límite de intentos del login vive en LoginRequest (5 por minuto por correo + IP).
 */

Route::middleware('guest')->group(function () {
    Route::get('/iniciar-sesion', [LoginController::class, 'create'])->name('login');
    Route::post('/iniciar-sesion', [LoginController::class, 'store'])->name('login.entrar');

    Route::get('/crear-usuario', [RegistroController::class, 'create'])->name('registro');
    Route::post('/crear-usuario', [RegistroController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('registro.guardar');

    Route::get('/recuperar-contrasena', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/recuperar-contrasena', [PasswordResetController::class, 'enviarEnlace'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordResetController::class, 'actualizar'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::post('/cerrar-sesion', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');