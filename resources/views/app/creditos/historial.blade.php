@extends('layouts.app')

@section('titulo', 'Historial de simulaciones · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/historial-simulaciones.css') }}">
@endpush

@section('content')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

            {{-- Encabezado --}}
            <header>
                <div class="flex items-center gap-3 mb-2">
                    <a
                        href="{{ route('creditos.simulador') }}"
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full hover:bg-surface-container transition-colors"
                        aria-label="Volver al simulador"
                    >
                        <span class="material-symbols-outlined">
                            arrow_back
                        </span>
                    </a>

                    <div>
                        <h1 class="font-headline-xl text-headline-xl text-on-surface">
                            Historial de simulaciones
                        </h1>

                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Consulta y administra tus simulaciones guardadas.
                        </p>
                    </div>
                </div>
            </header>

            {{-- Contenido --}}
            <section>
                @forelse($simulaciones as $simulacion)
                    @if ($loop->first)
                        <div class="historial-grid">
                    @endif

                    <article class="simulacion-card">

                        {{-- Encabezado de la tarjeta --}}
                        <div class="simulacion-card__header">
                            <div>
                                <h2 class="simulacion-card__titulo font-headline-sm text-headline-sm text-on-surface">
                                    {{ $simulacion->nombre }}
                                </h2>

                                <p class="simulacion-card__fecha">
                                    Guardada el {{ $simulacion->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Datos de la simulación --}}
                        <div class="simulacion-card__datos">

                            <div class="simulacion-card__dato">
                                <span class="simulacion-card__dato-label">
                                    Monto
                                </span>

                                <span class="simulacion-card__dato-valor">
                                    ${{ number_format($simulacion->monto, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="simulacion-card__dato">
                                <span class="simulacion-card__dato-label">
                                    Tasa mensual
                                </span>

                                <span class="simulacion-card__dato-valor">
                                    {{ $simulacion->tasa_mensual }}%
                                </span>
                            </div>

                            <div class="simulacion-card__dato">
                                <span class="simulacion-card__dato-label">
                                    Plazo
                                </span>

                                <span class="simulacion-card__dato-valor">
                                    {{ $simulacion->plazo_meses }} meses
                                </span>
                            </div>

                        </div>

                        {{-- Resultados --}}
                        <div class="simulacion-card__resultados">

                            <div class="simulacion-card__resultado">
                                <span class="simulacion-card__resultado-label">
                                    Cuota mensual
                                </span>

                                <span class="simulacion-card__resultado-valor">
                                    ${{ number_format($simulacion->cuota, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="simulacion-card__resultado">
                                <span class="simulacion-card__resultado-label">
                                    Total a pagar
                                </span>

                                <span class="simulacion-card__resultado-valor">
                                    ${{ number_format($simulacion->total_pagar, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="simulacion-card__resultado">
                                <span class="simulacion-card__resultado-label">
                                    Intereses
                                </span>

                                <span class="simulacion-card__resultado-valor">
                                    ${{ number_format($simulacion->intereses, 0, ',', '.') }}
                                </span>
                            </div>

                        </div>

                        {{-- Acción --}}
                        <div class="simulacion-card__acciones">
                            <form
                                action="{{ route('creditos.eliminar', $simulacion) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="simulacion-card__eliminar"
                                >
                                    <span class="material-symbols-outlined text-[18px]">
                                        delete
                                    </span>

                                    Eliminar
                                </button>
                            </form>
                        </div>

                    </article>

                    @if ($loop->last)
                        </div>
                    @endif

                @empty
                    <div class="historial-vacio">
                        <span class="material-symbols-outlined historial-vacio__icono">
                            history
                        </span>

                        <h2 class="font-headline-md text-headline-md text-on-surface">
                            No tienes simulaciones guardadas
                        </h2>

                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Crea una simulación de crédito y guárdala para consultarla aquí.
                        </p>

                        <a
                            href="{{ route('creditos.simulador') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md transition-colors"
                        >
                            <span class="material-symbols-outlined text-[18px]">
                                calculate
                            </span>

                            Crear simulación
                        </a>
                    </div>
                @endforelse
            </section>

        </div>
    </main>
@endsection
