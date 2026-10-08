@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6">

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Nuevo Gasto
            </h1>

            <p class="text-sm text-gray-500">
                Registra un nuevo gasto en tu control financiero.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">

            <form method="POST" action="{{ route('gastos.store') }}">
                @csrf

                <div class="space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Monto ($ COP)
                        </label>

                        <input
                            type="number"
                            name="monto"
                            min="1"
                            value="{{ old('monto') }}"
                            placeholder="Ej: 45000"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >

                        @error('monto')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Categoría
                        </label>

                        <select
                            name="categoria_id"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >
                            <option value="">Selecciona categoría</option>

                            @foreach($categorias as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    {{ old('categoria_id') == $cat->id ? 'selected' : '' }}
                                >
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach

                        </select>

                        @error('categoria_id')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Fecha
                        </label>

                        <input
                            type="date"
                            name="fecha"
                            value="{{ old('fecha', date('Y-m-d')) }}"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >

                        @error('fecha')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Descripción
                        </label>

                        <input
                            type="text"
                            name="descripcion"
                            value="{{ old('descripcion') }}"
                            maxlength="255"
                            placeholder="Ej: Compra de mercado"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >

                        @error('descripcion')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                </div>

               <div class="flex justify-end gap-3 mt-6">

    <a
        href="{{ route('gastos.index') }}"
        style="display: inline-block; padding: 10px 20px; border: 1px solid #ddd; border-radius: 8px; color: #666; text-decoration: none;"
    >
        Cancelar
    </a>

    <button
        type="submit"
        style="display: inline-block !important; padding: 10px 20px !important; background-color: #00855d !important; color: white !important; border: none !important; border-radius: 8px !important; font-weight: bold !important; cursor: pointer !important;"
    >
        Guardar Gasto
    </button>

</div>

    </div>

</div>

@endsection