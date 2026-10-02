{{--
  ==============================================================================
  ARCHIVO : resources/views/app/informes/actual.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Informe 50/30/20" (SIN diseño original todavía: el
            contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /informes (informes.actual) · Controlador: InformeController@actual
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $informe (total, % real vs recomendado por bloque)
    - $detalle (por categoría, con "excede")
  BASE DE DATOS: informes_mensuales, detalle_informe, gastos, categorias_gasto
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
    7. Al generar, GUARDAR el detalle; los meses cerrados no se recalculan (regla 27).
    8. Necesita la tabla categoría→bloque 50/30/20 (decisión #12).
    9. Sin datos: "No hay datos suficientes para generar el informe".
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/informes-50-30-20.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Informe 50/30/20 · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/informes-50-30-20.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Informe 50/30/20</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Cómo se distribuyó tu gasto</p>
      </header>
      <section class="card p-space-lg" data-dinamico="informe">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Distribución real vs recomendada
        </h2>
        {{-- DINÁMICO: informe --}}
        @forelse(($informe ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">
            No hay datos suficientes para generar el informe
          </p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="detalle">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Detalle por categoría</h2>
        {{-- DINÁMICO: detalle --}}
        @forelse(($detalle ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Sin categorías en el periodo</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
