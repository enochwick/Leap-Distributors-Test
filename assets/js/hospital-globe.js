import { feature } from 'https://esm.sh/topojson-client@3';

(function () {
  'use strict';

  /* ── Default view centred on continental US ── */
  var DEF_LAT =  38 * Math.PI / 180;
  var DEF_LNG = -96 * Math.PI / 180;
  var LAT_MIN = DEF_LAT - 0.45;
  var LAT_MAX = DEF_LAT + 0.45;
  var LNG_MIN = DEF_LNG - 0.65;
  var LNG_MAX = DEF_LNG + 0.65;

  var stateRings = null;

  /* ── Orthographic projection ───────────────── */
  function project(latRad, lngRad, lat0, lng0, R, cx, cy) {
    var dLng    = lngRad - lng0;
    var sinLat  = Math.sin(latRad), cosLat  = Math.cos(latRad);
    var sinLat0 = Math.sin(lat0),   cosLat0 = Math.cos(lat0);
    var cosDLng = Math.cos(dLng);
    if (sinLat0 * sinLat + cosLat0 * cosLat * cosDLng < 0) return null;
    return [
      cx + R * cosLat * Math.sin(dLng),
      cy - R * (cosLat0 * sinLat - sinLat0 * cosLat * cosDLng),
    ];
  }

  /* ── Draw one ring of a polygon ────────────── */
  function tracePath(ctx, ring, lat0, lng0, R, cx, cy) {
    var started = false;
    for (var i = 0; i < ring.length; i++) {
      var pt = project(
        ring[i][1] * Math.PI / 180,
        ring[i][0] * Math.PI / 180,
        lat0, lng0, R, cx, cy
      );
      if (!pt) { started = false; continue; }
      if (!started) { ctx.moveTo(pt[0], pt[1]); started = true; }
      else           { ctx.lineTo(pt[0], pt[1]); }
    }
  }

  /* ── Main render ────────────────────────────── */
  function render(canvas, lat0, lng0) {
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var w   = canvas.width, h = canvas.height;
    var R   = Math.min(w, h) * 0.47;
    var cx  = w / 2, cy = h / 2;
    var ctx = canvas.getContext('2d');

    ctx.clearRect(0, 0, w, h);

    /* Clip everything to the globe circle */
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.clip();

    /* Ocean background */
    var bg = ctx.createRadialGradient(cx - R * 0.15, cy - R * 0.25, R * 0.05, cx, cy, R);
    bg.addColorStop(0,   '#0e2647');
    bg.addColorStop(0.55,'#071830');
    bg.addColorStop(1,   '#030c1a');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, w, h);

    /* State fills */
    if (stateRings) {
      ctx.fillStyle = 'rgba(18, 52, 95, 0.85)';
      stateRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.closePath();
        ctx.fill();
      });

      /* State border lines */
      ctx.strokeStyle = 'rgba(0, 225, 190, 0.72)';
      ctx.lineWidth   = Math.max(1, 1.4 * dpr * 0.5);
      ctx.lineJoin    = 'round';
      stateRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.stroke();
      });
    }

    ctx.restore(); /* end clip */

    /* Atmosphere glow ring */
    var atm = ctx.createRadialGradient(cx, cy, R * 0.90, cx, cy, R * 1.22);
    atm.addColorStop(0,    'rgba(0, 200, 245, 0.55)');
    atm.addColorStop(0.35, 'rgba(0, 155, 220, 0.22)');
    atm.addColorStop(1,    'rgba(0,  80, 180, 0)');
    ctx.beginPath();
    ctx.arc(cx, cy, R * 1.22, 0, Math.PI * 2);
    ctx.fillStyle = atm;
    ctx.fill();

    /* Crisp edge highlight */
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(0, 210, 255, 0.75)';
    ctx.lineWidth   = 2.5;
    ctx.stroke();

    /* Inner edge shadow to sell the sphere */
    var inner = ctx.createRadialGradient(cx, cy, R * 0.7, cx, cy, R);
    inner.addColorStop(0,   'rgba(0,0,0,0)');
    inner.addColorStop(0.75,'rgba(0,0,0,0)');
    inner.addColorStop(1,   'rgba(0,0,0,0.55)');
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.clip();
    ctx.fillStyle = inner;
    ctx.fillRect(0, 0, w, h);
    ctx.restore();
  }

  /* ── Load state data and kick off ────────────── */
  function loadStateRings() {
    var base = (typeof window.leapData !== 'undefined') ? window.leapData.themeUrl : '';
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
      .catch(function (e) { console.warn('State data:', e); });
  }

  /* ── Init ────────────────────────────────────── */
  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    var dpr   = Math.min(window.devicePixelRatio || 1, 2);
    var lat0  = DEF_LAT, lng0 = DEF_LNG;
    var isDragging = false, prevX = 0, prevY = 0;
    var returning  = false, resetTimer = null, rafId = null;

    /* Size canvas to match CSS dimensions */
    function resize() {
      var size = canvas.offsetWidth;
      canvas.width  = size * dpr;
      canvas.height = size * dpr;
      render(canvas, lat0, lng0);
    }

    function loop() {
      if (returning && !isDragging) {
        var dLat = DEF_LAT - lat0, dLng = DEF_LNG - lng0;
        if (Math.abs(dLat) < 0.001 && Math.abs(dLng) < 0.001) {
          lat0 = DEF_LAT; lng0 = DEF_LNG; returning = false;
        } else {
          lat0 += dLat * 0.10;
          lng0 += dLng * 0.10;
        }
        render(canvas, lat0, lng0);
      }
      rafId = requestAnimationFrame(loop);
    }

    function scheduleReturn() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () { returning = true; }, 800);
    }

    canvas.style.cursor = 'grab';

    canvas.addEventListener('pointerdown', function (e) {
      isDragging = true; returning = false; clearTimeout(resetTimer);
      prevX = e.clientX; prevY = e.clientY;
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (e) {
      if (!isDragging) return;
      lng0 = Math.max(LNG_MIN, Math.min(LNG_MAX, lng0 - (e.clientX - prevX) * 0.004));
      lat0 = Math.max(LAT_MIN, Math.min(LAT_MAX, lat0 - (e.clientY - prevY) * 0.003));
      prevX = e.clientX; prevY = e.clientY;
      render(canvas, lat0, lng0);
    });
    canvas.addEventListener('pointerup',    function () { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); });
    canvas.addEventListener('pointerleave', function () { if (isDragging) { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); } });

    window.addEventListener('resize', resize);
    resize();
    loop();

    loadStateRings().then(function () { render(canvas, lat0, lng0); });

    window.addEventListener('beforeunload', function () {
      cancelAnimationFrame(rafId);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
