{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/terminos-y-condiciones.blade.php
  QUÉ ES  : Página estática de términos y condiciones de uso.
  RUTA    : GET /terminos (terminos)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno.
  BASE DE DATOS: Ninguna.
  QUÉ HAY QUE HACER (en orden):
    1. Redactar el texto legal.
    2. Enlazarla desde el registro si se exige aceptarla.
  CÓMO HACERLO / NOTAS:
    - Puede quedarse como Route::view.
  CÓMO SE COMPRUEBA:
    - El enlace del footer la abre.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Términos y condiciones · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/terminos-y-condiciones.css') }}">
@endpush
@section('content')
  <main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
    <article class="card max-w-3xl mx-auto p-space-xl">
      <h1 class="font-headline-xl text-headline-xl text-on-surface mb-space-md">Términos y condiciones</h1>
      {{-- TEXTO LEGAL pendiente de redacción (Ley 1581 de 2012). --}}
      <p class="font-body-md text-body-md text-on-surface-variant">Contenido pendiente.</p>
    </article>
  </main>
@endsection
