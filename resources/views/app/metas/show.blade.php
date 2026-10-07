{{--
  ==============================================================================
  ARCHIVO : resources/views/app/metas/show.blade.php        (J5 HECHO)
  QUÉ ES  : "Detalle de meta": avance CONJUNTO y el historial de MIS aportes.
  RUTA    : GET /metas/{meta} (metas.show) · POST aportes / finalizar / abandonar / invitar
            Controlador: MetaController@show
  DATOS   : $meta (individual o compartida) · $avance (total, porcentaje, falta) · $aportes (SOLO los míos) · $miTotal ·
            $participantes (solo nombres) · $puedeAportar/$puedeInvitar/$puedeFinalizar/$puedeAbandonar
  PRIVACIDAD (RQNF-03): el aporte del otro participante NO llega a esta vista (ni oculto): el
            controlador solo consulta los aportes del usuario actual y suma el total en SQL.
  MENSAJES: "Aporte registrado" · "Meta finalizada" · "Meta abandonada" · "Invitación enviada"
  CSS     : public/css/detalle-meta.css
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
      <header>
        <a href="{{ route('metas.index') }}" class="font-label-md text-label-md text-primary">← Metas compartidas</a>
        <h1 class="font-headline-xl text-headline-xl text-on-surface mt-2">{{ $meta->nombre }}</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
          @if($meta->esCompartida()) Avance conjunto (sin ver el aporte del otro participante) @else Meta individual @endif
          · <span class="detalle-estado detalle-estado--{{ $meta->estado }}">{{ ucfirst($meta->estado) }}</span>
        </p>
      </header>

      @error('meta')<p class="detalle-error" role="alert">{{ $message }}</p>@enderror

      <section class="card p-space-lg" data-dinamico="meta">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">{{ $meta->esCompartida() ? 'Avance conjunto y tu aporte' : 'Tu avance' }}</h2>
        <div class="detalle-barra" role="progressbar" aria-valuenow="{{ $avance['porcentaje'] }}" aria-valuemin="0" aria-valuemax="100">
          <span class="detalle-barra__relleno" style="width: {{ $avance['porcentaje'] }}%"></span>
        </div>
        <dl class="detalle-cifras">
          <div><dt>{{ $meta->esCompartida() ? 'Acumulado conjunto' : 'Acumulado' }}</dt><dd>${{ number_format($avance['total'], 0, ',', '.') }}</dd></div>
          <div><dt>Objetivo</dt><dd>${{ number_format($meta->monto_objetivo, 0, ',', '.') }}</dd></div>
          <div><dt>Avance</dt><dd>{{ $avance['porcentaje'] }}%</dd></div>
          <div><dt>Falta</dt><dd>${{ number_format($avance['falta'], 0, ',', '.') }}</dd></div>
          @if($meta->esCompartida())<div><dt>Tu aporte total</dt><dd>${{ number_format($miTotal, 0, ',', '.') }}</dd></div>@endif
          @if($meta->fecha_limite)<div><dt>Fecha límite</dt><dd>{{ $meta->fecha_limite->format('d/m/Y') }}</dd></div>@endif
        </dl>
        @if($meta->esCompartida())
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-md">
            Participantes:
            {{ $participantes->map(fn ($p) => $p->usuario->nombre.($p->estado_invitacion === 'pendiente' ? ' (invitación pendiente)' : ''))->join(', ') }}
          </p>
        @endif
      </section>

      @if($puedeAportar)
        <section class="card p-space-lg">
          <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Registrar un aporte</h2>
          <form method="POST" action="{{ route('metas.aportes', $meta) }}" class="detalle-formulario" novalidate>
            @csrf
            <div class="detalle-campo">
              <label for="monto" class="font-label-md text-label-md text-on-surface">Monto (COP)</label>
              <input id="monto" name="monto" type="text" inputmode="numeric" class="input" value="{{ old('monto') }}" required>
              @error('monto')<p class="detalle-error-campo">{{ $message }}</p>@enderror
            </div>
            <div class="detalle-campo">
              <label for="fecha" class="font-label-md text-label-md text-on-surface">Fecha (opcional)</label>
              <input id="fecha" name="fecha" type="date" class="input" max="{{ now()->toDateString() }}" value="{{ old('fecha') }}">
              @error('fecha')<p class="detalle-error-campo">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Guardar aporte</button>
          </form>
        </section>
      @endif

      <section class="card p-space-lg" data-dinamico="aportes">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Tu historial de aportes</h2>
        @forelse($aportes as $aporte)
          <div class="detalle-aporte">
            <span>{{ $aporte->fecha->format('d/m/Y') }}</span>
            <strong>${{ number_format($aporte->monto, 0, ',', '.') }}</strong>
          </div>
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Aún no registras aportes</p>
        @endforelse
      </section>

      @if($puedeInvitar)
        <section class="card p-space-lg">
          <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Invitar a alguien</h2>
          <form method="POST" action="{{ route('metas.invitar', $meta) }}" class="detalle-formulario" novalidate>
            @csrf
            <div class="detalle-campo">
              <label for="correo" class="font-label-md text-label-md text-on-surface">Correo</label>
              <input id="correo" name="correo" type="email" class="input" value="{{ old('correo') }}" required>
              @error('correo')<p class="detalle-error-campo">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-secondary">Enviar invitación</button>
          </form>
        </section>
      @endif

      @if($puedeFinalizar || $puedeAbandonar)
        <section class="card p-space-lg detalle-acciones">
          @if($puedeFinalizar)
            <form method="POST" action="{{ route('metas.finalizar', $meta) }}">
              @csrf
              <button type="submit" class="btn btn-primary">Finalizar meta</button>
            </form>
          @endif
          @if($puedeAbandonar)
            <form method="POST" action="{{ route('metas.abandonar', $meta) }}"
                  onsubmit="return confirm('{{ $meta->esCompartida() ? '¿Seguro que quieres abandonar la meta? Se cancelará para las dos personas y se conservará el historial.' : '¿Seguro que quieres cancelar la meta? Se conservará el historial de tus aportes.' }}')">
              @csrf
              <button type="submit" class="btn btn-danger">{{ $meta->esCompartida() ? 'Abandonar meta' : 'Cancelar meta' }}</button>
            </form>
          @endif
        </section>
      @endif
    </div>
  </main>
@endsection
