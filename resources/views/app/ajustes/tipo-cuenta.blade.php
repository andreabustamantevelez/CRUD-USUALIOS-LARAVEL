{{--
  ==============================================================================
  ARCHIVO : resources/views/app/ajustes/tipo-cuenta.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Ajustes · Tipo de cuenta" (SIN diseño original
            todavía: el contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /ajustes/tipo-cuenta (ajustes.tipo-cuenta) · PUT ajustes.tipo-cuenta.update ·
            Controlador: AjustesController@tipoCuenta
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - auth()->user()
    - $tieneMetasActivas
  BASE DE DATOS: usuarios (rol), metas_ahorro
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
    7. Estándar → familiar siempre se permite.
    8. Familiar → estándar bloqueado con metas activas: "Debes finalizar tus metas compartidas
       activas antes de cambiar tu tipo de cuenta".
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/ajustes.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Ajustes · Tipo de cuenta · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Ajustes · Tipo de cuenta</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Estándar o familiar</p>
      </header>
      <section class="card p-space-lg" data-dinamico="usuario">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Tipo de cuenta actual</h2>
        {{-- DINÁMICO: usuario --}}
        @forelse(($usuario ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Contenido pendiente de conectar</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
