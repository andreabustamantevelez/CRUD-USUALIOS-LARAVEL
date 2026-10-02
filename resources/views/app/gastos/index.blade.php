{{--
  ==============================================================================
  ARCHIVO : resources/views/app/gastos/index.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Gastos y transacciones" (SIN diseño original todavía:
            el contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /gastos y /gastos/nuevo (gastos.index, gastos.create) · POST gastos.store · PUT
            gastos.update · DELETE gastos.destroy · Controlador: GastoController
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $gastos (paginado, ya filtrado)
    - $categorias (propias + globales)
    - filtros en la URL: categoria, mes, desde, hasta (combinables)
  BASE DE DATOS: gastos, categorias_gasto
  QUÉ HAY QUE HACER (en orden):
    1. Crear la ruta con el NOMBRE indicado en routes/web.php, dentro del grupo con los middleware
       de la sección RUTA.
    2. Crear el controlador y devolver esta vista con las variables listadas en DATOS (compact o
       arreglo).
    3. Consultar SIEMPRE por el usuario autenticado (auth()->user()->relación()); nunca
       Modelo::all().
    4. Reemplazar el bloque "DISEÑO PENDIENTE" por el code.html original de esta pantalla,
       conservando los @forelse/@empty.
    5. Formularios: @csrf, @method cuando no sea POST, name= igual al de la validación
       (FormRequest) y @error bajo cada campo.
    6. Mensajes de éxito: redirect()->with('exito', 'texto de la especificación') (se muestran en
       <x-toast />).
    7. Validar: monto > 0, categoría válida, fecha no futura. Mensajes: "Gasto
       guardado/actualizado/eliminado".
    8. Eliminar con modal de confirmación; el gasto de otro usuario debe responder 403 (Policy).
    9. Estado vacío: "No hay gastos registrados en este periodo".
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/gastos.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Gastos y transacciones · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/gastos.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Gastos y transacciones</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Registra y consulta tus gastos</p>
      </header>
      <section class="card p-space-lg" data-dinamico="categorias">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Registrar gasto (monto, categoría, fecha, descripción)
        </h2>
        {{-- DINÁMICO: categorias --}}
        @forelse(($categorias ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Crea una categoría para empezar</p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="gastos">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Historial con filtros por categoría y periodo
        </h2>
        {{-- DINÁMICO: gastos --}}
        @forelse(($gastos ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">
            No hay gastos registrados en este periodo
          </p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
