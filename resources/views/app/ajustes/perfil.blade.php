@extends('layouts.app')

@section('titulo', 'Ajustes · Perfil · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush

@section('content')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
            <header>
                <h1 class="font-headline-xl text-headline-xl text-on-surface">
                    Ajustes · Perfil
                </h1>

                <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                    Actualiza tus datos personales y tu ingreso mensual.
                </p>
            </header>

            @include('app.ajustes._tabs')

            <section class="card p-space-lg">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                    Datos del perfil
                </h2>

                @if (session('exito'))
                    <div class="mb-4 p-3 rounded border border-green-500">
                        {{ session('exito') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded border border-red-500">
                        <p class="font-semibold">Revisa los siguientes datos:</p>

                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('ajustes.perfil.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-2 mb-4">
                        <label for="nombre">Nombre completo</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre', $usuario->nombre) }}"
                            maxlength="100"
                            required
                            class="w-full p-3 rounded border"
                        >
                        @error('nombre')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2 mb-4">
                        <label for="email">Correo electrónico</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $usuario->email) }}"
                            maxlength="255"
                            required
                            class="w-full p-3 rounded border"
                        >
                        @error('email')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2 mb-6">
                        <label for="ingreso_mensual">Ingreso mensual (COP)</label>
                        <input
                            type="number"
                            id="ingreso_mensual"
                            name="ingreso_mensual"
                            value="{{ old('ingreso_mensual', $usuario->ingreso_mensual) }}"
                            min="0"
                            max="999999999999"
                            step="1"
                            required
                            class="w-full p-3 rounded border"
                        >
                        @error('ingreso_mensual')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="text-sm text-on-surface-variant">
                            Ingresa el valor mensual en pesos colombianos, sin puntos ni comas.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="px-5 py-3 rounded bg-primary text-on-primary"
                    >
                        Guardar cambios
                    </button>
                </form>
            </section>
        </div>
    </main>
@endsection