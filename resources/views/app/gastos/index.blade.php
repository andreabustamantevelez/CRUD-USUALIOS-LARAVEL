@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">

    @if(session('exito'))
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-6">
            {{ session('exito') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Gastos</h1>
            <p class="text-sm text-gray-500">Administra y visualiza todos tus egresos y consumos.</p>
        </div>
    </div>

    <!-- Formulario para Registrar Nuevo Gasto -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Registrar Nuevo Gasto</h2>
        <form method="POST" action="{{ route('gastos.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Monto ($ COP)</label>
                <input type="number" name="monto" min="1" placeholder="Ej: 45000" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                @error('monto') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Categoría</label>
                <select name="categoria_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Selecciona categoría</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
                @error('categoria_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Fecha</label>
                <input type="date" name="fecha" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                @error('fecha') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Descripción</label>
                <input type="text" name="descripcion" placeholder="Ej: Compra de mercado" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button
    type="submit"
    style="background-color: #00855d !important; color: white !important; padding: 10px 20px !important; border: none !important; border-radius: 8px !important; font-weight: 600 !important; cursor: pointer !important;"
>
    Guardar Gasto
</button>
            </div>
        </form>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">TOTAL GASTADO (ESTE MES)</p>
            <h3 class="text-2xl font-extrabold text-red-600 mt-1">$ {{ number_format($totalMes, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nº DE REGISTROS</p>
            <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $gastos->total() }}</h3>
        </div>
    </div>

    <!-- Tabla de Registros Reales -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4">FECHA</th>
                        <th class="p-4">DESCRIPCIÓN</th>
                        <th class="p-4">CATEGORÍA</th>
                        <th class="p-4 text-right">MONTO</th>
                        <th class="p-4 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($gastos as $gasto)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                            <td class="p-4 font-medium text-gray-900">{{ $gasto->descripcion ?? 'Sin descripción' }}</td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $gasto->categoria->nombre ?? 'General' }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-bold text-red-600">$ {{ number_format($gasto->monto, 0, ',', '.') }}</td>
                            <td class="p-4 text-center">
    <div class="flex items-center justify-center gap-3">

        <a href="{{ route('gastos.edit', $gasto->id) }}"
           class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">
            Editar
        </a>

        <form method="POST"
              action="{{ route('gastos.destroy', $gasto->id) }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar este gasto?');">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="text-red-600 hover:text-red-800 text-xs font-semibold">
                Eliminar
            </button>
        </form>

    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">
                                No hay gastos registrados en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $gastos->links() }}
        </div>
    </div>

</div>
@endsection