import createGlobe from 'https://esm.sh/cobe@0.6.3';

(function () {
  'use strict';

  var HOSPITALS = [
    { loc: [32.90, -97.05], city: 'Fort Worth',    state: 'TX', cases: 280 },
    { loc: [32.78, -96.80], city: 'Dallas',         state: 'TX', cases: 210 },
    { loc: [33.15, -97.10], city: 'Denton',         state: 'TX', cases: 190 },
    { loc: [32.50, -97.35], city: 'Mansfield',      state: 'TX', cases: 175 },
    { loc: [33.20, -96.65], city: 'McKinney',       state: 'TX', cases: 160 },
    { loc: [32.95, -96.45], city: 'Rockwall',       state: 'TX', cases: 140 },
    { loc: [33.05, -97.52], city: 'Keller',         state: 'TX', cases: 120 },
    { loc: [32.35, -97.00], city: 'Cleburne',       state: 'TX', cases: 115 },
    { loc: [31.55, -97.15], city: 'Waco',           state: 'TX', cases: 108 },
    { loc: [31.10, -97.73], city: 'Temple',         state: 'TX', cases: 95  },
    { loc: [30.27, -97.74], city: 'Austin',         state: 'TX', cases: 190 },
    { loc: [30.50, -97.85], city: 'Round Rock',     state: 'TX', cases: 155 },
    { loc: [29.76, -95.37], city: 'Houston',        state: 'TX', cases: 245 },
    { loc: [29.90, -95.55], city: 'Cypress',        state: 'TX', cases: 180 },
    { loc: [29.62, -95.62], city: 'Sugar Land',     state: 'TX', cases: 145 },
    { loc: [29.42, -98.49], city: 'San Antonio',    state: 'TX', cases: 198 },
    { loc: [29.60, -98.62], city: 'San Antonio NW', state: 'TX', cases: 165 },
    { loc: [29.30, -98.35], city: 'San Antonio SE', state: 'TX', cases: 130 },
    { loc: [29.10, -99.10], city: 'Uvalde',         state: 'TX', cases: 88  },
    { loc: [27.80, -97.40], city: 'Corpus Christi', state: 'TX', cases: 72  },
    { loc: [26.20, -98.25], city: 'McAllen',        state: 'TX', cases: 64  },
    { loc: [31.77,-106.50], city: 'El Paso',        state: 'TX', cases: 49  },
    { loc: [35.47, -97.52], city: 'Oklahoma City',  state: 'OK', cases: 140 },
    { loc: [36.15, -95.99], city: 'Tulsa',          state: 'OK', cases: 112 },
    { loc: [35.38, -94.40], city: 'Fort Smith',     state: 'AR', cases: 88  },
    { loc: [36.37, -94.21], city: 'Bentonville',    state: 'AR', cases: 74  },
    { loc: [34.74, -92.33], city: 'Little Rock',    state: 'AR', cases: 66  },
    { loc: [36.17, -86.78], city: 'Nashville',      state: 'TN', cases: 108 },
    { loc: [35.15, -90.05], city: 'Memphis',        state: 'TN', cases: 58  },
    { loc: [32.30, -90.18], city: 'Jackson',        state: 'MS', cases: 44  },
    { loc: [33.75, -84.39], city: 'Atlanta',        state: 'GA', cases: 65  },
    { loc: [35.23,-101.83], city: 'Amarillo',       state: 'TX', cases: 52  },
    { loc: [33.45,-112.07], city: 'Phoenix',        state: 'AZ', cases: 145 },
    { loc: [33.42,-111.94], city: 'Mesa',           state: 'AZ', cases: 98  },
    { loc: [32.22,-110.97], city: 'Tucson',         state: 'AZ', cases: 58  },
    { loc: [36.18,-115.14], city: 'Las Vegas',      state: 'NV', cases: 42  },
    { loc: [34.05,-118.24], city: 'Los Angeles',    state: 'CA', cases: 97  },
    { loc: [32.73,-117.15], city: 'San Diego',      state: 'CA', cases: 45  },
    { loc: [39.74,-104.98], city: 'Denver',         state: 'CO', cases: 88  },
    { loc: [43.05, -83.68], city: 'Flint',          state: 'MI', cases: 38  },
    { loc: [43.05, -87.95], city: 'Milwaukee',      state: 'WI', cases: 34  },
    { loc: [41.88, -87.63], city: 'Chicago',        state: 'IL', cases: 55  },
    { loc: [44.98, -93.27], city: 'Minneapolis',    state: 'MN', cases: 42  },
  ];

  var MAX_CASES = 280;
  var MARKERS   = HOSPITALS.map(function (h) {
    return { location: h.loc, size: 0.026 + (h.cases / MAX_CASES) * 0.052 };
  });

  /* Locked default: continental US centred */
  var DEF_PHI   = 1.0;
  var DEF_THETA = -0.22;
  /* Clamp how far the user can drag from the default */
  var PHI_MIN   = DEF_PHI - 0.55;
  var PHI_MAX   = DEF_PHI + 0.55;
  var THETA_MIN = DEF_THETA - 0.35;
  var THETA_MAX = DEF_THETA + 0.35;

  function angleDiff(a, b) {
    return (((b - a) % (Math.PI * 2)) + Math.PI * 3) % (Math.PI * 2) - Math.PI;
  }

  /* Projection helpers for hover detection */
  function toXYZ(lat, lng) {
    var p = ((90 - lat) * Math.PI) / 180;
    var t = ((lng + 180) * Math.PI) / 180;
    return [Math.sin(p) * Math.cos(t), Math.cos(p), Math.sin(p) * Math.sin(t)];
  }
  function applyRot(v, phi, theta) {
    var x = v[0], y = v[1], z = v[2];
    var cp = Math.cos(phi),  sp = Math.sin(phi);
    var x2 = x*cp + z*sp,   z2 = -x*sp + z*cp; x = x2; z = z2;
    var ct = Math.cos(theta),st = Math.sin(theta);
    var y2 = y*ct + z*st,   z3 = -y*st + z*ct; y = y2; z = z3;
    return [x, y, z];
  }
  function toScreen(xyz, w, h) {
    var r = Math.min(w, h) / 2;
    return [w/2 + xyz[0]*r, h/2 - xyz[1]*r];
  }

  function init() {
    var canvas = document.getElementById('hcm-globe');
    var tipEl  = document.getElementById('hcm-globe-tip');
    if (!canvas) return;

    var phi = DEF_PHI, theta = DEF_THETA;
    var isDragging = false, prevX = 0, prevY = 0;
    var returning = false, resetTimer = null, time = 0;

    function scheduleReturn() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () { returning = true; }, 800);
    }

    canvas.addEventListener('pointerdown', function (e) {
      isDragging = true; returning = false; clearTimeout(resetTimer);
      prevX = e.clientX; prevY = e.clientY;
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
      if (tipEl) tipEl.style.display = 'none';
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
      if (tipEl) tipEl.style.display = 'none';
    });

    /* Hover tooltip */
    canvas.addEventListener('mousemove', function (e) {
      if (!tipEl || isDragging) return;
      var rect   = canvas.getBoundingClientRect();
      var scaleX = canvas.width  / rect.width;
      var scaleY = canvas.height / rect.height;
      var mx     = (e.clientX - rect.left) * scaleX;
      var my     = (e.clientY - rect.top)  * scaleY;
      var cw = canvas.width, ch = canvas.height;
      var threshold = 30 * scaleX;
      var best = null, bestD = threshold;

      HOSPITALS.forEach(function (h) {
        var rot = applyRot(toXYZ(h.loc[0], h.loc[1]), phi, theta);
        if (rot[2] > 0) return;
        var sp = toScreen(rot, cw, ch);
        var d  = Math.sqrt((sp[0]-mx)**2 + (sp[1]-my)**2);
        if (d < bestD) { bestD = d; best = { h: h, sp: sp }; }
      });

      if (best) {
        tipEl.innerHTML = '<strong>' + best.h.city + ', ' + best.h.state + '</strong>'
          + '<span>' + best.h.cases + ' cases</span>';
        tipEl.style.left    = (best.sp[0] / scaleX + 14) + 'px';
        tipEl.style.top     = (best.sp[1] / scaleY - 50) + 'px';
        tipEl.style.display = 'block';
      } else {
        tipEl.style.display = 'none';
      }
    });

    var globe = createGlobe(canvas, {
      devicePixelRatio: Math.min(window.devicePixelRatio || 1, 2),
      width:            canvas.offsetWidth  * 2,
      height:           canvas.offsetHeight * 2,
      phi:              DEF_PHI,
      theta:            DEF_THETA,
      dark:             0.8,
      diffuse:          2.2,
      mapSamples:       24000,
      mapBrightness:    8,
      baseColor:        [0.05, 0.18, 0.42],  /* deep ocean blue  */
      markerColor:      [0.0,  0.92, 0.74],  /* bright teal      */
      glowColor:        [0.18, 0.55, 1.0],   /* blue atmosphere  */
      markers:          MARKERS,
      onRender: function (state) {
        time += 0.015;

        if (returning && !isDragging) {
          var dP = angleDiff(phi, DEF_PHI), dT = DEF_THETA - theta;
          if (Math.abs(dP) < 0.002 && Math.abs(dT) < 0.002) {
            phi = DEF_PHI; theta = DEF_THETA; returning = false;
          } else { phi += dP * 0.10; theta += dT * 0.10; }
        }

        state.markers = MARKERS.map(function (m, i) {
          var pulse = 0.86 + 0.14 * Math.sin(time * 2.2 + i * 0.55);
          return { location: m.location, size: m.size * pulse };
        });

        state.phi    = phi;
        state.theta  = theta;
        state.width  = canvas.offsetWidth  * 2;
        state.height = canvas.offsetHeight * 2;
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
