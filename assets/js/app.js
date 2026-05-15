/**
 * LocAuto Pro — interactions légères (sans dépendance)
 */
(function () {
  'use strict';

  var nav = document.querySelector('.lap-navbar');
  if (nav) {
    function onScroll() {
      nav.classList.toggle('lap-navbar--scrolled', window.scrollY > 20);
    }
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
      var msg = el.getAttribute('data-confirm') || 'Confirmer cette action ?';
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  });

  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (alert) {
    var ms = parseInt(alert.getAttribute('data-auto-dismiss'), 10) || 5500;
    setTimeout(function () {
      var btn = alert.querySelector('.btn-close');
      if (btn) btn.click();
    }, ms);
  });

  /** Estimation location (fiche véhicule) */
  var debut = document.getElementById('res_debut');
  var fin = document.getElementById('res_fin');
  var out = document.getElementById('res_estime');
  var prixJour = document.getElementById('res_prix_jour');

  function parseISODate(s) {
    if (!s) return null;
    var d = new Date(s + 'T12:00:00');
    return isNaN(d.getTime()) ? null : d;
  }

  function updateEstimate() {
    if (!out || !prixJour) return;
    var pj = parseFloat(prixJour.getAttribute('data-prix'), 10);
    if (isNaN(pj)) pj = 0;
    var d0 = parseISODate(debut && debut.value);
    var d1 = parseISODate(fin && fin.value);
    if (!d0 || !d1 || d1 < d0) {
      out.textContent = '—';
      out.setAttribute('data-days', '0');
      return;
    }
    var ms = d1 - d0;
    var days = Math.ceil(ms / (1000 * 60 * 60 * 24)) + 1;
    if (days < 1) days = 1;
    var total = (pj * days).toFixed(2);
    out.textContent = days + ' jour' + (days > 1 ? 's' : '') + ' × ' + pj.toFixed(2) + ' € ≈ ' + total + ' €';
    out.setAttribute('data-days', String(days));
  }

  if (debut) debut.addEventListener('change', updateEstimate);
  if (fin) fin.addEventListener('change', updateEstimate);
  updateEstimate();

  /** Dates réservation : minimum aujourd'hui */
  var today = new Date().toISOString().split('T')[0];
  if (debut && !debut.getAttribute('min')) debut.setAttribute('min', today);
  if (fin && !fin.getAttribute('min')) fin.setAttribute('min', today);
  if (debut) {
    debut.addEventListener('change', function () {
      if (fin && debut.value) {
        fin.setAttribute('min', debut.value);
      }
    });
  }
})();
