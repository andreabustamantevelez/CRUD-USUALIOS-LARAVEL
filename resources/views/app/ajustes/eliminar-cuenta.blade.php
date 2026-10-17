@extends('layouts.app')

@section('titulo', 'Ajustes · Eliminar cuenta · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush

@section('content')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
            <header>
                <h1 class="font-headline-xl text-headline-xl text-on-surface">
                    Ajustes · Eliminar cuenta
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                    Esta acción es permanente y no se puede deshacer.
                </p>
            </header>

            @include('app.ajustes._tabs')

            @if ($errors->any())
                <div class="ajustes-error" role="alert">
                    <span class="ajustes-error__titulo">No se pudo eliminar la cuenta:</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="card p-space-lg">
                <div class="ajustes-peligro-cabecera">
                    <div>
                        <div id="titulo-eliminar-cuenta">
                            <span class="material-symbols-outlined ajustes-peligro-cabecera__icono" aria-hidden="true">
                                warning
                            </span>
                            <h2 class="font-headline-md text-headline-md text-on-surface">
                                Eliminar mi cuenta
                            </h2>
                        </div>

                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Hola, {{ $usuario->nombre }}. Al eliminar tu cuenta se borrarán
                            de forma permanente tus gastos, categorías propias, gastos fijos,
                            simulaciones, informes y notificaciones. Si tienes metas compartidas
                            activas, se cancelarán y la otra persona recibirá un aviso.
                        </p>
                    </div>
                </div>

                <hr class="ajustes-separador">

                <form
                    method="POST"
                    action="{{ route('ajustes.eliminar-cuenta.destroy') }}"
                    class="ajustes-form"
                    onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')

                    <div class="ajustes-campo">
                        <label for="contrasena_actual" class="ajustes-campo__label">
                            Confirma tu contraseña actual
                        </label>
                        <input
                            type="password"
                            id="contrasena_actual"
                            name="contrasena_actual"
                            autocomplete="current-password"
                            required
                            class="ajustes-campo__input">
                        @error('contrasena_actual')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="ajustes-peligro__confirm">
                        <input
                            type="checkbox"
                            name="confirmar_eliminacion"
                            value="1"
                            required>
                        <span>
                            Entiendo que esta acción es permanente y confirmo que deseo
                            eliminar mi cuenta.
                        </span>
                    </label>
                    @error('confirmar_eliminacion')
                        <p class="ajustes-campo__error">{{ $message }}</p>
                    @enderror

                    <div class="ajustes-acciones">
                        <button type="submit" class="btn btn-danger">
                            <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                            <span>Eliminar mi cuenta permanentemente</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
@endsection