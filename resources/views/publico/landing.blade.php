{{--
  ==============================================================================
  ARCHIVO : resources/views/publico/landing.blade.php
  QUÉ ES  : Landing page pública (diseño original code.html). Presenta el producto y lleva a
            registro o login.
  RUTA    : GET /  (nombre: inicio) · sin sesión · middleware guest opcional
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno obligatorio (es contenido estático).
  BASE DE DATOS: Ninguna.
  QUÉ HAY QUE HACER (en orden):
    1. Crear la ruta "inicio" y un controlador simple (o Route::view) que devuelva esta vista.
    2. Si el usuario ya inició sesión, mostrar "Ir a mi panel" en el header en lugar de Iniciar
       Sesión / Crear Cuenta (@auth / @guest en components/header).
    3. Verificar con el equipo cada cifra y promesa antes de publicar: "+18.000 hogares", "AES-256
       de extremo a extremo", "100% gratuita", "Cero reportes a centrales". Si no son ciertas,
       cambiar el texto.
    4. Guardar el logo y las imágenes externas (lh3.googleusercontent.com) en public/img y cambiar
       cada src por asset('img/...').
    5. Los botones de plan ya envían al registro; para preseleccionar el tipo agregar
       ?tipo=estandar o ?tipo=familiar al enlace (la vista de registro ya lo lee).
    6. Redactar Política de privacidad y Términos (enlaces del footer).
  CÓMO HACERLO / NOTAS:
    - Los enlaces ancla del menú (#como-funciona, #caracteristicas, #simulador, #metas-familiares,
      #seguridad) ya tienen su sección con ese id.
    - Los textos de marketing no son dinámicos: se editan directamente aquí.
  CÓMO SE COMPRUEBA:
    - Todos los enlaces llevan a donde dicen y ninguno queda en "#".
    - Se ve igual al code.html a 1440 px y 390 px.
  ==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Tus finanzas en orden · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">
@endpush
@section('content')
  <main class="w-full pt-20 bg-surface">
    <div class="flex flex-col w-full">
      <!-- HERO SECTION -->
      <section class="relative w-full overflow-hidden bg-gradient-to-b from-surface via-surface-container-low/40 to-surface pt-12 pb-24 lg:pt-16 lg:pb-32 pb-8">
        <!-- Ambient Glow Drops -->
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[720px] h-[360px] bg-primary/10 rounded-full blur-[110px] pointer-events-none"></div>
        <div class="absolute top-1/3 -right-24 w-80 h-80 bg-tertiary-container/15 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-margin relative z-10">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            <!-- Left Column: Copy & Conversions -->
            <div class="lg:col-span-7 flex flex-col gap-6 text-left">
              <!-- Institutional Pill Badge -->
              <div class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-primary-fixed/40 text-on-primary-fixed shadow-sm">
                <span
                  class="material-symbols-outlined text-[17px] text-primary"
                  style="font-variation-settings: &quot;FILL&quot; 1;"
                >verified</span>
                <span class="font-label-sm text-label-sm uppercase tracking-wider font-semibold">Plataforma Pedagógica y de Control Familiar • 100% Gratuita y Privada</span>
              </div>
              <!-- Main Dynamic Title -->
              <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight leading-[1.12]">
                Tus finanzas en orden, tus <span class="text-primary underline decoration-primary-fixed decoration-wavy underline-offset-8">metas compartidas</span> y tu tranquilidad asegurada.
              </h1>
              <!-- Value Clarification -->
              <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl leading-relaxed">
                Centraliza tus gastos diarios, automatiza alertas <span class="font-semibold text-on-surface">72h antes</span> de tus recibos fijos, simula créditos con cálculo transparente y ahorra en pareja o familia con absoluta confidencialidad.
              </p>
              <!-- Primary CTAs -->
              <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                <a
                  class="inline-flex items-center justify-center gap-3 px-7 py-3.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm shadow-md hover:shadow-lg transition-all duration-200 group active:scale-[0.98]"
                  data-path="crear-cuenta"
                  href="{{ route('registro') }}"
                ><span>Comenzar Gratis Ahora</span> <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span> </a>
              </div>
              <!-- Trust & Anti-anxiety Micro-guarantees -->
              <div class="pt-4 flex flex-wrap items-center gap-y-2 gap-x-6 text-on-surface-variant font-label-md text-label-md">
                <div class="flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[18px] text-primary">link_off</span>
                  <span>Sin vincular cuentas bancarias</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[18px] text-primary">check_circle</span>
                  <span>Cero reportes a centrales de riesgo</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[18px] text-primary">shield</span>
                  <span>Cumplimiento Ley 1581 Privacidad</span>
                </div>
              </div>
            </div>
            <!-- Right Column: Interactive Dashboard Simulation Canvas -->
            <div class="lg:col-span-5 relative">
              <!-- Glassmorphic Dashboard Showcase Container -->
              <div class="relative bg-surface-container-lowest rounded-2xl shadow-xl p-6 sm:p-7 flex flex-col gap-6">
                <!-- Floating Topbar within Widget -->
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                      <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                    </div>
                    <div>
                      <p class="font-label-sm text-label-sm text-secondary uppercase font-bold tracking-wider">
                        Flujo de Caja Disponible
                      </p>
                      <p class="font-numeric-lg text-numeric-lg text-on-surface font-bold tracking-tight">
                        $3.850.000 <span class="font-label-sm text-label-sm text-secondary font-medium">COP</span>
                      </p>
                    </div>
                  </div>
                  <span class="px-2.5 py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-semibold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span> En Línea </span>
                </div>
                <!-- Proactive Early Alert Notification (72h Notice) -->
                <div class="bg-error-container/40 p-4 rounded-xl flex items-center justify-between gap-3 shadow-sm">
                  <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-error/15 text-error flex items-center justify-center shrink-0 mt-0.5">
                      <span class="material-symbols-outlined text-[20px]">notifications_active</span>
                    </div>
                    <div>
                      <div class="flex items-center gap-2">
                        <span class="font-headline-sm text-[14px] leading-5 text-on-surface font-bold">Arriendo de Vivienda</span>
                        <span class="px-2 py-0.5 rounded-md bg-error/15 text-error font-label-sm text-[10px] font-bold">VENCE EN 2 DÍAS</span>
                      </div>
                      <p class="font-body-sm text-[12px] text-secondary mt-0.5">
                        $1.200.000 COP • Cuenta de ahorros
                      </p>
                    </div>
                  </div>
                  <button
                    type="button"
                    class="px-3.5 py-1.5 rounded-md bg-on-surface hover:bg-inverse-surface text-surface-bright font-label-sm text-label-sm font-bold shadow-sm transition-transform active:scale-95 shrink-0"
                  > Pagar </button>
                </div>
                <!-- 50/30/20 Visual Diagnostic Breakdown -->
                <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-3">
                  <div class="flex justify-between items-center">
                    <div class="flex items-center gap-1.5">
                      <span class="material-symbols-outlined text-[18px] text-primary">pie_chart</span>
                      <span class="font-headline-sm text-[14px] text-on-surface font-bold">Diagnóstico Regla 50 / 30 / 20</span>
                    </div>
                    <span class="font-label-sm text-label-sm text-primary font-bold">Equilibrado</span>
                  </div>
                  <!-- Segmented Multi-Color Progress Indicator -->
                  <div class="w-full h-3 rounded-full bg-surface-container-high overflow-hidden flex gap-0.5">
                    <div
                      class="h-full bg-primary transition-all duration-500 rounded-l-full"
                      style="width: 50%"
                      title="Necesidades 50%"
                    ></div>
                    <div
                      class="h-full bg-tertiary-container transition-all duration-500"
                      style="width: 28%"
                      title="Gustos 28% de 30%"
                    ></div>
                    <div
                      class="h-full bg-primary-fixed-dim transition-all duration-500 rounded-r-full"
                      style="width: 22%"
                      title="Ahorro 22% de 20%"
                    ></div>
                  </div>
                  <!-- Legend Details -->
                  <div class="grid grid-cols-3 gap-2 pt-1 font-label-sm text-[11px]">
                    <div class="flex flex-col">
                      <span class="text-secondary flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span>50% Necesidades</span>
                      <span class="font-bold text-on-surface mt-0.5">$1.925.000 COP</span>
                    </div>
                    <div class="flex flex-col">
                      <span class="text-secondary flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary-container"></span>28% Gustos</span>
                      <span class="font-bold text-on-surface mt-0.5">$1.078.000 COP</span>
                    </div>
                    <div class="flex flex-col">
                      <span class="text-secondary flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-fixed-dim"></span>22% Ahorro</span>
                      <span class="font-bold text-on-surface mt-0.5">$847.000 COP</span>
                    </div>
                  </div>
                </div>
                <!-- Recent Fixed Obligations Mini-ledger -->
                <div class="flex flex-col gap-2.5">
                  <span class="font-label-sm text-label-sm text-secondary uppercase font-bold tracking-wider">Próximos Recibos Calendarizados</span>
                  <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                    <div class="flex items-center gap-2.5">
                      <span class="material-symbols-outlined text-[18px] text-tertiary">bolt</span>
                      <span class="font-body-sm text-body-sm text-on-surface font-medium">Servicios Públicos (Enel / Acueducto)</span>
                    </div>
                    <div class="text-right">
                      <span class="font-headline-sm text-[13px] text-on-surface font-semibold">$184.500 COP</span>
                      <p class="font-label-sm text-[10px] text-secondary">Vence en 5 días</p>
                    </div>
                  </div>
                  <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                    <div class="flex items-center gap-2.5">
                      <span class="material-symbols-outlined text-[18px] text-secondary">wifi</span>
                      <span class="font-body-sm text-body-sm text-on-surface font-medium">Internet Fibra Óptica</span>
                    </div>
                    <div class="text-right">
                      <span class="font-headline-sm text-[13px] text-on-surface font-semibold">$89.900 COP</span>
                      <p class="font-label-sm text-[10px] text-secondary">Vence en 8 días</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Decorative Offset Card Emphasizing Confidentiality -->
              <div class="absolute -bottom-6 -left-6 hidden sm:flex items-center gap-3 bg-surface-container-highest/90 backdrop-blur-md px-4 py-3 rounded-xl shadow-lg">
                <span
                  class="material-symbols-outlined text-[24px] text-primary"
                  style="font-variation-settings: &quot;FILL&quot; 1;"
                >lock</span>
                <div class="flex flex-col">
                  <span class="font-headline-sm text-[12px] text-on-surface font-bold">Datos en Tu Poder</span>
                  <span class="font-label-sm text-[10px] text-on-surface-variant">Sin APIs invasivas a tu banco</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- SOCIAL PROOF / VALUE PILLARS STRIP -->
      <section class="w-full bg-surface-container-low py-10">
        <div class="max-w-7xl mx-auto px-margin">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="flex flex-col items-center">
              <span class="font-numeric-lg text-numeric-lg text-primary font-bold">+18.000</span>
              <span class="font-body-sm text-body-sm text-secondary mt-1">Hogares Organizados</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="font-numeric-lg text-numeric-lg text-on-surface font-bold">72 Horas</span>
              <span class="font-body-sm text-body-sm text-secondary mt-1">Alertas Preventivas Previas</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="font-numeric-lg text-numeric-lg text-primary font-bold">100% Privado</span>
              <span class="font-body-sm text-body-sm text-secondary mt-1">Sin Credenciales Bancarias</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="font-numeric-lg text-numeric-lg text-on-surface font-bold">0 COP</span>
              <span class="font-body-sm text-body-sm text-secondary mt-1">Acceso Completo Gratuito</span>
            </div>
          </div>
        </div>
      </section>
      <!-- VALUE PROPOSITION: PROBLEMS WE SOLVE -->
      <section id="como-funciona" class="w-full py-24 bg-surface relative">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-16">
          <!-- Section Header -->
          <div class="flex flex-col items-center text-center gap-4 max-w-3xl mx-auto">
            <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-widest bg-primary-fixed/40 px-3 py-1 rounded-full"> Soluciones Transparentes </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">
              Diseñado para eliminar el estrés financiero en hogares y jóvenes profesionales
            </h2>
            <p class="font-body-md text-body-md text-on-surface-variant">
              Cuentas Claras transforma hojas de cálculo caóticas y recordatorios dispersos en una experiencia
              guiada, pedagógica y visualmente serena.
            </p>
          </div>
          <!-- 3 Key Features Bento Cards -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1: Sin deudas sorpresivas -->
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
              <div class="w-14 h-14 rounded-2xl bg-primary-fixed/50 flex items-center justify-center text-on-primary-fixed mb-6">
                <span class="material-symbols-outlined text-[32px] text-primary">alarm_on</span>
              </div>
              <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-3">
                Sin deudas sorpresivas
              </h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-6">
                Notificaciones oportunas con <strong class="text-on-surface">3 días de antelación</strong> antes de cada vencimiento de recibos de luz, agua, arriendos o suscripciones streaming. Di adiós a los recargos por mora y cortes no programados.
              </p>
              <div class="mt-auto bg-surface-container-low p-4 rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[20px]">notifications_paused</span>
                <span class="font-label-md text-label-md text-on-surface-variant font-medium">Recordatorios vía correo o navegador</span>
              </div>
            </div>
            <!-- Card 2: Regla 50/30/20 Visual -->
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
              <div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center text-on-secondary-container mb-6">
                <span class="material-symbols-outlined text-[32px] text-tertiary">query_stats</span>
              </div>
              <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-3">
                Regla 50/30/20 Visual
              </h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-6">
                Diagnóstico automático mensual que clasifica cada centavo entre <strong class="text-on-surface">necesidades vitales (50%)</strong>, gustos personales que nutren tu vida (30%) y fondos de amortiguación o ahorro futuro (20%).
              </p>
              <!-- Mini Progress demo bar in card -->
              <div class="mt-auto bg-surface-container-low p-4 rounded-xl flex flex-col gap-2">
                <div class="flex justify-between font-label-sm text-label-sm font-semibold">
                  <span>Distribución Ideal</span>
                  <span class="text-primary">100% Asignado</span>
                </div>
                <div class="w-full h-2 rounded-full bg-surface-container-high flex overflow-hidden">
                  <div class="bg-primary h-full w-[50%]"></div>
                  <div class="bg-tertiary-container h-full w-[30%]"></div>
                  <div class="bg-primary-fixed-dim h-full w-[20%]"></div>
                </div>
              </div>
            </div>
            <!-- Card 3: Simulador de Créditos Inteligente -->
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
              <div class="w-14 h-14 rounded-2xl bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed mb-6">
                <span class="material-symbols-outlined text-[32px] text-tertiary">calculate</span>
              </div>
              <h3 id="simulador" class="font-headline-lg text-headline-lg text-on-surface font-bold mb-3">
                Simulador de Créditos
              </h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-6">
                Conoce la cuota exacta antes de solicitar un préstamo comercial o bancario. Con tabla de amortización con <strong class="text-on-surface">sistema francés</strong> y semáforo preventivo que te avisa si excedes el 30% de tu capacidad segura de pago.
              </p>
              <div class="mt-auto bg-surface-container-low p-4 rounded-xl flex items-center justify-between">
                <span class="font-label-sm text-label-sm font-semibold text-secondary">Tope de endeudamiento:</span>
                <span class="font-label-sm text-label-sm font-bold text-primary px-2.5 py-0.5 rounded-full bg-primary-fixed/50">Máximo 30% Seguro</span>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- FEATURE SPOTLIGHT: METAS COMPARTIDAS CON PRIVACIDAD TOTAL (RQNF-03) -->
      <section
        id="metas-familiares"
        class="w-full py-24 bg-surface-container-low/60 relative overflow-hidden"
      >
        <div class="max-w-7xl mx-auto px-margin">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            <!-- Left: Metas Visual Mockup and Privacy Shield -->
            <div class="lg:col-span-6 flex flex-col gap-6">
              <div class="relative bg-surface-container-lowest rounded-2xl p-6 sm:p-8 shadow-xl">
                <!-- Goal Header -->
                <div class="flex items-center justify-between pb-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                      <span class="material-symbols-outlined text-[26px] text-primary">flight_takeoff</span>
                    </div>
                    <div>
                      <h4 class="font-headline-md text-headline-md text-on-surface font-bold">
                        Vacaciones Familiares Caribe 2025
                      </h4>
                      <p class="font-body-sm text-body-sm text-secondary">
                        Meta compartida con 2 integrantes
                      </p>
                    </div>
                  </div>
                  <span class="px-3 py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm font-bold">Activo</span>
                </div>
                <!-- Global Unified Progress Bar -->
                <div class="bg-surface-container-low p-5 rounded-xl flex flex-col gap-3 my-2">
                  <div class="flex justify-between items-baseline">
                    <span class="font-headline-sm text-headline-sm text-secondary">Ahorro Consolidado</span>
                    <span class="font-numeric-lg text-numeric-lg text-primary font-bold">$7.200.000 <span class="font-body-sm text-body-sm text-secondary font-normal">/ $10.000.000</span></span>
                  </div>
                  <div class="w-full h-3 rounded-full bg-surface-container-high overflow-hidden">
                    <div
                      class="bg-primary h-full rounded-full transition-all duration-700"
                      style="width: 72%"
                    ></div>
                  </div>
                  <div class="flex justify-between font-label-sm text-label-sm text-secondary pt-1">
                    <span>Faltan $2.800.000 COP</span>
                    <span class="font-bold text-on-surface">72% de la meta</span>
                  </div>
                </div>
                <!-- Individual Member Cards with Shielding Indicator -->
                <div class="grid grid-cols-2 gap-4 mt-2">
                  <!-- Member A -->
                  <div class="bg-surface-container-low p-3.5 rounded-xl flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                      <span class="font-headline-sm text-[13px] text-on-surface font-bold">Tú (Organizador)</span>
                      <span class="material-symbols-outlined text-primary text-[16px]">lock</span>
                    </div>
                    <p class="font-label-sm text-label-sm text-secondary">
                      Aportes a la meta: <strong class="text-on-surface font-semibold">50%</strong>
                    </p>
                    <p class="font-label-sm text-[10px] text-secondary">
                      Gastos personales: <span class="text-primary font-bold">Privados</span>
                    </p>
                  </div>
                  <!-- Member B -->
                  <div class="bg-surface-container-low p-3.5 rounded-xl flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                      <span class="font-headline-sm text-[13px] text-on-surface font-bold">Pareja / Familiar</span>
                      <span class="material-symbols-outlined text-primary text-[16px]">lock</span>
                    </div>
                    <p class="font-label-sm text-label-sm text-secondary">
                      Aportes a la meta: <strong class="text-on-surface font-semibold">50%</strong>
                    </p>
                    <p class="font-label-sm text-[10px] text-secondary">
                      Gastos personales: <span class="text-primary font-bold">Privados</span>
                    </p>
                  </div>
                </div>
                <!-- Absolute Privacy Guarantee Banner Inside Component -->
                <div class="mt-6 flex items-start gap-3 p-3.5 rounded-xl bg-primary-fixed/30 text-on-primary-fixed">
                  <span
                    class="material-symbols-outlined text-[20px] text-primary shrink-0 mt-0.5"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >enhanced_encryption</span>
                  <p class="font-body-sm text-[12px] leading-snug">
                    <strong>Garantía RQNF-03:</strong> Ni tu pareja ni ningún familiar vinculado pueden ver tus compras en tarjeta, cuentas individuales o saldos personales. Solo el pozo de la meta es visible.
                  </p>
                </div>
              </div>
            </div>
            <!-- Right: Text Rationale & Benefits -->
            <div class="lg:col-span-6 flex flex-col gap-6">
              <div class="inline-flex items-center gap-2 self-start px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm font-bold">
                <span class="material-symbols-outlined text-[16px]">diversity_3</span>
                <span>Módulo de Metas Familiares (RQNF-03)</span>
              </div>
              <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold leading-tight">
                Ahorren juntos para grandes proyectos, manteniendo tu autonomía intacta.
              </h2>
              <p class="font-body-lg text-body-lg text-secondary leading-relaxed">
                Ya sea para la cuota inicial de una vivienda, el fondo de emergencias del hogar o las próximas
                vacaciones, la colaboración financiera no debe costar la pérdida de privacidad individual.
              </p>
              <div class="flex flex-col gap-4 pt-2">
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                      Progreso consolidado en tiempo real
                    </h4>
                    <p class="font-body-md text-body-md text-secondary">
                      Monitorean conjuntamente cuánto falta para el hito sin necesidad de hojas de cálculo
                      compartidas desactualizadas.
                    </p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                      Independencia en tus gastos diarios
                    </h4>
                    <p class="font-body-md text-body-md text-secondary">
                      Tus gustos, compras personales y saldo de emergencia individual permanecen 100%
                      invisibles para los demás miembros.
                    </p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                      Invitaciones seguras por correo
                    </h4>
                    <p class="font-body-md text-body-md text-secondary">
                      Vincula a tu pareja o hijos mediante enlaces temporales encriptados sin compartir
                      contraseñas.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- COMPARISON TABLE: PERFILES DE CUENTA -->
      <section class="w-full py-24 bg-surface">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-16">
          <div class="flex flex-col items-center text-center gap-3 max-w-2xl mx-auto">
            <span class="font-label-sm text-label-sm text-secondary uppercase font-bold tracking-widest"> Transparencia y Elección </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">
              Elige la modalidad que se adapte a tu momento de vida
            </h2>
            <p class="font-body-md text-body-md text-secondary">
              Ambas modalidades son completamente gratuitas y operan bajo los más rigurosos estándares de
              seguridad y confidencialidad.
            </p>
          </div>
          <!-- Pricing / Mode Cards -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto w-full">
            <!-- Standard Account -->
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl p-8 sm:p-10 shadow-sm hover:shadow-md transition-shadow relative">
              <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-bold">Control Personal</span>
                <span class="font-numeric-md text-numeric-md text-on-surface font-bold">Gratis</span>
              </div>
              <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">Usuario Estándar</h3>
              <p class="font-body-md text-body-md text-secondary mt-2 mb-8">
                Ideal para estudiantes, solteros o personas que buscan poner orden definitivo a su dinero,
                recibos y capacidad crediticia.
              </p>
              <ul class="flex flex-col gap-4 font-body-md text-body-md text-on-surface mb-8">
                <li class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                  <span>Registro de ingresos y gastos diarios ilimitados</span>
                </li>
                <li class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                  <span>Alertas preventivas 72 horas antes de vencimiento</span>
                </li>
                <li class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                  <span>Diagnóstico de balance con Regla 50/30/20</span>
                </li>
                <li class="flex items-center gap-3">
                  <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                  <span>Simulador de créditos con amortización francesa</span>
                </li>
                <li class="flex items-center gap-3 text-secondary opacity-60">
                  <span class="material-symbols-outlined text-[20px]">cancel</span>
                  <span>Metas colectivas compartidas en tiempo real</span>
                </li>
              </ul>
              <div class="mt-auto">
                <a
                  class="w-full inline-flex items-center justify-center px-6 py-3.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-headline-sm text-headline-sm transition-colors"
                  data-path="crear-cuenta"
                  href="{{ route('registro') }}"
                > Elegir Cuenta Estándar </a>
              </div>
            </div>
            <!-- Familiar Account (Highlighted) -->
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl p-8 sm:p-10 shadow-lg relative overflow-hidden">
              <!-- Subtle Accent bar on top -->
              <div class="absolute top-0 left-0 right-0 h-2 bg-primary"></div>
              <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">favorite</span> Recomendado para Parejas </span>
                <span class="font-numeric-md text-numeric-md text-primary font-bold">Gratis</span>
              </div>
              <h3 class="font-headline-xl text-headline-xl text-on-surface font-bold">Usuario Familiar</h3>
              <p class="font-body-md text-body-md text-secondary mt-2 mb-8">
                Diseñado para parejas, familias o compañeros de piso que coordinan gastos comunes y proyectos
                de ahorro colaborativos.
              </p>
              <ul class="flex flex-col gap-4 font-body-md text-body-md text-on-surface mb-8">
                <li class="flex items-center gap-3">
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >check_circle</span>
                  <span class="font-medium">Todo lo incluido en la cuenta Estándar</span>
                </li>
                <li class="flex items-center gap-3">
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >check_circle</span>
                  <span class="font-medium">Creación de Metas Colaborativas (RQNF-03)</span>
                </li>
                <li class="flex items-center gap-3">
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >check_circle</span>
                  <span class="font-medium">Invitación segura a miembros sin compartir contraseñas</span>
                </li>
                <li class="flex items-center gap-3">
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >check_circle</span>
                  <span class="font-medium">Privacidad absoluta de gastos individuales garantizada</span>
                </li>
                <li class="flex items-center gap-3">
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    style="font-variation-settings: &quot;FILL&quot; 1;"
                  >check_circle</span>
                  <span class="font-medium">Visualización de progreso consolidado conjunto</span>
                </li>
              </ul>
              <div class="mt-auto">
                <a
                  class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm shadow-md transition-all"
                  data-path="crear-cuenta"
                  href="{{ route('registro') }}"
                ><span>Crear Cuenta Familiar</span> <span class="material-symbols-outlined text-[18px]">arrow_forward</span> </a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- FAQ SECTION (Pedagogical & Regulatory Reassurance) -->
      <section id="seguridad" class="w-full py-20 bg-surface-container-low/40">
        <div class="max-w-4xl mx-auto px-margin flex flex-col gap-10">
          <div class="text-center flex flex-col gap-2">
            <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold">
              Preguntas Frecuentes sobre Seguridad y Uso
            </h3>
            <p class="font-body-md text-body-md text-secondary">
              Claridad absoluta en cómo funciona la plataforma y qué hacemos con tus datos.
            </p>
          </div>
          <div class="flex flex-col gap-4">
            <!-- Question 1 -->
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm flex flex-col gap-2">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[22px]">verified</span>
                <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                  ¿Cuentas Claras solicita contraseñas o claves bancarias?
                </h4>
              </div>
              <p class="font-body-sm text-body-sm text-secondary pl-8">
                <strong>Nunca.</strong> Nuestra plataforma es un sistema pedagógico de autogestión. No requerimos vinculación con APIs bancarias, credenciales dinámicas ni claves de ningún producto financiero.
              </p>
            </div>
            <!-- Question 2 -->
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm flex flex-col gap-2">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[22px]">verified</span>
                <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                  ¿Si tengo un retraso en mis pagos, seré reportado a centrales de riesgo?
                </h4>
              </div>
              <p class="font-body-sm text-body-sm text-secondary pl-8">
                <strong>Bajo ninguna circunstancia.</strong> Cuentas Claras no es una entidad prestamista ni bancaria. Su único objetivo es alertarte a ti 72 horas antes para que recuerdes tus compromisos y vivas sin preocupaciones.
              </p>
            </div>
            <!-- Question 3 -->
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm flex flex-col gap-2">
              <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[22px]">verified</span>
                <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                  ¿Cómo se cumple la Ley 1581 de 2012 de Tratamiento de Datos Personales?
                </h4>
              </div>
              <p class="font-body-sm text-body-sm text-secondary pl-8">
                Todos los registros que ingresas son cifrados de extremo a extremo con el protocolo AES-256.
                No comercializamos bases de datos con firmas publicitarias ni terceros. Puedes solicitar la
                eliminación total de tus registros en cualquier momento.
              </p>
            </div>
          </div>
        </div>
      </section>
      <!-- FINAL CALL TO ACTION BANNER -->
      <!-- BEFORE VS AFTER COMPARISON SECTION -->
      <section id="caracteristicas" class="w-full py-20 bg-surface border-t border-outline-variant/30">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-12">
          <div class="flex flex-col items-center text-center gap-3 max-w-3xl mx-auto">
            <span class="font-label-sm text-label-sm text-primary uppercase font-bold tracking-widest bg-primary-fixed/40 px-3 py-1 rounded-full"> Evoluciona tu método </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">
              Hojas de cálculo caóticas vs. Claridad con Cuentas Claras
            </h2>
            <p class="font-body-md text-body-md text-secondary">
              Compara la frustración del registro manual tradicional frente a un sistema diseñado para
              brindarte paz mental en pareja y familia.
            </p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto w-full">
            <!-- Before: Chaos -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-error/20 shadow-sm flex flex-col gap-6">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[24px]">cancel</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md text-on-surface font-bold">
                    Antes: El estrés de siempre
                  </h3>
                  <p class="font-body-sm text-body-sm text-secondary">
                    Excel, notas dispersas y recibos olvidados
                  </p>
                </div>
              </div>
              <ul class="flex flex-col gap-3 font-body-md text-body-md text-secondary">
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">close</span>
                  <span>Olvidar fechas de corte y pagar recargos por mora en servicios y tarjetas.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">close</span>
                  <span>Discusiones en pareja al revisar gastos íntimos compartidos en tablas públicas.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">close</span>
                  <span>Cálculos manuales enredados de amortización sin saber si te sobreendeudas.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">close</span>
                  <span>Desconocimiento del balance real entre necesidades vitales y ahorro a fin de mes.</span>
                </li>
              </ul>
            </div>
            <!-- After: Cuentas Claras -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-primary/30 shadow-md flex flex-col gap-6 relative overflow-hidden">
              <div class="absolute top-0 right-0 px-3 py-1 bg-primary text-on-primary font-label-sm text-label-sm font-semibold rounded-bl-xl">
                La Experiencia Serena
              </div>
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed flex items-center justify-center text-primary shrink-0">
                  <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>
                <div>
                  <h3 class="font-headline-md text-headline-md text-on-surface font-bold">
                    Ahora: Cuentas Claras
                  </h3>
                  <p class="font-body-sm text-body-sm text-primary font-medium">
                    Control automatizado y pedagogía familiar
                  </p>
                </div>
              </div>
              <ul class="flex flex-col gap-3 font-body-md text-body-md text-on-surface">
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_circle</span>
                  <span>Alertas preventivas 72h antes a tu correo y navegador, cero recargos.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_circle</span>
                  <span>Garantía de Privacidad RQNF-03: tus compras personales quedan 100% ocultas.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_circle</span>
                  <span>Simulador de crédito con sistema francés y semáforo preventivo del 30%.</span>
                </li>
                <li class="flex items-start gap-2.5">
                  <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_circle</span>
                  <span>Diagnóstico visual instantáneo de la Regla 50/30/20 en tiempo real.</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>
      <!-- TESTIMONIALS SECTION -->
      <section class="w-full py-20 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-12">
          <div class="flex flex-col items-center text-center gap-3 max-w-3xl mx-auto">
            <span class="font-label-sm text-label-sm text-tertiary uppercase font-bold tracking-widest bg-tertiary-fixed px-3 py-1 rounded-full"> Historias Reales </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">
              Familias y profesionales que transformaron sus finanzas
            </h2>
            <p class="font-body-md text-body-md text-secondary">
              Descubre cómo miles de colombianos eliminaron el estrés financiero y lograron sus proyectos
              compartidos.
            </p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm flex flex-col justify-between gap-6">
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-1 text-primary">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface leading-relaxed">
                  "Logramos ahorrar la cuota inicial de nuestro apartamento en Medellín sin tener que discutir
                  por dinero. Cada quien aportaba su cuota a la meta compartida mientras nuestros gastos del
                  día a día siguieron siendo completamente privados."
                </p>
              </div>
              <div class="flex items-center gap-3 pt-4 border-t border-outline-variant/30">
                <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary font-bold">
                  CM
                </div>
                <div>
                  <p class="font-headline-sm text-headline-sm text-on-surface font-bold">
                    Camila &amp; Mateo
                  </p>
                  <p class="font-body-sm text-[12px] text-secondary">Ahorro de Vivienda Propia • Medellín</p>
                </div>
              </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm flex flex-col justify-between gap-6">
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-1 text-primary">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface leading-relaxed">
                  "Las alertas 72 horas antes me salvaron de dos cortes de luz por olvido. Antes pagaba
                  recargos de reconexión todos los meses; hoy todo está calendarizado y mi historial está
                  impecable."
                </p>
              </div>
              <div class="flex items-center gap-3 pt-4 border-t border-outline-variant/30">
                <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container font-bold">
                  AR
                </div>
                <div>
                  <p class="font-headline-sm text-headline-sm text-on-surface font-bold">Andrés Restrepo</p>
                  <p class="font-body-sm text-[12px] text-secondary">Ingeniero Independiente • Bogotá</p>
                </div>
              </div>
            </div>
            <!-- Testimonial 3 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm flex flex-col justify-between gap-6">
              <div class="flex flex-col gap-3">
                <div class="flex items-center gap-1 text-primary">
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                  <span class="material-symbols-outlined text-[18px]">star</span>
                </div>
                <p class="font-body-md text-body-md text-on-surface leading-relaxed">
                  "Entender la regla 50/30/20 con gráficos tan claros me enseñó a no culparme por disfrutar de
                  mis salidas y hobbies, porque mi 20% de ahorro ya estaba protegido desde el día que recibo
                  mi salario."
                </p>
              </div>
              <div class="flex items-center gap-3 pt-4 border-t border-outline-variant/30">
                <div class="w-10 h-10 rounded-full bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed font-bold">
                  VG
                </div>
                <div>
                  <p class="font-headline-sm text-headline-sm text-on-surface font-bold">Valentina Gómez</p>
                  <p class="font-body-sm text-[12px] text-secondary">Diseñadora Gráfica • Cali</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!-- SECURITY & PRIVACY TRUST BADGES -->
      <section class="w-full py-16 bg-surface border-y border-outline-variant/30">
        <div class="max-w-7xl mx-auto px-margin">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="flex flex-col items-center gap-3 p-4">
              <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[28px]">enhanced_encryption</span>
              </div>
              <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                Cifrado Bancario AES-256
              </h4>
              <p class="font-body-sm text-body-sm text-secondary">
                Toda tu información viaja y se almacena bajo estándares criptográficos militares de extremo a
                extremo.
              </p>
            </div>
            <div class="flex flex-col items-center gap-3 p-4">
              <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[28px]">key_off</span>
              </div>
              <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                Sin Claves ni APIs Bancarias
              </h4>
              <p class="font-body-sm text-body-sm text-secondary">
                Nunca te pediremos tokens de acceso, números de tarjeta ni credenciales de tus cuentas
                bancarias.
              </p>
            </div>
            <div class="flex flex-col items-center gap-3 p-4">
              <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[28px]">policy</span>
              </div>
              <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                Cumplimiento Ley 1581 de 2012
              </h4>
              <p class="font-body-sm text-body-sm text-secondary">
                Tus datos personales son 100% tuyos. No comercializamos bases de datos ni compartimos
                información con terceros.
              </p>
            </div>
          </div>
        </div>
      </section>
      <section class="w-full pt-12 bg-surface pb-8">
        <div class="max-w-7xl mx-auto px-margin">
          <div class="relative rounded-3xl bg-inverse-surface text-inverse-on-surface overflow-hidden p-10 sm:p-16 flex flex-col items-center text-center shadow-xl">
            <div class="absolute -top-24 -left-24 w-80 h-80 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-80 h-80 bg-tertiary-container/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col items-center gap-6 max-w-2xl">
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary text-on-primary font-label-sm text-label-sm font-semibold">
                <span class="material-symbols-outlined text-[15px]">spa</span>
                <span>Empieza hoy tu tranquilidad financiera</span>
              </div>
              <h2 class="font-display-lg text-display-lg font-bold text-white tracking-tight">
                Toma el control de tu dinero hoy mismo
              </h2>
              <p class="font-body-lg text-body-lg text-surface-container-highest/80 leading-relaxed">
                Sin costos ocultos, sin vinculación a bancos y con la certeza de que tu información familiar
                está completamente resguardada.
              </p>
              <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 w-full sm:w-auto">
                <a
                  class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm shadow-lg transition-transform active:scale-95"
                  data-path="crear-cuenta"
                  href="{{ route('registro') }}"
                ><span>Crear Mi Cuenta Gratis</span><span class="material-symbols-outlined text-[20px]">bolt</span></a>
              </div>
              <p class="font-label-sm text-label-sm text-surface-container-highest/60 mt-2">
                Configuración en menos de 2 minutos • Acceso inmediato desde cualquier dispositivo
              </p>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
@endsection
