{{--
  ==============================================================================
  ARCHIVO : resources/views/components/footer.blade.php
  QUÉ ES  : Footer público. Uso: <x-footer />
  ACTUALIZADO 2026-10-16:
    - Logo externo reemplazado por <x-logo /> (se adapta al tema y al rol).
    - Comillas escapadas \" corregidas en los enlaces a politica y terminos.
  ==============================================================================
--}}

<footer class="w-full bg-surface-container-low text-on-surface-variant">
  <div class="max-w-7xl mx-auto px-margin py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8">

      {{-- Columna 1-2: marca + descripción --}}
      <div class="md:col-span-2 flex flex-col gap-3">
        <div class="flex items-center gap-3 flex-wrap">
          <x-logo :alto="32" />
          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold">
            <span class="material-symbols-outlined text-[13px]">verified_user</span>
            Privacidad y confianza
          </span>
        </div>

       <p class="font-body-sm text-body-sm text-on-surface-variant max-w-lg leading-relaxed">
       Plataforma para organizar las finanzas personales y familiares, llevar un registro de gastos,
       consultar compromisos y planificar metas de ahorro.
       </p>

        <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm mt-1">
          <span class="material-symbols-outlined text-[16px] text-primary">lock</span>
         <span>Consulta nuestra política de privacidad</span>
        </div>
      </div>

      {{-- Columna 3: Exploración --}}
      <div class="flex flex-col gap-2">
        <span class="font-label-md text-label-md text-on-surface font-bold uppercase tracking-wider mb-1">
          Exploración
        </span>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('inicio') }}#como-funciona">¿Cómo funciona?</a>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('inicio') }}#caracteristicas">Características</a>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('inicio') }}#simulador">Simulador de Ahorro</a>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('inicio') }}#metas-familiares">Metas Familiares</a>
      </div>

      {{-- Columna 4: Legal --}}
      <div class="flex flex-col gap-2">
        <span class="font-label-md text-label-md text-on-surface font-bold uppercase tracking-wider mb-1">
          Legal y Normativa
        </span>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('politica') }}">Política de Tratamiento de Datos (Ley 1581 de 2012)</a>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('terminos') }}">Términos y Condiciones del Servicio</a>
        <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
           href="{{ route('inicio') }}#seguridad">Arquitectura de Seguridad</a>
      </div>
    </div>

    {{-- Bloque inferior: aviso legal + copyright --}}
    <div class="pt-6 border-t border-outline-variant/30 flex flex-col md:flex-row items-center justify-between gap-4">
      <p class="font-body-sm text-[12px] text-secondary text-center md:text-left leading-relaxed max-w-4xl">
        Aviso Legal: Cuentas Claras es una herramienta tecnológica para organizar las finanzas personales y familiares.No es una entidad financiera ni una entidad prestamista.Su finalidad es facilitar el registro de gastos, el seguimiento de compromisos y la planificación de metas de ahorro.
      </p>
      <p class="font-label-sm text-label-sm text-secondary shrink-0">
       © 2026 Cuentas Claras. Todos los derechos reservados.
      </p>
    </div>
  </div>
</footer>
