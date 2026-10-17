
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
                    Esta acción es permanente.
                </p>
            </header>

            @include('app.ajustes._tabs')

            <section class="card p-space-lg">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-4">
                    Eliminar mi cuenta
                </h2>

                <p class="mb-4">
                    Hola, {{ $usuario->nombre }}. Si continúas, tu cuenta y los datos
                    asociados se eliminarán. Las metas compartidas activas se
                    gestionarán según las reglas de la aplicación.
                </p>

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-500">
                        <p class="font-semibold">
                            No se pudo eliminar la cuenta:
                        </p>

                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('ajustes.eliminar-cuenta.destroy') }}"
                >
                    @csrf
                    @method('DELETE')

                    <div class="flex flex-col gap-2 mb-4">
                        <label for="contrasena_actual">
                            Confirma tu contraseña actual
                        </label>

                        <input
                            type="password"
                            id="contrasena_actual"
                            name="contrasena_actual"
                            autocomplete="current-password"
                            required
                            class="w-full p-3 rounded border"
                        >
                    </div>

                    <div class="flex flex-col gap-2 mb-6">
                        <label class="flex items-start gap-2">
                            <input
                                type="checkbox"
                                name="confirmar_eliminacion"
                                value="1"
                                required
                                class="mt-1"
                            >

                            <span>
                                Entiendo que esta acción es permanente y confirmo
                                que deseo eliminar mi cuenta.
                            </span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="px-5 py-3 rounded bg-red-600 text-white"
                    >
                        Eliminar mi cuenta permanentemente
                    </button>
                </form>
            </section>
        </div>
    </main>
@endsection
