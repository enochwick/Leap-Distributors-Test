/**
 * Newsletter PDF viewer (lightweight, crisp).
 *
 * Renders one page at a time with pdf.js, re-rendering at the exact zoom level
 * so text stays sharp at any magnification (no blurry upscaling). Navigation is
 * simple next/previous paging; zoom in / out scales the page and the viewport
 * scrolls. Loaded only on newsletter posts.
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

  var MIN_ZOOM  = 1;
  var MAX_ZOOM  = 3;
  var ZOOM_STEP = 0.5;
  var MAX_FIT_W = 900; // cap the fit-to-width size so pages aren't huge on wide screens

  var pdfDoc  = null;
  var total   = 0;
  var pageNum = 1;
  var zoom    = 1;
  var canvas  = null;
  var renderTask  = null;   // active pdf.js render (so we can cancel)
  var renderToken = 0;      // guards against stale async renders

  function baseWidth() {
    var avail = (viewport ? viewport.clientWidth : el.clientWidth) || 600;
    return Math.min(avail, MAX_FIT_W);
  }

  function render(resetScroll) {
    if (!pdfDoc) return;
    var token = ++renderToken;

    pdfDoc.getPage(pageNum).then(function (page) {
      if (token !== renderToken) return;

      var unscaled = page.getViewport({ scale: 1 });
      var cssW     = baseWidth() * zoom;
      var cssScale = cssW / unscaled.width;
      var dpr      = Math.min(window.devicePixelRatio || 1, 2);
      var vp       = page.getViewport({ scale: cssScale * dpr });

      if (!canvas) {
        canvas = document.createElement('canvas');
        el.innerHTML = '';
        el.appendChild(canvas);
      }

      // Device pixels for crispness; CSS size drives the on-screen dimensions.
      canvas.width  = Math.floor(vp.width);
      canvas.height = Math.floor(vp.height);
      canvas.style.width  = Math.floor(vp.width / dpr) + 'px';
      canvas.style.height = Math.floor(vp.height / dpr) + 'px';
      canvas.style.opacity = '0';

      if (renderTask) { try { renderTask.cancel(); } catch (e) {} }
      var ctx = canvas.getContext('2d');
      renderTask = page.render({ canvasContext: ctx, viewport: vp });
      renderTask.promise.then(function () {
        if (token !== renderToken) return;
        canvas.style.opacity = '1';
        if (resetScroll && viewport) { viewport.scrollTop = 0; viewport.scrollLeft = 0; }
        updateUI();
      }, function () { /* cancelled — ignore */ });
    }).catch(function (err) {
      if (window.console && console.warn) console.warn('pdf-flip:', err);
    });
  }

  function updateUI() {
    if (pageLabel) pageLabel.textContent = pageNum + ' / ' + total;
    if (prevBtn)    prevBtn.disabled    = pageNum <= 1;
    if (nextBtn)    nextBtn.disabled    = pageNum >= total;
    if (zoomOutBtn) zoomOutBtn.disabled = zoom <= MIN_ZOOM;
    if (zoomInBtn)  zoomInBtn.disabled  = zoom >= MAX_ZOOM;
  }

  function goTo(n) {
    n = Math.max(1, Math.min(total, n));
    if (n === pageNum) return;
    pageNum = n;
    render(true);
  }
  function setZoom(z) {
    z = Math.max(MIN_ZOOM, Math.min(MAX_ZOOM, Math.round(z * 10) / 10));
    if (z === zoom) return;
    zoom = z;
    render(false);
  }

  pdfjsLib.getDocument(url).promise.then(function (pdf) {
    pdfDoc = pdf;
    total  = pdf.numPages;

    if (controls) controls.hidden = false;
    if (prevBtn)    prevBtn.addEventListener('click', function () { goTo(pageNum - 1); });
    if (nextBtn)    nextBtn.addEventListener('click', function () { goTo(pageNum + 1); });
    if (zoomInBtn)  zoomInBtn.addEventListener('click', function () { setZoom(zoom + ZOOM_STEP); });
    if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { setZoom(zoom - ZOOM_STEP); });

    // Arrow keys page through when the viewer has focus.
    el.setAttribute('tabindex', '0');
    el.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowRight') { goTo(pageNum + 1); e.preventDefault(); }
      else if (e.key === 'ArrowLeft') { goTo(pageNum - 1); e.preventDefault(); }
    });

    // Re-render to the new fit width on resize (keeps the current page + zoom).
    var rt;
    window.addEventListener('resize', function () {
      clearTimeout(rt);
      rt = setTimeout(function () { render(false); }, 200);
    });

    render(true);
  }).catch(function (err) {
    fallback();
    if (window.console && console.warn) console.warn('pdf-flip:', err);
  });
})();
