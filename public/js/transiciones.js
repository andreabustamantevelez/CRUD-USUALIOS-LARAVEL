/*
 * ==============================================================================
 * ARCHIVO : public/js/transiciones.js
 * QUÉ ES  : Marca las fuentes como "listas" cuando terminan de cargar, para que
 *           los iconos de Material Symbols aparezcan (evita el flash de texto).
 *           Si en 800 ms no han cargado, hace un fallback y muestra igual.
 * CARGA : defer, antes de theme.js y shell.js.
 * ==============================================================================
 */
(function () {
  'use strict';

  var html = document.documentElement;

  function marcarListo() {
    html.classList.add('fuentes-listas');
  }
  function marcarFallback() {
    html.classList.add('fuentes-listas-fallback');
  }

  // document.fonts.ready resuelve cuando TODAS las fuentes terminan de cargar
  // (o fallan). Es la forma limpia y sin polling.
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(marcarListo).catch(marcarFallback);
  } else {
    // Navegador muy viejo: cae al fallback directo.
    marcarFallback();
  }

  // Red de seguridad: si document.fonts.ready nunca resuelve (raro pero pasa),
  // mostramos los iconos pasados 800 ms.
  setTimeout(function () {
    if (!html.classList.contains('fuentes-listas')) {
      marcarFallback();
    }
  }, 800);
})();