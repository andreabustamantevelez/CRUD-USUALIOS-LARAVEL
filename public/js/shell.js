/*
 * ==============================================================================
 * ARCHIVO : public/js/shell.js
 * QUÉ ES  : Interacciones comunes del armazón: menú lateral móvil, menú del header público, ojo de
 *           contraseña, cierre automático del toast y menú del usuario de la barra superior. Lo carga layouts/base.blade.php en TODAS las
 *           páginas.
 * QUÉ HAY QUE HACER (en orden):
 *   1. Si se agregan modales de confirmación (eliminar gasto, cuenta, etc.), ponerlos aquí o en
 *      public/js/modales.js.
 *   2. Si se migra a Vite, importar este archivo desde resources/js/app.js.
 * CÓMO HACERLO / NOTAS:
 *   - Atributos que escucha: data-nav-toggle, data-nav-close, data-menu-toggle, data-menu-panel,
 *     data-user-menu y data-user-menu-button (menú del usuario, sección 4).
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

// 4) Menú del usuario (barra superior): clic o toque lo abre y lo deja fijo; clic afuera o Escape lo
//    cierra. En escritorio con mouse también se abre al pasar el cursor (eso lo hace menu-usuario.css).
(function () {
  function cerrar(menu) {
    menu.classList.remove('is-open');
    var boton = menu.querySelector('[data-user-menu-button]');
    if (boton) boton.setAttribute('aria-expanded', 'false');
  }
  document.addEventListener('click', function (e) {
    var boton = e.target.closest('[data-user-menu-button]');
    document.querySelectorAll('[data-user-menu].is-open').forEach(function (menu) {
      if (!menu.contains(e.target)) cerrar(menu);
    });
    if (boton) {
      var menu = boton.closest('[data-user-menu]');
      var abierto = menu.classList.toggle('is-open');
      boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('[data-user-menu].is-open').forEach(function (menu) {
      cerrar(menu);
      var boton = menu.querySelector('[data-user-menu-button]');
      if (boton) boton.focus();
    });
  });
})();
