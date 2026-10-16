{{--
  ==============================================================================
  ARCHIVO : resources/views/errors/403.blade.php
  QUÉ ES  : Página 403 personalizada con el diseño de Cuentas Claras.
            Se muestra cuando un usuario autenticado intenta acceder a un recurso
            que no le corresponde (policies, middleware 'rol', etc.).
  ==============================================================================
--}}

@extends('layouts.publico')

@section('titulo', 'Acceso denegado · Cuentas Claras')

@section('content')
<main class="min-h-screen flex items-center justify-center px-margin py-space-xl bg-surface">
  <div class="w-full max-w-lg text-center">

    {{-- Icono --}}
    <div class="flex justify-center mb-space-lg">
      <div class="error-icono">
        <span class="material-symbols-outlined error-icono__simbolo">lock</span>
      </div>
    </div>

    {{-- Código --}}
    <p class="error-codigo">403</p>

    {{-- Título --}}
    <h1 class="font-headline-xl text-headline-xl text-on-surface mt-space-sm">
      Acceso denegado
    </h1>

    {{-- Mensaje --}}
    <p class="font-body-md text-body-md text-on-surface-variant mt-space-md leading-relaxed">
      No tienes permiso para ver esta sección. Si crees que es un error, contacta al
      administrador del sistema.
    </p>

    {{-- Acciones --}}
    <div class="flex flex-wrap justify-center gap-space-sm mt-space-xl">
      @auth
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('panel') }}"
           class="btn btn-secondary">
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          <span>Volver atrás</span>
        </a>
        <a href="{{ auth()->user()->rol === 'administrador' ? route('admin.estadisticas') : route('panel') }}"
           class="btn btn-primary">
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