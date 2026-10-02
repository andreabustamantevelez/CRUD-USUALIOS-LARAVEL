{{--
  ==============================================================================
  ARCHIVO : resources/views/components/topbar.blade.php
  QUÉ ES  : Barra superior de las páginas internas. Uso: <x-topbar />
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - auth()->user()->nombre y ->rol
    - $notificacionesPendientes (entero)
  QUÉ HAY QUE HACER (en orden):
    1. Compartir $notificacionesPendientes en todas las vistas con un View Composer
       (app/Providers) que cuente las notificaciones pendientes del usuario.
    2. Crear la ruta POST "logout" (cierra sesión y redirige al login).
    3. Opcional: menú del usuario con enlaces a Ajustes.
  CÓMO HACERLO / NOTAS:
    - El punto rojo parpadeante solo aparece si hay pendientes.
  ==============================================================================
--}}

{{-- Barra superior: nuevo gasto, campana (punto solo con pendientes), usuario real y cerrar sesión. --}}
<header class="fixed top-0 left-72 right-0 h-16 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-margin">
  <div class="flex items-center gap-space-md">
    <button
      type="button"
      data-nav-toggle
      aria-label="Abrir menú"
      class="lg:hidden p-space-xs rounded-lg text-on-surface-variant hover:bg-surface-container flex items-center justify-center"
    ><span class="material-symbols-outlined text-[24px]">menu</span></button>
  </div>
  <div class="flex items-center gap-space-lg">
    <a
      class="hidden sm:inline-flex items-center gap-1.5 py-1.5 px-space-md bg-surface-container hover:bg-surface-container-high text-primary rounded-lg font-label-md text-label-md transition-colors"
      href="{{ route('gastos.create') }}"
    ><span class="material-symbols-outlined text-[18px]">add</span><span>Nuevo Gasto</span></a>
    <a
      class="relative p-space-xs rounded-full text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors flex items-center justify-center"
      href="{{ route('notificaciones.index') }}"
    >
      <span class="material-symbols-outlined text-[24px]">notifications</span>
      @if(($notificacionesPendientes ?? 0) > 0)
        <span class="absolute top-1 right-1 flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-error"></span></span>
      @endif
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button
        type="submit"
        title="Cerrar sesión"
        class="p-space-xs rounded-full text-on-surface-variant hover:bg-surface-container flex items-center justify-center"
      ><span class="material-symbols-outlined text-[22px]">logout</span></button>
    </form>
    <div class="flex items-center gap-space-md pl-space-md">
      <div class="text-right hidden md:block">
        <div class="font-label-md text-label-md text-on-surface">
          {{ auth()->user()->nombre ?? 'Usuario' }}
        </div>
        <div class="font-label-sm text-label-sm text-on-surface-variant">
          {{ ucfirst(auth()->user()->rol ?? 'estandar') }}
        </div>
      </div>
      <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-label-md font-bold ring-2 ring-primary/20">
        {{ mb_strtoupper(mb_substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
      </div>
    </div>
  </div>
</header>
