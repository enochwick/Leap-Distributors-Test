/**
 * Newsletter PDF viewer (lightweight, crisp, two-page spread).
 *
 * Shows two pages side by side (a magazine spread) and pages through two at a
 * time. On narrow screens it falls back to one page at a time. Each page is
 * rendered with pdf.js at the exact zoom level, so text stays sharp at any
 * magnification. Paging uses corner arrows (bottom-left / bottom-right) with a
 * quick crossfade — no flip animation. Zoom via pinch (touch), ctrl/cmd+wheel
 * (desktop), or the +/- buttons. Loaded only on newsletter posts.
 */
(function () {
  var el = document.querySelector('.pdf-flip');
  if (!el || typeof pdfjsLib === 'undefined') return;

  var url        = el.getAttribute('data-pdf');
  var viewport   = el.closest('.pdf-flip-viewport');
  var wrap       = el.closest('.pdf-flip-wrap');
  var prevBtn    = wrap ? wrap.querySelector('[data-flip-prev]') : null;
  var nextBtn    = wrap ? wrap.querySelector('[data-flip-next]') : null;
  var zoomBox    = wrap ? wrap.querySelector('.pdf-flip__zoom') : null;
  var zoomInBtn  = wrap ? wrap.querySelector('[data-flip-zoom-in]') : null;
  var zoomOutBtn = wrap ? wrap.querySelector('[data-flip-zoom-out]') : null;

  if (!url) return;

  if (window.leapPdf && leapPdf.worker) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = leapPdf.worker;
  }

  function fallback() {
    el.innerHTML =
      '<div class="pdf-flip__loading">The viewer couldn’t load. ' +
      '<a href="' + url + '" target="_blank" rel="noopener">Open the PDF</a> instead.</div>';
  }

  var MIN_ZOOM    = 1;
  var MAX_ZOOM    = 3;
  var ZOOM_STEP   = 0.25; // gradual steps for the +/- buttons and wheel
  var SPREAD_GAP  = 12;   // must match .pdf-spread gap in CSS

  var pdfDoc  = null;
  var total   = 0;
  var pageNum = 1;        // left page of the current spread
  var zoom    = 1;
  var perView = 1;
  var tasks   = [];       // active pdf.js render tasks (so we can cancel)
  var renderToken = 0;    // guards against stale async renders

  function computePerView() {
    return window.matchMedia('(min-width: 820px)').matches ? 2 : 1;
  }

  function cancelTasks() {
    for (var i = 0; i < tasks.length; i++) {
      try { tasks[i].cancel(); } catch (e) {}
    }
    tasks = [];
  }

  // Pages shown in the current view (1 or 2), in reading order.
  function currentPages() {
    if (perView === 1) return [pageNum];
    var arr = [pageNum];
    if (pageNum + 1 <= total) arr.push(pageNum + 1);
    return arr;
  }

  // CSS width of a single leaf at the current zoom. Fills the full container
  // width: at 1x the spread spans edge to edge; zoom scales up from there.
  function leafWidth() {
    var avail = (viewport ? viewport.clientWidth : el.clientWidth) || 600;
    var perLeaf = perView === 2 ? (avail - SPREAD_GAP) / 2 : avail;
    return perLeaf * zoom;
  }

  function render(resetScroll, dir) {
    if (!pdfDoc) return;
    var token = ++renderToken;
    cancelTasks();

    var pages   = currentPages();
    var cssW    = leafWidth();
    var dpr     = Math.min(window.devicePixelRatio || 1, 2);
    // First view? Paint into place immediately so pages appear as they finish,
    // instead of waiting for the whole spread. Page turns keep the swap-then-
    // fade so the old spread stays until the new one is ready.
    var firstPaint = el.querySelector('.pdf-flip__loading') !== null;

    var row = document.createElement('div');
    row.className = 'pdf-spread';

    // Pre-create canvases in reading order so pages never render out of order.
    var canvases = pages.map(function () {
      var c = document.createElement('canvas');
      row.appendChild(c);
      return c;
    });

    if (firstPaint) {
      el.innerHTML = '';
      el.appendChild(row);
      row.style.opacity = '1';
    } else {
      row.style.opacity = '0';
      if (dir) row.style.transform = 'translateX(' + (dir * 26) + 'px)';
    }

    var jobs = pages.map(function (num, i) {
      return pdfDoc.getPage(num).then(function (page) {
        if (token !== renderToken) return;
        var unscaled = page.getViewport({ scale: 1 });
        var vp = page.getViewport({ scale: (cssW / unscaled.width) * dpr });
        var c  = canvases[i];
        c.width  = Math.floor(vp.width);
        c.height = Math.floor(vp.height);
        c.style.width  = Math.floor(vp.width / dpr) + 'px';
        c.style.height = Math.floor(vp.height / dpr) + 'px';
        var task = page.render({ canvasContext: c.getContext('2d'), viewport: vp });
        tasks.push(task);
        return task.promise;
      });
    });

    Promise.all(jobs).then(function () {
      if (token !== renderToken) return;
      if (!firstPaint) {
        el.innerHTML = '';
        el.appendChild(row);
        // Settle in (next frame so the transition takes effect).
        requestAnimationFrame(function () {
          row.style.opacity = '1';
          row.style.transform = 'translateX(0)';
        });
      }
      if (resetScroll && viewport) { viewport.scrollTop = 0; viewport.scrollLeft = 0; }
      updateUI();
    }).catch(function (err) {
      if (err && err.name === 'RenderingCancelledException') return;
      if (window.console && console.warn) console.warn('pdf-flip:', err);
    });
  }

  function rightPage() {
    return perView === 2 ? Math.min(pageNum + 1, total) : pageNum;
  }

  function updateUI() {
    if (prevBtn)    prevBtn.disabled    = pageNum <= 1;
    if (nextBtn)    nextBtn.disabled    = rightPage() >= total;
    if (zoomOutBtn) zoomOutBtn.disabled = zoom <= MIN_ZOOM;
    if (zoomInBtn)  zoomInBtn.disabled  = zoom >= MAX_ZOOM;
  }

  function step(dir) {
    var next = pageNum + dir * perView;
    next = Math.max(1, Math.min(total, next));
    if (perView === 2 && next % 2 === 0) next--; // keep the left page odd
    if (next === pageNum) return;
    var dir = next > pageNum ? 1 : -1;
    pageNum = next;
    render(true, dir);
  }

  function clampZoom(z) {
    return Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, Math.round(z * 100) / 100));
  }

  // Re-render crisply at an exact zoom level (used after a live gesture settles).
  function setZoom(z) {
    z = clampZoom(z);
    if (z === zoom) return;
    zoom = z;
    render(false);
  }

  // Smooth, gradual zoom for the +/- buttons and wheel: animate a CSS scale on
  // the current spread, then re-render sharp once it settles. Repeated clicks
  // accumulate via pendingZoom so they ease continuously instead of jumping.
  var pendingZoom = null;
  var zoomSettle  = null;
  function smoothZoomBy(delta) {
    var base   = pendingZoom != null ? pendingZoom : zoom;
    var target = clampZoom(base + delta);
    if (target === base) return;
    pendingZoom = target;
    if (zoomInBtn)  zoomInBtn.disabled  = target >= MAX_ZOOM;
    if (zoomOutBtn) zoomOutBtn.disabled = target <= MIN_ZOOM;

    var row = el.querySelector('.pdf-spread');
    if (!row) { setZoom(target); pendingZoom = null; return; }
    row.style.transition      = 'transform .25s ease';
    row.style.transformOrigin = 'center top';
    row.style.transform       = 'scale(' + (target / zoom) + ')';

    clearTimeout(zoomSettle);
    zoomSettle = setTimeout(function () {
      var t = pendingZoom;
      pendingZoom = null;
      row.style.transition = '';
      setZoom(t);
    }, 260);
  }

  var loadingTask = pdfjsLib.getDocument(url);
  loadingTask.onProgress = function (p) {
    var ld = el.querySelector('.pdf-flip__loading');
    if (!ld || !p) return;
    if (p.total) {
      ld.textContent = 'Loading newsletter… ' + Math.min(100, Math.round((p.loaded / p.total) * 100)) + '%';
    }
  };
  loadingTask.promise.then(function (pdf) {
    pdfDoc  = pdf;
    total   = pdf.numPages;
    perView = computePerView();
    if (perView === 2 && pageNum % 2 === 0) pageNum--;

    if (prevBtn)  prevBtn.hidden = false;
    if (nextBtn)  nextBtn.hidden = false;
    if (zoomBox)  zoomBox.hidden = false;

    if (prevBtn)    prevBtn.addEventListener('click', function () { step(-1); });
    if (nextBtn)    nextBtn.addEventListener('click', function () { step(1); });
    if (zoomInBtn)  zoomInBtn.addEventListener('click', function () { smoothZoomBy(ZOOM_STEP); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { smoothZoomBy(-ZOOM_STEP); });

    // Arrow keys page through when the viewer has focus.
    el.setAttribute('tabindex', '0');
    el.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { step(1); e.preventDefault(); }
      else if (e.key === 'ArrowLeft') { step(-1); e.preventDefault(); }
    });

    // Desktop zoom: ctrl/cmd + wheel.
    if (viewport) {
      viewport.addEventListener('wheel', function (e) {
        if (!(e.ctrlKey || e.metaKey)) return;
        e.preventDefault();
        smoothZoomBy(e.deltaY < 0 ? ZOOM_STEP : -ZOOM_STEP);
      }, { passive: false });
    }

    // Touch pinch-to-zoom: scale the spread live, then re-render crisply on release.
    if (viewport) {
      var pinching = false, startDist = 0, startZoom = 1, liveZoom = 1, liveRow = null;
      var touchDist = function (t) {
        var dx = t[0].clientX - t[1].clientX, dy = t[0].clientY - t[1].clientY;
        return Math.hypot(dx, dy);
      };
      viewport.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 2) return;
        pinching  = true;
        startDist = touchDist(e.touches);
        startZoom = zoom;
        liveZoom  = zoom;
        liveRow   = el.querySelector('.pdf-spread');
        if (liveRow) {
          liveRow.style.transition      = 'none'; // follow the fingers 1:1
          liveRow.style.transformOrigin = 'center top';
        }
      }, { passive: true });
      viewport.addEventListener('touchmove', function (e) {
        if (!pinching || e.touches.length !== 2) return;
        e.preventDefault();
        var ratio = touchDist(e.touches) / (startDist || 1);
        liveZoom = clampZoom(startZoom * ratio);
        if (liveRow) liveRow.style.transform = 'scale(' + (liveZoom / startZoom) + ')';
      }, { passive: false });
      var endPinch = function () {
        if (!pinching) return;
        pinching = false;
        if (liveRow) { liveRow.style.transition = ''; liveRow.style.transform = ''; }
        setZoom(liveZoom);
      };
      viewport.addEventListener('touchend', endPinch);
      viewport.addEventListener('touchcancel', endPinch);
    }

    // On resize: switch between spread and single page, re-fit, and re-render.
    var rt;
    window.addEventListener('resize', function () {
      clearTimeout(rt);
      rt = setTimeout(function () {
        var pv = computePerView();
        if (pv !== perView) {
          perView = pv;
          if (perView === 2 && pageNum % 2 === 0) pageNum--;
        }
        render(false);
      }, 200);
    });

    render(true);
  }).catch(function (err) {
    fallback();
    if (window.console && console.warn) console.warn('pdf-flip:', err);
  });
})();
