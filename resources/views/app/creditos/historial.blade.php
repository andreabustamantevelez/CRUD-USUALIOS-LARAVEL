{{--
  ==============================================================================
  ARCHIVO : resources/views/app/creditos/historial.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Historial de simulaciones" (SIN diseño original
            todavía: el contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /creditos/historial (creditos.historial) · Controlador:
            SimulacionCreditoController@historial
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $simulaciones (nombre, monto, tasa, plazo, cuota, total, intereses, fecha)
  BASE DE DATOS: simulaciones_credito
  QUÉ HAY QUE HACER (en orden):
    1. Crear la ruta con el NOMBRE indicado en routes/web.php, dentro del grupo con los middleware
       de la sección RUTA.
    2. Crear el controlador y devolver esta vista con las variables listadas en DATOS (compact o
       arreglo).
    3. Consultar SIEMPRE por el usuario autenticado (auth()->user()->relación()); nunca
       Modelo::all().
    4. Reemplazar el bloque "DISEÑO PENDIENTE" por el code.html original de esta pantalla,
       conservando los @forelse/@empty.
    5. Formularios: @csrf, @method cuando no sea POST, name= igual al de la validación
       (FormRequest) y @error bajo cada campo.
    6. Mensajes de éxito: redirect()->with('exito', 'texto de la especificación') (se muestran en
       <x-toast />).
    7. Guardar cuota, total e intereses al simular para que el historial no cambie si cambia la
       fórmula.
    8. "Abrir en el simulador" precarga el formulario; eliminar con modal.
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/historial-simulaciones.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Historial de simulaciones · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/historial-simulaciones.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Historial de simulaciones</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Tus simulaciones guardadas</p>
      </header>
      <section class="card p-space-lg" data-dinamico="simulaciones">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Simulaciones guardadas</h2>
        {{-- DINÁMICO: simulaciones --}}
        @forelse(($simulaciones ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">No tienes simulaciones guardadas</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
