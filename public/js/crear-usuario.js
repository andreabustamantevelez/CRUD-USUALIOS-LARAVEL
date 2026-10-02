/*
 * ==============================================================================
 * ARCHIVO : public/js/crear-usuario.js
 * QUÉ ES  : Interacciones visuales del formulario de registro: tarjetas de tipo de cuenta, ojo de
 *           contraseña y medidor de fortaleza.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Confirmar que los ids (card-standard, card-family, tipo_cuenta, password,
 *      strength-label...) coinciden con crear-usuario.blade.php.
 *   2. Si el servidor devuelve errores y old('tipo_cuenta') = familiar, llamar
 *      selectAccountType('family') al cargar para que la tarjeta quede marcada (falta).
 *   3. Mantener la regla de contraseña igual a la de StoreRegistroRequest.
 * CÓMO HACERLO / NOTAS:
 *   - Nada de esto reemplaza la validación del servidor.
 * ==============================================================================
 */

// Marca la tarjeta elegida y guarda el tipo en el campo oculto #tipo_cuenta, que es el que viaja al servidor.
function selectAccountType(type) {
  document.getElementById('tipo_cuenta').value = (type === 'standard') ? 'estandar' : 'familiar';
  const cardStd = document.getElementById('card-standard');
  const cardFam = document.getElementById('card-family');
  const radioStd = document.getElementById('radio-standard');
  const radioFam = document.getElementById('radio-family');

  if (type === 'standard') {
    cardStd.classList.remove('bg-surface-container', 'opacity-90');
    cardStd.classList.add('bg-surface-container-low', 'shadow-sm');
    radioStd.className = "w-5 h-5 rounded-full bg-primary flex items-center justify-center mt-1";
    radioStd.innerHTML = '<div class="w-2 h-2 rounded-full bg-surface-container-lowest"></div>';

    cardFam.classList.remove('bg-surface-container-low', 'shadow-sm');
    cardFam.classList.add('bg-surface-container', 'opacity-90');
    radioFam.className = "w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center mt-1";
    radioFam.innerHTML = '<div class="w-2 h-2 rounded-full bg-surface-container-lowest opacity-0"></div>';
  } else {
    cardFam.classList.remove('bg-surface-container', 'opacity-90');
    cardFam.classList.add('bg-surface-container-low', 'shadow-sm');
    radioFam.className = "w-5 h-5 rounded-full bg-primary flex items-center justify-center mt-1";
    radioFam.innerHTML = '<div class="w-2 h-2 rounded-full bg-surface-container-lowest"></div>';

    cardStd.classList.remove('bg-surface-container-low', 'shadow-sm');
    cardStd.classList.add('bg-surface-container', 'opacity-90');
    radioStd.className = "w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center mt-1";
    radioStd.innerHTML = '<div class="w-2 h-2 rounded-full bg-surface-container-lowest opacity-0"></div>';
  }
}

// Muestra u oculta la contraseña (solo ayuda visual).
function togglePasswordVisibility(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);
  if (!input || !icon) return;

  if (input.type === 'password') {
    input.type = 'text';
    icon.textContent = 'visibility_off';
  } else {
    input.type = 'password';
    icon.textContent = 'visibility';
  }
}

// Medidor de seguridad de la clave. El servidor valida la regla real (mínimo 8 caracteres).
function evaluatePasswordStrength(val) {
  const bar1 = document.getElementById('bar-1');
  const bar2 = document.getElementById('bar-2');
  const bar3 = document.getElementById('bar-3');
  const label = document.getElementById('strength-label');

  const reqLen = document.getElementById('req-len');
  const reqNum = document.getElementById('req-num');
  const reqSym = document.getElementById('req-sym');

  const hasLen = val.length >= 8;
  const hasNum = /\d/.test(val);
  const hasSymOrUpper = /[^A-Za-z0-9]/.test(val) || /[A-Z]/.test(val);

  // Requirement indicators
  updateReqItem(reqLen, hasLen);
  updateReqItem(reqNum, hasNum);
  updateReqItem(reqSym, hasSymOrUpper);

  let score = 0;
  if (hasLen) score++;
  if (hasNum) score++;
  if (hasSymOrUpper) score++;

  if (val.length === 0) {
    label.textContent = 'Mínima';
    label.className = 'font-label-sm text-label-sm text-secondary font-bold';
    bar1.className = 'h-full w-1/3 bg-error rounded-full transition-all duration-300';
    bar2.className = 'h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300';
    bar3.className = 'h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300';
  } else if (score === 1) {
    label.textContent = 'Débil';
    label.className = 'font-label-sm text-label-sm text-error font-bold';
    bar1.className = 'h-full w-1/3 bg-error rounded-full transition-all duration-300';
    bar2.className = 'h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300';
    bar3.className = 'h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300';
  } else if (score === 2) {
    label.textContent = 'Media';
    label.className = 'font-label-sm text-label-sm text-secondary font-bold';
    bar1.className = 'h-full w-1/3 bg-secondary-container rounded-full transition-all duration-300';
    bar2.className = 'h-full w-1/3 bg-secondary-container rounded-full transition-all duration-300';
    bar3.className = 'h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300';
  } else if (score === 3) {
    label.textContent = 'Fuerte y Segura';
    label.className = 'font-label-sm text-label-sm text-primary font-bold';
    bar1.className = 'h-full w-1/3 bg-primary rounded-full transition-all duration-300';
    bar2.className = 'h-full w-1/3 bg-primary rounded-full transition-all duration-300';
    bar3.className = 'h-full w-1/3 bg-primary rounded-full transition-all duration-300';
  }
}

// Pinta cada requisito (8 caracteres, número, símbolo/mayúscula) como cumplido o pendiente.
function updateReqItem(element, isValid) {
  if (!element) return;
  const icon = element.querySelector('.material-symbols-outlined');
  if (isValid) {
    icon.textContent = 'check_circle';
    icon.className = 'material-symbols-outlined text-xs text-primary';
    element.className = 'flex items-center gap-1 text-on-surface font-semibold';
  } else {
    icon.textContent = 'radio_button_unchecked';
    icon.className = 'material-symbols-outlined text-xs text-secondary';
    element.className = 'flex items-center gap-1 text-on-surface-variant font-normal';
  }
}
