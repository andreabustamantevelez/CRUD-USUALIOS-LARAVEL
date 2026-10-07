{{--
  ==============================================================================
  ARCHIVO : resources/views/app/metas/invitaciones.blade.php        (J5 HECHO)
  QUÉ ES  : "Invitaciones a metas": las que recibí y están pendientes.
  RUTA    : GET /metas/invitaciones (metas.invitaciones) · POST aceptar / rechazar
            Controlador: InvitacionMetaController
  DATOS   : $invitaciones (solo las del usuario actual, de metas activas)
  MENSAJES: "Invitación aceptada" / "Invitación rechazada"
  CSS     : public/css/invitaciones-metas.css
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Invitaciones a metas · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/invitaciones-metas.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Invitaciones a metas</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Acepta o rechaza invitaciones</p>
      </header>
      <section class="card p-space-lg" data-dinamico="invitaciones">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Invitaciones recibidas</h2>
        @forelse($invitaciones as $invitacion)
          <article class="invitacion">
            <div class="invitacion__datos">
              <h3 class="font-headline-sm text-headline-sm text-on-surface">{{ $invitacion->meta->nombre }}</h3>
              <p class="font-body-sm text-body-sm text-on-surface-variant">
                Te invitó {{ $invitacion->meta->creador->nombre }} · objetivo
                ${{ number_format($invitacion->meta->monto_objetivo, 0, ',', '.') }}
                @if($invitacion->meta->fecha_limite) · hasta {{ $invitacion->meta->fecha_limite->format('d/m/Y') }} @endif
              </p>
            </div>
            <div class="invitacion__acciones">
              <form method="POST" action="{{ route('metas.aceptar', $invitacion) }}">
                @csrf
                <button type="submit" class="btn btn-primary">Aceptar</button>
              </form>
              <form method="POST" action="{{ route('metas.rechazar', $invitacion) }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Rechazar</button>
              </form>
            </div>
          </article>
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Sin invitaciones pendientes</p>
        @endforelse
        @error('invitacion')<p class="invitacion__error">{{ $message }}</p>@enderror
      </section>
      <a href="{{ route('metas.index') }}" class="font-label-md text-label-md text-primary">← Volver a metas</a>
    </div>
  </main>
@endsection
