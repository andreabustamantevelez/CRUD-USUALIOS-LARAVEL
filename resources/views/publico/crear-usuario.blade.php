{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/crear-usuario.blade.php
  QUÉ ES  : Formulario de registro (diseño original). Pantalla completa sin header/footer.
  RUTA    : GET /crear-usuario (registro) · POST /crear-usuario (registro.guardar) · middleware
            guest
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - old() y $errors (los pone Laravel solo)
    - request('tipo') opcional: estandar | familiar (viene de la landing)
  ESTADO  : HECHO (J3, 2026-10-06, Juan). Controlador: Auth\RegistroController. Validación:
            Auth\RegistroRequest.
  BASE DE DATOS: users: nombre, cedula (viene del campo "identificacion"), email (viene del campo
            "correo"), password (hash), rol (= tipo_cuenta: estandar|familiar), activo=true,
            consentimiento_datos_at.
  QUÉ SE HIZO:
    - El formulario NO cambia sus name=; el controlador traduce identificacion -> cedula y
      correo -> email.
    - Mensajes exactos: "Este correo ya se encuentra registrado" y "Registro exitoso".
    - Tras crear la cuenta se inicia sesión y se va al panel.
    - Decisión #1: la identificación se conserva (users.cedula, única).
    - La página legal "politica" es el enlace de la casilla de consentimiento.
  CÓMO HACERLO / NOTAS:
    - No hay modo demo: se eliminó el botón y la ruta "demo".
    - tipo_cuenta es un campo oculto que el JS (crear-usuario.js) actualiza al tocar cada tarjeta.
    - El medidor de contraseña y el ojo son ayudas visuales; la validación REAL es del servidor.
  CÓMO SE COMPRUEBA:
    - Registro válido estándar y familiar; correo repetido; contraseñas distintas; sin
      consentimiento.
    - Tras un error se conservan nombre, identificación y correo (old).
  ==============================================================================
--}}

@extends('layouts.base')
@section('titulo', 'Crear usuario · Cuentas Claras')
@section('body-class', 'bg-surface font-body-md text-body-md text-on-surface antialiased min-h-screen flex flex-col justify-center items-center')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/crear-usuario.css') }}">
@endpush
@section('body')
  <main class="w-full flex justify-center items-center p-margin-mobile md:p-margin">
    <div class="flex flex-col w-full">
      <div class="w-full max-w-7xl mx-auto py-space-sm md:py-space-md">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-stretch">
          <!-- Main Registration Form (8 Cols) -->
          <section class="lg:col-span-7 xl:col-span-8 flex flex-col justify-center">
            <div class="bg-surface-container-lowest shadow-xl rounded-xl p-6 sm:p-10 md:p-12 relative overflow-hidden">
              <!-- Subtle ambient glow inside card -->
              <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none -z-0"></div>
              <!-- Form Header -->
              <div class="relative z-10 flex flex-col items-start space-y-3 mb-space-lg">
                <img
                  alt="Cuentas Claras"
                  class="h-10 w-auto object-contain"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDsPXFJFUBfIdCkden6QFlZBL_VoX-pn5QXMhZzksGuqRGH8G9VV3YUK5cJnDkNNI8vhoUygpeW0N2t6fEOIzgq5nbZ-uaSQU4vR1fAzrnKt2X5tg4Mw6_WL_1JCsA7G11Vpe1gmVRroy14aWi6ZREFMVpuaPhVU_zjLf6jXUSClaev2iAJF6UBhGXnQxWqYRRU-PaVjzW7LWimOfTX-SO8wESOk45ZZrqO0XQuU5eZPeK_IuCibtq29xbSDWzew_8Uw"
                >
                <div class="pt-2">
                  <h1 class="font-headline-xl text-headline-xl text-on-surface">Únete a Cuentas Claras</h1>
                  <p class="font-body-md text-body-md text-secondary mt-1 max-w-xl">
                    Comienza a organizar tus finanzas personales o familiares en menos de 2 minutos.
                  </p>
                </div>
                <div class="inline-flex items-center gap-1.5 pt-1">
                  <span class="font-body-sm text-body-sm text-secondary">¿Ya tienes una cuenta?</span>
                  <a
                    class="font-label-md text-label-md text-primary hover:text-primary-container transition-colors duration-150 underline decoration-2 underline-offset-4"
                    href="{{ route('login') }}"
                  > Iniciar Sesión </a>
                </div>
              </div>
              <!-- Account Type Selector (RQF-01 vs RQF-02) -->
              <div class="relative z-10 mb-space-lg">
                <label class="block font-label-md text-label-md text-on-surface mb-2.5"> Selecciona el perfil de tu cuenta <span class="text-error">*</span> </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="account-type-group">
                  <!-- Option A: Standard (Default) -->
                  <div
                    class="cursor-pointer rounded-lg p-4 bg-surface-container-low transition-all duration-200 shadow-sm relative flex flex-col justify-between group"
                    id="card-standard"
                    onclick="selectAccountType('standard')"
                  >
                    <div>
                      <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2.5">
                          <div class="w-9 h-9 rounded-md bg-primary-container text-on-primary flex items-center justify-center shadow-xs">
                            <span class="material-symbols-outlined text-xl">person</span>
                          </div>
                          <span class="font-headline-sm text-headline-sm text-on-surface">Usuario Estándar</span>
                        </div>
                        <!-- Custom Radio -->
                        <div
                          class="w-5 h-5 rounded-full bg-primary flex items-center justify-center mt-1"
                          id="radio-standard"
                        >
                          <div class="w-2 h-2 rounded-full bg-surface-container-lowest"></div>
                        </div>
                      </div>
                      <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-3">
                        Control personal de gastos, alertas preventivas de servicios fijos, simulador de
                        crédito y diagnóstico 50/30/20.
                      </p>
                    </div>
                    <div class="mt-3.5 pt-2 flex items-center">
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Recomendado para uso personal </span>
                    </div>
                  </div>
                  <!-- Option B: Family / Collaborative -->
                  <div
                    class="cursor-pointer rounded-lg p-4 bg-surface-container transition-all duration-200 opacity-90 relative flex flex-col justify-between group hover:opacity-100"
                    id="card-family"
                    onclick="selectAccountType('family')"
                  >
                    <div>
                      <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2.5">
                          <div class="w-9 h-9 rounded-md bg-secondary-container text-on-secondary-fixed flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">diversity_1</span>
                          </div>
                          <span class="font-headline-sm text-headline-sm text-on-surface">Familiar / Colaborativo</span>
                        </div>
                        <!-- Custom Radio -->
                        <div
                          class="w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center mt-1"
                          id="radio-family"
                        >
                          <div class="w-2 h-2 rounded-full bg-surface-container-lowest opacity-0 transition-opacity"></div>
                        </div>
                      </div>
                      <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-3">
                        Todo lo del usuario estándar + metas de ahorro conjuntas e invitación a pareja o
                        familiar con privacidad blindada.
                      </p>
                    </div>
                    <div class="mt-3.5 pt-2 flex items-center">
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm"><span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Multiusuario protegido </span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Registration Form Fields -->
              <form
                class="relative z-10 flex flex-col space-y-4"
                method="POST"
                action="{{ route('registro.guardar') }}"
              >
                @csrf
                <input
                  hidden
                  type="hidden"
                  name="tipo_cuenta"
                  id="tipo_cuenta"
                  value="{{ old('tipo_cuenta', request('tipo', 'estandar')) }}"
                >
                @if($errors->any())
                  <div class="p-3 rounded-lg bg-error-container text-on-error-container font-body-sm text-body-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span>Revisa los campos marcados.</span>
                  </div>
                @endif
                <!-- Grid Row 1: Nombre + Cédula -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Full Name -->
                  <div>
                    <label
                      class="block font-label-md text-label-md text-on-surface mb-1.5"
                      for="fullName"
                    > Nombre Completo <span class="text-error">*</span> </label>
                    <div class="relative flex items-center">
                      <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">badge</span>
                      <input
                        class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                        id="fullName"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        placeholder="Ej. Andrea Gómez"
                        required=""
                        type="text"
                      >
                    </div>
                    @error('nombre')
                      <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                      </p>
                    @enderror
                  </div>
                  <!-- Identification / Cédula (CU-01) -->
                  <div>
                    <label
                      class="block font-label-md text-label-md text-on-surface mb-1.5"
                      for="documentId"
                    > Identificación / Cédula <span class="text-error">*</span> </label>
                    <div class="relative flex items-center">
                      <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">fingerprint</span>
                      <input
                        class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                        id="documentId"
                        name="identificacion"
                        value="{{ old('identificacion') }}"
                        placeholder="Número de documento de identidad"
                        required=""
                        type="text"
                      >
                    </div>
                    @error('identificacion')
                      <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                      </p>
                    @enderror
                  </div>
                </div>
                <!-- Email Address with uniqueness indicator -->
                <div>
                  <div class="flex items-center justify-between mb-1.5">
                    <label
                      class="font-label-md text-label-md text-on-surface"
                      for="email"
                    > Correo Electrónico <span class="text-error">*</span> </label>
                    <span class="font-label-sm text-label-sm text-secondary">Será tu acceso único al sistema</span>
                  </div>
                  <div class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">mail</span>
                    <input
                      class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                      id="email"
                      name="correo"
                      value="{{ old('correo') }}"
                      placeholder="andrea@ejemplo.com"
                      required=""
                      type="email"
                    >
                  </div>
                  @error('correo')
                    <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                      <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                    </p>
                  @enderror
                </div>
                <!-- Password & Confirmation Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Password -->
                  <div>
                    <label
                      class="block font-label-md text-label-md text-on-surface mb-1.5"
                      for="password"
                    > Contraseña <span class="text-error">*</span> </label>
                    <div class="relative flex items-center">
                      <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">lock</span>
                      <input
                        class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                        id="password"
                        name="password"
                        oninput="evaluatePasswordStrength(this.value)"
                        placeholder="••••••••••••"
                        required=""
                        type="password"
                      >
                      <button
                        class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                        onclick="togglePasswordVisibility('password', 'toggle-pwd-icon')"
                        title="Mostrar u ocultar contraseña"
                        type="button"
                      ><span class="material-symbols-outlined text-lg leading-none" id="toggle-pwd-icon">visibility</span> </button>
                    </div>
                    @error('password')
                      <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                      </p>
                    @enderror
                  </div>
                  <!-- Confirm Password -->
                  <div>
                    <label
                      class="block font-label-md text-label-md text-on-surface mb-1.5"
                      for="confirmPassword"
                    > Confirmar Contraseña <span class="text-error">*</span> </label>
                    <div class="relative flex items-center">
                      <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none">lock_reset</span>
                      <input
                        class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                        id="confirmPassword"
                        name="password_confirmation"
                        placeholder="••••••••••••"
                        required=""
                        type="password"
                      >
                      <button
                        class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                        onclick="togglePasswordVisibility('confirmPassword', 'toggle-conf-icon')"
                        title="Mostrar u ocultar confirmación"
                        type="button"
                      ><span class="material-symbols-outlined text-lg leading-none" id="toggle-conf-icon">visibility</span> </button>
                    </div>
                    @error('password_confirmation')
                      <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                      </p>
                    @enderror
                  </div>
                </div>
                <!-- Password Requirements Checklist / Strength Indicator -->
                <div class="p-3 bg-surface-container-low rounded-lg space-y-2">
                  <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Seguridad de la clave:</span>
                    <span
                      class="font-label-sm text-label-sm text-secondary font-bold"
                      id="strength-label"
                    >Mínima</span>
                  </div>
                  <div class="w-full h-1.5 bg-surface-container-highest rounded-full overflow-hidden flex gap-1">
                    <div
                      class="h-full w-1/3 bg-error rounded-full transition-all duration-300"
                      id="bar-1"
                    ></div>
                    <div
                      class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300"
                      id="bar-2"
                    ></div>
                    <div
                      class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300"
                      id="bar-3"
                    ></div>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 pt-1 text-on-surface-variant font-label-sm text-label-sm">
                    <span
                      class="flex items-center gap-1"
                      id="req-len"
                    ><span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span> Mín. 8 caracteres</span>
                    <span
                      class="flex items-center gap-1"
                      id="req-num"
                    ><span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span> Al menos 1 número</span>
                    <span
                      class="flex items-center gap-1"
                      id="req-sym"
                    ><span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span> Símbolo o mayúscula</span>
                  </div>
                </div>
                <!-- Consent & Legal Guarantee (RQNF-06 & Ley 1581) -->
                <div class="pt-2 space-y-3">
                  <label class="flex items-start gap-3 cursor-pointer group"><input name="consentimiento" value="1" {{ old('consentimiento') ? 'checked' : '' }} class="mt-1 w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer accent-primary" required="" type="checkbox"> <span class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed"> Acepto el tratamiento de mis datos personales de acuerdo con la <strong class="text-on-surface">Ley 1581 de 2012</strong> y la <a class="text-primary underline hover:text-primary-container" href="{{ route('politica') }}">Política de Privacidad</a> de Cuentas Claras. </span> </label>
                  @error('consentimiento')
                    <p class="font-body-sm text-body-sm text-error flex items-center gap-1">
                      <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                    </p>
                  @enderror
                  <!-- Confidentiality Note Banner -->
                  <div class="p-3.5 bg-surface-container-low rounded-lg flex items-start gap-3">
                    <span
                      class="material-symbols-outlined text-primary text-xl mt-0.5"
                      style="font-variation-settings: 'FILL' 1;"
                    >shield</span>
                    <div class="text-on-surface-variant font-body-sm text-body-sm leading-snug">
                      <strong class="text-on-surface">Tus datos son estrictamente confidenciales:</strong> Cuentas Claras <span class="underline">no</span> solicita claves bancarias, <span class="underline">no</span> realiza intermediación de captación financiera ni genera reportes a centrales de riesgo.
                    </div>
                  </div>
                </div>
                <!-- CTA Buttons -->
                <div class="pt-4 flex flex-col space-y-3">
                  <button
                    class="w-full h-12 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
                    type="submit"
                  ><span>Crear Mi Cuenta (Gratis)</span> <span class="material-symbols-outlined text-xl">arrow_forward</span> </button>
                  <!-- Subtle Divider -->
                </div>
              </form>
            </div>
          </section>
          <!-- Lateral Backing / Social Proof & Benefits Panel (4-5 Cols) -->
          <aside class="lg:col-span-5 xl:col-span-4 flex flex-col justify-between space-y-6">
            <!-- Value Highlights Card -->
            <div class="bg-surface-container-lowest shadow-md rounded-xl p-6 sm:p-8 flex flex-col justify-between flex-grow">
              <div>
                <div class="flex items-center gap-2 mb-4">
                  <span
                    class="material-symbols-outlined text-primary text-2xl"
                    style="font-variation-settings: 'FILL' 1;"
                  >verified</span>
                  <span class="font-label-md text-label-md text-primary uppercase tracking-wider">Tranquilidad Financiera Real</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-3">
                  Diseñado para sanar tu relación con el dinero.
                </h2>
                <p class="font-body-md text-body-md text-secondary mb-6 leading-relaxed">
                  Dile adiós a la incertidumbre al final de la quincena. Cuentas Claras automatiza tu panorama
                  para que tomes decisiones informadas sin fricción.
                </p>
                <!-- Feature Mosaic List -->
                <div class="space-y-4">
                  <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                    <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                      <span class="material-symbols-outlined text-lg">pie_chart</span>
                    </div>
                    <div>
                      <h3 class="font-headline-sm text-headline-sm text-on-surface">
                        Regla 50 / 30 / 20 Automática
                      </h3>
                      <p class="font-body-sm text-body-sm text-secondary">
                        Categoriza necesidades, deseos y ahorro con alertas en tiempo real.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                    <div class="w-8 h-8 rounded-md bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                      <span class="material-symbols-outlined text-lg">notifications_active</span>
                    </div>
                    <div>
                      <h3 class="font-headline-sm text-headline-sm text-on-surface">
                        Vencimientos sin sorpresas
                      </h3>
                      <p class="font-body-sm text-body-sm text-secondary">
                        Notificaciones 3 días antes de cada servicio o pago recurrente.
                      </p>
                    </div>
                  </div>
                  <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                    <div class="w-8 h-8 rounded-md bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
                      <span class="material-symbols-outlined text-lg">lock_person</span>
                    </div>
                    <div>
                      <h3 class="font-headline-sm text-headline-sm text-on-surface">
                        Espacio Compartido Blindado
                      </h3>
                      <p class="font-body-sm text-body-sm text-secondary">
                        Crea metas con tu pareja sin revelar gastos personales privados.
                      </p>
                    </div>
                  </div>
                </div>
                <!-- 50/30/20 Mini Visualization Widget -->
                <div class="mt-6 pt-5 border-t border-surface-container-highest">
                  <div class="flex justify-between items-center text-label-sm font-label-sm text-secondary mb-2">
                    <span>Tu equilibrio ideal</span>
                    <span class="text-primary font-bold">100% Bajo control</span>
                  </div>
                  <div class="h-3 w-full bg-surface-container rounded-full overflow-hidden flex">
                    <div class="h-full bg-primary" style="width: 50%" title="50% Necesidades"></div>
                    <div
                      class="h-full bg-tertiary-container"
                      style="width: 30%"
                      title="30% Discrecional"
                    ></div>
                    <div
                      class="h-full bg-primary-fixed-dim"
                      style="width: 20%"
                      title="20% Ahorro e Inversión"
                    ></div>
                  </div>
                  <div class="flex justify-between items-center text-label-sm font-label-sm text-secondary mt-2 px-0.5">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span> 50% Fijos</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary-container"></span> 30% Gustos</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-fixed-dim"></span> 20% Ahorro</span>
                  </div>
                </div>
              </div>
              <!-- User Testimonial -->
              <div class="mt-6 pt-5 bg-surface-container-high/40 rounded-lg p-4">
                <div class="flex items-center gap-1 text-primary mb-2">
                  <span
                    class="material-symbols-outlined text-sm"
                    style="font-variation-settings: 'FILL' 1;"
                  >star</span>
                  <span
                    class="material-symbols-outlined text-sm"
                    style="font-variation-settings: 'FILL' 1;"
                  >star</span>
                  <span
                    class="material-symbols-outlined text-sm"
                    style="font-variation-settings: 'FILL' 1;"
                  >star</span>
                  <span
                    class="material-symbols-outlined text-sm"
                    style="font-variation-settings: 'FILL' 1;"
                  >star</span>
                  <span
                    class="material-symbols-outlined text-sm"
                    style="font-variation-settings: 'FILL' 1;"
                  >star</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant italic">
                  "Con mi novio planeamos nuestro primer viaje al exterior gracias a las metas compartidas. Lo
                  mejor es que cada uno mantiene su privacidad financiera intacta."
                </p>
                <div class="mt-3 flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-sm text-headline-sm text-xs">
                    MC
                  </div>
                  <div class="flex flex-col">
                    <span class="font-label-md text-label-md text-on-surface leading-none">Mariana &amp; Carlos</span>
                    <span class="font-label-sm text-label-sm text-secondary">Usuarios Plan Familiar • Medellín</span>
                  </div>
                </div>
              </div>
            </div>
            <!-- Security and Regulation Badge Footnote -->
            <div class="p-4 rounded-lg bg-surface-container-low flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-secondary text-xl">gavel</span>
                <span class="font-label-sm text-label-sm text-secondary">Cumplimiento Habeas Data Ley 1581</span>
              </div>
              <span class="font-label-sm text-label-sm text-primary font-bold">Cifrado AES-256</span>
            </div>
          </aside>
        </div>
      </div>
      <!-- Inline Script for UI Micro-Interactions -->
    </div>
  </main>
@endsection
@push('scripts')
  <script src="{{ asset('js/crear-usuario.js') }}"></script>
@endpush
