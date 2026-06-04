/* Hospital Coverage Map — platform page */
(function () {
  'use strict';

  var HOSPITALS = [
    { lat: 32.90, lng: -97.05, city: 'Fort Worth',      state: 'TX', cases: 280, type: 'consignment' },
    { lat: 32.78, lng: -96.80, city: 'Dallas',           state: 'TX', cases: 210, type: 'stock'       },
    { lat: 33.15, lng: -97.10, city: 'Denton',           state: 'TX', cases: 190, type: 'consignment' },
    { lat: 32.50, lng: -97.35, city: 'Mansfield',        state: 'TX', cases: 175, type: 'consignment' },
    { lat: 33.20, lng: -96.65, city: 'McKinney',         state: 'TX', cases: 160, type: 'stock'       },
    { lat: 32.95, lng: -96.45, city: 'Rockwall',         state: 'TX', cases: 140, type: 'consignment' },
    { lat: 33.05, lng: -97.52, city: 'Keller',           state: 'TX', cases: 120, type: 'stock'       },
    { lat: 32.35, lng: -97.00, city: 'Cleburne',         state: 'TX', cases: 115, type: 'consignment' },
    { lat: 31.55, lng: -97.15, city: 'Waco',             state: 'TX', cases: 108, type: 'consignment' },
    { lat: 31.10, lng: -97.73, city: 'Temple',           state: 'TX', cases: 95,  type: 'stock'       },
    { lat: 30.27, lng: -97.74, city: 'Austin',           state: 'TX', cases: 190, type: 'consignment' },
    { lat: 30.50, lng: -97.85, city: 'Round Rock',       state: 'TX', cases: 155, type: 'stock'       },
    { lat: 29.76, lng: -95.37, city: 'Houston',          state: 'TX', cases: 245, type: 'consignment' },
    { lat: 29.90, lng: -95.55, city: 'Cypress',          state: 'TX', cases: 180, type: 'stock'       },
    { lat: 29.62, lng: -95.62, city: 'Sugar Land',       state: 'TX', cases: 145, type: 'consignment' },
    { lat: 29.42, lng: -98.49, city: 'San Antonio',      state: 'TX', cases: 198, type: 'consignment' },
    { lat: 29.60, lng: -98.62, city: 'San Antonio NW',   state: 'TX', cases: 165, type: 'stock'       },
    { lat: 29.30, lng: -98.35, city: 'San Antonio SE',   state: 'TX', cases: 130, type: 'consignment' },
    { lat: 29.10, lng: -99.10, city: 'Uvalde',           state: 'TX', cases: 88,  type: 'stock'       },
    { lat: 27.80, lng: -97.40, city: 'Corpus Christi',   state: 'TX', cases: 72,  type: 'consignment' },
    { lat: 26.20, lng: -98.25, city: 'McAllen',          state: 'TX', cases: 64,  type: 'stock'       },
    { lat: 31.77, lng: -106.50,city: 'El Paso',          state: 'TX', cases: 49,  type: 'consignment' },
    { lat: 35.47, lng: -97.52, city: 'Oklahoma City',    state: 'OK', cases: 140, type: 'consignment' },
    { lat: 36.15, lng: -95.99, city: 'Tulsa',            state: 'OK', cases: 112, type: 'stock'       },
    { lat: 35.38, lng: -94.40, city: 'Fort Smith',       state: 'AR', cases: 88,  type: 'consignment' },
    { lat: 36.37, lng: -94.21, city: 'Bentonville',      state: 'AR', cases: 74,  type: 'stock'       },
    { lat: 34.74, lng: -92.33, city: 'Little Rock',      state: 'AR', cases: 66,  type: 'consignment' },
    { lat: 36.17, lng: -86.78, city: 'Nashville',        state: 'TN', cases: 108, type: 'consignment' },
    { lat: 35.15, lng: -90.05, city: 'Memphis',          state: 'TN', cases: 58,  type: 'stock'       },
    { lat: 32.30, lng: -90.18, city: 'Jackson',          state: 'MS', cases: 44,  type: 'consignment' },
    { lat: 33.75, lng: -84.39, city: 'Atlanta',          state: 'GA', cases: 65,  type: 'stock'       },
    { lat: 35.23, lng: -101.83,city: 'Amarillo',         state: 'TX', cases: 52,  type: 'stock'       },
    { lat: 33.45, lng: -112.07,city: 'Phoenix',          state: 'AZ', cases: 145, type: 'consignment' },
    { lat: 33.42, lng: -111.94,city: 'Mesa',             state: 'AZ', cases: 98,  type: 'stock'       },
    { lat: 32.22, lng: -110.97,city: 'Tucson',           state: 'AZ', cases: 58,  type: 'consignment' },
    { lat: 36.18, lng: -115.14,city: 'Las Vegas',        state: 'NV', cases: 42,  type: 'stock'       },
    { lat: 34.05, lng: -118.24,city: 'Los Angeles',      state: 'CA', cases: 97,  type: 'consignment' },
    { lat: 32.73, lng: -117.15,city: 'San Diego',        state: 'CA', cases: 45,  type: 'stock'       },
    { lat: 39.74, lng: -104.98,city: 'Denver',           state: 'CO', cases: 88,  type: 'consignment' },
    { lat: 43.05, lng: -83.68, city: 'Flint',            state: 'MI', cases: 38,  type: 'stock'       },
    { lat: 43.05, lng: -87.95, city: 'Milwaukee',        state: 'WI', cases: 34,  type: 'consignment' },
    { lat: 41.88, lng: -87.63, city: 'Chicago',          state: 'IL', cases: 55,  type: 'stock'       },
    { lat: 44.98, lng: -93.27, city: 'Minneapolis',      state: 'MN', cases: 42,  type: 'consignment' },
  ];

  function init() {
    var mapEl = document.getElementById('hcm-map');
    if (!mapEl || typeof L === 'undefined') return;

    /* Default view — centred on main TX/OK cluster */
    var DEF_CENTER = [32.5, -97.5];
    var DEF_ZOOM   = 5;
    var resetTimer = null;

    var map = L.map('hcm-map', {
      center:           DEF_CENTER,
      zoom:             DEF_ZOOM,
      zoomControl:      true,
      scrollWheelZoom:  false,
      attributionControl: true,
      minZoom: 3,
      maxZoom: 10,
    });

    /* Dark techy tiles — no labels for clean look */
    L.tileLayer(
      'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png',
      { attribution: '&copy; CARTO', subdomains: 'abcd', maxZoom: 19 }
    ).addTo(map);

    /* Teal state borders */
    fetch('https://raw.githubusercontent.com/PublicaMundi/MappingAPI/master/data/geojson/us-states.json')
      .then(function (r) { return r.json(); })
      .then(function (data) {
        L.geoJSON(data, {
          style: { color: '#00d2af', weight: 0.9, opacity: 0.28, fill: false },
          interactive: false,
        }).addTo(map);
      });

    /* Markers */
    var maxCases = Math.max.apply(null, HOSPITALS.map(function (h) { return h.cases; }));
    var markerList = [];

    HOSPITALS.forEach(function (h) {
      var r     = 5 + Math.round((h.cases / maxCases) * 13);
      var color = h.type === 'consignment' ? '#00d2af' : '#2a7de1';
      var d     = r * 2;

      var icon = L.divIcon({
        className: '',
        html: '<div style="width:' + d + 'px;height:' + d + 'px;border-radius:50%;'
          + 'background:' + color + ';opacity:0.85;'
          + 'border:1.5px solid rgba(255,255,255,0.28);'
          + 'box-shadow:0 0 ' + (r + 5) + 'px ' + color + '88;"></div>',
        iconSize:   [d, d],
        iconAnchor: [r, r],
      });

      var marker = L.marker([h.lat, h.lng], { icon: icon })
        .bindTooltip(
          '<div class="hcm-tip">'
            + '<strong>' + h.city + ', ' + h.state + '</strong>'
            + '<span>' + h.cases + ' cases &middot; ' + (h.type === 'consignment' ? 'Consignment' : 'Stock & Bill') + '</span>'
          + '</div>',
          { className: 'hcm-tip-wrap', direction: 'top', offset: [0, -r - 2] }
        )
        .addTo(map);

      markerList.push({ marker: marker, type: h.type });
    });

    /* Auto-reset after 3s of no interaction */
    function scheduleReset() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () {
        map.flyTo(DEF_CENTER, DEF_ZOOM, { duration: 1.4, easeLinearity: 0.3 });
      }, 3000);
    }
    map.on('moveend zoomend', scheduleReset);
    map.on('mousedown touchstart', function () { clearTimeout(resetTimer); });

    /* Scroll zoom on hover only */
    mapEl.addEventListener('mouseenter', function () { map.scrollWheelZoom.enable(); });
    mapEl.addEventListener('mouseleave', function () { map.scrollWheelZoom.disable(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
