/**
 * Newsletter PDF viewer (lightweight, crisp, two-page spread).
 *
 * Shows two pages side by side (a magazine spread) and pages through two at a
 * time. On narrow screens it falls back to one page at a time. Each page is
 * rendered with pdf.js at the exact zoom level, so text stays sharp at any
 * magnification. Page changes use a quick crossfade — no flip animation.
 * Loaded only on newsletter posts.
 */
(function () {
  var el = document.querySelector('.pdf-flip');
  if (!el || typeof pdfjsLib === 'undefined') return;

  var url       = el.getAttribute('data-pdf');
  var viewport  = el.closest('.pdf-flip-viewport');
  var wrap      = el.closest('.pdf-flip-wrap');
  var controls  = wrap ? wrap.querySelector('.pdf-flip__controls') : null;
  var pageLabel = controls ? controls.querySelector('[data-flip-page]') : null;
  var prevBtn   = controls ? controls.querySelector('[data-flip-prev]') : null;
  var nextBtn   = controls ? controls.querySelector('[data-flip-next]') : null;
  var zoomInBtn = controls ? controls.querySelector('[data-flip-zoom-in]') : null;
  var zoomOutBtn= controls ? controls.querySelector('[data-flip-zoom-out]') : null;

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
  var ZOOM_STEP   = 0.5;
  var SPREAD_GAP  = 12;   // must match .pdf-spread gap in CSS
  var MAX_LEAF_W  = 520;  // cap per-page width in a two-up spread
  var MAX_SINGLE_W= 900;  // cap page width when showing one at a time

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

  // CSS width of a single leaf at the current zoom.
  function leafWidth() {
    var avail = (viewport ? viewport.clientWidth : el.clientWidth) || 600;
    var perLeaf;
    if (perView === 2) {
      perLeaf = Math.min((avail - SPREAD_GAP) / 2, MAX_LEAF_W);
    } else {
      perLeaf = Math.min(avail, MAX_SINGLE_W);
    }
    return perLeaf * zoom;
  }

  function render(resetScroll) {
    if (!pdfDoc) return;
    var token = ++renderToken;
    cancelTasks();

    var pages   = currentPages();
    var cssW    = leafWidth();
    var dpr     = Math.min(window.devicePixelRatio || 1, 2);
    var row     = document.createElement('div');
    row.className = 'pdf-spread';
    row.style.opacity = '0';

    // Pre-create canvases in reading order so pages never render out of order.
    var canvases = pages.map(function () {
      var c = document.createElement('canvas');
      row.appendChild(c);
      return c;
    });

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
      el.innerHTML = '';
      el.appendChild(row);
      // Quick fade-in (next frame so the transition takes effect).
      requestAnimationFrame(function () { row.style.opacity = '1'; });
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
    if (pageLabel) {
      var r = rightPage();
      pageLabel.textContent = (r > pageNum ? pageNum + '–' + r : String(pageNum)) + ' / ' + total;
    }
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
    pageNum = next;
    render(true);
  }
  function setZoom(z) {
    z = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, Math.round(z * 10) / 10));
    if (z === zoom) return;
    zoom = z;
    render(false);
  }

  pdfjsLib.getDocument(url).promise.then(function (pdf) {
    pdfDoc  = pdf;
    total   = pdf.numPages;
    perView = computePerView();
    if (perView === 2 && pageNum % 2 === 0) pageNum--;

    if (controls) controls.hidden = false;
    if (prevBtn)    prevBtn.addEventListener('click', function () { step(-1); });
    if (nextBtn)    nextBtn.addEventListener('click', function () { step(1); });
    if (zoomInBtn)  zoomInBtn.addEventListener('click', function () { setZoom(zoom + ZOOM_STEP); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { setZoom(zoom - ZOOM_STEP); });

    // Arrow keys page through when the viewer has focus.
    el.setAttribute('tabindex', '0');
    el.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { step(1); e.preventDefault(); }
      else if (e.key === 'ArrowLeft') { step(-1); e.preventDefault(); }
    });

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
