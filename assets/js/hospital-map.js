(function () {
  'use strict';

  /* ── Hospital data ──────────────────────────────────────────
     Colors match the vivid per-rep dots in the platform
     screenshot (Unknown-7.png), concentrated in south-central US */
  var HOSPITALS = [
    /* DFW Metroplex */
    { lng: -97.05, lat: 32.90, city: 'Fort Worth',    state: 'TX', cases: 280, color: '#2196f3' },
    { lng: -96.80, lat: 32.78, city: 'Dallas',         state: 'TX', cases: 210, color: '#00bcd4' },
    { lng: -97.10, lat: 33.15, city: 'Denton',         state: 'TX', cases: 190, color: '#e91e63' },
    { lng: -97.35, lat: 32.50, city: 'Mansfield',      state: 'TX', cases: 175, color: '#ff9800' },
    { lng: -96.65, lat: 33.20, city: 'McKinney',       state: 'TX', cases: 160, color: '#9c27b0' },
    { lng: -96.45, lat: 32.95, city: 'Rockwall',       state: 'TX', cases: 140, color: '#4caf50' },
    { lng: -97.52, lat: 33.05, city: 'Keller',         state: 'TX', cases: 120, color: '#ffc107' },
    { lng: -97.00, lat: 32.35, city: 'Cleburne',       state: 'TX', cases: 115, color: '#607d8b' },
    /* Central TX */
    { lng: -97.15, lat: 31.55, city: 'Waco',           state: 'TX', cases: 108, color: '#f44336' },
    { lng: -97.73, lat: 31.10, city: 'Temple',         state: 'TX', cases:  95, color: '#00acc1' },
    { lng: -97.74, lat: 30.27, city: 'Austin',         state: 'TX', cases: 190, color: '#673ab7' },
    { lng: -97.85, lat: 30.50, city: 'Round Rock',     state: 'TX', cases: 155, color: '#ec407a' },
    /* Houston Area */
    { lng: -95.37, lat: 29.76, city: 'Houston',        state: 'TX', cases: 245, color: '#ff5722' },
    { lng: -95.55, lat: 29.90, city: 'Cypress',        state: 'TX', cases: 180, color: '#26c6da' },
    { lng: -95.62, lat: 29.62, city: 'Sugar Land',     state: 'TX', cases: 145, color: '#7e57c2' },
    /* San Antonio */
    { lng: -98.49, lat: 29.42, city: 'San Antonio',    state: 'TX', cases: 198, color: '#ab47bc' },
    { lng: -98.62, lat: 29.60, city: 'San Antonio NW', state: 'TX', cases: 165, color: '#29b6f6' },
    { lng: -98.35, lat: 29.30, city: 'San Antonio SE', state: 'TX', cases: 130, color: '#66bb6a' },
    /* South TX */
    { lng: -99.10, lat: 29.10, city: 'Uvalde',         state: 'TX', cases:  88, color: '#78909c' },
    { lng: -97.40, lat: 27.80, city: 'Corpus Christi', state: 'TX', cases:  72, color: '#ffa726' },
    { lng: -98.25, lat: 26.20, city: 'McAllen',        state: 'TX', cases:  64, color: '#ef5350' },
    { lng:-106.50, lat: 31.77, city: 'El Paso',        state: 'TX', cases:  49, color: '#42a5f5' },
    { lng:-101.83, lat: 35.23, city: 'Amarillo',       state: 'TX', cases:  52, color: '#26a69a' },
    /* Oklahoma */
    { lng: -97.52, lat: 35.47, city: 'Oklahoma City',  state: 'OK', cases: 140, color: '#d4e157' },
    { lng: -95.99, lat: 36.15, city: 'Tulsa',          state: 'OK', cases: 112, color: '#455a64' },
    /* Arkansas */
    { lng: -94.40, lat: 35.38, city: 'Fort Smith',     state: 'AR', cases:  88, color: '#ce93d8' },
    { lng: -94.21, lat: 36.37, city: 'Bentonville',    state: 'AR', cases:  74, color: '#80cbc4' },
    { lng: -92.33, lat: 34.74, city: 'Little Rock',    state: 'AR', cases:  66, color: '#f06292' },
    /* Tennessee */
    { lng: -86.78, lat: 36.17, city: 'Nashville',      state: 'TN', cases: 108, color: '#ffb300' },
    { lng: -90.05, lat: 35.15, city: 'Memphis',        state: 'TN', cases:  58, color: '#8d6e63' },
    /* Southeast */
    { lng: -90.18, lat: 32.30, city: 'Jackson',        state: 'MS', cases:  44, color: '#90a4ae' },
    { lng: -84.39, lat: 33.75, city: 'Atlanta',        state: 'GA', cases:  65, color: '#4db6ac' },
    /* Arizona */
    { lng:-112.07, lat: 33.45, city: 'Phoenix',        state: 'AZ', cases: 145, color: '#e040fb' },
    { lng:-111.94, lat: 33.42, city: 'Mesa',           state: 'AZ', cases:  98, color: '#ff8a65' },
    { lng:-110.97, lat: 32.22, city: 'Tucson',         state: 'AZ', cases:  58, color: '#81c784' },
    /* West */
    { lng:-115.14, lat: 36.18, city: 'Las Vegas',      state: 'NV', cases:  42, color: '#e53935' },
    { lng:-118.24, lat: 34.05, city: 'Los Angeles',    state: 'CA', cases:  97, color: '#5c6bc0' },
    { lng:-117.15, lat: 32.73, city: 'San Diego',      state: 'CA', cases:  45, color: '#26c6da' },
    { lng:-104.98, lat: 39.74, city: 'Denver',         state: 'CO', cases:  88, color: '#ffca28' },
    /* Midwest */
    { lng: -83.68, lat: 43.05, city: 'Flint',          state: 'MI', cases:  38, color: '#1565c0' },
    { lng: -87.95, lat: 43.05, city: 'Milwaukee',      state: 'WI', cases:  34, color: '#0288d1' },
    { lng: -87.63, lat: 41.88, city: 'Chicago',        state: 'IL', cases:  55, color: '#6a1b9a' },
    { lng: -93.27, lat: 44.98, city: 'Minneapolis',    state: 'MN', cases:  42, color: '#1e88e5' },
  ];

  var MAX_CASES = 280;

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

      /* ── Hide rivers and lakes from the CartoDB dark-matter style ──────────
         Iterate every layer in the loaded style and suppress anything that
         belongs to the waterway or water source-layers (rivers, streams,
         lakes, ponds, reservoirs). Country/state admin layers are untouched. */
      map.getStyle().layers.forEach(function (layer) {
        var sl = layer['source-layer'] || '';
        var id = layer.id;
        if (
          sl === 'waterway' ||
          sl === 'water' ||
          id === 'water' ||
          id === 'water-shadow' ||
          id === 'water-pattern' ||
          id.indexOf('waterway') !== -1 ||
          id.indexOf('water-label') !== -1 ||
          id.indexOf('water_label') !== -1
        ) {
          try { map.setLayoutProperty(id, 'visibility', 'none'); } catch (e) { /* layer may not exist */ }
        }
      });

      /* ── US states GeoJSON ── */
      fetch('https://d2ad6b4ur7yvpq.cloudfront.net/naturalearth-3.3.0/ne_110m_admin_1_states_provinces_shp.geojson')
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var usStates = {
            type: 'FeatureCollection',
            features: data.features.filter(function (f) {
              var p = f.properties;
              return (
                p.admin   === 'United States of America' ||
                p.iso_a2  === 'US' ||
                p.adm0_a3 === 'USA'
              );
            }),
          };

          map.addSource('us-states', { type: 'geojson', data: usStates });

          /* State border lines — light teal, on top of CartoDB land */
          map.addLayer({
            id: 'us-borders',
            type: 'line',
            source: 'us-states',
            paint: {
              'line-color': 'rgba(0, 210, 190, 0.6)',
              'line-width': 1,
            },
          });

          /* Faded state name labels — uses CartoDB dark-matter's built-in fonts */
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
              'text-color':      'rgba(255, 255, 255, 0.22)',
              'text-halo-color': 'rgba(0, 0, 0, 0.4)',
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

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
