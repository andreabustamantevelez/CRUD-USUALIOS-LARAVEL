/*
 * ==============================================================================
 * ARCHIVO : public/js/simulador-de-creditos.js
 * QUÉ ES  : Lógica en el navegador del simulador de créditos (vista previa en vivo).
 * QUÉ HAY QUE HACER (en orden):
 *   1. Verificar que la vista defina window.CC = { ingreso: ... } ANTES de cargar este archivo (ya
 *      lo hace simulador.blade.php).
 *   2. Activar el guardado real (ver TODO "GUARDAR SIMULACIÓN" más abajo).
 *   3. Confirmar que la fórmula del servidor da el mismo resultado que este archivo.
 *   4. Cuando el ingreso es 0 no hay semáforo: mostrar el aviso para registrarlo en Ajustes >
 *      Perfil (falta en la vista).
 * CÓMO HACERLO / NOTAS:
 *   - Este archivo NO guarda ni consulta nada; solo calcula y pinta.
 *   - Los ids (amountInput, rateInput, installmentDisplay...) son contrato con la vista: no
 *     renombrarlos sin cambiar ambos.
 * CÓMO SE COMPRUEBA:
 *   - Probar montos/tasas/plazos extremos y comparar con el cálculo del servidor.
 * ==============================================================================
 */

(function() {
    // Parámetros del usuario. El ingreso mensual lo imprime el backend en la vista (window.CC.ingreso).
    const MONTHLY_INCOME = (window.CC && window.CC.ingreso) || 0; // viene del backend (perfil del usuario)
    const MAX_SAFE_RATIO = 0.30; // 30%
    const SAFE_MAX_AMOUNT = MONTHLY_INCOME * MAX_SAFE_RATIO; // $1.155.000 COP

    // Referencias a elementos de la vista (los ids deben existir en simulador.blade.php)
    const amountInput = document.getElementById('amountInput');
    const amountSlider = document.getElementById('amountSlider');
    const rateInput = document.getElementById('rateInput');
    const eaRateDisplay = document.getElementById('eaRateDisplay');
    const termValueLabel = document.getElementById('termValueLabel');
    const installmentDisplay = document.getElementById('installmentDisplay');
    const totalPaymentDisplay = document.getElementById('totalPaymentDisplay');
    const totalInterestDisplay = document.getElementById('totalInterestDisplay');
    const capitalBarSegment = document.getElementById('capitalBarSegment');
    const interestBarSegment = document.getElementById('interestBarSegment');
    const debtRatioDisplay = document.getElementById('debtRatioDisplay');
    const debtRatioBar = document.getElementById('debtRatioBar');
    const debtEvaluationText = document.getElementById('debtEvaluationText');
    const semaphoreBadge = document.getElementById('semaphoreBadge');
    const amortizationBody = document.getElementById('amortizationBody');
    const termButtons = document.querySelectorAll('.term-btn');
    const toggleTableBtn = document.getElementById('toggleTableBtn');
    const tableContainer = document.getElementById('tableContainer');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    const resetBtn = document.getElementById('resetBtn');
    const saveSimBtn = document.getElementById('saveSimBtn');
    const saveToast = document.getElementById('saveToast');
    const toastMessage = document.getElementById('toastMessage');
    const endYearDisplay = document.getElementById('endYearDisplay');
    const costMultiplierDisplay = document.getElementById('costMultiplierDisplay');
    const loanNameInput = document.getElementById('loanName');

    let currentTerm = 36;

    // Formato de moneda colombiana (COP, sin decimales)
    function formatCOP(val) {
      return '$' + Math.round(val).toLocaleString('es-CO');
    }

    // Motor de cálculo (amortización francesa). TODO: el servidor debe replicar EXACTAMENTE esta fórmula (RQNF-04) y es el que vale al guardar.
    window.recalculateLoan = function() {
      let principal = parseFloat(amountInput.value) || 0;
      let monthlyRate = (parseFloat(rateInput.value) || 0) / 100;
      let n = currentTerm;

      if (principal <= 0 || monthlyRate <= 0 || n <= 0) return;

      // Calculate Effective Annual Rate: EA = (1 + i_m)^12 - 1
      let ea = (Math.pow(1 + monthlyRate, 12) - 1) * 100;
      eaRateDisplay.textContent = ea.toFixed(2) + '%';

      // French formula: Cuota = P * [ i / (1 - (1+i)^-n) ]
      let monthlyInstallment = principal * (monthlyRate / (1 - Math.pow(1 + monthlyRate, -n)));
      let totalPayment = monthlyInstallment * n;
      let totalInterest = totalPayment - principal;

      // Update Top Result Cards
      installmentDisplay.textContent = formatCOP(monthlyInstallment);
      totalPaymentDisplay.textContent = formatCOP(totalPayment);
      totalInterestDisplay.textContent = formatCOP(totalInterest);

      // Relative bar
      let capitalShare = (principal / totalPayment) * 100;
      let interestShare = (totalInterest / totalPayment) * 100;
      capitalBarSegment.style.width = capitalShare.toFixed(1) + '%';
      interestBarSegment.style.width = interestShare.toFixed(1) + '%';

      // Calculate Debt Capacity
      let debtRatio = MONTHLY_INCOME > 0 ? (monthlyInstallment / MONTHLY_INCOME) * 100 : 0;
      debtRatioDisplay.textContent = debtRatio.toFixed(1) + '%';
      debtRatioBar.style.width = Math.min(debtRatio, 100).toFixed(1) + '%';

      // Semaphore Logic & Feedback
      if (debtRatio <= 20) {
        semaphoreBadge.className = 'px-2.5 py-0.5 rounded-full bg-primary/15 text-primary font-label-sm text-label-sm flex items-center gap-1.5';
        semaphoreBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> Rango Saludable';
        debtRatioBar.className = 'h-full bg-primary rounded-full transition-all duration-500';
        debtEvaluationText.innerHTML = `<strong class="text-on-surface">Diagnóstico:</strong> Esta cuota mensual compromete únicamente el <strong class="text-primary">${debtRatio.toFixed(1)}%</strong> de tus ingresos familiares (${formatCOP(MONTHLY_INCOME)} COP). Mantienes un margen libre de seguridad de <strong class="text-on-surface">${formatCOP(SAFE_MAX_AMOUNT - monthlyInstallment)} COP</strong> antes de tocar el límite prudencial del 30%.`;
      } else if (debtRatio <= 30) {
        semaphoreBadge.className = 'px-2.5 py-0.5 rounded-full bg-surface-container-highest text-secondary font-label-sm text-label-sm flex items-center gap-1.5';
        semaphoreBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-secondary"></span> Rango Moderado';
        debtRatioBar.className = 'h-full bg-secondary rounded-full transition-all duration-500';
        debtEvaluationText.innerHTML = `<strong class="text-on-surface">Diagnóstico:</strong> La cuota se encuentra en el rango moderado (<strong class="text-secondary">${debtRatio.toFixed(1)}%</strong>). Cumple con las directrices de salud financiera pero deja poco margen para otros créditos simultáneos.`;
      } else {
        semaphoreBadge.className = 'px-2.5 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm flex items-center gap-1.5';
        semaphoreBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-error animate-ping"></span> Riesgo de Sobreendeudamiento';
        debtRatioBar.className = 'h-full bg-error rounded-full transition-all duration-500';
        debtEvaluationText.innerHTML = `<strong class="text-error">Alerta Preventiva:</strong> La cuota representa el <strong class="text-error">${debtRatio.toFixed(1)}%</strong> de tus ingresos, superando el umbral del 30% recomendado (${formatCOP(SAFE_MAX_AMOUNT)} COP max). Recomendamos aumentar el plazo a 48 o 60 meses o reducir el capital solicitado.`;
      }

      // Projections: Estimated date & Multiplier
      let futureDate = new Date();
      futureDate.setMonth(futureDate.getMonth() + n);
      const monthsEs = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
      endYearDisplay.textContent = monthsEs[futureDate.getMonth()] + ' ' + futureDate.getFullYear();
      let multiplier = (totalPayment / principal).toFixed(2);
      costMultiplierDisplay.textContent = multiplier + 'x el capital prestado';

      // Populate First 6 Amortization Periods
      let currentBalance = principal;
      let tableRowsHTML = '';
      let periodsToShow = Math.min(6, n);

      for (let i = 1; i <= periodsToShow; i++) {
        let interestPart = currentBalance * monthlyRate;
        let principalPart = monthlyInstallment - interestPart;
        let finalBalance = currentBalance - principalPart;
        if (finalBalance < 0) finalBalance = 0;

        tableRowsHTML += `
        <tr class="hover:bg-surface-container transition-colors">
        <td class="py-3.5 px-4 font-label-md text-label-md text-primary">Mes ${i}</td>
        <td class="py-3.5 px-4 font-numeric-md text-[14px]">${formatCOP(currentBalance)}</td>
        <td class="py-3.5 px-4 font-numeric-md text-[14px] font-semibold text-on-surface">${formatCOP(monthlyInstallment)}</td>
        <td class="py-3.5 px-4 font-numeric-md text-[14px] text-error font-medium">${formatCOP(interestPart)}</td>
        <td class="py-3.5 px-4 font-numeric-md text-[14px] text-primary-container font-medium">${formatCOP(principalPart)}</td>
        <td class="py-3.5 px-4 font-numeric-md text-[14px] text-right font-medium">${formatCOP(finalBalance)}</td>
        </tr>
        `;
        currentBalance = finalBalance;
      }
      amortizationBody.innerHTML = tableRowsHTML;
    };

    // Sincroniza el control deslizante con la caja numérica del monto
    amountSlider.addEventListener('input', function() {
        amountInput.value = this.value;
        window.recalculateLoan();
    });

    amountInput.addEventListener('input', function() {
        amountSlider.value = this.value;
        window.recalculateLoan();
    });

    rateInput.addEventListener('input', function() {
        window.recalculateLoan();
    });

    // Botones de plazo (12, 24, 36, 48, 60 meses)
    termButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            termButtons.forEach(b => {
                b.className = 'term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center';
            });
            this.className = 'term-btn py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-all text-center';
            currentTerm = parseInt(this.getAttribute('data-term'), 10);
            termValueLabel.textContent = currentTerm;
            window.recalculateLoan();
        });
    });

    // Mostrar / ocultar la tabla de amortización
    let tableOpen = true;
    toggleTableBtn.addEventListener('click', function() {
        tableOpen = !tableOpen;
        if (!tableOpen) {
          tableContainer.classList.add('hidden');
          toggleIcon.textContent = 'expand_more';
          toggleText.textContent = 'Ver Detalle';
        } else {
          tableContainer.classList.remove('hidden');
          toggleIcon.textContent = 'expand_less';
          toggleText.textContent = 'Ocultar Detalle';
        }
    });

    // Limpiar: vuelve a los valores iniciales del formulario
    resetBtn.addEventListener('click', function() {
        loanNameInput.value = 'Crédito Libre Inversión';
        amountInput.value = 15000000;
        amountSlider.value = 15000000;
        rateInput.value = 1.80;
        currentTerm = 36;
        termValueLabel.textContent = 36;
        termButtons.forEach(btn => {
            if (btn.getAttribute('data-term') === '36') {
              btn.className = 'term-btn py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-all text-center';
            } else {
              btn.className = 'term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center';
            }
        });
        window.recalculateLoan();
    });

    // ---------------------------------------------------------------------------
    // TODO(backend) GUARDAR SIMULACIÓN. Hoy solo muestra un aviso (demo): NO guarda nada.
    //   1. Copiar a los campos ocultos de #saveSimForm (nombre, monto, tasa, plazo) los valores del simulador.
    //   2. Enviar ese formulario (POST a la ruta creditos.guardar; el token CSRF ya está dentro).
    //   3. El servidor recalcula cuota, total e intereses con CalculadoraCredito y guarda la fila.
    //   4. Al volver, el controlador responde con ->with('exito', 'Simulación guardada') y <x-toast /> lo muestra.
    //   5. Cuando esto funcione, borrar el aviso temporal de abajo (toast simulado).
    // ---------------------------------------------------------------------------
    saveSimBtn.addEventListener('click', function() {
        let simName = loanNameInput.value || 'Cotización Personalizada';
        toastMessage.textContent = `"${simName}" guardada en tu historial de simulaciones.`;
        saveToast.classList.remove('opacity-0', 'pointer-events-none');
        saveToast.classList.add('opacity-100');

        setTimeout(() => {
            saveToast.classList.add('opacity-0', 'pointer-events-none');
            saveToast.classList.remove('opacity-100');
          }, 3500);
    });

    // Carga una simulación guardada en el formulario. La llama el botón de cada tarjeta del historial (los datos vienen de la base de datos).
    window.loadPreset = function(name, amount, rate, term) {
      loanNameInput.value = name;
      amountInput.value = amount;
      amountSlider.value = amount;
      rateInput.value = rate;
      currentTerm = term;
      termValueLabel.textContent = term;

      termButtons.forEach(btn => {
          if (parseInt(btn.getAttribute('data-term'), 10) === term) {
            btn.className = 'term-btn py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-all text-center';
          } else {
            btn.className = 'term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center';
          }
      });

      window.recalculateLoan();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Primer cálculo al abrir la página
    window.recalculateLoan();
})();
