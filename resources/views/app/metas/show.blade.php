{{--
  ==============================================================================
  ARCHIVO : resources/views/app/metas/show.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Detalle de meta" (SIN diseño original todavía: el
            contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /metas/{meta} (metas.show) · POST aportes · POST finalizar · POST abandonar ·
            Controlador: MetaController@show
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $meta (objetivo, acumulado conjunto, %, estado, fecha límite)
    - $aportes (SOLO los del usuario actual)
  BASE DE DATOS: metas_ahorro, participaciones_meta, aportes_meta
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
    7. Policy: solo participantes. Calcular total conjunto con SUM sin exponer filas del otro
       (RQNF-03).
    8. Aporte: monto > 0. Mensajes: "Aporte registrado", "Meta finalizada", "Meta abandonada".
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/detalle-meta.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Detalle de meta · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/detalle-meta.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Detalle de meta</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Avance conjunto (sin ver el aporte del otro participante)
        </p>
      </header>
      <section class="card p-space-lg" data-dinamico="meta">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Avance conjunto y tu aporte
        </h2>
        {{-- DINÁMICO: meta --}}
        @forelse(($meta ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Meta no encontrada</p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="aportes">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Tu historial de aportes</h2>
        {{-- DINÁMICO: aportes --}}
        @forelse(($aportes ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Aún no registras aportes</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
