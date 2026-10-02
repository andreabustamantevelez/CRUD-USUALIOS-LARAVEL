{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/recuperar-contrasena.blade.php
  QUÉ ES  : Solicitud de enlace para restablecer contraseña.
  RUTA    : GET /recuperar-contrasena (password.request) · POST (password.email)
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - session('estado')
  BASE DE DATOS: password_reset_tokens (tabla estándar de Laravel).
  QUÉ HAY QUE HACER (en orden):
    1. Usar el broker de contraseñas de Laravel (Password::sendResetLink).
    2. Mostrar SIEMPRE el mismo mensaje exista o no el correo: "Si el correo está registrado, te
       enviamos un enlace".
    3. Configurar el correo (MAIL_* en .env; en desarrollo usar Mailtrap o log).
    4. Personalizar la notificación de correo con el logo y colores de la marca.
  CÓMO HACERLO / NOTAS:
    - El enlace caduca (60 min por defecto) y es de un solo uso.
  CÓMO SE COMPRUEBA:
    - Correo existente e inexistente dan el mismo mensaje.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Recuperar contraseña · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/recuperar-contrasena.css') }}">
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
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Recuperar contraseña</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">
          Te enviaremos un enlace para restablecerla.
        </p>
      </div>
      @if(session('estado'))
        <div class="relative z-10 mb-4 p-3 rounded-lg bg-primary/10 text-primary font-body-sm text-body-sm">
          {{ session('estado') }}
        </div>
      @endif
      <form
        class="relative z-10 flex flex-col space-y-4"
        method="POST"
        action="{{ route('password.email') }}"
      >
        @csrf
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
        <button
          class="w-full h-12 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
          type="submit"
        ><span>Recuperar contraseña</span><span class="material-symbols-outlined text-xl">arrow_forward</span></button>
      </form>
      <div class="relative z-10 mt-5 text-center font-body-sm text-body-sm text-on-surface-variant">
        {{-- Mensaje neutro tras enviar (session estado) --}}
        <a
          class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4"
          href="{{ route('login') }}"
        >Volver a iniciar sesión</a>
      </div>
    </div>
  </main>
@endsection
