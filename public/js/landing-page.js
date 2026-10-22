/*
 * ==============================================================================
 * ARCHIVO : public/js/landing-page.js
 * QUÉ ES  : Interactividad EXCLUSIVA de la Landing page pública.
 * HACE    : aparición al scroll, contadores, palabra rotatoria, fotos del hero que
 *           se alternan, barra de progreso,
 *           inclinación 3D, cinta infinita, respaldo de imágenes y el demo
 *           interactivo (diagnóstico 50/30/20 + simulador de crédito).
 * NO HACE : no guarda nada, no llama al servidor, no usa datos reales.
 *           El demo es solo una prueba; los cálculos reales viven en el backend.
 * ==============================================================================
 */
(function () {
  'use strict';

  var root = document.documentElement;
  root.classList.add('lp-js');

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fmt = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

  function cop(n) {
    return '$' + fmt.format(Math.round(n));
  }
  function $(id) {
    return document.getElementById(id);
  }
  function digits(str) {
    return parseInt(String(str).replace(/\D/g, ''), 10) || 0;
  }

  /* ------------------------------------------------------------------ */
  /* Contadores                                                          */
  /* ------------------------------------------------------------------ */
  function countUp(el) {
    if (el.dataset.done) return;
    el.dataset.done = '1';
    var end = parseFloat(el.dataset.count) || 0;
    var isCop = el.dataset.format === 'cop';
    var suffix = el.dataset.suffix || '';

    function write(v) {
      el.textContent = (isCop ? cop(v) : fmt.format(Math.round(v))) + suffix;
    }
    if (reduce) {
      write(end);
      return;
    }
    var t0 = null;
    var dur = 1500;
    write(0);
    function step(ts) {
      if (!t0) t0 = ts;
      var p = Math.min((ts - t0) / dur, 1);
      write(end * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ------------------------------------------------------------------ */
  /* Aparición al hacer scroll                                           */
  /* ------------------------------------------------------------------ */
  function show(el) {
    el.classList.add('is-visible');
    if (el.hasAttribute('data-count')) countUp(el);
    [].forEach.call(el.querySelectorAll('[data-count]'), countUp);
  }

  var revealEls = [].slice.call(document.querySelectorAll('.lp-reveal'));
  if (!('IntersectionObserver' in window) || reduce) {
    revealEls.forEach(show);
  } else {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            show(e.target);
            io.unobserve(e.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: '0px 0px -8% 0px' }
    );
    revealEls.forEach(function (el) {
      io.observe(el);
    });
  }

  /* Contadores que no están dentro de un bloque .lp-reveal */
  [].forEach.call(document.querySelectorAll('[data-count]'), function (el) {
    if (!el.closest('.lp-reveal')) countUp(el);
  });

  /* ------------------------------------------------------------------ */
  /* Palabra rotatoria del título                                        */
  /* ------------------------------------------------------------------ */
  var rot = document.querySelector('[data-rotator]');
  if (rot && !reduce) {
    var words = (rot.getAttribute('data-words') || '').split('|').filter(Boolean);
    var wi = 0;
    if (words.length > 1) {
      setInterval(function () {
        rot.classList.add('is-out');
        setTimeout(function () {
          wi = (wi + 1) % words.length;
          rot.textContent = words[wi];
          rot.classList.remove('is-out');
        }, 350);
      }, 2400);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Hero: las fotos de fondo se alternan solas (fundido, sin botones)   */
  /* ------------------------------------------------------------------ */
  var heroBg = document.querySelector('.lp-hero-bg');
  if (heroBg && !reduce) {
    var heroImgs = [].slice.call(heroBg.querySelectorAll('img'));
    // Juegos de tarjetas: el N-ésimo acompaña a la N-ésima foto
    var heroSets = [].slice.call(document.querySelectorAll('.lp-cards-set'));
    var hi = 0;
    if (heroImgs.length > 1) {
      setInterval(function () {
        if (document.hidden) return; // pestaña en segundo plano: no avanza
        var next = hi;
        for (var k = 1; k < heroImgs.length; k++) {
          var cand = (hi + k) % heroImgs.length;
          if (!heroImgs[cand].classList.contains('is-missing')) {
            next = cand;
            break;
          }
        }
        if (next === hi) return; // la otra foto no existe: se queda la actual
        heroImgs[hi].classList.remove('is-active');
        heroImgs[next].classList.add('is-active');
        if (heroSets[hi]) heroSets[hi].classList.remove('is-active');
        if (heroSets[next]) heroSets[next].classList.add('is-active');
        hi = next;
      }, 9500);
    }
  }

  /* ------------------------------------------------------------------ */
  /* Barra de progreso de scroll                                         */
  /* ------------------------------------------------------------------ */
  var bar = document.querySelector('.lp-progress');
  if (bar) {
    var ticking = false;
    var update = function () {
      var max = root.scrollHeight - root.clientHeight;
      var p = max > 0 ? Math.min(window.scrollY / max, 1) : 0;
      bar.style.transform = 'scaleX(' + p + ')';
      ticking = false;
    };
    window.addEventListener(
      'scroll',
      function () {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(update);
        }
      },
      { passive: true }
    );
    update();
  }

  /* ------------------------------------------------------------------ */
  /* Inclinación 3D (solo con mouse)                                     */
  /* ------------------------------------------------------------------ */
  if (!reduce && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    [].forEach.call(document.querySelectorAll('.lp-tilt'), function (card) {
      card.addEventListener('pointermove', function (e) {
        var r = card.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - 0.5;
        var y = (e.clientY - r.top) / r.height - 0.5;
        card.style.setProperty('--ry', (x * 8).toFixed(2) + 'deg');
        card.style.setProperty('--rx', (-y * 8).toFixed(2) + 'deg');
      });
      card.addEventListener('pointerleave', function () {
        card.style.setProperty('--rx', '0deg');
        card.style.setProperty('--ry', '0deg');
      });
    });
  }

  /* ------------------------------------------------------------------ */
  /* Cinta infinita: se duplica el carril para que no tenga saltos       */
  /* ------------------------------------------------------------------ */
  [].forEach.call(document.querySelectorAll('.lp-marquee'), function (m) {
    var track = m.querySelector('.lp-marquee__track');
    if (!track) return;
    var clone = track.cloneNode(true);
    clone.setAttribute('aria-hidden', 'true');
    m.appendChild(clone);
  });

  /* ------------------------------------------------------------------ */
  /* Respaldo de imágenes que aún no existen                             */
  /* ------------------------------------------------------------------ */
  [].forEach.call(document.querySelectorAll('img.lp-img'), function (img) {
    function miss() {
      img.classList.add('is-missing');
    }
    img.addEventListener('error', miss);
    if (img.complete && img.naturalWidth === 0) miss();
  });

  /* ------------------------------------------------------------------ */
  /* Demo: formato de campos de dinero                                   */
  /* ------------------------------------------------------------------ */
  function bindMoney(input, onChange) {
    input.addEventListener('input', function () {
      var n = digits(input.value);
      input.value = n ? fmt.format(n) : '';
      onChange();
    });
  }

  /* ------------------------------------------------------------------ */
  /* Demo: pestañas                                                      */
  /* ------------------------------------------------------------------ */
  var tabs = [].slice.call(document.querySelectorAll('.lp-tab'));
  var panels = [].slice.call(document.querySelectorAll('.lp-panel'));
  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = tab.getAttribute('data-tab');
      tabs.forEach(function (t) {
        t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
      });
      panels.forEach(function (p) {
        p.hidden = p.getAttribute('data-panel') !== target;
      });
    });
  });

  /* ------------------------------------------------------------------ */
  /* Demo 1: diagnóstico 50/30/20                                        */
  /* ------------------------------------------------------------------ */
  var rIng = $('reg-ingreso');
  var rNec = $('reg-necesidades');
  var rGus = $('reg-gustos');

  function diagnostico() {
    var ing = digits(rIng.value);
    var nec = digits(rNec.value);
    var gus = digits(rGus.value);
    var ok = ing > 0;

    $('reg-vacio').hidden = ok;
    $('reg-resultado').hidden = !ok;
    if (!ok) return;

    var ahorro = ing - nec - gus;
    var sobregiro = ahorro < 0;
    if (sobregiro) ahorro = 0;

    var total = nec + gus + ahorro;
    var pNec = total ? (nec / total) * 100 : 0;
    var pGus = total ? (gus / total) * 100 : 0;
    var pAho = total ? (ahorro / total) * 100 : 0;

    $('reg-bar-nec').style.width = pNec + '%';
    $('reg-bar-gus').style.width = pGus + '%';
    $('reg-bar-aho').style.width = pAho + '%';

    $('reg-pct-nec').textContent = Math.round((nec / ing) * 100) + '%';
    $('reg-pct-gus').textContent = Math.round((gus / ing) * 100) + '%';
    $('reg-pct-aho').textContent = Math.round((ahorro / ing) * 100) + '%';
    $('reg-val-nec').textContent = cop(nec);
    $('reg-val-gus').textContent = cop(gus);
    $('reg-val-aho').textContent = cop(ahorro);

    $('reg-ideal').textContent =
      'Ideal para tu ingreso: ' + cop(ing * 0.5) + ' · ' + cop(ing * 0.3) + ' · ' + cop(ing * 0.2);

    var msg;
    if (sobregiro) {
      msg = 'Estás gastando más de lo que ingresa. Empieza revisando tus gustos.';
    } else if (nec > ing * 0.5) {
      msg = 'Tus necesidades superan el 50%. Revisa gastos fijos que puedas renegociar.';
    } else if (gus > ing * 0.3) {
      msg = 'Tus gustos superan el 30%. Un pequeño ajuste libera ahorro.';
    } else if (ahorro < ing * 0.2) {
      msg = 'Estás cerca. Te faltan ' + cop(ing * 0.2 - ahorro) + ' para llegar al 20% de ahorro.';
    } else {
      msg = '¡Equilibrado! Tu distribución respeta la regla 50/30/20.';
    }
    $('reg-mensaje').textContent = msg;
  }

  if (rIng && rNec && rGus) {
    [rIng, rNec, rGus].forEach(function (i) {
      bindMoney(i, diagnostico);
    });
    diagnostico();
  }

  /* ------------------------------------------------------------------ */
  /* Demo 2: simulador de crédito (sistema francés)                      */
  /* ------------------------------------------------------------------ */
  var cMonto = $('cr-monto');
  var cTasa = $('cr-tasa');
  var cPlazo = $('cr-plazo');
  var cIng = $('cr-ingreso');

  function credito() {
    var P = digits(cMonto.value);
    var i = (parseFloat(String(cTasa.value).replace(',', '.')) || 0) / 100;
    var n = parseInt(cPlazo.value, 10) || 1;
    var ing = digits(cIng.value);

    $('cr-plazo-out').textContent = n + (n === 1 ? ' mes' : ' meses');

    var ok = P > 0;
    $('cr-vacio').hidden = ok;
    $('cr-resultado').hidden = !ok;
    if (!ok) return;

    var cuota = i > 0 ? (P * i) / (1 - Math.pow(1 + i, -n)) : P / n;
    cuota = Math.round(cuota);
    var totalPagar = cuota * n;

    $('cr-cuota').textContent = cop(cuota);
    $('cr-total').textContent = cop(totalPagar);
    $('cr-intereses').textContent = cop(totalPagar - P);

    var hasIng = ing > 0;
    $('cr-semaforo').hidden = !hasIng;
    $('cr-sin-ingreso').hidden = hasIng;
    if (!hasIng) return;

    var ratio = cuota / ing;
    var pct = Math.round(ratio * 1000) / 10;
    $('cr-pct').textContent = String(pct).replace('.', ',') + '%';

    var state = ratio < 0.2 ? 'ok' : ratio <= 0.3 ? 'mid' : 'bad';
    ['ok', 'mid', 'bad'].forEach(function (s) {
      $('cr-chip-' + s).hidden = s !== state;
      $('cr-seg-' + s).classList.toggle('is-active', s === state);
    });
    $('cr-marker').style.left = Math.min(ratio / 0.5, 1) * 100 + '%';
  }

  if (cMonto && cTasa && cPlazo && cIng) {
    bindMoney(cMonto, credito);
    bindMoney(cIng, credito);
    cTasa.addEventListener('input', credito);
    cPlazo.addEventListener('input', credito);
    credito();
  }
})();