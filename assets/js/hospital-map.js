(function () {
  'use strict';

  var HOSPITALS = [
    /* ── DFW Metroplex ── */
    { lat: 32.90, lng: -97.05, city: 'Fort Worth',     state: 'TX', cases: 280 },
    { lat: 32.78, lng: -96.80, city: 'Dallas',          state: 'TX', cases: 210 },
    { lat: 33.15, lng: -97.10, city: 'Denton',          state: 'TX', cases: 190 },
    { lat: 32.50, lng: -97.35, city: 'Mansfield',       state: 'TX', cases: 175 },
    { lat: 33.20, lng: -96.65, city: 'McKinney',        state: 'TX', cases: 160 },
    { lat: 32.95, lng: -96.45, city: 'Rockwall',        state: 'TX', cases: 140 },
    { lat: 33.05, lng: -97.52, city: 'Keller',          state: 'TX', cases: 120 },
    { lat: 32.35, lng: -97.00, city: 'Cleburne',        state: 'TX', cases: 115 },
    /* ── Central TX ── */
    { lat: 31.55, lng: -97.15, city: 'Waco',            state: 'TX', cases: 108 },
    { lat: 31.10, lng: -97.73, city: 'Temple',          state: 'TX', cases:  95 },
    { lat: 30.27, lng: -97.74, city: 'Austin',          state: 'TX', cases: 190 },
    { lat: 30.50, lng: -97.85, city: 'Round Rock',      state: 'TX', cases: 155 },
    /* ── Houston Area ── */
    { lat: 29.76, lng: -95.37, city: 'Houston',         state: 'TX', cases: 245 },
    { lat: 29.90, lng: -95.55, city: 'Cypress',         state: 'TX', cases: 180 },
    { lat: 29.62, lng: -95.62, city: 'Sugar Land',      state: 'TX', cases: 145 },
    /* ── San Antonio ── */
    { lat: 29.42, lng: -98.49, city: 'San Antonio',     state: 'TX', cases: 198 },
    { lat: 29.60, lng: -98.62, city: 'San Antonio NW',  state: 'TX', cases: 165 },
    { lat: 29.30, lng: -98.35, city: 'San Antonio SE',  state: 'TX', cases: 130 },
    /* ── South TX ── */
    { lat: 29.10, lng: -99.10, city: 'Uvalde',          state: 'TX', cases:  88 },
    { lat: 27.80, lng: -97.40, city: 'Corpus Christi',  state: 'TX', cases:  72 },
    { lat: 26.20, lng: -98.25, city: 'McAllen',         state: 'TX', cases:  64 },
    { lat: 31.77, lng:-106.50, city: 'El Paso',         state: 'TX', cases:  49 },
    { lat: 35.23, lng:-101.83, city: 'Amarillo',        state: 'TX', cases:  52 },
    /* ── Oklahoma ── */
    { lat: 35.47, lng: -97.52, city: 'Oklahoma City',   state: 'OK', cases: 140 },
    { lat: 36.15, lng: -95.99, city: 'Tulsa',           state: 'OK', cases: 112 },
    /* ── Arkansas ── */
    { lat: 35.38, lng: -94.40, city: 'Fort Smith',      state: 'AR', cases:  88 },
    { lat: 36.37, lng: -94.21, city: 'Bentonville',     state: 'AR', cases:  74 },
    { lat: 34.74, lng: -92.33, city: 'Little Rock',     state: 'AR', cases:  66 },
    /* ── Tennessee ── */
    { lat: 36.17, lng: -86.78, city: 'Nashville',       state: 'TN', cases: 108 },
    { lat: 35.15, lng: -90.05, city: 'Memphis',         state: 'TN', cases:  58 },
    /* ── Southeast ── */
    { lat: 32.30, lng: -90.18, city: 'Jackson',         state: 'MS', cases:  44 },
    { lat: 33.75, lng: -84.39, city: 'Atlanta',         state: 'GA', cases:  65 },
    /* ── Arizona ── */
    { lat: 33.45, lng:-112.07, city: 'Phoenix',         state: 'AZ', cases: 145 },
    { lat: 33.42, lng:-111.94, city: 'Mesa',            state: 'AZ', cases:  98 },
    { lat: 32.22, lng:-110.97, city: 'Tucson',          state: 'AZ', cases:  58 },
    /* ── West ── */
    { lat: 36.18, lng:-115.14, city: 'Las Vegas',       state: 'NV', cases:  42 },
    { lat: 34.05, lng:-118.24, city: 'Los Angeles',     state: 'CA', cases:  97 },
    { lat: 32.73, lng:-117.15, city: 'San Diego',       state: 'CA', cases:  45 },
    { lat: 39.74, lng:-104.98, city: 'Denver',          state: 'CO', cases:  88 },
    /* ── Midwest ── */
    { lat: 43.05, lng: -83.68, city: 'Flint',           state: 'MI', cases:  38 },
    { lat: 43.05, lng: -87.95, city: 'Milwaukee',       state: 'WI', cases:  34 },
    { lat: 41.88, lng: -87.63, city: 'Chicago',         state: 'IL', cases:  55 },
    { lat: 44.98, lng: -93.27, city: 'Minneapolis',     state: 'MN', cases:  42 },
  ];

  var MAX_CASES = 280;

  function markerRadius(cases) {
    return 6 + (cases / MAX_CASES) * 14;
  }

  function init() {
    var el = document.getElementById('hcm-map');
    if (!el || typeof L === 'undefined') return;

    var map = L.map('hcm-map', {
      center:          [35.5, -97.5],
      zoom:            5,
      zoomControl:     true,
      scrollWheelZoom: false,
      attributionControl: false,
    });

    /* Dark CartoDB tiles */
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19,
    }).addTo(map);

    /* Subtle state labels layer on top */
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_only_labels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19,
      opacity: 0.5,
    }).addTo(map);

    /* Hospital markers */
    HOSPITALS.forEach(function (h) {
      var r = markerRadius(h.cases);

      var circle = L.circleMarker([h.lat, h.lng], {
        radius:      r,
        fillColor:   '#00dbb7',
        fillOpacity: 0.75,
        color:       'rgba(0,220,185,0.9)',
        weight:      1.5,
      }).addTo(map);

      circle.bindTooltip(
        '<strong>' + h.city + ', ' + h.state + '</strong>' +
        '<span style="display:block;font-size:11px;opacity:.8;margin-top:2px">' + h.cases + ' cases / yr</span>',
        {
          direction:  'top',
          offset:     [0, -r],
          opacity:    1,
          className:  'hcm-tooltip',
        }
      );

      /* Pulse on hover */
      circle.on('mouseover', function () {
        this.setStyle({ fillOpacity: 1, weight: 2.5 });
      });
      circle.on('mouseout', function () {
        this.setStyle({ fillOpacity: 0.75, weight: 1.5 });
      });
    });

    /* Attribution */
    L.control.attribution({ prefix: false })
      .addAttribution('© <a href="https://carto.com">CARTO</a>')
      .addTo(map);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
