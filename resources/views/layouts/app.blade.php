{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/app.blade.php
  QUÉ ES  : Plantilla de la zona privada (estándar y familiar): menú lateral + barra superior.
  QUÉ HAY QUE HACER (en orden):
    1. Registrar los middleware auth y cuenta.activa en el grupo de rutas que use esta plantilla.
  CÓMO HACERLO / NOTAS:
    - .nav-backdrop es el fondo oscuro del menú móvil; shell.js lo cierra al tocarlo.
    - El contenedor lleva pt-16 y min-h-screen para compensar el topbar fijo (h-16).
  ==============================================================================
--}}

@extends('layouts.base')

@section('body')
  {{-- Menú lateral (según rol) + barra superior; cada página aporta su <main> --}}
  <x-sidebar />

  <div class="nav-backdrop" data-nav-close></div>

  <div class="pl-72 pt-16 min-h-screen">
    <x-topbar />
    @yield('content')
  </div>
@endsection