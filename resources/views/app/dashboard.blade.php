{{--
  ==============================================================================
  ARCHIVO : resources/views/app/dashboard.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Dashboard principal" (SIN diseño original todavía: el
            contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /panel  (nombre: panel) · middleware auth + cuenta.activa + rol:estandar,familiar
            · Controlador: DashboardController@index
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $metricas (ingreso, gastos del mes, disponible, fijos pendientes)
    - $distribucion (% real 50/30/20 del mes)
    - $proximos (máx. 5 gastos fijos por vencer)
    - $ultimosGastos (5 más recientes)
    - $notificaciones (3 pendientes)
  BASE DE DATOS: usuarios, gastos, pagos_gasto_fijo, notificaciones, participaciones_meta
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
    7. Disponible del mes = ingreso mensual − gastos del mes (decisión #14: no guardar saldo).
    8. La tarjeta de metas se dibuja SOLO si el rol es familiar.
    9. Usuario nuevo: tarjeta de bienvenida con 3 pasos (ingreso, primer gasto, primer gasto
       fijo).
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/dashboard-principal.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Dashboard principal · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard-principal.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Dashboard principal</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Resumen de tu mes</p>
      </header>
      <section class="card p-space-lg" data-dinamico="metricas">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Métricas del mes</h2>
        {{-- DINÁMICO: metricas --}}
        @forelse(($metricas ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Aún no tienes datos este mes</p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="distribucion">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Distribución 50/30/20</h2>
        {{-- DINÁMICO: distribucion --}}
        @forelse(($distribucion ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">
            No hay datos suficientes para generar el informe
          </p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="proximos">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Próximos vencimientos</h2>
        {{-- DINÁMICO: proximos --}}
        @forelse(($proximos ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">No tienes vencimientos próximos</p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="ultimosGastos">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Últimos gastos</h2>
        {{-- DINÁMICO: ultimosGastos --}}
        @forelse(($ultimosGastos ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">
            No hay gastos registrados en este periodo
          </p>
        @endforelse
      </section>
      <section class="card p-space-lg" data-dinamico="notificaciones">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Notificaciones recientes
        </h2>
        {{-- DINÁMICO: notificaciones --}}
        @forelse(($notificaciones ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">No tienes notificaciones pendientes</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
