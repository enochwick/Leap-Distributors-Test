/* Hospital Coverage Globe — canvas, vanilla JS */
(function () {
  'use strict';

  var HOSPITALS = [
    { lat: 32.90, lng: -97.05, city: 'Fort Worth',    state: 'TX', cases: 280 },
    { lat: 32.78, lng: -96.80, city: 'Dallas',         state: 'TX', cases: 210 },
    { lat: 33.15, lng: -97.10, city: 'Denton',         state: 'TX', cases: 190 },
    { lat: 32.50, lng: -97.35, city: 'Mansfield',      state: 'TX', cases: 175 },
    { lat: 33.20, lng: -96.65, city: 'McKinney',       state: 'TX', cases: 160 },
    { lat: 32.95, lng: -96.45, city: 'Rockwall',       state: 'TX', cases: 140 },
    { lat: 33.05, lng: -97.52, city: 'Keller',         state: 'TX', cases: 120 },
    { lat: 32.35, lng: -97.00, city: 'Cleburne',       state: 'TX', cases: 115 },
    { lat: 31.55, lng: -97.15, city: 'Waco',           state: 'TX', cases: 108 },
    { lat: 31.10, lng: -97.73, city: 'Temple',         state: 'TX', cases: 95  },
    { lat: 30.27, lng: -97.74, city: 'Austin',         state: 'TX', cases: 190 },
    { lat: 30.50, lng: -97.85, city: 'Round Rock',     state: 'TX', cases: 155 },
    { lat: 29.76, lng: -95.37, city: 'Houston',        state: 'TX', cases: 245 },
    { lat: 29.90, lng: -95.55, city: 'Cypress',        state: 'TX', cases: 180 },
    { lat: 29.62, lng: -95.62, city: 'Sugar Land',     state: 'TX', cases: 145 },
    { lat: 29.42, lng: -98.49, city: 'San Antonio',    state: 'TX', cases: 198 },
    { lat: 29.60, lng: -98.62, city: 'San Antonio NW', state: 'TX', cases: 165 },
    { lat: 29.30, lng: -98.35, city: 'San Antonio SE', state: 'TX', cases: 130 },
    { lat: 29.10, lng: -99.10, city: 'Uvalde',         state: 'TX', cases: 88  },
    { lat: 27.80, lng: -97.40, city: 'Corpus Christi', state: 'TX', cases: 72  },
    { lat: 26.20, lng: -98.25, city: 'McAllen',        state: 'TX', cases: 64  },
    { lat: 31.77, lng: -106.50,city: 'El Paso',        state: 'TX', cases: 49  },
    { lat: 35.47, lng: -97.52, city: 'Oklahoma City',  state: 'OK', cases: 140 },
    { lat: 36.15, lng: -95.99, city: 'Tulsa',          state: 'OK', cases: 112 },
    { lat: 35.38, lng: -94.40, city: 'Fort Smith',     state: 'AR', cases: 88  },
    { lat: 36.37, lng: -94.21, city: 'Bentonville',    state: 'AR', cases: 74  },
    { lat: 34.74, lng: -92.33, city: 'Little Rock',    state: 'AR', cases: 66  },
    { lat: 36.17, lng: -86.78, city: 'Nashville',      state: 'TN', cases: 108 },
    { lat: 35.15, lng: -90.05, city: 'Memphis',        state: 'TN', cases: 58  },
    { lat: 32.30, lng: -90.18, city: 'Jackson',        state: 'MS', cases: 44  },
    { lat: 33.75, lng: -84.39, city: 'Atlanta',        state: 'GA', cases: 65  },
    { lat: 35.23, lng: -101.83,city: 'Amarillo',       state: 'TX', cases: 52  },
    { lat: 33.45, lng: -112.07,city: 'Phoenix',        state: 'AZ', cases: 145 },
    { lat: 33.42, lng: -111.94,city: 'Mesa',           state: 'AZ', cases: 98  },
    { lat: 32.22, lng: -110.97,city: 'Tucson',         state: 'AZ', cases: 58  },
    { lat: 36.18, lng: -115.14,city: 'Las Vegas',      state: 'NV', cases: 42  },
    { lat: 34.05, lng: -118.24,city: 'Los Angeles',    state: 'CA', cases: 97  },
    { lat: 32.73, lng: -117.15,city: 'San Diego',      state: 'CA', cases: 45  },
    { lat: 39.74, lng: -104.98,city: 'Denver',         state: 'CO', cases: 88  },
    { lat: 43.05, lng: -83.68, city: 'Flint',          state: 'MI', cases: 38  },
    { lat: 43.05, lng: -87.95, city: 'Milwaukee',      state: 'WI', cases: 34  },
    { lat: 41.88, lng: -87.63, city: 'Chicago',        state: 'IL', cases: 55  },
    { lat: 44.98, lng: -93.27, city: 'Minneapolis',    state: 'MN', cases: 42  },
  ];

  var ARCS = [
    { from: [32.90,-97.05], to: [29.76,-95.37] },
    { from: [32.90,-97.05], to: [29.42,-98.49] },
    { from: [32.90,-97.05], to: [35.47,-97.52] },
    { from: [32.90,-97.05], to: [30.27,-97.74] },
    { from: [29.76,-95.37], to: [29.42,-98.49] },
    { from: [35.47,-97.52], to: [36.15,-95.99] },
    { from: [36.37,-94.21], to: [34.74,-92.33] },
    { from: [36.37,-94.21], to: [35.47,-97.52] },
    { from: [33.45,-112.07],to: [34.05,-118.24]},
    { from: [32.90,-97.05], to: [33.45,-112.07]},
    { from: [36.17,-86.78], to: [33.75,-84.39] },
    { from: [36.17,-86.78], to: [35.47,-97.52] },
  ];

  /* ── Math helpers ── */
  function ll2xyz(lat, lng, r) {
    var phi = ((90 - lat) * Math.PI) / 180;
    var th  = ((lng + 180) * Math.PI) / 180;
    return [-(r * Math.sin(phi) * Math.cos(th)), r * Math.cos(phi), r * Math.sin(phi) * Math.sin(th)];
  }
  function rotY(x,y,z,a){ var c=Math.cos(a),s=Math.sin(a); return [x*c+z*s,y,-x*s+z*c]; }
  function rotX(x,y,z,a){ var c=Math.cos(a),s=Math.sin(a); return [x,y*c-z*s,y*s+z*c]; }
  function proj(x,y,z,cx,cy,fov){ var sc=fov/(fov+z); return [x*sc+cx, y*sc+cy]; }

  /* Fibonacci sphere */
  function buildDots(n) {
    var gr=( 1+Math.sqrt(5))/2, pts=[];
    for(var i=0;i<n;i++){
      var th=(2*Math.PI*i)/gr, ph=Math.acos(1-(2*(i+0.5))/n);
      pts.push([Math.cos(th)*Math.sin(ph), Math.cos(ph), Math.sin(th)*Math.sin(ph)]);
    }
    return pts;
  }

  /* Angle lerp (handles wrap-around) */
  function angleDiff(a,b){ return ((b-a)%(Math.PI*2)+Math.PI*3)%(Math.PI*2)-Math.PI; }

  function init() {
    var canvas = document.getElementById('hcm-globe');
    if (!canvas) return;
    var ctx  = canvas.getContext('2d');
    var dots = buildDots(1400);

    /* Default: face continental US */
    var DEF_RY = ((-97 + 180) * Math.PI) / 180 + Math.PI;
    var DEF_RX = 0.28;
    var curRY  = DEF_RY, curRX = DEF_RX;
    var drag   = null, time = 0, raf = 0;
    var resetTimer = null, returning = false;

    /* Tooltip */
    var tip = document.createElement('div');
    tip.className = 'hcm-globe-tip';
    tip.style.display = 'none';
    canvas.parentNode.appendChild(tip);

    function scheduleReturn() {
      clearTimeout(resetTimer);
      resetTimer = setTimeout(function(){ returning = true; }, 2500);
    }

    canvas.addEventListener('pointerdown', function(e){
      returning = false; clearTimeout(resetTimer);
      drag = { sx:e.clientX, sy:e.clientY, ry:curRY, rx:curRX };
      canvas.setPointerCapture(e.pointerId);
      canvas.style.cursor = 'grabbing';
      tip.style.display = 'none';
    });
    canvas.addEventListener('pointermove', function(e){
      if (!drag) return;
      curRY = drag.ry + (e.clientX - drag.sx) * 0.005;
      curRX = Math.max(-1.1, Math.min(1.1, drag.rx + (e.clientY - drag.sy) * 0.005));
    });
    canvas.addEventListener('pointerup', function(){
      drag = null; canvas.style.cursor = 'grab'; scheduleReturn();
    });
    canvas.addEventListener('pointerleave', function(){
      drag = null; canvas.style.cursor = 'grab';
      tip.style.display = 'none';
      scheduleReturn();
    });

    /* Hover — nearest visible marker */
    canvas.addEventListener('mousemove', function(e){
      if (drag) return;
      var rect = canvas.getBoundingClientRect();
      var mx = e.clientX - rect.left, my = e.clientY - rect.top;
      var w = canvas.clientWidth, h = canvas.clientHeight;
      var radius = Math.min(w,h) * 0.44, fov = 680;
      var best = null, bestD = 16;
      HOSPITALS.forEach(function(m){
        var p = ll2xyz(m.lat, m.lng, radius);
        var r1 = rotX(p[0],p[1],p[2],curRX);
        var r2 = rotY(r1[0],r1[1],r1[2],curRY);
        if (r2[2] > radius * 0.1) return;
        var sp = proj(r2[0],r2[1],r2[2],w/2,h/2,fov);
        var d = Math.sqrt((sp[0]-mx)**2+(sp[1]-my)**2);
        if (d < bestD){ bestD=d; best={m:m, sx:sp[0], sy:sp[1]}; }
      });
      if (best) {
        tip.innerHTML = '<strong>'+best.m.city+', '+best.m.state+'</strong><span>'+best.m.cases+' cases</span>';
        tip.style.display = 'block';
        tip.style.left = (best.sx + 14) + 'px';
        tip.style.top  = (best.sy - 38) + 'px';
      } else {
        tip.style.display = 'none';
      }
    });

    function draw() {
      /* Smooth snap back */
      if (returning && !drag) {
        var dRY = angleDiff(curRY, DEF_RY), dRX = DEF_RX - curRX;
        if (Math.abs(dRY) < 0.002 && Math.abs(dRX) < 0.002) {
          curRY = DEF_RY; curRX = DEF_RX; returning = false;
        } else { curRY += dRY * 0.07; curRX += dRX * 0.07; }
      }
      time += 0.015;

      var dpr = window.devicePixelRatio || 1;
      var w = canvas.clientWidth, h = canvas.clientHeight;
      if (!w || !h) { raf = requestAnimationFrame(draw); return; }
      canvas.width  = w * dpr;
      canvas.height = h * dpr;
      ctx.scale(dpr, dpr);

      var cx = w/2, cy = h/2;
      var radius = Math.min(w,h) * 0.44;
      var fov = 680;

      ctx.clearRect(0,0,w,h);

      /* Ambient glow */
      var gl = ctx.createRadialGradient(cx,cy,radius*0.3,cx,cy,radius*1.8);
      gl.addColorStop(0,'rgba(0,210,175,0.08)');
      gl.addColorStop(1,'rgba(0,210,175,0)');
      ctx.fillStyle = gl; ctx.fillRect(0,0,w,h);

      /* Globe outline */
      ctx.beginPath(); ctx.arc(cx,cy,radius,0,Math.PI*2);
      ctx.strokeStyle='rgba(0,210,175,0.07)'; ctx.lineWidth=1; ctx.stroke();

      /* Lat/lng grid lines for techy feel */
      var gridAlpha = 0.06;
      for (var lat=-60; lat<=60; lat+=30) {
        ctx.beginPath();
        var first = true;
        for (var lng2=-180; lng2<=180; lng2+=4) {
          var p = ll2xyz(lat, lng2, radius);
          var r1 = rotX(p[0],p[1],p[2],curRX);
          var r2 = rotY(r1[0],r1[1],r1[2],curRY);
          if (r2[2] > 0) { first=true; continue; }
          var sp = proj(r2[0],r2[1],r2[2],cx,cy,fov);
          if (first) { ctx.moveTo(sp[0],sp[1]); first=false; } else { ctx.lineTo(sp[0],sp[1]); }
        }
        ctx.strokeStyle='rgba(0,210,175,'+gridAlpha+')'; ctx.lineWidth=0.5; ctx.stroke();
      }
      for (var lng3=-150; lng3<=180; lng3+=30) {
        ctx.beginPath(); first=true;
        for (var lat2=-80; lat2<=80; lat2+=3) {
          var p2 = ll2xyz(lat2, lng3, radius);
          var r1b = rotX(p2[0],p2[1],p2[2],curRX);
          var r2b = rotY(r1b[0],r1b[1],r1b[2],curRY);
          if (r2b[2] > 0) { first=true; continue; }
          var sp2 = proj(r2b[0],r2b[1],r2b[2],cx,cy,fov);
          if (first) { ctx.moveTo(sp2[0],sp2[1]); first=false; } else { ctx.lineTo(sp2[0],sp2[1]); }
        }
        ctx.strokeStyle='rgba(0,210,175,'+gridAlpha+')'; ctx.lineWidth=0.5; ctx.stroke();
      }

      /* Globe dots */
      for (var i=0;i<dots.length;i++){
        var d=dots[i], pp=[d[0]*radius,d[1]*radius,d[2]*radius];
        var pr1=rotX(pp[0],pp[1],pp[2],curRX), pr2=rotY(pr1[0],pr1[1],pr1[2],curRY);
        if(pr2[2]>0) continue;
        var spp=proj(pr2[0],pr2[1],pr2[2],cx,cy,fov);
        var alpha=Math.max(0.06,1-(pr2[2]+radius)/(2*radius));
        ctx.beginPath(); ctx.arc(spp[0],spp[1],0.8+alpha*0.6,0,Math.PI*2);
        ctx.fillStyle='rgba(100,200,255,'+alpha.toFixed(2)+')'; ctx.fill();
      }

      /* Arcs */
      for (var a=0;a<ARCS.length;a++){
        var arc=ARCS[a];
        var a1=ll2xyz(arc.from[0],arc.from[1],radius), a2=ll2xyz(arc.to[0],arc.to[1],radius);
        var ar1=rotX(a1[0],a1[1],a1[2],curRX); ar1=rotY(ar1[0],ar1[1],ar1[2],curRY);
        var ar2=rotX(a2[0],a2[1],a2[2],curRX); ar2=rotY(ar2[0],ar2[1],ar2[2],curRY);
        if(ar1[2]>radius*0.3&&ar2[2]>radius*0.3) continue;
        var sp1=proj(ar1[0],ar1[1],ar1[2],cx,cy,fov), sp2=proj(ar2[0],ar2[1],ar2[2],cx,cy,fov);
        var mx2=(ar1[0]+ar2[0])/2,my2=(ar1[1]+ar2[1])/2,mz2=(ar1[2]+ar2[2])/2;
        var mlen=Math.sqrt(mx2*mx2+my2*my2+mz2*mz2)||1, elev=radius*1.28;
        var sc=proj(mx2/mlen*elev,my2/mlen*elev,mz2/mlen*elev,cx,cy,fov);
        ctx.beginPath(); ctx.moveTo(sp1[0],sp1[1]); ctx.quadraticCurveTo(sc[0],sc[1],sp2[0],sp2[1]);
        ctx.strokeStyle='rgba(0,210,175,0.35)'; ctx.lineWidth=1; ctx.stroke();
        var t=(Math.sin(time*1.1+arc.from[0]*0.1)+1)/2;
        var tx=(1-t)*(1-t)*sp1[0]+2*(1-t)*t*sc[0]+t*t*sp2[0];
        var ty=(1-t)*(1-t)*sp1[1]+2*(1-t)*t*sc[1]+t*t*sp2[1];
        ctx.beginPath(); ctx.arc(tx,ty,2,0,Math.PI*2);
        ctx.fillStyle='#00d2af'; ctx.fill();
      }

      /* Markers — solid filled circles like flat map dots */
      var maxCases=280;
      for (var m=0;m<HOSPITALS.length;m++){
        var mk=HOSPITALS[m];
        var mp=ll2xyz(mk.lat,mk.lng,radius);
        var mr1=rotX(mp[0],mp[1],mp[2],curRX), mr2=rotY(mr1[0],mr1[1],mr1[2],curRY);
        if(mr2[2]>radius*0.1) continue;
        var msp=proj(mr2[0],mr2[1],mr2[2],cx,cy,fov);
        var mSize=4+(mk.cases/maxCases)*10;
        var pulse=Math.sin(time*2+mk.lat)*0.5+0.5;

        /* Glow */
        var grd=ctx.createRadialGradient(msp[0],msp[1],0,msp[0],msp[1],mSize*2.5);
        grd.addColorStop(0,'rgba(0,210,175,0.25)');
        grd.addColorStop(1,'rgba(0,210,175,0)');
        ctx.beginPath(); ctx.arc(msp[0],msp[1],mSize*2.5,0,Math.PI*2);
        ctx.fillStyle=grd; ctx.fill();

        /* Pulse ring */
        ctx.beginPath(); ctx.arc(msp[0],msp[1],mSize+pulse*5,0,Math.PI*2);
        ctx.strokeStyle='rgba(0,230,195,'+(0.1+pulse*0.12)+')'; ctx.lineWidth=1; ctx.stroke();

        /* Solid dot */
        ctx.beginPath(); ctx.arc(msp[0],msp[1],mSize,0,Math.PI*2);
        ctx.fillStyle='#00d2af';
        ctx.shadowBlur=mSize*2; ctx.shadowColor='rgba(0,210,175,0.6)';
        ctx.fill();
        ctx.shadowBlur=0;

        /* White border like flat map dots */
        ctx.beginPath(); ctx.arc(msp[0],msp[1],mSize,0,Math.PI*2);
        ctx.strokeStyle='rgba(255,255,255,0.28)'; ctx.lineWidth=1.5; ctx.stroke();
      }

      raf = requestAnimationFrame(draw);
    }

    draw();

    var obs = new IntersectionObserver(function(entries){
      entries.forEach(function(e){
        if(e.isIntersecting){ raf=requestAnimationFrame(draw); }
        else { cancelAnimationFrame(raf); }
      });
    }, { threshold:0.1 });
    obs.observe(canvas);
  }

  if (document.readyState==='loading') { document.addEventListener('DOMContentLoaded', init); }
  else { init(); }
})();
