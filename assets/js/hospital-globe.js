import { feature, mesh } from 'https://esm.sh/topojson-client@3';

(function () {
  'use strict';

  /* ── Default view centred on US ─────────────── */
  var DEF_LAT =  30 * Math.PI / 180;   /* slightly south so US is upper-centre */
  var DEF_LNG = -96 * Math.PI / 180;
  var LAT_MIN = DEF_LAT - 0.5;
  var LAT_MAX = DEF_LAT + 0.5;
  var LNG_MIN = DEF_LNG - 0.7;
  var LNG_MAX = DEF_LNG + 0.7;

  var landRings  = null;   /* world land polygons   */
  var stateRings = null;   /* US state borders only */

  /* ── Orthographic projection ─────────────────── */
  function project(latRad, lngRad, lat0, lng0, R, cx, cy) {
    var dLng    = lngRad - lng0;
    var sinLat  = Math.sin(latRad),  cosLat  = Math.cos(latRad);
    var sinLat0 = Math.sin(lat0),    cosLat0 = Math.cos(lat0);
    var cosDLng = Math.cos(dLng);
    if (sinLat0 * sinLat + cosLat0 * cosLat * cosDLng < 0) return null;
    return [
      cx + R * cosLat * Math.sin(dLng),
      cy - R * (cosLat0 * sinLat - sinLat0 * cosLat * cosDLng),
    ];
  }

  function tracePath(ctx, ring, lat0, lng0, R, cx, cy, isDeg) {
    var started = false;
    for (var i = 0; i < ring.length; i++) {
      var rawLng = isDeg ? ring[i][0] * Math.PI / 180 : ring[i][0];
      var rawLat = isDeg ? ring[i][1] * Math.PI / 180 : ring[i][1];
      var pt = project(rawLat, rawLng, lat0, lng0, R, cx, cy);
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

    /* ── Globe sphere ── */
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.clip();

    /* Deep ocean gradient — lighter in upper-centre like sunlit */
    var bg = ctx.createRadialGradient(cx, cy - R * 0.25, R * 0.05, cx, cy, R);
    bg.addColorStop(0,    '#0e2e55');
    bg.addColorStop(0.45, '#071c38');
    bg.addColorStop(1,    '#020d1f');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, w, h);

    /* Land fills */
    if (landRings) {
      ctx.fillStyle = '#123466';
      landRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy, true);
        ctx.closePath();
        ctx.fill();
      });

      /* Subtle land highlight (lit from above) */
      var landLight = ctx.createLinearGradient(cx, cy - R, cx, cy + R);
      landLight.addColorStop(0,   'rgba(0,180,240,0.13)');
      landLight.addColorStop(0.4, 'rgba(0,140,210,0.06)');
      landLight.addColorStop(1,   'rgba(0,0,0,0)');
      ctx.fillStyle = landLight;
      if (landRings) {
        landRings.forEach(function (ring) {
          ctx.beginPath();
          tracePath(ctx, ring, lat0, lng0, R, cx, cy, true);
          ctx.closePath();
          ctx.fill();
        });
      }
    }

    /* US state border lines */
    if (stateRings) {
      ctx.strokeStyle = 'rgba(0, 220, 190, 0.65)';
      ctx.lineWidth   = Math.max(0.8, 1.2 / dpr);
      ctx.lineJoin    = 'round';
      stateRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy, true);
        ctx.stroke();
      });
    }

    /* Edge darkening to sell sphere curvature */
    var edge = ctx.createRadialGradient(cx, cy, R * 0.68, cx, cy, R);
    edge.addColorStop(0, 'rgba(0,0,0,0)');
    edge.addColorStop(1, 'rgba(0,0,0,0.62)');
    ctx.fillStyle = edge;
    ctx.fillRect(0, 0, w, h);

    ctx.restore();

    /* ── Atmosphere glow (outside clip) ── */
    /* Wide soft halo */
    var halo = ctx.createRadialGradient(cx, cy, R * 0.88, cx, cy, R * 1.28);
    halo.addColorStop(0,    'rgba(0, 195, 255, 0.50)');
    halo.addColorStop(0.3,  'rgba(0, 160, 230, 0.22)');
    halo.addColorStop(0.65, 'rgba(0, 100, 200, 0.08)');
    halo.addColorStop(1,    'rgba(0,  50, 160, 0)');
    ctx.beginPath();
    ctx.arc(cx, cy, R * 1.28, 0, Math.PI * 2);
    ctx.fillStyle = halo;
    ctx.fill();

    /* Bright crisp rim */
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(0, 220, 255, 0.9)';
    ctx.lineWidth   = 2.5;
    ctx.shadowColor = 'rgba(0, 200, 255, 0.9)';
    ctx.shadowBlur  = 18;
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
        var geoms = f.type === 'FeatureCollection' ? f.features.map(function(x){ return x.geometry; }) : [f.geometry];
        geoms.forEach(function (geom) {
          var polys = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
          polys.forEach(function (poly) { poly.forEach(function (ring) { landRings.push(ring); }); });
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
          polys.forEach(function (poly) { poly.forEach(function (ring) { stateRings.push(ring); }); });
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
