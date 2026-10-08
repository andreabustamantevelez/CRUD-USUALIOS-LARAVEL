{{--
  Tabs de navegación de la sección Ajustes.
  Uso: @include('app.ajustes._tabs')
  Dueño: Valeria (V4). No tiene datos; solo dibuja los 3 enlaces.
--}}
<nav class="flex gap-2 mb-space-lg flex-wrap" aria-label="Secciones de Ajustes">
  <a href="{{ route('ajustes.perfil') }}"
     class="btn {{ request()->routeIs('ajustes.perfil') ? 'btn-primary' : 'btn-secondary' }}">Perfil</a>
  <a href="{{ route('ajustes.tipo-cuenta') }}"
     class="btn {{ request()->routeIs('ajustes.tipo-cuenta') ? 'btn-primary' : 'btn-secondary' }}">Tipo de cuenta</a>
  <a href="{{ route('ajustes.eliminar-cuenta') }}"
     class="btn {{ request()->routeIs('ajustes.eliminar-cuenta') ? 'btn-primary' : 'btn-secondary' }}">Eliminar cuenta</a>
</nav>