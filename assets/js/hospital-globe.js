/* Hospital Coverage Map — platform page */
(function () {
  'use strict';

  var HOSPITALS = [
    /* Texas — dense cluster */
    { lat: 32.89,  lng: -97.04,  label: 'DFW Medical District',        cases: 312, type: 'consignment' },
    { lat: 33.03,  lng: -96.75,  label: 'Baylor Scott & White – Plano', cases: 198, type: 'consignment' },
    { lat: 32.72,  lng: -97.32,  label: 'Texas Health – Fort Worth',    cases: 176, type: 'stock'       },
    { lat: 29.76,  lng: -95.37,  label: 'Houston Methodist',            cases: 284, type: 'consignment' },
    { lat: 29.71,  lng: -95.40,  label: 'St. Luke\'s Medical Center',   cases: 143, type: 'stock'       },
    { lat: 29.42,  lng: -98.49,  label: 'Baptist Health – San Antonio', cases: 198, type: 'consignment' },
    { lat: 29.55,  lng: -98.61,  label: 'CHRISTUS Santa Rosa',          cases: 112, type: 'stock'       },
    { lat: 30.27,  lng: -97.74,  label: 'St. David\'s HealthCare',      cases: 176, type: 'consignment' },
    { lat: 31.54,  lng: -97.14,  label: 'Baylor Scott & White – Waco',  cases: 88,  type: 'stock'       },
    { lat: 32.45,  lng: -99.73,  label: 'Hendrick Medical – Abilene',   cases: 64,  type: 'consignment' },
    { lat: 35.47,  lng: -97.52,  label: 'Integris Health – OKC',        cases: 121, type: 'consignment' },
    { lat: 36.08,  lng: -95.89,  label: 'Hillcrest Medical – Tulsa',    cases: 96,  type: 'stock'       },
    { lat: 36.37,  lng: -94.21,  label: 'Washington Regional – NWA',    cases: 74,  type: 'consignment' },
    { lat: 34.74,  lng: -92.33,  label: 'Baptist Health – Little Rock', cases: 66,  type: 'stock'       },
    { lat: 36.17,  lng: -86.78,  label: 'Vanderbilt UMC – Nashville',   cases: 108, type: 'consignment' },
    { lat: 35.23,  lng: -101.83, label: 'BSA Health System – Amarillo', cases: 52,  type: 'stock'       },
    { lat: 31.77,  lng: -106.50, label: 'Del Sol Medical – El Paso',    cases: 49,  type: 'consignment' },
    { lat: 33.45,  lng: -112.07, label: 'HonorHealth – Phoenix',        cases: 143, type: 'consignment' },
    { lat: 33.38,  lng: -111.94, label: 'Banner Desert Medical',        cases: 97,  type: 'stock'       },
    { lat: 39.74,  lng: -104.98, label: 'UCHealth – Denver',            cases: 88,  type: 'consignment' },
    { lat: 34.05,  lng: -118.24, label: 'Cedars-Sinai – LA',            cases: 97,  type: 'consignment' },
    { lat: 32.73,  lng: -117.15, label: 'Sharp Memorial – San Diego',   cases: 45,  type: 'stock'       },
    { lat: 37.34,  lng: -121.89, label: 'Good Samaritan – San Jose',    cases: 41,  type: 'consignment' },
    { lat: 44.97,  lng: -93.27,  label: 'Abbott Northwestern – Mpls',   cases: 58,  type: 'stock'       },
    { lat: 41.88,  lng: -87.63,  label: 'Northwestern Memorial – Chi',  cases: 72,  type: 'consignment' },
    { lat: 33.75,  lng: -84.39,  label: 'Emory University Hospital',    cases: 65,  type: 'stock'       },
    { lat: 47.61,  lng: -122.33, label: 'Swedish Medical – Seattle',    cases: 37,  type: 'consignment' },
  ];

  function init() {
    var mapEl = document.getElementById('hcm-leaflet-map');
    if (!mapEl || typeof L === 'undefined') return;

    var DEFAULT_CENTER = [37.5, -96.5];
    var DEFAULT_ZOOM   = 4;
    var resetTimer     = null;

    var map = L.map('hcm-leaflet-map', {
      center: DEFAULT_CENTER,
      zoom:   DEFAULT_ZOOM,
      zoomControl: true,
      scrollWheelZoom: false,
      attributionControl: true,
    });

    /* Dark techy tiles — CartoDB Dark Matter */
    L.tileLayer(
      'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
      {
        attribution: '&copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
      }
    ).addTo(map);

    /* Auto-reset to US after 3s of inactivity */
    function scheduleReset() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function() {
        map.flyTo(DEFAULT_CENTER, DEFAULT_ZOOM, { duration: 1.4, easeLinearity: 0.3 });
      }, 3000);
    }

    map.on('moveend zoomend', scheduleReset);
    map.on('mousedown touchstart', function() { clearTimeout(resetTimer); });

    var maxCases = Math.max.apply(null, HOSPITALS.map(function(h){ return h.cases; }));

    /* Active filter state */
    var activeType = 'all';

    var markerMap = {};

    HOSPITALS.forEach(function(h) {
      var r       = 6 + Math.round((h.cases / maxCases) * 16);
      var isConsignment = h.type === 'consignment';
      var color   = isConsignment ? '#00d2af' : '#2a7de1';
      var opacity = 0.82;

      var icon = L.divIcon({
        className: '',
        html: '<div style="'
          + 'width:'  + (r * 2) + 'px;'
          + 'height:' + (r * 2) + 'px;'
          + 'border-radius:50%;'
          + 'background:' + color + ';'
          + 'opacity:' + opacity + ';'
          + 'border:2px solid rgba(255,255,255,0.35);'
          + 'box-shadow:0 0 ' + (r + 4) + 'px ' + color + '88;'
          + '"></div>',
        iconSize:   [r * 2, r * 2],
        iconAnchor: [r, r],
      });

      var marker = L.marker([h.lat, h.lng], { icon: icon })
        .bindPopup(
          '<div class="hcm-popup">'
          + '<strong>' + h.label + '</strong>'
          + '<span>' + h.cases + ' cases · ' + (isConsignment ? 'Consignment' : 'Stock & Bill') + '</span>'
          + '</div>',
          { className: 'hcm-popup-wrap', maxWidth: 220 }
        )
        .addTo(map);

      markerMap[h.label] = { marker: marker, type: h.type };
    });

    /* Filter toggle */
    document.querySelectorAll('.hcm-toggle__btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.hcm-toggle__btn').forEach(function(b){ b.classList.remove('is-active'); });
        btn.classList.add('is-active');
        activeType = btn.dataset.type;

        Object.values(markerMap).forEach(function(obj) {
          var el = obj.marker.getElement();
          if (!el) return;
          var show = activeType === 'all' || obj.type === activeType;
          el.style.opacity = show ? '1' : '0.12';
          el.style.pointerEvents = show ? '' : 'none';
        });
      });
    });

    /* Enable scroll zoom only while focused */
    mapEl.addEventListener('mouseenter', function(){ map.scrollWheelZoom.enable(); });
    mapEl.addEventListener('mouseleave', function(){ map.scrollWheelZoom.disable(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
