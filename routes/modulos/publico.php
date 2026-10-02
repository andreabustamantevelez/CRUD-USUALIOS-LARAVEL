<?php

use Illuminate\Support\Facades\Route;

/*
 * routes/modulos/publico.php — DUEÑA: Nikol (N5). Páginas públicas estáticas.
 * Nombres: inicio, politica, terminos
 */

Route::view('/', 'publico.landing')->name('inicio');
Route::view('/politica-de-privacidad', 'publico.politica-de-privacidad')->name('politica');
Route::view('/terminos', 'publico.terminos-y-condiciones')->name('terminos');
