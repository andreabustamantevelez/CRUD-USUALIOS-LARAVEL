<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/auth.php — DUEÑO: Juan (J3).
 * Nombres: login, login.entrar, registro, registro.guardar, password.request,
 *          password.email, password.reset, password.update, logout
 * MAQUETA: los POST solo devuelven un aviso. J3 los reemplaza por controladores
 *          (y agrega middleware 'guest' a los de login/registro/recuperar).
 */

$pendiente = fn () => back()->with('exito', 'MAQUETA: esta acción aún no está implementada');

Route::view('/iniciar-sesion', 'publico.iniciar-sesion')->name('login');
Route::post('/iniciar-sesion', $pendiente)->name('login.entrar');

Route::view('/crear-usuario', 'publico.crear-usuario')->name('registro');
Route::post('/crear-usuario', $pendiente)->name('registro.guardar');

Route::view('/recuperar-contrasena', 'publico.recuperar-contrasena')->name('password.request');
Route::post('/recuperar-contrasena', $pendiente)->name('password.email');

Route::get('/restablecer-contrasena/{token}', fn (string $token) => view('publico.restablecer-contrasena', ['token' => $token]))
    ->name('password.reset');
Route::post('/restablecer-contrasena', $pendiente)->name('password.update');

Route::post('/cerrar-sesion', fn () => redirect()->route('inicio'))->name('logout');
