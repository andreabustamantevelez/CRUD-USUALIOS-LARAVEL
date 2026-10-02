{{--
  ==============================================================================
  ARCHIVO : resources/views/components/footer.blade.php
  QUÉ ES  : Footer público. Uso: <x-footer />
  QUÉ HAY QUE HACER (en orden):
    1. Crear las páginas "politica" y "terminos" (enlaces ya conectados).
    2. Quitar los "#" restantes o enlazarlos a páginas reales.
    3. Revisar la insignia "Privacidad Garantizada" y las columnas de enlaces.
  ==============================================================================
--}}

<footer class="w-full bg-surface-container-low text-on-surface-variant">
  <div class="max-w-7xl mx-auto px-margin py-12">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-8">
      <div class="md:col-span-2 flex flex-col gap-3">
        <div class="flex items-center gap-3">
          <img
            alt="Cuentas Claras"
            class="h-8 w-auto object-contain"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBweuxEWQKRy7qEJRkD6QD5dQ8essKjJaIIZk8QNSF0zCKp4Mxu43xW98kZtwUPFp4O0BcUyJ_pNlWv9GG3rWw9l4p2joZzgDX4s96SbiOq1HZRmY6k9BobYik_eMTBbPd5ouVWSCVV5zOHYWTc6RXFBhLfnONaTsqUcpvP-Y6KEjSOlJQ7P9I5A60vifxW7YZh-C8lOzwGpbZ9pn27DtYI_LfqNbnos3EUU4rBz4FJDAG1ZLX52ZcwUk7k5nFJYGA1_Q"
          >
          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold"><span class="material-symbols-outlined text-[13px]">verified_user</span>Privacidad Garantizada</span>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-lg leading-relaxed">
          Plataforma inteligente de gestión de finanzas personales, presupuestos colaborativos y optimización
          de flujo familiar. Diseñada bajo estándares de seguridad bancaria y transparencia absoluta.
        </p>
        <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm mt-1">
          <span class="material-symbols-outlined text-[16px] text-primary">lock</span>
          <span>Encriptación AES-256 de extremo a extremo</span>
        </div>
      </div>
      <div class="flex flex-col gap-2">
        <span class="font-label-md text-label-md text-on-surface font-bold uppercase tracking-wider mb-1">Exploración</span>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="como-funciona"
          href="{{ route('inicio') }}#como-funciona"
        >¿Cómo funciona?</a>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="caracteristicas"
          href="{{ route('inicio') }}#caracteristicas"
        >Características</a>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="simulador"
          href="{{ route('inicio') }}#simulador"
        >Simulador de Ahorro</a>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="metas-familiares"
          href="{{ route('inicio') }}#metas-familiares"
        >Metas Familiares</a>
      </div>
      <div class="flex flex-col gap-2">
        <span class="font-label-md text-label-md text-on-surface font-bold uppercase tracking-wider mb-1">Legal y Normativa</span>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="politica-privacidad"
          href=\"{{
          route('politica')
          }}\"
        >Política de Tratamiento de Datos (Ley 1581 de 2012)</a>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="terminos-condiciones"
          href=\"{{
          route('terminos')
          }}\"
        >Términos y Condiciones del Servicio</a>
        <a
          class="font-body-sm text-body-sm text-on-surface-variant hover:text-on-surface transition-colors"
          data-path="seguridad"
          href="{{ route('inicio') }}#seguridad"
        >Arquitectura de Seguridad</a>
      </div>
    </div>
    <div class="pt-6 border-t border-outline-variant/30 flex flex-col md:flex-row items-center justify-between gap-4">
      <p class="font-body-sm text-[12px] text-secondary text-center md:text-left leading-relaxed max-w-4xl">
        Aviso Legal: Cuentas Claras es una herramienta tecnológica orientada a la planeación y educación
        financiera personal. No constituye una entidad financiera, no realiza captación masiva ni
        intermediación bancaria, y no reporta información crediticia negativa ante centrales de riesgo.
      </p>
      <p class="font-label-sm text-label-sm text-secondary shrink-0">
        © 2024 Cuentas Claras. Todos los derechos reservados.
      </p>
    </div>
  </div>
</footer>
