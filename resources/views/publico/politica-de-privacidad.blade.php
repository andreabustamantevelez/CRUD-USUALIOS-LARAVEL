{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/politica-de-privacidad.blade.php
  QUÉ ES  : Página estática del tratamiento de datos personales (Ley 1581 de 2012).
  RUTA    : GET /politica-de-privacidad (politica)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno.
  BASE DE DATOS: Ninguna.
  QUÉ HAY QUE HACER (en orden):
    1. Redactar el texto legal con quien corresponda (no es asesoría de este equipo).
    2. Verificar que el texto coincida con lo que realmente hace el sistema (qué datos se guardan
       y para qué).
  CÓMO HACERLO / NOTAS:
    - Puede quedarse como Route::view.
  CÓMO SE COMPRUEBA:
    - El enlace del registro y del footer la abren.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Política de privacidad · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/politica-de-privacidad.css') }}">
@endpush
@section('content')
  <main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
    <article class="card max-w-3xl mx-auto p-space-xl">
      <h1 class="font-headline-xl text-headline-xl text-on-surface mb-space-md">Política de privacidad</h1>
      {{-- TEXTO LEGAL pendiente de redacción (Ley 1581 de 2012). --}}
      <p class="font-body-md text-body-md text-on-surface-variant">Contenido pendiente.</p>
    </article>
  </main>
@endsection
