```blade
@extends('layouts.app')

@section('titulo', 'Categorías · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categorias.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        <header>
            <h1 class="font-headline-xl text-headline-xl text-on-surface">
                Categorías
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Organiza tus gastos
            </p>
        </header>

        <section class="card p-space-lg" data-dinamico="categorias">

            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Mis categorías y las del sistema
            </h2>

            {{-- Mensaje de éxito --}}
            @if(session('exito'))
                <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700">
                    {{ session('exito') }}
                </div>
            @endif

            {{-- Formulario para crear categoría --}}
            <form
                method="POST"
                action="{{ route('categorias.store') }}"
                class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200"
            >
                @csrf

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Crear nueva categoría
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label for="nombre" class="block text-sm font-semibold text-gray-600 mb-1">
                            Nombre
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre') }}"
                            maxlength="50"
                            required
                            placeholder="Ej: Gimnasio"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >

                        @error('nombre')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="bloque" class="block text-sm font-semibold text-gray-600 mb-1">
                            Bloque
                        </label>

                        <select
                            id="bloque"
                            name="bloque"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >
                            <option value="">Selecciona un bloque</option>

                            <option value="necesidades"
                                {{ old('bloque') === 'necesidades' ? 'selected' : '' }}>
                                Necesidades
                            </option>

                            <option value="discrecionales"
                                {{ old('bloque') === 'discrecionales' ? 'selected' : '' }}>
                                Discrecionales
                            </option>

                            <option value="ahorro"
                                {{ old('bloque') === 'ahorro' ? 'selected' : '' }}>
                                Ahorro
                            </option>
                        </select>

                        @error('bloque')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        type="submit"
                        style="background-color: #00855d; color: white; padding: 10px 20px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;"
                    >
                        Crear categoría
                    </button>
                </div>
            </form>

            {{-- Formulario para editar categoría --}}
            @if($categoriaEditar)
                <div class="mb-6 p-4 border border-gray-200 rounded-xl">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Editar categoría
                    </h3>

                    <form
                        method="POST"
                        action="{{ route('categorias.update', $categoriaEditar->id) }}"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="editar_nombre" class="block font-semibold mb-1">
                                Nombre
                            </label>

                            <input
                                type="text"
                                id="editar_nombre"
                                name="nombre"
                                value="{{ old('nombre', $categoriaEditar->nombre) }}"
                                maxlength="50"
                                required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg"
                            >

                            @error('nombre')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="editar_bloque" class="block font-semibold mb-1">
                                Bloque
                            </label>

                            <select
                                id="editar_bloque"
                                name="bloque"
                                required
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg"
                            >
                                <option value="necesidades"
                                    {{ old('bloque', $categoriaEditar->bloque) === 'necesidades' ? 'selected' : '' }}>
                                    Necesidades
                                </option>

                                <option value="discrecionales"
                                    {{ old('bloque', $categoriaEditar->bloque) === 'discrecionales' ? 'selected' : '' }}>
                                    Discrecionales
                                </option>

                                <option value="ahorro"
                                    {{ old('bloque', $categoriaEditar->bloque) === 'ahorro' ? 'selected' : '' }}>
                                    Ahorro
                                </option>
                            </select>

                            @error('bloque')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-3">
                            <button
                                type="submit"
                                style="background-color: #00855d; color: white; padding: 10px 16px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;"
                            >
                                Guardar cambios
                            </button>

                            <a
                                href="{{ route('categorias.index') }}"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Listado de categorías --}}
            @forelse(($categorias ?? []) as $item)

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 border border-gray-100 rounded-xl mb-3 bg-white">

                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-gray-800">
                                {{ $item->nombre }}
                            </h3>

                            @if($item->esDelSistema())
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">
                                    🔒 Sistema
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            Bloque: {{ ucfirst($item->bloque) }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ $item->gastos_count }} gasto(s)
                        </p>
                    </div>

                    {{-- Las categorías del sistema no se pueden editar ni eliminar --}}
                   @if(!$item->esDelSistema())
    <div class="flex items-center gap-3 self-center" style="align-items: baseline;">

        <a
    href="{{ route('categorias.index', ['editar' => $item->id]) }}"
    class="inline-flex items-center leading-5 text-indigo-600 hover:text-indigo-800 text-sm font-semibold"
   >
    Editar
</a>

                            {{-- Si tiene gastos, solicitar una categoría de destino --}}
                            @if($item->gastos_count > 0)

                              <button
    type="button"
    onclick="document.getElementById('modal-eliminar-{{ $item->id }}').classList.remove('hidden')"
    class="inline-flex items-center text-red-600 hover:text-red-800 text-sm font-semibold"
    >
    Eliminar
</button>

                                {{-- Modal de traslado de gastos --}}
                                <div
                                    id="modal-eliminar-{{ $item->id }}"
                                    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
                                >
                                    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">

                                        <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                            Eliminar categoría
                                        </h3>

                                        <p class="text-sm text-gray-600 mb-4">
                                            Esta categoría tiene {{ $item->gastos_count }} gasto(s).
                                            Selecciona dónde quieres trasladarlos antes de eliminarla.
                                        </p>

                                        <form
                                            method="POST"
                                            action="{{ route('categorias.destroy', $item->id) }}"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <label
                                                for="destino-{{ $item->id }}"
                                                class="block text-sm font-semibold text-gray-700 mb-2"
                                            >
                                                Categoría de destino
                                            </label>

                                            <select
                                                id="destino-{{ $item->id }}"
                                                name="categoria_destino_id"
                                                required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-5"
                                            >
                                                <option value="">Selecciona una categoría</option>

                                                @foreach($categorias as $destino)
                                                    @if($destino->id !== $item->id)
                                                        <option value="{{ $destino->id }}">
                                                            {{ $destino->nombre }}
                                                            {{ $destino->esDelSistema() ? '(Sistema)' : '' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>

                                            @error('categoria_destino_id')
                                                <p class="text-red-600 text-sm mb-3">
                                                    {{ $message }}
                                                </p>
                                            @enderror

                                            <div class="flex justify-end gap-3">
                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('modal-eliminar-{{ $item->id }}').classList.add('hidden')"
                                                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700"
                                                >
                                                    Cancelar
                                                </button>

                                                <button
                                                    type="submit"
                                                    style="background-color: #dc2626; color: white; padding: 10px 16px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;"
                                                >
                                                    Mover gastos y eliminar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Si no tiene gastos, se puede eliminar directamente --}}
                                <form
                                    method="POST"
                                    action="{{ route('categorias.destroy', $item->id) }}"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-800 text-sm font-semibold"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Aún no tienes categorías propias.
                </p>
            @endforelse
        </section>
    </div>
</main>
@endsection