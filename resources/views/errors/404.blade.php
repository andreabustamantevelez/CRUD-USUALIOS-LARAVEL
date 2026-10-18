{{--
  ==============================================================================
  ARCHIVO : resources/views/errors/404.blade.php
  QUÉ ES  : Página 404 personalizada con el diseño de Cuentas Claras.
            Se muestra cuando Laravel no encuentra la ruta solicitada.
            No es una ruta: no aparece en route:list.
  LAYOUT  : layouts.publico (no requiere sesión). Mismo esqueleto que la 403.
  ESTILOS : theme.css bloque 9 (mismas clases que 403.blade.php).
  CÓMO SE PRUEBA:
    - Abrir /cualquier-cosa-que-no-existe.
    - Abrir /admin/tipo-cuenta (ruta que no existe).
    - Con sesión iniciada: verificar que "Ir al inicio" lleva a panel o admin.
  ==============================================================================
--}}

@extends('layouts.publico')

@section('titulo', 'Página no encontrada · Cuentas Claras')

@section('content')
<main class="min-h-screen flex items-center justify-center px-margin py-space-xl bg-surface">
  <div class="w-full max-w-lg text-center">

    {{-- Icono --}}
    <div class="flex justify-center mb-space-lg">
      <div class="error-icono">
        <span class="material-symbols-outlined error-icono__simbolo">explore_off</span>
      </div>
    </div>

    {{-- Código --}}
    <p class="error-codigo">404</p>

    {{-- Título --}}
    <h1 class="font-headline-xl text-headline-xl text-on-surface mt-space-sm">
      Página no encontrada
    </h1>

    {{-- Mensaje --}}
    <p class="font-body-md text-body-md text-on-surface-variant mt-space-md leading-relaxed">
      La página que buscas no existe o fue movida. Verifica la dirección o vuelve
      al inicio para seguir navegando.
    </p>

    {{-- Acciones --}}
    <div class="flex flex-wrap justify-center gap-space-sm mt-space-xl">
      @auth
        @php
          $esAdmin = auth()->user()->rol === 'administrador';
          $destino = $esAdmin ? route('admin.estadisticas') : route('panel');
        @endphp

        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : $destino }}"
           class="btn btn-secondary">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Volver atrás</span>
        </a>
        <a href="{{ $destino }}" class="btn btn-primary">
          <span class="material-symbols-outlined text-[18px]">home</span>
          <span>Ir al inicio</span>
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-primary">
          <span class="material-symbols-outlined text-[18px]">login</span>
          <span>Iniciar sesión</span>
        </a>
        <a href="{{ route('inicio') }}" class="btn btn-secondary">
          <span class="material-symbols-outlined text-[18px]">home</span>
          <span>Ir al inicio</span>
        </a>
      @endauth
    </div>

  </div>
</main>
@endsection