import { feature } from 'https://esm.sh/topojson-client@3';

(function () {
  'use strict';

  var DEF_LAT =  32 * Math.PI / 180;
  var DEF_LNG = -96 * Math.PI / 180;
  var LAT_MIN = DEF_LAT - 0.5;
  var LAT_MAX = DEF_LAT + 0.5;
  var LNG_MIN = DEF_LNG - 0.7;
  var LNG_MAX = DEF_LNG + 0.7;

  var landRings  = null;
  var stateRings = null;

  /* ── Orthographic projection ─────────────────── */
  function project(latR, lngR, lat0, lng0, R, cx, cy) {
    var dLng = lngR - lng0;
    var sLat = Math.sin(latR),  cLat = Math.cos(latR);
    var sLat0 = Math.sin(lat0), cLat0 = Math.cos(lat0);
    var cDL  = Math.cos(dLng);
    if (sLat0 * sLat + cLat0 * cLat * cDL < 0) return null;
    return [
      cx + R * cLat * Math.sin(dLng),
      cy - R * (cLat0 * sLat - sLat0 * cLat * cDL),
    ];
  }

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

  /* ── Render ──────────────────────────────────── */
  function render(canvas, lat0, lng0) {
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var w = canvas.width, h = canvas.height;
    var R = Math.min(w, h) * 0.47;
    var cx = w / 2, cy = h / 2;
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, w, h);

    /* ── Clip to sphere ── */
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.clip();

    /* Deep ocean — nearly black, matches COBE backdrop */
    var ocean = ctx.createRadialGradient(cx, cy - R * 0.3, 0, cx, cy, R);
    ocean.addColorStop(0,    '#0b2044');
    ocean.addColorStop(0.5,  '#061428');
    ocean.addColorStop(1,    '#020810');
    ctx.fillStyle = ocean;
    ctx.fillRect(0, 0, w, h);

    /* Land fills — same mid-blue as COBE dots */
    if (landRings) {
      ctx.fillStyle = '#0f2f5c';
      landRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.closePath();
        ctx.fill();
      });

      /* Subtle top-light to match COBE's map brightness */
      var lit = ctx.createLinearGradient(cx, cy - R, cx, cy + R * 0.3);
      lit.addColorStop(0,   'rgba(60, 140, 255, 0.18)');
      lit.addColorStop(0.45,'rgba(30,  90, 200, 0.07)');
      lit.addColorStop(1,   'rgba(0,   0,   0,  0)');
      ctx.fillStyle = lit;
      landRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.closePath();
        ctx.fill();
      });
    }

    /* US state borders — teal, same as COBE markers */
    if (stateRings) {
      ctx.strokeStyle = 'rgba(0, 215, 185, 0.7)';
      ctx.lineWidth   = 1.5;
      ctx.lineJoin    = 'round';
      stateRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.stroke();
      });
    }

    /* Edge vignette — sells the sphere curvature */
    var vignette = ctx.createRadialGradient(cx, cy, R * 0.65, cx, cy, R);
    vignette.addColorStop(0, 'rgba(0,0,0,0)');
    vignette.addColorStop(1, 'rgba(0,0,0,0.7)');
    ctx.fillStyle = vignette;
    ctx.fillRect(0, 0, w, h);

    ctx.restore();

    /* ── Atmosphere glow — matches COBE's blue rim ── */
    var halo = ctx.createRadialGradient(cx, cy, R * 0.89, cx, cy, R * 1.3);
    halo.addColorStop(0,    'rgba(30, 160, 255, 0.60)');
    halo.addColorStop(0.25, 'rgba(20, 120, 240, 0.28)');
    halo.addColorStop(0.6,  'rgba(10,  70, 200, 0.10)');
    halo.addColorStop(1,    'rgba(0,   30, 140, 0)');
    ctx.beginPath();
    ctx.arc(cx, cy, R * 1.3, 0, Math.PI * 2);
    ctx.fillStyle = halo;
    ctx.fill();

    /* Crisp glowing rim */
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(60, 190, 255, 0.95)';
    ctx.lineWidth   = 3;
    ctx.shadowColor = 'rgba(40, 170, 255, 1)';
    ctx.shadowBlur  = 22;
    ctx.stroke();
    ctx.shadowBlur  = 0;
  }

  /* ── Load data ───────────────────────────────── */
  function loadData() {
    var base = (typeof window.leapData !== 'undefined') ? window.leapData.themeUrl : '';

    var p1 = fetch(base + '/assets/js/world-land.json')
      .then(function (r) { return r.json(); })
      .then(function (topo) {
        var f = feature(topo, topo.objects.land);
        landRings = [];
        var geoms = (f.type === 'FeatureCollection')
          ? f.features.map(function (x) { return x.geometry; })
          : [f.geometry];
        geoms.forEach(function (g) {
          var polys = g.type === 'Polygon' ? [g.coordinates] : g.coordinates;
          polys.forEach(function (poly) {
            poly.forEach(function (ring) { landRings.push(ring); });
          });
        });
      });

    var p2 = fetch(base + '/assets/js/us-states.json')
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
      });

    return Promise.all([p1, p2]).catch(function (e) { console.warn('Globe data:', e); });
  }

  /* ── Init ────────────────────────────────────── */
  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    var dpr  = Math.min(window.devicePixelRatio || 1, 2);
    var lat0 = DEF_LAT, lng0 = DEF_LNG;
    var isDragging = false, prevX = 0, prevY = 0;
    var returning  = false, resetTimer = null, rafId = null;

    function resize() {
      var s = canvas.offsetWidth;
      canvas.width  = s * dpr;
      canvas.height = s * dpr;
      render(canvas, lat0, lng0);
    }

    function loop() {
      if (returning && !isDragging) {
        var dLat = DEF_LAT - lat0, dLng = DEF_LNG - lng0;
        if (Math.abs(dLat) < 0.001 && Math.abs(dLng) < 0.001) {
          lat0 = DEF_LAT; lng0 = DEF_LNG; returning = false;
        } else {
          lat0 += dLat * 0.1; lng0 += dLng * 0.1;
          render(canvas, lat0, lng0);
        }
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
    loadData().then(function () { render(canvas, lat0, lng0); });
    window.addEventListener('beforeunload', function () { cancelAnimationFrame(rafId); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
