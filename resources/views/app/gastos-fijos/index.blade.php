{{--
  ==============================================================================
  ARCHIVO : resources/views/app/gastos-fijos/index.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Gastos fijos y vencimientos" (SIN diseño original
            todavía: el contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /gastos-fijos (gastos-fijos.index) · POST/PUT/DELETE gastos-fijos.* · POST
            gastos-fijos.pagar · Controlador: GastoFijoController
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $resumen (pagados, próximos, vencidos)
    - $gastosFijos (con su pago del periodo actual)
    - $categorias
  BASE DE DATOS: gastos_fijos, pagos_gasto_fijo, categorias_gasto
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
    7. Estados: pagado, próximo (<3 días), vencido, pendiente (chips del diseño).
    8. Marcar pagado: guardar fecha real y CANCELAR la alerta pendiente del periodo.
    9. Día 29–31: vence el último día del mes (propuesta pendiente de aprobar).
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/gastos-fijos.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Gastos fijos y vencimientos · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/gastos-fijos.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Gastos fijos y vencimientos</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Controla tus pagos recurrentes</p>
      </header>
      <section class="card p-space-lg" data-dinamico="resumen">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Resumen: pagados, próximos y vencidos
        </h2>
        {{-- DINÁMICO: resumen --}}
        @forelse(($resumen ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Sin datos</p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="gastosFijos">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Lista de gastos fijos con estado
        </h2>
        {{-- DINÁMICO: gastosFijos --}}
        @forelse(($gastosFijos ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Aún no tienes gastos fijos</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
