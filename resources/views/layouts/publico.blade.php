{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/publico.blade.php
  QUÉ ES  : Plantilla pública: header + contenido + footer.
  CÓMO HACERLO / NOTAS:
    - Cada vista aporta su propio <main>.
  ==============================================================================
--}}

@extends('layouts.base')
@section('body')
  <x-header />
  @yield('content')
  <x-footer />
@endsection
