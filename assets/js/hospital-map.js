(function () {
  'use strict';

  /* ── Case data by city ──────────────────────────────────────
     Real case counts from "Case Count by Location.xlsx" (110 cities,
     6,357 cases across TX, OK, AZ, AR, NM, UT). lng/lat geocoded;
     colors cycle a vivid palette so each city reads as its own dot. */
  var HOSPITALS = window.LEAP_CASES || [];

  var MAX_CASES = HOSPITALS.reduce(function (m, h) { return Math.max(m, h.cases); }, 1);

  function toGeoJSON(hospitals) {
    return {
      type: 'FeatureCollection',
      features: hospitals.map(function (h) {
        return {
          type: 'Feature',
          properties: {
            city:  h.city,
            state: h.state,
            cases: h.cases,
            color: h.color,
            /* radius encoded as property so MapLibre expression can read it */
            radius: Math.round(6 + (h.cases / MAX_CASES) * 14),
          },
          geometry: { type: 'Point', coordinates: [h.lng, h.lat] },
        };
      }),
    };
  }

  var DEFAULT_CENTER = [-96, 37.8];
  var DEFAULT_ZOOM   = 4;
  /* Hard pan limit — user cannot drag beyond this box */
  var MAX_BOUNDS     = [[-145, 17], [-50, 57]];
  /* Softer inner box — drifting outside triggers auto-reset */
  var RESET_BOUNDS   = new maplibregl.LngLatBounds([-130, 22], [-62, 52]);

  function init() {
    var el = document.getElementById('hcm-map');
    if (!el || typeof maplibregl === 'undefined') return;

    var map = new maplibregl.Map({
      container:  'hcm-map',
      /* CartoDB dark-matter vector style — same as the mapcn component */
      style:      'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
      center:     DEFAULT_CENTER,
      zoom:       DEFAULT_ZOOM,
      minZoom:    3,
      maxZoom:    9,
      maxBounds:  MAX_BOUNDS,
      attributionControl: { compact: true },
    });

    map.scrollZoom.disable();

    /* ── Auto-reset ─────────────────────────────────────────────
       2.5 s after the user stops moving, fly back to the default
       US view if the centre has drifted outside the US region.  */
    var resetTimer = null;
    map.on('moveend', function () {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function () {
        var c = map.getCenter();
        if (!RESET_BOUNDS.contains(c) || map.getZoom() < 3.2) {
          map.flyTo({ center: DEFAULT_CENTER, zoom: DEFAULT_ZOOM, duration: 1400, essential: true });
        }
      }, 2500);
    });

    map.addControl(
      new maplibregl.NavigationControl({ showCompass: false }),
      'bottom-right'
    );

    map.on('load', function () {

      /* ── 1. Hide rivers, lakes and all city / place labels ─────────────────
         Water features (rivers, lakes, reservoirs) are removed for a clean
         look. City and place name labels are hidden — we render only our own
         faded state names below.                                              */
      map.getStyle().layers.forEach(function (layer) {
        var sl  = layer['source-layer'] || '';
        var id  = layer.id;
        var hide =
          sl === 'waterway' || sl === 'water' ||
          id === 'water' || id === 'water-shadow' || id === 'water-pattern' ||
          id.indexOf('waterway')    !== -1 ||
          id.indexOf('water-label') !== -1 ||
          id.indexOf('water_label') !== -1 ||
          /* city / place / POI labels */
          sl === 'place' || sl === 'poi' ||
          id.indexOf('place')       !== -1 ||
          id.indexOf('poi')         !== -1;
        if (hide) {
          try { map.setLayoutProperty(id, 'visibility', 'none'); } catch (e) {}
        }
      });

      /* ── 2. Load local TopoJSON (topologically correct — no cut-off borders) */
      var base = '';
      if (typeof window.leapData !== 'undefined' && window.leapData.themeUrl) {
        base = window.leapData.themeUrl;
      } else {
        var scripts = document.querySelectorAll('script[src*="hospital-map"]');
        if (scripts.length) base = scripts[0].src.replace(/\/assets\/js\/hospital-map\.js.*$/, '');
      }

      fetch(base + '/assets/js/us-states.json')
        .then(function (r) { return r.json(); })
        .then(function (topo) {
          var states = topojson.feature(topo, topo.objects.states);
          var nation = topojson.feature(topo, topo.objects.nation);

          map.addSource('us-states', { type: 'geojson', data: states });
          map.addSource('us-nation',  { type: 'geojson', data: nation });

          /* Individual state borders */
          map.addLayer({
            id: 'us-state-borders',
            type: 'line',
            source: 'us-states',
            paint: {
              'line-color': 'rgba(0, 210, 190, 0.55)',
              'line-width': 0.8,
            },
          });

          /* Outer US national border — slightly thicker to close the perimeter */
          map.addLayer({
            id: 'us-national-border',
            type: 'line',
            source: 'us-nation',
            paint: {
              'line-color': 'rgba(0, 210, 190, 0.75)',
              'line-width': 1.2,
            },
          });

          /* Faded state name labels — all 50 states, no cities */
          map.addLayer({
            id: 'us-state-names',
            type: 'symbol',
            source: 'us-states',
            layout: {
              'text-field':          ['get', 'name'],
              'text-font':           ['DIN Offc Pro Medium', 'Arial Unicode MS Regular'],
              'text-size':           ['interpolate', ['linear'], ['zoom'], 3, 9, 7, 13],
              'text-transform':      'uppercase',
              'text-letter-spacing': 0.1,
              'text-max-width':      6,
            },
            paint: {
              /* same teal as the state borders */
              'text-color':      'rgba(0, 210, 190, 0.55)',
              'text-halo-color': 'rgba(0, 0, 0, 0.55)',
              'text-halo-width': 1,
            },
          });

          addHospitals(map);
        })
        .catch(function () {
          addHospitals(map);
        });
    });
  }

  function addHospitals(map) {
    map.addSource('hospitals', {
      type: 'geojson',
      data: toGeoJSON(HOSPITALS),
    });

    map.addLayer({
      id: 'hospital-dots',
      type: 'circle',
      source: 'hospitals',
      paint: {
        'circle-color':        ['get', 'color'],
        'circle-radius':       ['get', 'radius'],
        'circle-opacity':      0.88,
        'circle-stroke-width': 1.5,
        'circle-stroke-color': 'rgba(255,255,255,0.35)',
      },
    });

    /* Radar-pulse halo beneath each dot */
    map.addLayer({
      id: 'hospital-pulse',
      type: 'circle',
      source: 'hospitals',
      paint: {
        'circle-color':   ['get', 'color'],
        'circle-radius':  ['get', 'radius'],
        'circle-opacity': 0,
        'circle-blur':    0.4,
      },
    }, 'hospital-dots');

    if (!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
      var pulseStart = null;
      (function pulse(ts) {
        if (pulseStart === null) { pulseStart = ts; }
        var p = ( ( ts - pulseStart ) % 1900 ) / 1900; // 0→1 over 1.9s
        map.setPaintProperty('hospital-pulse', 'circle-radius',  ['*', ['get', 'radius'], 1 + p * 2.2]);
        map.setPaintProperty('hospital-pulse', 'circle-opacity', 0.5 * (1 - p));
        requestAnimationFrame(pulse);
      })(performance.now());
    }

    /* Hover popup */
    var popup = new maplibregl.Popup({
      closeButton:  false,
      closeOnClick: false,
      className:    'hcm-popup',
      offset:       12,
    });

    map.on('mouseenter', 'hospital-dots', function (e) {
      map.getCanvas().style.cursor = 'pointer';
      var f    = e.features[0];
      var p    = f.properties;
      var coords = f.geometry.coordinates.slice();

      popup
        .setLngLat(coords)
        .setHTML(
          '<strong>' + p.city + ', ' + p.state + '</strong>' +
          '<span>' + p.cases + ' cases / yr</span>'
        )
        .addTo(map);

      /* Brighten hovered dot */
      map.setPaintProperty('hospital-dots', 'circle-opacity', [
        'case', ['==', ['get', 'city'], p.city], 1, 0.88,
      ]);
      map.setPaintProperty('hospital-dots', 'circle-stroke-color', [
        'case', ['==', ['get', 'city'], p.city], '#fff', 'rgba(255,255,255,0.35)',
      ]);
    });

    map.on('mouseleave', 'hospital-dots', function () {
      map.getCanvas().style.cursor = '';
      popup.remove();
      map.setPaintProperty('hospital-dots', 'circle-opacity', 0.88);
      map.setPaintProperty('hospital-dots', 'circle-stroke-color', 'rgba(255,255,255,0.35)');
    });
  }

  /* Build the sliding case-feed ticker from the same data. */
  function buildTicker() {
    var el = document.getElementById('hcm-ticker');
    if (!el) return;
    var sorted = HOSPITALS.slice().sort(function (a, b) { return b.cases - a.cases; });
    var html = sorted.map(function (h) {
      return '<span class="hcm-ticker__item">' +
        '<span class="hcm-ticker__dot" style="color:' + h.color + ';background:' + h.color + '"></span>' +
        '<span class="hcm-ticker__city">' + h.city + ', ' + h.state + '</span>' +
        '<span class="hcm-ticker__count">' + h.cases + ' cases</span>' +
        '</span><span class="hcm-ticker__sep" aria-hidden="true">•</span>';
    }).join('');
    el.innerHTML = html + html; /* two copies → seamless -50% loop */
  }

  function boot() { buildTicker(); init(); }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
