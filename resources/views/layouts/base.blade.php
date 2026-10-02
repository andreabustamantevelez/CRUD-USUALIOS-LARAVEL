{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/base.blade.php
  QUÉ ES  : Plantilla raíz: <head>, CSS global, toast y scripts. Todas las demás la extienden.
  QUÉ HAY QUE HACER (en orden):
    1. Si se migra a Vite + Tailwind, cambiar el <link> de style.css por @vite([...]).
    2. Agregar favicon y meta description.
    3. Alojar localmente la fuente Plus Jakarta Sans y Material Symbols si no se quiere depender
       de Google Fonts.
  CÓMO HACERLO / NOTAS:
    - Cada vista agrega su CSS con @push('styles') y sus scripts con @push('scripts').
  ==============================================================================
--}}

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
      @yield('titulo', 'Cuentas Claras')
    </title>
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    >
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
      rel="stylesheet"
    >
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
  </head>
  <body class="@yield('body-class', 'bg-surface font-body-md text-body-md text-on-surface antialiased')">
    @yield('body')
    <x-toast />
    <script src="{{ asset('js/shell.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
