{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/base.blade.php
  QUÉ ES  : Plantilla raíz: <head>, CSS global, theme (claro/oscuro), toast y scripts.
            Todas las demás la extienden.
  ACTUALIZADO 2026-10-14:
    - Incluye public/css/theme.css (modo oscuro).
    - Incluye public/css/transiciones.css (fade-in + anti-reflow + view transitions).
    - Incluye un script inline en <head> para aplicar el tema ANTES de renderizar
      el body (evita el destello blanco al cargar en oscuro).
    - data-account-type en <html> para el tinte por tipo de cuenta.
    - Preconnect + preload de Google Fonts (evita el salto de fuente).
  ==============================================================================
--}}

@php
  // Tinte por tipo de cuenta (ver theme.css secciones 5 y 8).
  $rol = strtolower((string) optional(auth()->user())->rol);
  $accountType = match ($rol) {
      'familiar'      => 'familiar',
      'administrador' => 'administrador',
      default         => 'estandar',
  };
@endphp

<!DOCTYPE html>
<html
  lang="es"
  data-theme="claro"
  data-account-type="{{ $accountType }}"
>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
      @yield('titulo', 'Cuentas Claras')
    </title>

    {{-- Preconnect: abre la conexión TLS con Google Fonts antes de pedir nada --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Aplica el tema antes de pintar el body (evita el "flash" blanco) --}}
    <script>
      (function () {
        try {
          var t = localStorage.getItem('cc-tema');
          if (t !== 'oscuro' && t !== 'claro') {
            t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
              ? 'oscuro' : 'claro';
          }
          document.documentElement.setAttribute('data-theme', t);
          document.documentElement.style.colorScheme = (t === 'oscuro' ? 'dark' : 'light');
        } catch (e) {
          document.documentElement.setAttribute('data-theme', 'claro');
        }
      })();
    </script>

    {{--
      display=swap → Plus Jakarta Sans usa fallback y luego intercambia.
      display=block → Material Symbols queda invisible hasta que carga
                      (evita que se lea "menu", "settings"… como texto).
    --}}
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    >
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block"
      rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transiciones.css') }}">
    @stack('styles')
  </head>
  <body class="@yield('body-class', 'bg-surface font-body-md text-body-md text-on-surface antialiased')">
    @yield('body')
    <x-toast />
    <script src="{{ asset('js/transiciones.js') }}" defer></script>
    <script src="{{ asset('js/theme.js') }}" defer></script>
    <script src="{{ asset('js/shell.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>