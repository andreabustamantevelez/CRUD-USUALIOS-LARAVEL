{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/iniciar-sesion.blade.php
  QUÉ ES  : Formulario de inicio de sesión. Campos tomados del mismo markup del registro (no hay
            code.html propio).
  RUTA    : GET /iniciar-sesion (login) · POST (login.entrar) · middleware guest
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $errors / old()
  ESTADO  : HECHO (J3, 2026-10-06, Juan). Controlador: Auth\LoginController. Validación e
            intentos: Auth\LoginRequest.
  BASE DE DATOS: users (email, password, activo). El campo del formulario se llama "correo".
  QUÉ SE HIZO:
    - Auth::attempt; error de credenciales con el texto exacto "Usuario o contraseña incorrectos"
      (sin decir cuál falló).
    - Cuenta suspendida (activo = false): se rechaza solo si la contraseña era correcta.
    - Límite: 5 intentos fallidos por minuto por correo + IP (después, mensaje de espera).
    - Redirección: estandar/familiar -> panel; administrador -> admin.estadisticas (o a la URL
      protegida que intentaron abrir).
    - Pendiente: login con identificación (decisión #1) no se hizo; se entra solo con correo.
  CÓMO HACERLO / NOTAS:
    - Logout: POST a la ruta "logout" (botón de la barra superior).
    - Si hay diseño original de esta pantalla, reemplazar el contenido conservando @csrf, name= y
      @error.
  CÓMO SE COMPRUEBA:
    - Login válido por cada rol; inválido; cuenta suspendida; cerrar sesión.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Iniciar sesión · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/iniciar-sesion.css') }}">
@endpush
@section('content')
  <main class="min-h-screen flex items-center justify-center px-space-md pt-28 pb-16 bg-surface">
    <div class="bg-surface-container-lowest shadow-xl rounded-xl p-6 sm:p-10 w-full max-w-md relative overflow-hidden">
      <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
      <div class="relative z-10 flex flex-col space-y-3 mb-space-lg">
        <x-logo :alto="48" />
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Iniciar sesión</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Entra a tu panel de Cuentas Claras.</p>
      </div>
      @if(session('estado'))
        <div class="relative z-10 mb-4 p-3 rounded-lg bg-primary/10 text-primary font-body-sm text-body-sm">
          {{ session('estado') }}
        </div>
      @endif
      <form class="relative z-10 flex flex-col space-y-4" method="POST" action="{{ route('login.entrar') }}">
        @csrf
        {{-- Mensaje de error de credenciales: "Usuario o contraseña incorrectos" (lo envía el backend en $errors) --}}
        <div>
          <label
            class="block font-label-md text-label-md text-on-surface mb-1.5"
            for="correo"
          >Correo electrónico <span class="text-error">*</span></label>
          <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">mail</span>
            <input
              class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
              id="correo"
              name="correo"
              value="{{ old('correo') }}"
              placeholder="tu@correo.com"
              required
              type="email"
            >
          </div>
          @error('correo')
            <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
            </p>
          @enderror
        </div>
        <div>
          <label
            class="block font-label-md text-label-md text-on-surface mb-1.5"
            for="password"
          >Contraseña <span class="text-error">*</span></label>
          <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">lock</span>
            <input
              class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
              id="password"
              name="password"
              placeholder="••••••••••••"
              required
              type="password"
            >
            <button
              class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
              onclick="togglePasswordVisibility('password', 'icon-password')"
              title="Mostrar u ocultar"
              type="button"
            ><span class="material-symbols-outlined text-lg" id="icon-password">visibility</span></button>
          </div>
          @error('password')
            <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
            </p>
          @enderror
        </div>
        <button
          class="w-full h-12 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
          type="submit"
        ><span>Iniciar sesión</span><span class="material-symbols-outlined text-xl">arrow_forward</span></button>
      </form>
      <div class="relative z-10 mt-5 text-center font-body-sm text-body-sm text-on-surface-variant">
        ¿Olvidaste tu contraseña? <a class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4" href="{{ route('password.request') }}">Recupérala</a> · ¿Sin cuenta? <a class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4" href="{{ route('registro') }}">Crear cuenta gratis</a>
      </div>
    </div>
  </main>
@endsection
