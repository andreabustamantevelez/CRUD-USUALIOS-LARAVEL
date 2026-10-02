{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/restablecer-contrasena.blade.php
  QUÉ ES  : Pantalla a la que llega el usuario desde el correo para crear una nueva contraseña.
  RUTA    : GET /restablecer-contrasena/{token} (password.reset) · POST (password.update)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $token (de la ruta)
    - $errors / old()
  BASE DE DATOS: password_reset_tokens, usuarios.password.
  QUÉ HAY QUE HACER (en orden):
    1. Pasar $token desde el controlador (view(..., ['token' => $token])).
    2. Validar token + correo + password confirmed (mínimo 8) con Password::reset.
    3. Éxito: mensaje y redirección a login. Enlace vencido: avisar con botón para pedir otro.
  CÓMO HACERLO / NOTAS:
    - El campo correo debería venir prellenado y de solo lectura.
  CÓMO SE COMPRUEBA:
    - Token válido, vencido y reutilizado.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Nueva contraseña · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/restablecer-contrasena.css') }}">
@endpush
@section('content')
  <main class="min-h-screen flex items-center justify-center px-space-md pt-28 pb-16 bg-surface">
    <div class="bg-surface-container-lowest shadow-xl rounded-xl p-6 sm:p-10 w-full max-w-md relative overflow-hidden">
      <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
      <div class="relative z-10 flex flex-col space-y-3 mb-space-lg">
        <img
          alt="Cuentas Claras"
          class="h-10 w-auto object-contain"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuDthDtnBs_dHctRok9VmvCXjbV9BPsv8HdZ9E8ncZmEz2ndmE38q5Ub56zSst9zoEJjGn4KGoWfe093ejcoBO8ELcf2SkCQKoNuZ3eFxm5st2RQkuO0Om0wGBb7KV7o6CSBQg65kN7Yj0cygELR839JbncGqb8EC2BYk-DVII5PtW-SHMjoADjfqBa7CvLWGbGHb3PN1-gewO4A6FW9f-waairOTuPY8SV8I_6ngC42VorFjBKPEYrkOdpQggZVX_7B7w"
        >
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Nueva contraseña</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Elige una contraseña segura.</p>
      </div>
      @if(session('estado'))
        <div class="relative z-10 mb-4 p-3 rounded-lg bg-primary/10 text-primary font-body-sm text-body-sm">
          {{ session('estado') }}
        </div>
      @endif
      <form
        class="relative z-10 flex flex-col space-y-4"
        method="POST"
        action="{{ route('password.update') }}"
      >
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">
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
          >Nueva contraseña <span class="text-error">*</span></label>
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
        <div>
          <label
            class="block font-label-md text-label-md text-on-surface mb-1.5"
            for="password_confirmation"
          >Confirmar contraseña <span class="text-error">*</span></label>
          <div class="relative flex items-center">
            <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">lock_reset</span>
            <input
              class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
              id="password_confirmation"
              name="password_confirmation"
              placeholder="••••••••••••"
              required
              type="password"
            >
            <button
              class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
              onclick="togglePasswordVisibility('password_confirmation', 'icon-password_confirmation')"
              title="Mostrar u ocultar"
              type="button"
            ><span class="material-symbols-outlined text-lg" id="icon-password_confirmation">visibility</span></button>
          </div>
          @error('password_confirmation')
            <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
              <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
            </p>
          @enderror
        </div>
        <button
          class="w-full h-12 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
          type="submit"
        ><span>Nueva contraseña</span><span class="material-symbols-outlined text-xl">arrow_forward</span></button>
      </form>
      <div class="relative z-10 mt-5 text-center font-body-sm text-body-sm text-on-surface-variant"></div>
    </div>
  </main>
@endsection
