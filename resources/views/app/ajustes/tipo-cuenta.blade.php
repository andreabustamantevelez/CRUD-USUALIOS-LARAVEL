{{--
  ==============================================================================
  ARCHIVO : resources/views/app/ajustes/tipo-cuenta.blade.php
  QUÉ ES  : "Ajustes · Tipo de cuenta": cambia entre estándar y familiar.
  RUTA    : GET /ajustes/tipo-cuenta (ajustes.tipo-cuenta) · PUT ajustes.tipo-cuenta.update
            Controlador: AjustesController
  DATOS   : $usuario (auth()->user()) · $tieneMetasCompartidasActivas
  REGLAS  : estandar -> familiar siempre; familiar -> estandar bloqueado con metas compartidas activas.
  MENSAJE : "Tipo de cuenta actualizado"
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
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Estándar o familiar</p>
      </header>
      @include('app.ajustes._tabs')
      <section class="card p-space-lg">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Tipo de cuenta actual</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
          Hoy tienes una cuenta <strong>{{ ucfirst($usuario->rol) }}</strong>.
        </p>

        @error('tipo_cuenta')
          <p class="font-body-md text-body-md" style="color: #b3261e; margin-bottom: 12px;">{{ $message }}</p>
        @enderror

        <form method="POST" action="{{ route('ajustes.tipo-cuenta.update') }}" novalidate>
          @csrf
          @method('PUT')

          <label style="display: block; margin-bottom: 12px;">
            <input type="radio" name="tipo_cuenta" value="estandar"
                   @checked(old('tipo_cuenta', $usuario->rol) === 'estandar')
                   @if($tieneMetasCompartidasActivas) disabled @endif>
            <span class="font-label-md text-label-md text-on-surface">Cuenta estándar</span>
            <small class="font-body-sm text-body-sm text-on-surface-variant" style="display: block;">
              Gastos, categorías, gastos fijos, simulador, informes y metas individuales.
            </small>
          </label>

          <label style="display: block; margin-bottom: 12px;">
            <input type="radio" name="tipo_cuenta" value="familiar"
                   @checked(old('tipo_cuenta', $usuario->rol) === 'familiar')>
            <span class="font-label-md text-label-md text-on-surface">Cuenta familiar</span>
            <small class="font-body-sm text-body-sm text-on-surface-variant" style="display: block;">
              Todo lo del estándar + metas compartidas con otra persona familiar.
            </small>
          </label>

          @if($tieneMetasCompartidasActivas)
            <p class="font-body-sm text-body-sm text-on-surface-variant" style="margin-bottom: 12px;">
              No puedes cambiar a estándar mientras tengas metas compartidas activas. Finalízalas o cancelalas primero.
            </p>
          @endif

          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </section>
    </div>
  </main>
@endsection