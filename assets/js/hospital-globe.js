import createGlobe from 'https://esm.sh/cobe@0.6.3';
import { feature } from 'https://esm.sh/topojson-client@3';

(function () {
  'use strict';

  var DEF_PHI   = 3.14;
  var DEF_THETA = -0.22;
  var PHI_MIN   = DEF_PHI - 0.55;
  var PHI_MAX   = DEF_PHI + 0.55;
  var THETA_MIN = DEF_THETA - 0.35;
  var THETA_MAX = DEF_THETA + 0.35;

  function angleDiff(a, b) {
    return (((b - a) % (Math.PI * 2)) + Math.PI * 3) % (Math.PI * 2) - Math.PI;
  }

  /* Correct spherical coords matching COBE's internal convention */
  function toXYZ(lat, lng) {
    var phi_r = lat * Math.PI / 180;
    var lam   = lng * Math.PI / 180;
    return [
      Math.cos(phi_r) * Math.cos(lam),
      Math.sin(phi_r),
      Math.cos(phi_r) * Math.sin(lam),
    ];
  }

  function applyRot(v, phi, theta) {
    var x = v[0], y = v[1], z = v[2];
    var cp = Math.cos(phi), sp = Math.sin(phi);
    var x2 = x * cp + z * sp, z2 = -x * sp + z * cp;
    x = x2; z = z2;
    var ct = Math.cos(theta), st = Math.sin(theta);
    var y2 = y * ct + z * st, z3 = -y * st + z * ct;
    y = y2; z = z3;
    return [x, y, z];
  }

  function toScreen(xyz, w, h) {
    var r = Math.min(w, h) / 2;
    return [w / 2 + xyz[0] * r, h / 2 - xyz[1] * r];
  }

  /* ── Load US state rings from local file ─────────── */
  var stateRings = null;

  function loadStateRings() {
    var base = (typeof leapData !== 'undefined') ? leapData.themeUrl : '';
    return fetch(base + '/assets/js/us-states.json')
      .then(function (r) { return r.json(); })
      .then(function (topo) {
        var features = feature(topo, topo.objects.states).features;
        stateRings = [];
        features.forEach(function (f) {
          var geom = f.geometry;
          var polys = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
          polys.forEach(function (poly) {
            poly.forEach(function (ring) { stateRings.push(ring); });
          });
        });
      })
      .catch(function (e) { console.warn('State borders:', e); });
  }

  /* ── Draw state borders on overlay ──────────────── */
  function drawBorders(ctx, phi, theta, cw, ch) {
    if (!stateRings) return;
    ctx.clearRect(0, 0, cw, ch);
    ctx.strokeStyle = 'rgba(0, 225, 190, 0.7)';
    ctx.lineWidth   = 1.5;
    ctx.lineJoin    = 'round';
    ctx.beginPath();

    for (var ri = 0; ri < stateRings.length; ri++) {
      var ring = stateRings[ri];
      var penDown = false;
      for (var i = 0; i < ring.length; i++) {
        var lng = ring[i][0], lat = ring[i][1];
        var rot = applyRot(toXYZ(lat, lng), phi, theta);
        if (rot[2] > 0) { penDown = false; continue; }
        var sp = toScreen(rot, cw, ch);
        if (!penDown) { ctx.moveTo(sp[0], sp[1]); penDown = true; }
        else          { ctx.lineTo(sp[0], sp[1]); }
      }
    }
    ctx.stroke();
  }

  /* ── Init ────────────────────────────────────────── */
  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    /* Overlay canvas — same position & size as COBE canvas */
    var overlay = document.createElement('canvas');
    overlay.setAttribute('aria-hidden', 'true');
    overlay.style.cssText = [
      'position:absolute',
      'left:50%',
      'top:-40px',
      'transform:translateX(-50%)',
      'width:min(130vw,1440px)',
      'height:min(130vw,1440px)',
      'pointer-events:none',
      'z-index:1',
    ].join(';');
    canvas.parentNode.appendChild(overlay);
    var octx = overlay.getContext('2d');

    var phi = DEF_PHI, theta = DEF_THETA;
    var isDragging = false, prevX = 0, prevY = 0;
    var returning = false, resetTimer = null;

    function scheduleReturn() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () { returning = true; }, 800);
    }

    canvas.addEventListener('pointerdown', function (e) {
      isDragging = true; returning = false; clearTimeout(resetTimer);
      prevX = e.clientX; prevY = e.clientY;
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (e) {
      if (!isDragging) return;
      phi   = Math.max(PHI_MIN,   Math.min(PHI_MAX,   phi   + (e.clientX - prevX) * 0.004));
      theta = Math.max(THETA_MIN, Math.min(THETA_MAX, theta + (e.clientY - prevY) * 0.003));
      prevX = e.clientX; prevY = e.clientY;
    });
    canvas.addEventListener('pointerup', function () {
      isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn();
    });
    canvas.addEventListener('pointerleave', function () {
      if (isDragging) { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); }
    });

    loadStateRings();

    var globe = createGlobe(canvas, {
      devicePixelRatio: dpr,
      width:            canvas.offsetWidth  * dpr,
      height:           canvas.offsetHeight * dpr,
      phi:              DEF_PHI,
      theta:            DEF_THETA,
      dark:             1,
      diffuse:          1.2,
      mapSamples:       1,
      mapBrightness:    0,
      baseColor:        [0.05, 0.18, 0.42],
      markerColor:      [0.0,  0.92, 0.74],
      glowColor:        [0.18, 0.55, 1.0],
      markers:          [],
      onRender: function (state) {
        if (returning && !isDragging) {
          var dP = angleDiff(phi, DEF_PHI), dT = DEF_THETA - theta;
          if (Math.abs(dP) < 0.002 && Math.abs(dT) < 0.002) {
            phi = DEF_PHI; theta = DEF_THETA; returning = false;
          } else { phi += dP * 0.10; theta += dT * 0.10; }
        }

        state.phi    = phi;
        state.theta  = theta;
        state.width  = canvas.offsetWidth  * dpr;
        state.height = canvas.offsetHeight * dpr;

        var cw = state.width, ch = state.height;
        if (overlay.width !== cw || overlay.height !== ch) {
          overlay.width = cw; overlay.height = ch;
        }
        drawBorders(octx, phi, theta, cw, ch);
      },
    });

    window.addEventListener('beforeunload', function () { globe.destroy(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
