{{--
  ==============================================================================
  ARCHIVO : resources/views/components/header.blade.php
  QUÉ ES  : Header público fijo (landing, login, registro, recuperar). Uso: <x-header />
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno (usa route()).
  QUÉ HAY QUE HACER (en orden):
    1. Mostrar "Ir a mi panel" cuando haya sesión (@auth) y los botones actuales solo a invitados
       (@guest).
    2. Cambiar el logo externo por asset('img/logo.png').
    3. El panel móvil se abre con el botón hamburguesa (public/js/shell.js).
  CÓMO HACERLO / NOTAS:
    - Los enlaces ancla apuntan a route('inicio')#seccion para que funcionen desde cualquier
      página pública.
  ==============================================================================
--}}

{{-- Header público (landing, login, registro, recuperar). Los enlaces ancla apuntan a secciones de la landing. --}}
<header class="fixed top-0 left-0 w-full z-50 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(15,23,42,0.04)]">
  <div class="h-20 max-w-7xl mx-auto px-margin flex items-center justify-between gap-space-md">
    <div class="flex items-center gap-space-md">
      <a
        class="flex items-center gap-space-sm"
        data-path="landing-page"
        href="{{ route('inicio') }}"
      ><img alt="Cuentas Claras" class="h-10 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDthDtnBs_dHctRok9VmvCXjbV9BPsv8HdZ9E8ncZmEz2ndmE38q5Ub56zSst9zoEJjGn4KGoWfe093ejcoBO8ELcf2SkCQKoNuZ3eFxm5st2RQkuO0Om0wGBb7KV7o6CSBQg65kN7Yj0cygELR839JbncGqb8EC2BYk-DVII5PtW-SHMjoADjfqBa7CvLWGbGHb3PN1-gewO4A6FW9f-waairOTuPY8SV8I_6ngC42VorFjBKPEYrkOdpQggZVX_7B7w"></a>
    </div>
    <nav
      class="hidden lg:flex items-center gap-space-xs"
      data-active-classes="bg-surface-container-high text-on-surface rounded-lg"
    >
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="como-funciona"
        href="{{ route('inicio') }}#como-funciona"
      >¿Cómo funciona?</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="caracteristicas"
        href="{{ route('inicio') }}#caracteristicas"
      >Características</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="simulador"
        href="{{ route('inicio') }}#simulador"
      >Simulador</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="metas-familiares"
        href="{{ route('inicio') }}#metas-familiares"
      >Metas Familiares</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="seguridad"
        href="{{ route('inicio') }}#seguridad"
      >Seguridad</a>
    </nav>
    <div class="flex items-center gap-space-sm">
      <a
        class="px-space-md py-space-xs rounded-lg text-on-surface hover:bg-surface-container hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-all"
        data-path="iniciar-sesion"
        href="{{ route('login') }}"
      >Iniciar Sesión</a>
      <a
        class="inline-flex items-center px-space-md py-space-xs rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md whitespace-nowrap transition-all shadow-[0_1px_3px_rgba(15,23,42,0.04)]"
        data-path="crear-cuenta"
        href="{{ route('registro') }}"
      >Crear Cuenta Gratis</a>
      <button
        type="button"
        data-menu-toggle
        aria-label="Abrir menú"
        class="lg:hidden p-space-xs rounded-lg text-on-surface hover:bg-surface-container flex items-center justify-center"
      ><span class="material-symbols-outlined text-[24px]">menu</span></button>
      <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 ml-space-xs">
        <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
      </div>
    </div>
  </div>
  <div
    data-menu-panel
    class="hidden lg:hidden flex flex-col gap-space-xs px-margin py-space-md border-t border-outline-variant/30 bg-surface"
  >
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#como-funciona"
    >¿Cómo funciona?</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#caracteristicas"
    >Características</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#simulador"
    >Simulador</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#metas-familiares"
    >Metas Familiares</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#seguridad"
    >Seguridad</a>
  </div>
</header>
