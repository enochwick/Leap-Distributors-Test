/* Hospital Coverage Globe — platform page */
(function () {
  'use strict';

  const MARKERS = [
    { lat: 32.89, lng: -97.04,  label: 'DFW',         cases: 312 },
    { lat: 29.76, lng: -95.37,  label: 'Houston',      cases: 284 },
    { lat: 29.42, lng: -98.49,  label: 'San Antonio',  cases: 198 },
    { lat: 30.27, lng: -97.74,  label: 'Austin',       cases: 176 },
    { lat: 33.45, lng: -112.07, label: 'Phoenix',      cases: 143 },
    { lat: 35.47, lng: -97.52,  label: 'OKC',          cases: 121 },
    { lat: 36.17, lng: -86.78,  label: 'Nashville',    cases: 108 },
    { lat: 34.05, lng: -118.24, label: 'Los Angeles',  cases: 97  },
    { lat: 39.74, lng: -104.98, label: 'Denver',       cases: 88  },
    { lat: 36.37, lng: -94.21,  label: 'NW Arkansas',  cases: 74  },
    { lat: 34.74, lng: -92.33,  label: 'Little Rock',  cases: 66  },
    { lat: 35.23, lng: -101.83, label: 'Amarillo',     cases: 52  },
    { lat: 31.77, lng: -106.50, label: 'El Paso',      cases: 49  },
    { lat: 32.73, lng: -117.15, label: 'San Diego',    cases: 45  },
    { lat: 37.34, lng: -121.89, label: 'San Jose',     cases: 41  },
  ];

  const CONNECTIONS = [
    { from: [32.89, -97.04],  to: [29.76, -95.37]  },
    { from: [32.89, -97.04],  to: [29.42, -98.49]  },
    { from: [32.89, -97.04],  to: [35.47, -97.52]  },
    { from: [32.89, -97.04],  to: [36.17, -86.78]  },
    { from: [29.76, -95.37],  to: [29.42, -98.49]  },
    { from: [30.27, -97.74],  to: [32.89, -97.04]  },
    { from: [33.45, -112.07], to: [34.05, -118.24] },
    { from: [33.45, -112.07], to: [39.74, -104.98] },
    { from: [32.89, -97.04],  to: [39.74, -104.98] },
    { from: [36.37, -94.21],  to: [34.74, -92.33]  },
    { from: [36.37, -94.21],  to: [32.89, -97.04]  },
    { from: [34.05, -118.24], to: [37.34, -121.89] },
  ];

  function latLngToXYZ(lat, lng, r) {
    const phi   = ((90 - lat) * Math.PI) / 180;
    const theta = ((lng + 180) * Math.PI) / 180;
    return [
      -(r * Math.sin(phi) * Math.cos(theta)),
       r * Math.cos(phi),
       r * Math.sin(phi) * Math.sin(theta),
    ];
  }

  function rotY(x, y, z, a) {
    const c = Math.cos(a), s = Math.sin(a);
    return [x * c + z * s, y, -x * s + z * c];
  }
  function rotX(x, y, z, a) {
    const c = Math.cos(a), s = Math.sin(a);
    return [x, y * c - z * s, y * s + z * c];
  }
  function project(x, y, z, cx, cy, fov) {
    const sc = fov / (fov + z);
    return [x * sc + cx, y * sc + cy];
  }

  function buildDots(n) {
    const gr  = (1 + Math.sqrt(5)) / 2;
    const pts = [];
    for (let i = 0; i < n; i++) {
      const th = (2 * Math.PI * i) / gr;
      const ph = Math.acos(1 - (2 * (i + 0.5)) / n);
      pts.push([Math.cos(th) * Math.sin(ph), Math.cos(ph), Math.sin(th) * Math.sin(ph)]);
    }
    return pts;
  }

  function init() {
    const canvas = document.getElementById('hospital-globe-canvas');
    if (!canvas) return;

    const ctx  = canvas.getContext('2d');
    const dots = buildDots(1200);

    // Start centered on continental US: lng ≈ -98°
    let rotYAngle = ((-98 + 180) * Math.PI) / 180 + Math.PI;
    let rotXAngle = 0.38;
    let time = 0;
    let raf  = 0;
    let drag = null;

    canvas.addEventListener('pointerdown', e => {
      drag = { sx: e.clientX, sy: e.clientY, ry: rotYAngle, rx: rotXAngle };
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
    });
    canvas.addEventListener('pointermove', e => {
      if (!drag) return;
      rotYAngle = drag.ry + (e.clientX - drag.sx) * 0.005;
      rotXAngle = Math.max(-1, Math.min(1, drag.rx + (e.clientY - drag.sy) * 0.005));
    });
    canvas.addEventListener('pointerup',    () => { drag = null; canvas.style.cursor = 'grab'; });
    canvas.addEventListener('pointerleave', () => { drag = null; canvas.style.cursor = 'grab'; });

    function draw() {
      const dpr = window.devicePixelRatio || 1;
      const w   = canvas.clientWidth;
      const h   = canvas.clientHeight;
      canvas.width  = w * dpr;
      canvas.height = h * dpr;
      ctx.scale(dpr, dpr);

      const cx     = w / 2;
      const cy     = h / 2;
      const radius = Math.min(w, h) * 0.40;
      const fov    = 620;

      if (!drag) rotYAngle += 0.0015;
      time += 0.015;

      ctx.clearRect(0, 0, w, h);

      // Ambient glow behind globe
      const glow = ctx.createRadialGradient(cx, cy, radius * 0.5, cx, cy, radius * 1.6);
      glow.addColorStop(0, 'rgba(0, 210, 175, 0.08)');
      glow.addColorStop(1, 'rgba(0, 210, 175, 0)');
      ctx.fillStyle = glow;
      ctx.fillRect(0, 0, w, h);

      // Globe outline
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.strokeStyle = 'rgba(0, 210, 175, 0.07)';
      ctx.lineWidth = 1;
      ctx.stroke();

      const ry = rotYAngle;
      const rx = rotXAngle;

      // Dots
      for (const [dx, dy, dz] of dots) {
        let [x, y, z] = [dx * radius, dy * radius, dz * radius];
        [x, y, z] = rotX(x, y, z, rx);
        [x, y, z] = rotY(x, y, z, ry);
        if (z > 0) continue;
        const [sx, sy] = project(x, y, z, cx, cy, fov);
        const alpha = Math.max(0.08, 1 - (z + radius) / (2 * radius));
        const ds    = 0.9 + alpha * 0.7;
        ctx.beginPath();
        ctx.arc(sx, sy, ds, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(100, 200, 255, ${alpha.toFixed(2)})`;
        ctx.fill();
      }

      // Connections
      for (const conn of CONNECTIONS) {
        let [x1, y1, z1] = latLngToXYZ(conn.from[0], conn.from[1], radius);
        let [x2, y2, z2] = latLngToXYZ(conn.to[0],   conn.to[1],   radius);
        [x1, y1, z1] = rotX(x1, y1, z1, rx); [x1, y1, z1] = rotY(x1, y1, z1, ry);
        [x2, y2, z2] = rotX(x2, y2, z2, rx); [x2, y2, z2] = rotY(x2, y2, z2, ry);
        if (z1 > radius * 0.3 && z2 > radius * 0.3) continue;

        const [sx1, sy1] = project(x1, y1, z1, cx, cy, fov);
        const [sx2, sy2] = project(x2, y2, z2, cx, cy, fov);

        const mxLen = Math.sqrt(
          ((x1+x2)/2)**2 + ((y1+y2)/2)**2 + ((z1+z2)/2)**2
        );
        const elev  = radius * 1.28;
        const [scx, scy] = project(
          ((x1+x2)/2 / mxLen) * elev,
          ((y1+y2)/2 / mxLen) * elev,
          ((z1+z2)/2 / mxLen) * elev,
          cx, cy, fov
        );

        ctx.beginPath();
        ctx.moveTo(sx1, sy1);
        ctx.quadraticCurveTo(scx, scy, sx2, sy2);
        ctx.strokeStyle = 'rgba(0, 210, 175, 0.45)';
        ctx.lineWidth = 1.1;
        ctx.stroke();

        // Traveling dot
        const t  = (Math.sin(time * 1.1 + conn.from[0] * 0.1) + 1) / 2;
        const tx = (1-t)*(1-t)*sx1 + 2*(1-t)*t*scx + t*t*sx2;
        const ty = (1-t)*(1-t)*sy1 + 2*(1-t)*t*scy + t*t*sy2;
        ctx.beginPath();
        ctx.arc(tx, ty, 2, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0, 230, 195, 1)';
        ctx.fill();
      }

      // Markers
      for (const m of MARKERS) {
        let [x, y, z] = latLngToXYZ(m.lat, m.lng, radius);
        [x, y, z] = rotX(x, y, z, rx);
        [x, y, z] = rotY(x, y, z, ry);
        if (z > radius * 0.1) continue;
        const [sx, sy] = project(x, y, z, cx, cy, fov);

        const pulse = Math.sin(time * 2 + m.lat) * 0.5 + 0.5;
        ctx.beginPath();
        ctx.arc(sx, sy, 4 + pulse * 5, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(0, 230, 195, ${0.15 + pulse * 0.15})`;
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(sx, sy, 2.5, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0, 230, 195, 1)';
        ctx.fill();

        if (m.label) {
          ctx.font = '10px system-ui, sans-serif';
          ctx.fillStyle = 'rgba(0, 220, 185, 0.65)';
          ctx.fillText(m.label, sx + 8, sy + 3);
        }
      }

      raf = requestAnimationFrame(draw);
    }

    draw();

    // Pause when off-screen for perf
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { raf = requestAnimationFrame(draw); }
        else                  { cancelAnimationFrame(raf); }
      });
    }, { threshold: 0.1 });
    observer.observe(canvas);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
