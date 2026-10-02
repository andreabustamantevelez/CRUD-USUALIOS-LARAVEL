{{--
  ==============================================================================
  ARCHIVO : resources/views/admin/categorias.blade.php
  QUÉ ES  : Estructura Blade de la pantalla "Categorías del sistema" (SIN diseño original todavía:
            el contenido es una plantilla con zonas dinámicas).
  RUTA    : GET /admin/categorias (admin.categorias) · POST/PUT/PATCH desactivar · Controlador:
            Admin\CategoriaSistemaController
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $categorias (usuario_id NULL)
  BASE DE DATOS: categorias_gasto
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
    7. Desactivar en vez de borrar para no romper gastos ya clasificados.
  CÓMO HACERLO / NOTAS:
    - Los textos de "estado vacío" ya son los de la especificación.
    - El CSS exclusivo de esta pantalla va en public/css/admin-categorias.css.
  CÓMO SE COMPRUEBA:
    - Entrar con un usuario sin datos: se ve el texto de estado vacío.
    - Entrar con dos usuarios: ninguno ve datos del otro.
    - Probar en 1440, 768 y 390 px sin scroll horizontal.
  ==============================================================================
--}}

@extends('layouts.admin')
@section('titulo', 'Categorías del sistema · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-categorias.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      {{-- DISEÑO PENDIENTE: reemplazar por el code.html original de esta pantalla --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Categorías del sistema</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Crear, editar y desactivar</p>
      </header>
      <section class="card p-space-lg" data-dinamico="categorias">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Categorías globales</h2>
        {{-- DINÁMICO: categorias --}}
        @forelse(($categorias ?? []) as $item)
          {{-- Aquí va el diseño de un elemento --}}
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">Aún no hay categorías del sistema</p>
        @endforelse
      </section>
    </div>
  </main>
@endsection
