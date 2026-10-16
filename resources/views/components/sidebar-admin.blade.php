{{--
  ==============================================================================
  ARCHIVO : resources/views/components/sidebar-admin.blade.php
  QUÉ ES  : Menú lateral del administrador. Uso: <x-sidebar-admin />
  ESTADO  : Ajustes se MOVIÓ al menú del usuario de la barra superior
            (components/topbar.blade.php); ya no aparece aquí (2026-10-06, Juan).
            Logo reemplazado por <x-logo /> (2026-10-16, J7).
  QUÉ HAY QUE HACER (en orden):
    1. Las rutas admin.usuarios, admin.categorias y admin.estadisticas ya existen
       con middleware rol:administrador (J7).
    2. El administrador no tiene finanzas personales (decisión #20).
  CÓMO HACERLO / NOTAS:
    - No incluye ningún enlace financiero a propósito (regla 26).
    - El ítem activo se marca con request()->routeIs('patrón*') SIN punto antes del
      asterisco: así coincide con la ruta base y con sus sub-rutas.
  ==============================================================================
--}}

{{-- Menú lateral del administrador: solo administración (Ajustes está en el menú del usuario; sin páginas financieras). --}}
<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between py-space-lg px-space-md">
  <div class="flex flex-col gap-space-lg">
    <div class="flex items-center gap-space-sm px-space-sm">
      <x-logo :alto="36" />
    </div>
    <div class="px-space-xs"></div>
    <nav
      class="flex flex-col gap-1"
      data-active-classes="bg-primary-container text-on-primary-container font-semibold rounded-lg shadow-sm"
    >
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.usuarios*') ? ' nav-active' : '' }}"
        href="{{ route('admin.usuarios') }}"
      >Usuarios y Roles</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.categorias*') ? ' nav-active' : '' }}"
        href="{{ route('admin.categorias') }}"
      >Categorías del Sistema</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.estadisticas*') ? ' nav-active' : '' }}"
        href="{{ route('admin.estadisticas') }}"
      >Estadísticas</a>
    </nav>
  </div>
  <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
    <div class="flex items-center gap-space-xs text-primary">
      <span class="material-symbols-outlined text-[18px]">verified_user</span>
      <span class="font-label-md text-label-md">Panel admin</span>
    </div>
    <p class="font-body-sm text-body-sm text-on-surface-variant">
      Solo conteos agregados. Nunca datos financieros individuales.
    </p>
  </div>
</aside>