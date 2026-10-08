```blade
@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6">

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Editar Gasto</h1>
            <p class="text-sm text-gray-500">
                Modifica la información del gasto seleccionado.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">

            <form method="POST" action="{{ route('gastos.update', $gasto->id) }}">

                @csrf
                @method('PUT')

                <div class="space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Monto ($ COP)
                        </label>

                        <input
                            type="number"
                            name="monto"
                            min="1"
                            value="{{ old('monto', $gasto->monto) }}"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        >

                        @error('monto')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Categoría
                        </label>

                        <select
                            name="categoria_id"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">Selecciona categoría</option>

                            @foreach($categorias as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    {{ old('categoria_id', $gasto->categoria_id) == $cat->id ? 'selected' : '' }}
                                >
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach

                        </select>

                        @error('categoria_id')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Fecha
                        </label>

                        <input
                            type="date"
                            name="fecha"
                            value="{{ old('fecha', \Carbon\Carbon::parse($gasto->fecha)->format('Y-m-d')) }}"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        >

                        @error('fecha')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Descripción
                        </label>

                        <input
                            type="text"
                            name="descripcion"
                            value="{{ old('descripcion', $gasto->descripcion) }}"
                            maxlength="200"
                            placeholder="Ej: Compra de mercado"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        >

                        @error('descripcion')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-6">

                    <a
                        href="{{ route('gastos.index') }}"
                        class="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50"
                    >
                        Cancelar
                    </a>

                    <button
    type="submit"
    style="display: block !important; background: #00855d !important; color: white !important; padding: 10px 20px !important; border-radius: 8px !important;"
>
    Guardar cambios
</button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
```
