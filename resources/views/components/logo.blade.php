{{--
  ==============================================================================
  COMPONENTE : resources/views/components/logo.blade.php
  QUÉ ES  : Logo de Cuentas Claras como SVG inline. Se adapta automáticamente
            al tema (claro/oscuro) y al tipo de cuenta (verde/morado/azul).
            Los colores vienen de variables CSS definidas en theme.css:
              --logo-icon-bg, --logo-icon-bg-soft, --logo-icon-fg,
              --logo-text, --logo-text-accent, --logo-muted
  USO :
      <x-logo />                       → logo completo, 36px de alto
      <x-logo alto="32" />             → tamaño custom
      <x-logo :texto="false" />        → solo el icono (sin texto, para espacios chicos)
  ==============================================================================
--}}

@props([
    'alto' => 36,
    'texto' => true,
])

<svg
    viewBox="0 0 200 48"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    class="logo-cc"
    style="height: {{ $alto }}px; width: auto; display: block;"
    role="img"
    aria-label="Cuentas Claras — Finanzas personales"
>
    {{-- Icono --}}
    <rect x="2" y="6" width="36" height="36" rx="10" fill="var(--logo-icon-bg)"/>
    <path
        d="M12 24C12 18.4772 16.4772 14 22 14C25.5 14 28.5 15.8 30.2 18.5"
        stroke="var(--logo-icon-fg)"
        stroke-width="3"
        stroke-linecap="round"
    />
    <path
        d="M28 24C28 29.5228 23.5228 34 18 34C14.5 34 11.5 32.2 9.8 29.5"
        stroke="var(--logo-icon-bg-soft)"
        stroke-width="3"
        stroke-linecap="round"
    />
    <circle cx="20" cy="24" r="3" fill="var(--logo-icon-fg)"/>

    {{-- Texto --}}
    @if ($texto)
        <text
            x="48"
            y="27"
            font-family="'Plus Jakarta Sans', system-ui, sans-serif"
            font-size="19"
            font-weight="700"
            fill="var(--logo-text)"
        >Cuentas<tspan fill="var(--logo-text-accent)">Claras</tspan></text>

        <text
            x="48"
            y="38"
            font-family="'Plus Jakarta Sans', system-ui, sans-serif"
            font-size="9"
            font-weight="600"
            fill="var(--logo-muted)"
            letter-spacing="1"
        >FINANZAS PERSONALES</text>
    @endif
</svg>