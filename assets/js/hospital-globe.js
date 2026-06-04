import createGlobe from 'https://esm.sh/cobe@0.6.3';

(function () {
  'use strict';

  /* Hospital markers — [lat, lng], size scaled by case volume */
  var RAW = [
    { loc: [32.90, -97.05], cases: 280 },  // Fort Worth
    { loc: [32.78, -96.80], cases: 210 },  // Dallas
    { loc: [33.15, -97.10], cases: 190 },  // Denton
    { loc: [32.50, -97.35], cases: 175 },  // Mansfield
    { loc: [33.20, -96.65], cases: 160 },  // McKinney
    { loc: [32.95, -96.45], cases: 140 },  // Rockwall
    { loc: [33.05, -97.52], cases: 120 },  // Keller
    { loc: [32.35, -97.00], cases: 115 },  // Cleburne
    { loc: [31.55, -97.15], cases: 108 },  // Waco
    { loc: [31.10, -97.73], cases: 95  },  // Temple
    { loc: [30.27, -97.74], cases: 190 },  // Austin
    { loc: [30.50, -97.85], cases: 155 },  // Round Rock
    { loc: [29.76, -95.37], cases: 245 },  // Houston
    { loc: [29.90, -95.55], cases: 180 },  // Cypress
    { loc: [29.62, -95.62], cases: 145 },  // Sugar Land
    { loc: [29.42, -98.49], cases: 198 },  // San Antonio
    { loc: [29.60, -98.62], cases: 165 },  // San Antonio NW
    { loc: [29.30, -98.35], cases: 130 },  // San Antonio SE
    { loc: [29.10, -99.10], cases: 88  },  // Uvalde
    { loc: [27.80, -97.40], cases: 72  },  // Corpus Christi
    { loc: [26.20, -98.25], cases: 64  },  // McAllen
    { loc: [31.77,-106.50], cases: 49  },  // El Paso
    { loc: [35.47, -97.52], cases: 140 },  // Oklahoma City
    { loc: [36.15, -95.99], cases: 112 },  // Tulsa
    { loc: [35.38, -94.40], cases: 88  },  // Fort Smith
    { loc: [36.37, -94.21], cases: 74  },  // Bentonville
    { loc: [34.74, -92.33], cases: 66  },  // Little Rock
    { loc: [36.17, -86.78], cases: 108 },  // Nashville
    { loc: [35.15, -90.05], cases: 58  },  // Memphis
    { loc: [32.30, -90.18], cases: 44  },  // Jackson MS
    { loc: [33.75, -84.39], cases: 65  },  // Atlanta
    { loc: [35.23,-101.83], cases: 52  },  // Amarillo
    { loc: [33.45,-112.07], cases: 145 },  // Phoenix
    { loc: [33.42,-111.94], cases: 98  },  // Mesa
    { loc: [32.22,-110.97], cases: 58  },  // Tucson
    { loc: [36.18,-115.14], cases: 42  },  // Las Vegas
    { loc: [34.05,-118.24], cases: 97  },  // Los Angeles
    { loc: [32.73,-117.15], cases: 45  },  // San Diego
    { loc: [39.74,-104.98], cases: 88  },  // Denver
    { loc: [43.05, -83.68], cases: 38  },  // Flint MI
    { loc: [43.05, -87.95], cases: 34  },  // Milwaukee
    { loc: [41.88, -87.63], cases: 55  },  // Chicago
    { loc: [44.98, -93.27], cases: 42  },  // Minneapolis
  ];

  var MAX_CASES = 280;
  var MARKERS   = RAW.map(function (h) {
    return { location: h.loc, size: 0.022 + (h.cases / MAX_CASES) * 0.048 };
  });

  /* Default orientation: face continental US */
  var DEF_PHI   = 1.75;
  var DEF_THETA = 0.28;

  function angleDiff(a, b) {
    return (((b - a) % (Math.PI * 2)) + Math.PI * 3) % (Math.PI * 2) - Math.PI;
  }

  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;

    var phi       = DEF_PHI;
    var theta     = DEF_THETA;
    var isDragging = false;
    var prevX = 0, prevY = 0;
    var returning  = false;
    var resetTimer = null;
    var time       = 0;

    function scheduleReturn() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () { returning = true; }, 2500);
    }

    canvas.addEventListener('pointerdown', function (e) {
      isDragging = true; returning = false; clearTimeout(resetTimer);
      prevX = e.clientX; prevY = e.clientY;
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', function (e) {
      if (!isDragging) return;
      phi   += (e.clientX - prevX) * 0.005;
      theta  = Math.max(-0.7, Math.min(0.7, theta + (e.clientY - prevY) * 0.003));
      prevX  = e.clientX; prevY = e.clientY;
    });
    canvas.addEventListener('pointerup', function () {
      isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn();
    });
    canvas.addEventListener('pointerleave', function () {
      if (isDragging) { isDragging = false; canvas.style.cursor = 'grab'; scheduleReturn(); }
    });

    var globe = createGlobe(canvas, {
      devicePixelRatio: Math.min(window.devicePixelRatio || 1, 2),
      width:           canvas.offsetWidth  * 2,
      height:          canvas.offsetHeight * 2,
      phi:             DEF_PHI,
      theta:           DEF_THETA,
      dark:            1,
      diffuse:         1.4,
      mapSamples:      20000,
      mapBrightness:   5,
      baseColor:       [0.05, 0.14, 0.22],   /* dark navy land */
      markerColor:     [0.0,  0.82, 0.68],   /* #00d2af teal   */
      glowColor:       [0.0,  0.45, 0.38],   /* teal atmosphere */
      markers:         MARKERS,
      onRender: function (state) {
        time += 0.015;

        /* Smooth snap back to US when idle */
        if (returning && !isDragging) {
          var dPhi   = angleDiff(phi,   DEF_PHI);
          var dTheta = DEF_THETA - theta;
          if (Math.abs(dPhi) < 0.002 && Math.abs(dTheta) < 0.002) {
            phi = DEF_PHI; theta = DEF_THETA; returning = false;
          } else {
            phi   += dPhi   * 0.07;
            theta += dTheta * 0.07;
          }
        }

        /* Pulsing markers — staggered sine per marker */
        state.markers = MARKERS.map(function (m, i) {
          var pulse = 0.88 + 0.12 * Math.sin(time * 2.2 + i * 0.55);
          return { location: m.location, size: m.size * pulse };
        });

        state.phi    = phi;
        state.theta  = theta;
        state.width  = canvas.offsetWidth  * 2;
        state.height = canvas.offsetHeight * 2;
      },
    });

    /* Cleanup on page unload */
    window.addEventListener('beforeunload', function () { globe.destroy(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
