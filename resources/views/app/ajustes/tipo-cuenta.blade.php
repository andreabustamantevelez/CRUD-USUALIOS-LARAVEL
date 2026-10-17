{{--
  ==============================================================================
  ARCHIVO : resources/views/app/ajustes/tipo-cuenta.blade.php
  QUÉ ES  : "Ajustes · Tipo de cuenta": cambia entre estándar y familiar.
  RUTA    : GET /ajustes/tipo-cuenta (ajustes.tipo-cuenta) · PUT ajustes.tipo-cuenta.update
  REGLAS  : estandar -> familiar siempre; familiar -> estandar bloqueado con metas compartidas activas.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Ajustes · Tipo de cuenta · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Ajustes · Tipo de cuenta</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Elige cómo quieres usar Cuentas Claras. Puedes cambiarlo cuando quieras.
        </p>
      </header>

      @include('app.ajustes._tabs')

      @if (session('exito'))
        <div class="ajustes-exito" role="status">
          <span class="material-symbols-outlined text-[20px]">check_circle</span>
          <span>{{ session('exito') }}</span>
        </div>
      @endif

      @if ($tieneMetasCompartidasActivas)
        <div class="ajustes-aviso" role="alert">
          <span class="material-symbols-outlined">info</span>
          <div>
            <strong>No puedes cambiar a estándar todavía.</strong>
            Tienes metas compartidas activas. Finalízalas o cancélalas primero
            para poder bajar de tipo de cuenta.
          </div>
        </div>
      @endif

      @error('tipo_cuenta')
        <div class="ajustes-error" role="alert">
          <span class="ajustes-error__titulo">{{ $message }}</span>
        </div>
      @enderror

      <section class="card p-space-lg">
        <form method="POST" action="{{ route('ajustes.tipo-cuenta.update') }}" novalidate>
          @csrf
          @method('PUT')

          <div class="ajustes-seccion-header">
            <h2 class="ajustes-seccion-titulo">Tipo de cuenta actual</h2>
            <p class="ajustes-seccion-desc">
              Hoy tienes una cuenta <strong>{{ ucfirst($usuario->rol) }}</strong>.
              Selecciona el tipo al que quieres cambiar.
            </p>
          </div>

          <div class="ajustes-tipo-cuenta" role="radiogroup" aria-label="Tipo de cuenta">
            <label class="ajustes-tipo-cuenta__opcion {{ $tieneMetasCompartidasActivas ? 'ajustes-tipo-cuenta__opcion--bloqueada' : '' }}">
              <input
                type="radio"
                name="tipo_cuenta"
                value="estandar"
                class="ajustes-tipo-cuenta__radio"
                @checked(old('tipo_cuenta', $usuario->rol) === 'estandar')
                @if ($tieneMetasCompartidasActivas) disabled @endif>

              <span class="ajustes-tipo-cuenta__icono" aria-hidden="true">
                <span class="material-symbols-outlined text-[20px]">person</span>
              </span>

              <span class="ajustes-tipo-cuenta__texto">
                <span class="ajustes-tipo-cuenta__titulo">Cuenta estándar</span>
                <span class="ajustes-tipo-cuenta__desc">
                  Gastos, categorías, gastos fijos, simulador, informes y metas individuales.
                </span>
              </span>
            </label>

            <label class="ajustes-tipo-cuenta__opcion">
              <input
                type="radio"
                name="tipo_cuenta"
                value="familiar"
                class="ajustes-tipo-cuenta__radio"
                @checked(old('tipo_cuenta', $usuario->rol) === 'familiar')>

              <span class="ajustes-tipo-cuenta__icono" aria-hidden="true">
                <span class="material-symbols-outlined text-[20px]">diversity_1</span>
              </span>

              <span class="ajustes-tipo-cuenta__texto">
                <span class="ajustes-tipo-cuenta__titulo">Cuenta familiar</span>
                <span class="ajustes-tipo-cuenta__desc">
                  Todo lo del estándar + metas compartidas con otra persona familiar.
                </span>
              </span>
            </label>
          </div>

          <div class="ajustes-acciones">
            <button type="submit" class="btn btn-primary">
              Guardar cambios
            </button>
          </div>
        </form>
      </section>
    </div>
  </main>
@endsection