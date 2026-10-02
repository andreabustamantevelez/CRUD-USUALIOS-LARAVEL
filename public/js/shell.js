/*
 * ==============================================================================
 * ARCHIVO : public/js/shell.js
 * QUÉ ES  : Interacciones comunes del armazón: menú lateral móvil, menú del header público, ojo de
 *           contraseña y cierre automático del toast. Lo carga layouts/base.blade.php en TODAS las
 *           páginas.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Si se agregan modales de confirmación (eliminar gasto, cuenta, etc.), ponerlos aquí o en
 *      public/js/modales.js.
 *   2. Si se migra a Vite, importar este archivo desde resources/js/app.js.
 * CÓMO HACERLO / NOTAS:
 *   - Atributos que escucha: data-nav-toggle, data-nav-close, data-menu-toggle, data-menu-panel.
 * ==============================================================================
 */

// 1) Clics delegados: abrir/cerrar menús.
document.addEventListener('click', function (e) {
    // Menú lateral (zona privada, solo móvil)
    if (e.target.closest('[data-nav-toggle]')) document.body.classList.toggle('nav-open');
    if (e.target.closest('[data-nav-close]') || e.target.closest('aside nav a')) document.body.classList.remove('nav-open');
    // Menú del header público (móvil)
    if (e.target.closest('[data-menu-toggle]')) {
      var panel = document.querySelector('[data-menu-panel]');
      if (panel) panel.classList.toggle('hidden');
    }
});
// 2) Ojo de contraseña (lo usan login, registro y restablecer).
if (!window.togglePasswordVisibility) {
  window.togglePasswordVisibility = function (inputId, iconId) {
    var input = document.getElementById(inputId), icon = document.getElementById(iconId);
    if (!input || !icon) return;
    var oculto = input.type === 'password';
    input.type = oculto ? 'text' : 'password';
    icon.textContent = oculto ? 'visibility_off' : 'visibility';
  };
}
// 3) El mensaje flash (toast) desaparece solo a los 3,5 s.
setTimeout(function () {
    var toast = document.getElementById('toastFlash');
    if (toast) toast.classList.add('opacity-0');
  }, 3500);
