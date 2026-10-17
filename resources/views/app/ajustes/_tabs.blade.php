{{--
  Tabs de navegación de la sección Ajustes.
  Uso: @include('app.ajustes._tabs')
  Dueño: Valeria (V4). No tiene datos; solo dibuja los 3 enlaces.
--}}
<nav class="ajustes-tabs" aria-label="Secciones de Ajustes">
  <a href="{{ route('ajustes.perfil') }}"
     class="ajustes-tab {{ request()->routeIs('ajustes.perfil') ? 'ajustes-tab--activa' : '' }}"
     @if (request()->routeIs('ajustes.perfil')) aria-current="page" @endif>
    <span class="material-symbols-outlined text-[18px]">person</span>
    <span>Perfil</span>
  </a>
  <a href="{{ route('ajustes.tipo-cuenta') }}"
     class="ajustes-tab {{ request()->routeIs('ajustes.tipo-cuenta') ? 'ajustes-tab--activa' : '' }}"
     @if (request()->routeIs('ajustes.tipo-cuenta')) aria-current="page" @endif>
    <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
    <span>Tipo de cuenta</span>
  </a>
  <a href="{{ route('ajustes.eliminar-cuenta') }}"
     class="ajustes-tab {{ request()->routeIs('ajustes.eliminar-cuenta') ? 'ajustes-tab--activa' : '' }}"
     @if (request()->routeIs('ajustes.eliminar-cuenta')) aria-current="page" @endif>
    <span class="material-symbols-outlined text-[18px]">delete</span>
    <span>Eliminar cuenta</span>
  </a>
</nav>