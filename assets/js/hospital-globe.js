import createGlobe from 'https://esm.sh/cobe@0.6.3';

(function () {
  'use strict';

  /* ── Hospital markers (US locations) ─────────── */
  var HOSPITALS = [
    { id: 'fw',  location: [32.90, -97.05], delay: 0.0 },
    { id: 'hou', location: [29.76, -95.37], delay: 0.6 },
    { id: 'dal', location: [32.78, -96.80], delay: 1.1 },
    { id: 'sa',  location: [29.42, -98.49], delay: 0.3 },
    { id: 'okc', location: [35.47, -97.52], delay: 0.9 },
    { id: 'nas', location: [36.17, -86.78], delay: 1.5 },
    { id: 'phx', location: [33.45,-112.07], delay: 0.7 },
    { id: 'atl', location: [33.75, -84.39], delay: 1.2 },
    { id: 'aus', location: [30.27, -97.74], delay: 0.4 },
    { id: 'den', location: [39.74,-104.98], delay: 1.8 },
  ];

  /* ── Projection (matches COBE's internal math) ─ */
  function toXYZ(lat, lng) {
    var pr = lat * Math.PI / 180, lr = lng * Math.PI / 180;
    return [Math.cos(pr) * Math.cos(lr), Math.sin(pr), Math.cos(pr) * Math.sin(lr)];
  }
  function applyRot(v, phi, theta) {
    var x = v[0], y = v[1], z = v[2];
    var cp = Math.cos(phi), sp = Math.sin(phi);
    var x2 = x*cp + z*sp, z2 = -x*sp + z*cp; x = x2; z = z2;
    var ct = Math.cos(theta), st = Math.sin(theta);
    var y2 = y*ct + z*st, z3 = -y*st + z*ct; y = y2; z = z3;
    return [x, y, z];
  }

  /* ── Draw pulse rings on overlay canvas ──────── */
  function drawPulses(ctx, phi, theta, time, cw, ch) {
    ctx.clearRect(0, 0, cw, ch);
    var r = Math.min(cw, ch) / 2;

    HOSPITALS.forEach(function (h) {
      var rot = applyRot(toXYZ(h.location[0], h.location[1]), phi, theta);
      if (rot[2] > 0) return;

      var visibility = Math.min(1, Math.max(0, -rot[2] * 3));
      if (visibility < 0.05) return;

      var sx = cw / 2 + rot[0] * r;
      var sy = ch / 2 - rot[1] * r;

      /* Two offset pulse rings */
      [0, 0.7].forEach(function (offset) {
        var t = ((time * 0.5 + h.delay + offset) % 2);
        var pr = r * 0.009 + r * 0.022 * t;
        var alpha = (1 - t / 2) * visibility * 0.85;
        if (alpha < 0.01) return;
        ctx.beginPath();
        ctx.arc(sx, sy, pr, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(0,220,185,' + alpha + ')';
        ctx.lineWidth   = 1.5;
        ctx.stroke();
      });

      /* Centre dot */
      var dotR = 3 * visibility;
      ctx.beginPath();
      ctx.arc(sx, sy, dotR, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(0,230,195,' + visibility + ')';
      ctx.fill();

      /* Dot ring */
      ctx.beginPath();
      ctx.arc(sx, sy, dotR + 3, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(0,220,185,' + (visibility * 0.6) + ')';
      ctx.lineWidth   = 1;
      ctx.stroke();
    });
  }

  /* ── Init ────────────────────────────────────── */
  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    var dpr = Math.min(window.devicePixelRatio || 1, 2);

    /* Pulse overlay canvas */
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

    /* State */
    var phi = 0, time = 0;
    var isDragging  = false, prevX = 0, prevY = 0;
    var phiOffset   = 0, thetaOffset = 0;
    var dragPhi     = 0, dragTheta  = 0;

    canvas.style.cursor = 'grab';

    canvas.addEventListener('pointerdown', function (e) {
      isDragging = true;
      prevX = e.clientX; prevY = e.clientY;
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (e) {
      if (!isDragging) return;
      dragPhi   = (e.clientX - prevX) / 300;
      dragTheta = (e.clientY - prevY) / 1000;
    });
    canvas.addEventListener('pointerup', function () {
      phiOffset   += dragPhi;
      thetaOffset += dragTheta;
      dragPhi = dragTheta = 0;
      isDragging = false;
      canvas.style.cursor = 'grab';
    });
    canvas.addEventListener('pointerleave', function () {
      if (!isDragging) return;
      phiOffset   += dragPhi;
      thetaOffset += dragTheta;
      dragPhi = dragTheta = 0;
      isDragging = false;
      canvas.style.cursor = 'grab';
    });

    var globe = createGlobe(canvas, {
      devicePixelRatio: dpr,
      width:            canvas.offsetWidth  * dpr,
      height:           canvas.offsetHeight * dpr,
      phi:              0,
      theta:            0.2,
      dark:             1,
      diffuse:          1.5,
      mapSamples:       16000,
      mapBrightness:    8,
      baseColor:        [0.08, 0.22, 0.48],
      markerColor:      [0.0,  0.88, 0.74],
      glowColor:        [0.12, 0.40, 0.90],
      markers: HOSPITALS.map(function (h) {
        return { location: h.location, size: 0.028 };
      }),
      onRender: function (state) {
        if (!isDragging) phi += 0.003;
        time += 0.016;

        var curPhi   = phi + phiOffset   + dragPhi;
        var curTheta = 0.2 + thetaOffset + dragTheta;

        state.phi    = curPhi;
        state.theta  = curTheta;
        state.width  = canvas.offsetWidth  * dpr;
        state.height = canvas.offsetHeight * dpr;

        var cw = state.width, ch = state.height;
        if (overlay.width !== cw || overlay.height !== ch) {
          overlay.width = cw; overlay.height = ch;
        }
        drawPulses(octx, curPhi, curTheta, time, cw, ch);
      },
    });

    setTimeout(function () {
      if (canvas) canvas.style.opacity = '1';
    }, 100);

    window.addEventListener('beforeunload', function () { globe.destroy(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
