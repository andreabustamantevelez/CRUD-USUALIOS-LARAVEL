/*
 * Header de la landing: siempre fijo arriba + efecto "compact on scroll".
 * - Al top: header completo (80px, logo normal).
 * - Al bajar más de 30px: se añade .is-scrolled → header de 64px,
 *   logo un poco más pequeño, fondo más opaco y sombra.
 * - Al volver arriba: vuelve al estado original.
 * - El header NUNCA se oculta.
 */
(function () {
  const header = document.querySelector('[data-landing-header]');
  if (!header) return;

  const UMBRAL = 30;
  let ticking = false;
  let ultimoEvento = null;

  // Defensa 1: si el header quedó dentro de un contenedor con transform/filter,
  // position:fixed deja de ser relativo a la pantalla. Se saca a <body>.
  if (header.parentElement !== document.body) {
    document.body.prepend(header);
  }

  // Lee cuánto se ha desplazado la página. Cubre el caso en que el scroll
  // ocurre en <body> o en un contenedor interno y no en window.
  function leerScroll(e) {
    const t = e && e.target;
    if (t && t !== document && t.nodeType === 1 && t.scrollTop) {
      return t.scrollTop;
    }
    return (
      window.pageYOffset ||
      document.documentElement.scrollTop ||
      document.body.scrollTop ||
      0
    );
  }

  function actualizar() {
    header.classList.toggle('is-scrolled', leerScroll(ultimoEvento) > UMBRAL);
    ticking = false;
  }

  function alScroll(e) {
    ultimoEvento = e;
    if (!ticking) {
      window.requestAnimationFrame(actualizar);
      ticking = true;
    }
  }

  // Defensa 2: "capture" detecta el scroll de cualquier contenedor, no solo de window.
  document.addEventListener('scroll', alScroll, { passive: true, capture: true });

  // Al volver con el botón "atrás" el navegador restaura el scroll.
  window.addEventListener('pageshow', function () {
    ultimoEvento = null;
    actualizar();
  });

  actualizar();
})();