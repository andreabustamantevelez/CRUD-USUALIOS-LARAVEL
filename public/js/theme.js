/*
 * ==============================================================================
 * ARCHIVO : public/js/theme.js
 * QUÉ ES  : Controla el modo claro / oscuro de toda la aplicación.
 * CÓMO FUNCIONA:
 *   - Al cargar: lee la preferencia guardada en localStorage ('cc-tema').
 *     Si no hay ninguna, respeta el sistema operativo (prefers-color-scheme).
 *   - Aplica data-theme="claro" o "oscuro" al <html>.
 *   - Los botones con data-theme-toggle cambian el tema al hacer clic.
 *   - El ícono del botón (sol / luna) se actualiza solo.
 *   - Al alternar, la transición global (html.cambiando-tema) se activa con
 *     un reflow forzado para que el navegador interpole de verdad.
 *   - Respeta prefers-reduced-motion: reduce (sin animación).
 * Lo carga layouts/base.blade.php con defer.
 * ==============================================================================
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'cc-tema';
  var html = document.documentElement;
  var temporizadorTransicion = null;

  /** Devuelve 'oscuro' | 'claro' según la preferencia efectiva. */
  function temaActual() {
    var guardado = null;
    try { guardado = localStorage.getItem(STORAGE_KEY); } catch (e) { /* modo privado */ }
    if (guardado === 'oscuro' || guardado === 'claro') return guardado;
    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'oscuro' : 'claro';
  }

  /** ¿El usuario prefiere movimiento reducido? */
  function movimientoReducido() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  /** Aplica el tema al <html> y actualiza todos los botones toggle. */
  function aplicar(tema) {
    html.setAttribute('data-theme', tema);
    if (window.CC && typeof window.CC.refreshIcons === 'function') {
      window.CC.refreshIcons();
    }
    html.style.colorScheme = tema === 'oscuro' ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-toggle]').forEach(function (boton) {
      var icono = boton.querySelector('[data-theme-icon]');
      var etiqueta = boton.querySelector('[data-theme-label]');
      if (icono) {
        icono.textContent = tema === 'oscuro' ? 'light_mode' : 'dark_mode';
      }
      if (etiqueta) {
        etiqueta.textContent = tema === 'oscuro' ? 'Modo claro' : 'Modo oscuro';
      }
      boton.setAttribute('aria-label', tema === 'oscuro' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
      boton.setAttribute('title', tema === 'oscuro' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
    });
  }

  /** Cambia al tema opuesto y guarda la preferencia, con transición suave. */
  function alternar() {
    var nuevo = temaActual() === 'oscuro' ? 'claro' : 'oscuro';
    try { localStorage.setItem(STORAGE_KEY, nuevo); } catch (e) { /* ignorar */ }

    // Sin animación si el usuario lo pidió.
    if (movimientoReducido()) {
      aplicar(nuevo);
      return;
    }

    // 1. Activar la clase de transición.
    html.classList.add('cambiando-tema');

    // 2. FORZAR REFLOW: el navegador "ve" el estado inicial antes de cambiar
    //    data-theme. Sin esto, add + setAttribute caen en el mismo frame y
    //    el navegador aplica el estado final sin interpolar.
    //    (leer offsetWidth fuerza layout; también vale getBoundingClientRect)
    void html.offsetWidth;

    // 3. Cambiar el tema (ahora sí se interpola).
    aplicar(nuevo);

    // 4. Quitar la clase cuando termine la transición.
    //    La duración real es --t-slow (240ms en transiciones.css); 300ms
    //    deja margen para no cortar antes de tiempo.
    if (temporizadorTransicion) {
      window.clearTimeout(temporizadorTransicion);
    }
    temporizadorTransicion = window.setTimeout(function () {
      html.classList.remove('cambiando-tema');
      temporizadorTransicion = null;
    }, 200);
  }

  // Aplica el tema lo antes posible (sin transición, para no parpadear al cargar).
  aplicar(temaActual());

  // Enlaza los botones (después de que el DOM esté listo).
  function enlazar() {
    document.querySelectorAll('[data-theme-toggle]').forEach(function (boton) {
      if (boton.dataset.themeBound === '1') return;
      boton.dataset.themeBound = '1';
      boton.addEventListener('click', alternar);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', enlazar);
  } else {
    enlazar();
  }

  // Re-enlaza si Blade reemplaza contenido por AJAX (Livewire, etc.).
  document.addEventListener('cc:tema-cambiado', enlazar);
})();