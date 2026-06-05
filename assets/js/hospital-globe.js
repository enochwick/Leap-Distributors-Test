import { feature } from 'https://esm.sh/topojson-client@3';

(function () {
  'use strict';

  /* ── Default view: continental US centred ─────── */
  var DEF_LAT =  32 * Math.PI / 180;
  var DEF_LNG = -96 * Math.PI / 180;
  var LAT_MIN = DEF_LAT - 0.5;
  var LAT_MAX = DEF_LAT + 0.5;
  var LNG_MIN = DEF_LNG - 0.7;
  var LNG_MAX = DEF_LNG + 0.7;

  /* ── Hospital pulse markers ───────────────────── */
  var HOSPITALS = [
    { location: [32.90, -97.05], delay: 0.0 },
    { location: [29.76, -95.37], delay: 0.6 },
    { location: [32.78, -96.80], delay: 1.1 },
    { location: [29.42, -98.49], delay: 0.3 },
    { location: [35.47, -97.52], delay: 0.9 },
    { location: [36.17, -86.78], delay: 1.5 },
    { location: [33.45,-112.07], delay: 0.7 },
    { location: [33.75, -84.39], delay: 1.2 },
    { location: [30.27, -97.74], delay: 0.4 },
    { location: [39.74,-104.98], delay: 1.8 },
  ];

  var landRings  = null;
  var stateRings = null;

  /* ── Orthographic projection ─────────────────── */
  function project(latR, lngR, lat0, lng0, R, cx, cy) {
    var dLng = lngR - lng0;
    var sL = Math.sin(latR),  cL = Math.cos(latR);
    var sL0 = Math.sin(lat0), cL0 = Math.cos(lat0);
    var cD = Math.cos(dLng);
    if (sL0 * sL + cL0 * cL * cD < 0) return null;
    return [cx + R * cL * Math.sin(dLng), cy - R * (cL0 * sL - sL0 * cL * cD)];
  }

  function tracePath(ctx, ring, lat0, lng0, R, cx, cy) {
    var started = false;
    for (var i = 0; i < ring.length; i++) {
      var pt = project(ring[i][1] * Math.PI / 180, ring[i][0] * Math.PI / 180, lat0, lng0, R, cx, cy);
      if (!pt) { started = false; continue; }
      if (!started) { ctx.moveTo(pt[0], pt[1]); started = true; }
      else           { ctx.lineTo(pt[0], pt[1]); }
    }
  }

  /* ── Render globe ────────────────────────────── */
  function render(canvas, lat0, lng0, time) {
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var w = canvas.width, h = canvas.height;
    var R = Math.min(w, h) * 0.47;
    var cx = w / 2, cy = h / 2;
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, w, h);

    /* Clip to sphere */
    ctx.save();
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.clip();

    /* Deep teal ocean — dark at edges, slightly lighter centre */
    var ocean = ctx.createRadialGradient(cx - R * 0.15, cy - R * 0.2, 0, cx, cy, R);
    ocean.addColorStop(0,   '#0d3535');
    ocean.addColorStop(0.5, '#061e1e');
    ocean.addColorStop(1,   '#020d0d');
    ctx.fillStyle = ocean;
    ctx.fillRect(0, 0, w, h);

    /* Dark teal continents */
    if (landRings) {
      /* Base fill */
      ctx.fillStyle = '#0f3a3a';
      landRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.closePath();
        ctx.fill();
      });

      /* Sunlight from upper-left — teal highlight */
      var lit = ctx.createLinearGradient(cx - R * 0.4, cy - R, cx + R * 0.2, cy + R * 0.6);
      lit.addColorStop(0,   'rgba(0, 180, 160, 0.32)');
      lit.addColorStop(0.4, 'rgba(0, 140, 120, 0.14)');
      lit.addColorStop(1,   'rgba(0,   0,   0,  0)');
      ctx.fillStyle = lit;
      landRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.closePath();
        ctx.fill();
      });
    }

    /* US state borders — light teal lines */
    if (stateRings) {
      ctx.strokeStyle = 'rgba(0, 210, 190, 0.55)';
      ctx.lineWidth   = 1.0;
      ctx.lineJoin    = 'round';
      stateRings.forEach(function (ring) {
        ctx.beginPath();
        tracePath(ctx, ring, lat0, lng0, R, cx, cy);
        ctx.stroke();
      });
    }

    /* Hospital pulse markers */
    HOSPITALS.forEach(function (h) {
      var pt = project(h.location[0] * Math.PI / 180, h.location[1] * Math.PI / 180, lat0, lng0, R, cx, cy);
      if (!pt) return;

      /* Visibility: fade near the horizon */
      var latR = h.location[0] * Math.PI / 180;
      var lngR = h.location[1] * Math.PI / 180;
      var dLng = lngR - lng0;
      var dot  = Math.sin(lat0)*Math.sin(latR) + Math.cos(lat0)*Math.cos(latR)*Math.cos(dLng);
      var vis  = Math.min(1, Math.max(0, dot * 4));
      if (vis < 0.05) return;

      var sx = pt[0], sy = pt[1];

      /* Two staggered pulse rings */
      [0, 0.7].forEach(function (off) {
        var t = ((time * 0.5 + h.delay + off) % 2);
        var pr = R * 0.009 + R * 0.024 * t;
        var a  = (1 - t / 2) * vis * 0.9;
        if (a < 0.01) return;
        ctx.beginPath();
        ctx.arc(sx, sy, pr, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(0,220,180,' + a + ')';
        ctx.lineWidth   = 1.5;
        ctx.stroke();
      });

      /* Centre dot */
      ctx.beginPath();
      ctx.arc(sx, sy, 3 * vis, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(0,230,185,' + vis + ')';
      ctx.fill();

      ctx.beginPath();
      ctx.arc(sx, sy, 3 * vis + 3, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(0,210,170,' + (vis * 0.55) + ')';
      ctx.lineWidth   = 1;
      ctx.stroke();
    });

    /* Edge vignette */
    var vig = ctx.createRadialGradient(cx, cy, R * 0.65, cx, cy, R);
    vig.addColorStop(0, 'rgba(0,0,0,0)');
    vig.addColorStop(1, 'rgba(0,0,0,0.65)');
    ctx.fillStyle = vig;
    ctx.fillRect(0, 0, w, h);

    ctx.restore();

    /* Wide atmosphere halo — teal glow */
    var halo = ctx.createRadialGradient(cx, cy, R * 0.86, cx, cy, R * 1.35);
    halo.addColorStop(0,    'rgba(0, 200, 180, 0.65)');
    halo.addColorStop(0.25, 'rgba(0, 160, 140, 0.30)');
    halo.addColorStop(0.55, 'rgba(0,  90,  80, 0.12)');
    halo.addColorStop(1,    'rgba(0,  30,  30, 0)');
    ctx.beginPath();
    ctx.arc(cx, cy, R * 1.35, 0, Math.PI * 2);
    ctx.fillStyle = halo;
    ctx.fill();

    /* Bright crisp rim — teal */
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(0, 210, 190, 0.95)';
    ctx.lineWidth   = 3;
    ctx.shadowColor = 'rgba(0, 200, 180, 1)';
    ctx.shadowBlur  = 24;
    ctx.stroke();
    ctx.shadowBlur  = 0;
  }

  /* ── Load geo data ───────────────────────────── */
  function loadData() {
    var base = '';
    if (typeof window.leapData !== 'undefined' && window.leapData.themeUrl) {
      base = window.leapData.themeUrl;
    } else {
      /* Derive theme URL from this script's own src */
      var scripts = document.querySelectorAll('script[src*="hospital-globe"]');
      if (scripts.length) base = scripts[0].src.replace(/\/assets\/js\/hospital-globe\.js.*$/, '');
    }
    var p1 = fetch(base + '/assets/js/world-land.json')
      .then(function (r) { return r.json(); })
      .then(function (topo) {
        var f = feature(topo, topo.objects.land);
        landRings = [];
        var geoms = f.type === 'FeatureCollection'
          ? f.features.map(function (x) { return x.geometry; }) : [f.geometry];
        geoms.forEach(function (g) {
          var polys = g.type === 'Polygon' ? [g.coordinates] : g.coordinates;
          polys.forEach(function (poly) { poly.forEach(function (ring) { landRings.push(ring); }); });
        });
      });
    var p2 = fetch(base + '/assets/js/us-states.json')
      .then(function (r) { return r.json(); })
      .then(function (topo) {
        var features = feature(topo, topo.objects.states).features;
        stateRings = [];
        features.forEach(function (f) {
          var g = f.geometry;
          var polys = g.type === 'Polygon' ? [g.coordinates] : g.coordinates;
          polys.forEach(function (poly) { poly.forEach(function (ring) { stateRings.push(ring); }); });
        });
      });
    return Promise.all([p1, p2]).catch(function (e) { console.warn('Globe data:', e); });
  }

  /* ── Init ────────────────────────────────────── */
  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    /* Make visible immediately — CSS sets opacity:0 for COBE fade-in */
    canvas.style.opacity = '1';
    canvas.style.transition = 'opacity 1s ease';

    var dpr  = Math.min(window.devicePixelRatio || 1, 2);
    var lat0 = DEF_LAT, lng0 = DEF_LNG;
    var isDragging = false, prevX = 0, prevY = 0;
    var returning  = false, resetTimer = null, rafId = null;
    var time = 0;

    function resize() {
      var s = canvas.offsetWidth || 600; /* fallback if not laid out yet */
      canvas.width  = s * dpr;
      canvas.height = s * dpr;
      render(canvas, lat0, lng0, time);
    }

    function loop() {
      time += 0.016;
      if (returning && !isDragging) {
        var dLat = DEF_LAT - lat0, dLng = DEF_LNG - lng0;
        if (Math.abs(dLat) < 0.001 && Math.abs(dLng) < 0.001) {
          lat0 = DEF_LAT; lng0 = DEF_LNG; returning = false;
        } else {
          lat0 += dLat * 0.08; lng0 += dLng * 0.08;
        }
      }
      render(canvas, lat0, lng0, time);
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
    });
    canvas.addEventListener('pointerup',    function () { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); });
    canvas.addEventListener('pointerleave', function () { if (isDragging) { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); } });

    window.addEventListener('resize', resize);
    resize();
    loop();
    loadData().then(function () { render(canvas, lat0, lng0, time); });
    window.addEventListener('beforeunload', function () { cancelAnimationFrame(rafId); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
