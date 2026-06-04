/* Hospital Coverage Map — platform page */
(function () {
  'use strict';

  /* Coordinates mapped from the Power BI screenshot */
  var HOSPITALS = [
    /* ── Dense Texas cluster ── */
    { lat: 32.90, lng: -97.05, cases: 280, type: 'consignment' },  // DFW core
    { lat: 32.78, lng: -96.80, cases: 210, type: 'stock'        },  // Dallas
    { lat: 33.15, lng: -97.10, cases: 190, type: 'consignment' },  // Denton
    { lat: 32.50, lng: -97.35, cases: 175, type: 'consignment' },  // Mansfield
    { lat: 33.20, lng: -96.65, cases: 160, type: 'stock'        },  // McKinney
    { lat: 32.95, lng: -96.45, cases: 140, type: 'consignment' },  // Rockwall
    { lat: 33.05, lng: -97.52, cases: 120, type: 'stock'        },  // Keller
    { lat: 32.35, lng: -97.00, cases: 115, type: 'consignment' },  // Cleburne
    { lat: 31.55, lng: -97.15, cases: 108, type: 'consignment' },  // Waco
    { lat: 31.10, lng: -97.73, cases: 95,  type: 'stock'        },  // Temple
    { lat: 30.27, lng: -97.74, cases: 190, type: 'consignment' },  // Austin
    { lat: 30.50, lng: -97.85, cases: 155, type: 'stock'        },  // Round Rock
    { lat: 29.76, lng: -95.37, cases: 245, type: 'consignment' },  // Houston
    { lat: 29.90, lng: -95.55, cases: 180, type: 'stock'        },  // Cypress/NW Houston
    { lat: 29.62, lng: -95.62, cases: 145, type: 'consignment' },  // Sugar Land
    { lat: 29.42, lng: -98.49, cases: 198, type: 'consignment' },  // San Antonio core
    { lat: 29.60, lng: -98.62, cases: 165, type: 'stock'        },  // San Antonio NW
    { lat: 29.30, lng: -98.35, cases: 130, type: 'consignment' },  // San Antonio SE
    { lat: 29.10, lng: -99.10, cases: 88,  type: 'stock'        },  // Uvalde
    { lat: 27.80, lng: -97.40, cases: 72,  type: 'consignment' },  // Corpus Christi
    { lat: 26.20, lng: -98.25, cases: 64,  type: 'stock'        },  // McAllen
    { lat: 31.77, lng: -106.50, cases: 49, type: 'consignment' }, // El Paso
    { lat: 35.47, lng: -97.52, cases: 140, type: 'consignment' },  // OKC
    { lat: 36.15, lng: -95.99, cases: 112, type: 'stock'        },  // Tulsa
    { lat: 35.38, lng: -94.40, cases: 88,  type: 'consignment' },  // Fort Smith AR
    { lat: 36.37, lng: -94.21, cases: 74,  type: 'stock'        },  // NW Arkansas
    { lat: 34.74, lng: -92.33, cases: 66,  type: 'consignment' },  // Little Rock
    { lat: 36.17, lng: -86.78, cases: 108, type: 'consignment' },  // Nashville
    { lat: 35.15, lng: -90.05, cases: 58,  type: 'stock'        },  // Memphis
    { lat: 32.30, lng: -90.18, cases: 44,  type: 'consignment' },  // Jackson MS
    { lat: 33.75, lng: -84.39, cases: 65,  type: 'stock'        },  // Atlanta
    { lat: 35.23, lng: -101.83, cases: 52, type: 'stock'        },  // Amarillo
    /* ── Arizona / Southwest ── */
    { lat: 33.45, lng: -112.07, cases: 145, type: 'consignment' }, // Phoenix
    { lat: 33.42, lng: -111.94, cases: 98,  type: 'stock'        }, // Scottsdale/Mesa
    { lat: 32.22, lng: -110.97, cases: 58,  type: 'consignment' }, // Tucson
    /* ── Nevada ── */
    { lat: 36.18, lng: -115.14, cases: 42,  type: 'stock'        }, // Las Vegas
    /* ── California ── */
    { lat: 34.05, lng: -118.24, cases: 97,  type: 'consignment' }, // LA
    { lat: 32.73, lng: -117.15, cases: 45,  type: 'stock'        }, // San Diego
    /* ── Colorado ── */
    { lat: 39.74, lng: -104.98, cases: 88,  type: 'consignment' }, // Denver
    /* ── Great Lakes / Midwest ── */
    { lat: 43.05, lng: -83.68, cases: 38,  type: 'stock'        }, // Flint/Detroit area (blue dot in image)
    { lat: 43.05, lng: -87.95, cases: 34,  type: 'consignment' }, // Milwaukee (teal dot)
    { lat: 41.88, lng: -87.63, cases: 55,  type: 'stock'        }, // Chicago
    { lat: 44.98, lng: -93.27, cases: 42,  type: 'consignment' }, // Minneapolis
  ];

  function init() {
    var mapEl = document.getElementById('hcm-leaflet-map');
    if (!mapEl || typeof L === 'undefined') return;

    var DEFAULT_CENTER = [37.0, -96.5];
    var DEFAULT_ZOOM   = 4;
    var resetTimer     = null;

    var map = L.map('hcm-leaflet-map', {
      center: DEFAULT_CENTER,
      zoom:   DEFAULT_ZOOM,
      zoomControl: true,
      scrollWheelZoom: false,
      attributionControl: true,
      minZoom: 3,
      maxZoom: 10,
    });

    /* Dark techy base tiles */
    L.tileLayer(
      'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png',
      {
        attribution: '&copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
      }
    ).addTo(map);

    /* Teal state borders via GeoJSON */
    fetch('https://raw.githubusercontent.com/PublicaMundi/MappingAPI/master/data/geojson/us-states.json')
      .then(function(r){ return r.json(); })
      .then(function(data){
        L.geoJSON(data, {
          style: {
            color:       '#00d2af',
            weight:      1.2,
            opacity:     0.55,
            fillColor:   'transparent',
            fillOpacity: 0,
          },
          interactive: false,
        }).addTo(map);
      });

    /* Place markers */
    var maxCases = Math.max.apply(null, HOSPITALS.map(function(h){ return h.cases; }));
    var markerList = [];

    HOSPITALS.forEach(function(h) {
      var r     = 5 + Math.round((h.cases / maxCases) * 14);
      var color = h.type === 'consignment' ? '#00d2af' : '#2a7de1';

      var icon = L.divIcon({
        className: '',
        html: '<div style="'
          + 'width:'+ (r*2) +'px;height:'+ (r*2) +'px;'
          + 'border-radius:50%;'
          + 'background:'+ color +';'
          + 'opacity:0.82;'
          + 'border:1.5px solid rgba(255,255,255,0.3);'
          + 'box-shadow:0 0 '+ (r+6) +'px '+ color +'99;'
          + '"></div>',
        iconSize:   [r*2, r*2],
        iconAnchor: [r,   r  ],
      });

      var marker = L.marker([h.lat, h.lng], { icon: icon })
        .bindPopup(
          '<div class="hcm-popup">'
            + '<strong>' + h.cases + ' cases</strong>'
            + '<span>' + (h.type === 'consignment' ? 'Consignment' : 'Stock & Bill') + '</span>'
          + '</div>',
          { className: 'hcm-popup-wrap', maxWidth: 180 }
        )
        .addTo(map);

      markerList.push({ marker: marker, type: h.type });
    });

    /* Filter toggle */
    document.querySelectorAll('.hcm-toggle__btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.hcm-toggle__btn').forEach(function(b){ b.classList.remove('is-active'); });
        btn.classList.add('is-active');
        var active = btn.dataset.type;

        markerList.forEach(function(obj) {
          var el = obj.marker.getElement();
          if (!el) return;
          var show = active === 'all' || obj.type === active;
          el.style.opacity       = show ? '1' : '0.08';
          el.style.pointerEvents = show ? ''  : 'none';
        });
      });
    });

    /* Auto-reset to US after 3s of inactivity */
    function scheduleReset() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function() {
        map.flyTo(DEFAULT_CENTER, DEFAULT_ZOOM, { duration: 1.4, easeLinearity: 0.3 });
      }, 3000);
    }
    map.on('moveend zoomend', scheduleReset);
    map.on('mousedown touchstart', function(){ clearTimeout(resetTimer); });

    /* Scroll zoom only while hovering */
    mapEl.addEventListener('mouseenter', function(){ map.scrollWheelZoom.enable(); });
    mapEl.addEventListener('mouseleave', function(){ map.scrollWheelZoom.disable(); });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
