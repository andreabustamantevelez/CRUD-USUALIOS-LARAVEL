{{--
  ==============================================================================
  ARCHIVO : resources/views/app/creditos/simulador.blade.php
  QUÉ ES  : Simulador de créditos (diseño original). Calcula cuota, total, intereses, semáforo de
            endeudamiento y tabla de amortización francesa.
  RUTA    : GET /creditos/simulador (creditos.simulador) · POST (creditos.guardar) · DELETE
            (creditos.eliminar) · middleware auth, cuenta.activa
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - $ingresoMensual (entero, pesos; ingreso del perfil. Si es 0, el semáforo no tiene base)
    - $simulaciones (nombre, monto, tasa, plazo, cuota, created_at)
  BASE DE DATOS: simulaciones_credito (usuario_id, nombre, monto, tasa, plazo, cuota, total,
            intereses).
  QUÉ HAY QUE HACER (en orden):
    1. Crear SimulacionCreditoController@index (envía $ingresoMensual y $simulaciones del usuario)
       y @store.
    2. Crear el servicio CalculadoraCredito (app/Services) con la MISMA fórmula del JS (sistema
       francés, tasa mensual vencida) y usarlo en @store: el cálculo oficial es del servidor
       (RQNF-04, 0 % de error).
    3. Activar el guardado: public/js/simulador-de-creditos.js tiene un TODO en el botón "Guardar
       Simulación" que debe rellenar y enviar el formulario #saveSimForm.
    4. Si $ingresoMensual es 0, mostrar invitación a registrarlo en Ajustes > Perfil en lugar del
       semáforo.
    5. Decidir (decisión #13) el redondeo y que no hay comisiones ni seguros; documentarlo en la
       caja informativa.
    6. La caja educativa dice que un abono "podría recortar hasta 4 meses": calcularlo de verdad o
       quitar la cifra.
    7. Tabla de amortización: muestra 6 periodos; agregar botón "Ver todos" si se quiere.
  CÓMO HACERLO / NOTAS:
    - window.CC.ingreso se imprime al final de esta vista y lo lee el JS.
    - Eliminar una simulación = formulario DELETE (ya incluido en la tarjeta); falta el modal de
      confirmación.
    - La tarjeta "recargar" llama window.loadPreset con los datos guardados.
  CÓMO SE COMPRUEBA:
    - Varios montos, tasas y plazos contra cálculo manual.
    - Guardar, ver en el historial, recargar y eliminar.
  ==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Simulador de Créditos · Cuentas Claras')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/simulador-de-creditos.css') }}">
@endpush
@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="flex flex-col w-full pb-16">
      <!-- Top Ambient Halo -->
      <div class="relative w-full mb-6">
        <div class="absolute -top-10 left-1/4 w-96 h-36 bg-primary/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute -top-6 right-1/3 w-80 h-32 bg-tertiary/10 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <!-- Header & Educational Notice -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-2">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm tracking-wide uppercase">Módulo Financiero Inteligente</span>
              <span class="font-body-sm text-body-sm text-outline"></span>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">
              Simulador de Créditos &amp; Salud de Deuda
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
              Calcula amortización proyectada en sistema francés y evalúa el impacto real sobre el presupuesto
              familiar.
            </p>
          </div>
          <!-- Pedagogical badge -->
          <div class="flex items-start gap-3 p-3.5 bg-surface-container-lowest rounded-xl shadow-sm max-w-md">
            <div class="p-2 rounded-lg bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center shrink-0">
              <span class="material-symbols-outlined text-[20px]">info</span>
            </div>
            <div class="flex flex-col">
              <span class="font-label-md text-label-md text-on-surface">Herramienta Pedagógica Neutral</span>
              <p class="font-body-sm text-body-sm text-on-surface-variant leading-snug">
                No somos entidad bancaria ni intermediarios financieros: simulador pedagógico de amortización
                con base matemática verificada.
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- Main Grid: Calculator Form & Real-Time Impact -->
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-gutter items-start">
        <!-- Left Column: Parameters Form (7 Cols) -->
        <div class="xl:col-span-7 flex flex-col gap-6">
          <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-5">
            <div class="flex items-center justify-between pb-2">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-6 bg-primary rounded-full"></span>
                <h2 class="font-headline-md text-headline-md text-on-surface">
                  Configura las Condiciones del Crédito
                </h2>
              </div>
              <span class="font-label-sm text-label-sm px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant">Método Francés (Cuota Fija)</span>
            </div>
            <form
              class="flex flex-col gap-5"
              id="loanForm"
              onsubmit="event.preventDefault(); window.recalculateLoan();"
            >
              <!-- Simulation Title -->
              <div class="flex flex-col gap-1.5">
                <label
                  class="font-label-md text-label-md text-on-surface"
                  for="loanName"
                >Nombre de la Simulación</label>
                <div class="relative flex items-center">
                  <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px]">edit_note</span>
                  <input
                    class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/40 shadow-inner bg-surface-container-low"
                    id="loanName"
                    placeholder="Ej. Préstamo Vehículo, Consolidación"
                    type="text"
                    value="Crédito Libre Inversión"
                  >
                </div>
              </div>
              <!-- Financed Amount with Slider & Direct Input -->
              <div class="flex flex-col gap-2 p-4 bg-surface-container-low rounded-xl">
                <div class="flex items-center justify-between">
                  <label
                    class="font-label-md text-label-md text-on-surface"
                    for="amountInput"
                  >Monto a Financiar (COP)</label>
                  <div class="flex items-center gap-1 bg-surface-container-lowest px-3 py-1.5 rounded-lg shadow-sm">
                    <span class="font-numeric-md text-numeric-md text-on-surface-variant">$</span>
                    <input
                      class="w-36 text-right font-numeric-md text-numeric-md text-on-surface bg-transparent focus:outline-none"
                      id="amountInput"
                      max="150000000"
                      min="500000"
                      step="100000"
                      type="number"
                      value="15000000"
                    >
                    <span class="font-label-sm text-label-sm text-outline">COP</span>
                  </div>
                </div>
                <!-- Range Slider -->
                <input
                  class="w-full accent-primary h-2 bg-surface-container-high rounded-lg cursor-pointer"
                  id="amountSlider"
                  max="80000000"
                  min="1000000"
                  step="500000"
                  type="range"
                  value="15000000"
                >
                <div class="flex justify-between font-label-sm text-label-sm text-outline-variant pt-0.5">
                  <span>$1.000.000</span>
                  <span>$40.000.000</span>
                  <span>$80.000.000 COP</span>
                </div>
              </div>
              <!-- Interest Rate Dual Box (M.V. and E.A.) -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5 p-3.5 bg-surface-container-low rounded-xl">
                  <div class="flex items-center justify-between">
                    <label
                      class="font-label-md text-label-md text-on-surface"
                      for="rateInput"
                    >Tasa Mensual Vencida</label>
                    <span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container-highest text-primary">M.V.</span>
                  </div>
                  <div class="relative flex items-center mt-1">
                    <input
                      class="w-full h-10 px-3 pr-8 bg-surface-container-lowest font-numeric-md text-numeric-md text-on-surface rounded-lg shadow-sm focus:outline-none"
                      id="rateInput"
                      max="5.0"
                      min="0.1"
                      step="0.05"
                      type="number"
                      value="1.80"
                    >
                    <span class="absolute right-3 font-label-md text-label-md text-outline">%</span>
                  </div>
                  <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Base mensual para cálculo de cuota</span>
                </div>
                <div class="flex flex-col gap-1.5 p-3.5 bg-surface-container-low rounded-xl">
                  <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface">Efectiva Anual Equivalente</span>
                    <span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container-highest text-secondary">E.A.</span>
                  </div>
                  <div class="h-10 px-3 flex items-center justify-between bg-surface-container-lowest rounded-lg shadow-sm mt-1">
                    <span
                      class="font-numeric-md text-numeric-md text-on-surface"
                      id="eaRateDisplay"
                    >23.87%</span>
                    <span class="material-symbols-outlined text-[18px] text-outline">calculate</span>
                  </div>
                  <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Tasa nominal anual compuesta</span>
                </div>
              </div>
              <!-- Term (Months) + Quick Chips -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                  <label class="font-label-md text-label-md text-on-surface">Plazo de Amortización</label>
                  <div class="flex items-center gap-1">
                    <span class="font-numeric-md text-numeric-md text-primary" id="termValueLabel">36</span>
                    <span class="font-label-md text-label-md text-on-surface-variant">Meses</span>
                  </div>
                </div>
                <!-- Quick Term Selector Buttons -->
                <div class="grid grid-cols-5 gap-2" id="termButtonsContainer">
                  <button
                    class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
                    data-term="12"
                    type="button"
                  >12 m</button>
                  <button
                    class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
                    data-term="24"
                    type="button"
                  >24 m</button>
                  <button
                    class="term-btn py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-all text-center"
                    data-term="36"
                    type="button"
                  >36 m</button>
                  <button
                    class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
                    data-term="48"
                    type="button"
                  >48 m</button>
                  <button
                    class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
                    data-term="60"
                    type="button"
                  >60 m</button>
                </div>
              </div>
              <!-- Household Income Preset context -->
              <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-surface-container-low rounded-lg">
                <div class="flex items-center gap-2.5">
                  <span class="material-symbols-outlined text-[20px] text-primary">family_restroom</span>
                  <div>
                    <span class="font-label-md text-label-md text-on-surface block">Ingreso mensual declarado Declarado</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Tomado de tu perfil</span>
                  </div>
                </div>
                <div class="text-right">
                  <span class="font-numeric-md text-numeric-md text-on-surface">${{ number_format($ingresoMensual ?? 0, 0, ',', '.') }}</span>
                  <span class="font-label-sm text-label-sm text-outline block">COP / mes</span>
                </div>
              </div>
              <!-- Action Buttons -->
              <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <button
                  class="flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors"
                  id="resetBtn"
                  type="button"
                ><span class="material-symbols-outlined text-[18px]">restart_alt</span> <span>Limpiar</span> </button>
                <div class="flex flex-wrap items-center gap-3">
                  <button
                    class="flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary-container hover:bg-secondary-fixed text-on-secondary-container font-label-md text-label-md transition-colors shadow-sm"
                    id="saveSimBtn"
                    type="button"
                  ><span class="material-symbols-outlined text-[18px]">bookmark_add</span> <span>Guardar Simulación</span> </button>
                  <button
                    class="flex items-center gap-1.5 px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md shadow-md transition-all active:scale-95"
                    type="submit"
                  ><span class="material-symbols-outlined text-[18px]">calculate</span> <span>Recalcular Cuota</span> </button>
                </div>
              </div>
            </form>
          </div>
          <!-- Financial Education Tip Box -->
          <div class="p-space-md rounded-xl bg-surface-container-low flex items-start gap-3.5 shadow-sm">
            <span class="material-symbols-outlined text-[24px] text-primary-container shrink-0 mt-0.5">tips_and_updates</span>
            <div>
              <h4 class="font-headline-sm text-headline-sm text-on-surface">
                Principio del Abono Inteligente a Capital
              </h4>
              <p class="font-body-sm text-body-sm text-on-surface-variant mt-1 leading-relaxed">
                En el sistema francés, durante los primeros periodos la mayor parte de tu cuota cubre
                intereses y poco capital. Si realizas un abono extraordinario de tan solo $500.000 COP en el
                mes 6, podrías recortar hasta 4 meses de deuda total.
              </p>
            </div>
          </div>
        </div>
        <!-- Right Column: Calculation Card + Debt Capacity Semaforo (5 Cols) -->
        <div class="xl:col-span-5 flex flex-col gap-6">
          <!-- High-Precision Output Card -->
          <div class="relative overflow-hidden bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-5">
            <div class="absolute -right-6 -bottom-6 w-36 h-36 bg-primary-fixed/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center justify-between pb-1">
              <span class="font-label-sm text-label-sm uppercase tracking-wider text-outline">Resultado Verificado (0% Error)</span>
              <span class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm font-semibold flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Simulación Activa </span>
            </div>
            <!-- Hero Monthly Installment -->
            <div class="p-4 bg-surface-container-low rounded-xl flex flex-col gap-1">
              <span class="font-label-md text-label-md text-on-surface-variant">Cuota Mensual Estimada (Fija)</span>
              <div class="flex items-baseline gap-2">
                <span
                  class="font-display-lg text-display-lg text-primary tracking-tight"
                  id="installmentDisplay"
                >$569.757</span>
                <span class="font-headline-sm text-headline-sm text-outline font-normal">COP/mes</span>
              </div>
              <span class="font-body-sm text-body-sm text-on-surface-variant">Incluye intereses fijos mensuales según tasa pactada.</span>
            </div>
            <!-- Micro Financial Breakdown -->
            <div class="grid grid-cols-2 gap-3">
              <div class="p-3.5 bg-surface-container rounded-lg flex flex-col gap-0.5">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Total a Pagar (Plazo)</span>
                <span
                  class="font-numeric-md text-numeric-md text-on-surface"
                  id="totalPaymentDisplay"
                >$20.511.269</span>
                <span class="font-body-sm text-body-sm text-outline">Capital + Intereses</span>
              </div>
              <div class="p-3.5 bg-surface-container rounded-lg flex flex-col gap-0.5">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Intereses Totales</span>
                <span
                  class="font-numeric-md text-numeric-md text-tertiary"
                  id="totalInterestDisplay"
                >$5.511.269</span>
                <span class="font-body-sm text-body-sm text-outline">Costo financiero puro</span>
              </div>
            </div>
            <!-- Proportional Visual Bar: Capital vs Interés -->
            <div class="flex flex-col gap-1.5 pt-1">
              <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant">
                <span>Capital prestado (72.5%)</span>
                <span>Intereses generados (27.5%)</span>
              </div>
              <div class="w-full h-3 bg-surface-container-highest rounded-full overflow-hidden flex">
                <div
                  class="h-full bg-primary transition-all duration-500"
                  id="capitalBarSegment"
                  style="width: 73.1%;"
                ></div>
                <div
                  class="h-full bg-tertiary-container transition-all duration-500"
                  id="interestBarSegment"
                  style="width: 26.9%;"
                ></div>
              </div>
            </div>
            <!-- Semaphore Component: Capacidad de Endeudamiento -->
            <div class="mt-2 p-4 bg-surface rounded-xl flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-[20px]">speed</span>
                  <h3 class="font-headline-sm text-headline-sm text-on-surface">
                    Capacidad de Endeudamiento
                  </h3>
                </div>
                <span
                  class="px-2.5 py-0.5 rounded-full bg-primary/15 text-primary font-label-sm text-label-sm flex items-center gap-1.5"
                  id="semaphoreBadge"
                ><span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Rango Saludable</span>
              </div>
              <!-- Gauge progress representation -->
              <div class="flex flex-col gap-1.5">
                <div class="flex justify-between items-baseline font-body-sm text-body-sm">
                  <span class="text-on-surface-variant">Compromiso sobre el ingreso familiar:</span>
                  <span
                    class="font-numeric-md text-numeric-md text-primary font-bold"
                    id="debtRatioDisplay"
                  >14.8%</span>
                </div>
                <!-- Progress with safe zone marker -->
                <div class="relative w-full h-3 bg-surface-container-highest rounded-full overflow-hidden">
                  <div
                    class="h-full bg-primary rounded-full transition-all duration-500"
                    id="debtRatioBar"
                    style="width: 14.8%;"
                  ></div>
                </div>
                <!-- Metric labels -->
                <div class="flex justify-between font-label-sm text-label-sm text-outline pt-0.5">
                  <span>0%</span>
                  <span class="text-primary font-bold">Umbral Recomendado: 30% ($1.155.000 max)</span>
                  <span>40%+ Alerta</span>
                </div>
              </div>
              <p
                class="font-body-sm text-body-sm text-on-surface-variant leading-normal bg-surface-container-lowest p-3 rounded-lg"
                id="debtEvaluationText"
              >
                <strong class="text-on-surface">Diagnóstico:</strong> Esta cuota mensual compromete únicamente el <strong class="text-primary">14.8%</strong> de tus ingresos familiares (${{ number_format($ingresoMensual ?? 0, 0, ',', '.') }} COP). Mantienes un margen libre de seguridad de <strong class="text-on-surface">$585.243 COP</strong> antes de tocar el límite prudencial del 30%.
              </p>
            </div>
          </div>
          <!-- Quick Summary Metric Pills -->
          <div class="grid grid-cols-2 gap-3">
            <div class="p-3.5 bg-surface-container-lowest rounded-xl shadow-sm flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[20px]">date_range</span>
              </div>
              <div>
                <span class="font-label-sm text-label-sm text-outline block">Finalización estimada</span>
                <span
                  class="font-label-md text-label-md text-on-surface"
                  id="endYearDisplay"
                >Septiembre 2029</span>
              </div>
            </div>
            <div class="p-3.5 bg-surface-container-lowest rounded-xl shadow-sm flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-tertiary">
                <span class="material-symbols-outlined text-[20px]">percent</span>
              </div>
              <div>
                <span class="font-label-sm text-label-sm text-outline block">Costo total relativo</span>
                <span
                  class="font-label-md text-label-md text-on-surface"
                  id="costMultiplierDisplay"
                >1.37x el capital prestado</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Collapsible Amortization Table Section -->
      <div class="mt-8 bg-surface-container-lowest rounded-xl p-space-lg shadow-sm flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
          <div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-primary text-[22px]">table_view</span>
              <h3 class="font-headline-md text-headline-md text-on-surface">
                Tabla de Amortización Francesa Proyectada
              </h3>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">
              Visualización detallada de los primeros 6 periodos mensuales y comportamiento del saldo.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors"
              id="toggleTableBtn"
            ><span class="material-symbols-outlined text-[18px]" id="toggleIcon">expand_less</span> <span id="toggleText">Ocultar Detalle</span> </button>
          </div>
        </div>
        <div class="overflow-x-auto transition-all duration-300" id="tableContainer">
          <table class="w-full text-left font-body-sm text-body-sm">
            <thead>
              <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                <th class="py-3 px-4 rounded-l-lg">Periodo</th>
                <th class="py-3 px-4">Saldo Inicial</th>
                <th class="py-3 px-4">Cuota Fija</th>
                <th class="py-3 px-4">Interés (1.8% M.V.)</th>
                <th class="py-3 px-4">Abono a Capital</th>
                <th class="py-3 px-4 rounded-r-lg text-right">Saldo Final</th>
              </tr>
            </thead>
            <tbody class="text-on-surface" id="amortizationBody">
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 1</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$15.000.000</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$270.000</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $299.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$14.700.243</td>
              </tr>
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 2</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$14.700.243</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$264.604</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $305.153
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$14.395.089</td>
              </tr>
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 3</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$14.395.089</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$259.112</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $310.646
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$14.084.444</td>
              </tr>
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 4</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$14.084.444</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$253.520</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $316.237
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$13.768.206</td>
              </tr>
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 5</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$13.768.206</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$247.828</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $321.930
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$13.446.276</td>
              </tr>
              <tr class="hover:bg-surface-container transition-colors">
                <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes 6</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px]">$13.446.276</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">
                  $569.757
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">$242.033</td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">
                  $327.725
                </td>
                <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">$13.118.552</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Table helper footer -->
        <div class="flex items-center justify-between pt-2 text-on-surface-variant font-body-sm text-body-sm">
          <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-primary">lock_clock</span> Simulación calculada con precisión matemática decimal truncada a moneda nacional COP. </span>
          <span class="font-label-sm text-label-sm text-outline">Mostrando primeros 6 periodos</span>
        </div>
      </div>
      <!-- Bottom Section: Saved Simulations History -->
      <div class="mt-8 flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div>
            <h3 class="font-headline-lg text-headline-lg text-on-surface">
              Historial de Simulaciones Guardadas
            </h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
              Compara diferentes alternativas de crédito y toma decisiones informadas en familia.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <span class="font-label-sm text-label-sm text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-full">3 simulaciones archivadas</span>
          </div>
        </div>
        <!-- Cards Grid -->
        {{-- TODO(backend): $simulaciones viene de SimulacionCreditoController@index. DINÁMICO: simulaciones guardadas del usuario ($simulaciones). Botón eliminar = formulario DELETE; "recargar" lee data-* de la tarjeta. --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter" id="savedCardsGrid">
          @forelse(($simulaciones ?? []) as $sim)
            <!-- Card 1: Moto Nueva -->
            <div class="p-space-md bg-surface-container-lowest rounded-xl shadow-sm flex flex-col justify-between gap-4 hover:shadow-md transition-shadow">
              <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between">
                  <span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container">Simulación</span>
                  <span class="font-label-sm text-label-sm text-outline">{{ $sim->created_at->diffForHumans() }}</span>
                </div>
                <div>
                  <h4 class="font-headline-sm text-headline-sm text-on-surface">{{ $sim->nombre }}</h4>
                  <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $sim->plazo }} meses</p>
                </div>
                <div class="p-3 bg-surface-container-low rounded-lg flex items-center justify-between">
                  <div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant block">Monto solicitado</span>
                    <span class="font-numeric-md text-numeric-md text-on-surface">${{ number_format($sim->monto, 0, ',', '.') }}</span>
                  </div>
                  <div class="text-right">
                    <span class="font-label-sm text-label-sm text-on-surface-variant block">Cuota mensual</span>
                    <span class="font-numeric-md text-numeric-md text-primary">${{ number_format($sim->cuota, 0, ',', '.') }}</span>
                  </div>
                </div>
                <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
                  <span class="material-symbols-outlined text-[16px] text-primary">verified</span>
                  <span>Tasa {{ $sim->tasa }}% M.V.</span>
                </div>
              </div>
              <div class="flex items-center justify-between pt-2">
                <button
                  type="button"
                  class="flex items-center gap-1 font-label-md text-label-md text-primary hover:underline"
                  onclick="window.loadPreset(@js($sim->nombre), {{ $sim->monto }}, {{ $sim->tasa }}, {{ $sim->plazo }})"
                ><span class="material-symbols-outlined text-[16px]">sync</span> <span>Cargar Datos</span> </button>
                <form method="POST" action="{{ route('creditos.eliminar', $sim->id) }}">
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="p-1.5 text-outline hover:text-error rounded-lg hover:bg-error-container/20 transition-colors"
                    title="Eliminar simulación"
                  ><span class="material-symbols-outlined text-[18px]">delete</span> </button>
                </form>
              </div>
            </div>
          @empty
            <p class="font-body-md text-body-md text-on-surface-variant col-span-full">
              No tienes simulaciones guardadas
            </p>
          @endforelse
        </div>
      </div>
      <!-- Notification Toast for Save simulation -->
      <div
        class="fixed bottom-6 right-6 bg-inverse-surface text-inverse-on-surface px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 transition-opacity duration-300 opacity-0 pointer-events-none z-50"
        id="saveToast"
      >
        <span class="material-symbols-outlined text-primary-fixed text-[22px]">check_circle</span>
        <span
          class="font-body-md text-body-md"
          id="toastMessage"
        >Simulación guardada correctamente en tu historial.</span>
      </div>
    </div>
    <script src="js/components.js"></script>
    <script src="js/simulador-de-creditos.js"></script>
    {{-- Formulario oculto para GUARDAR: el JS copia aquí los valores del simulador y lo envía (ver TODO en simulador-de-creditos.js). --}}
    <form id="saveSimForm" class="hidden" method="POST" action="{{ route('creditos.guardar') }}">
      @csrf
      <input type="hidden" name="nombre">
      <input type="hidden" name="monto">
      <input type="hidden" name="tasa">
      <input type="hidden" name="plazo">
    </form>
  </main>
@endsection
@push('scripts')
  <script>window.CC = { ingreso: {{ (int) ($ingresoMensual ?? 0) }} };</script>
  <script src="{{ asset('js/simulador-de-creditos.js') }}"></script>
@endpush
