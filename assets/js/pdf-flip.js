/**
 * Newsletter page-flip viewer.
 * Renders each PDF page with pdf.js, then feeds the images into StPageFlip
 * for a realistic magazine-style flip. Loaded only on newsletter posts.
 *
 * Interactions:
 *   - drag / swipe at 1× zoom  → turn pages (StPageFlip)
 *   - zoom in / out buttons    → scale the spread
 *   - drag while zoomed in     → pan around the page
 */
(function () {
  var el = document.querySelector('.pdf-flip');
  if (!el || typeof pdfjsLib === 'undefined' || typeof St === 'undefined' || !St.PageFlip) return;

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

  // Render every page to a lossless PNG data URL (sequentially, to keep memory
  // sane). A leaf is shown up to 1200 CSS px, doubled on retina, and can be
  // zoomed up to 2.5×, so we render with that much headroom — anything less
  // gets upscaled by the browser and the text goes soft. PNG (not JPEG) keeps
  // text edges crisp with no compression fringing.
  var MAX_ZOOM = 2.5;
  function renderPages(pdf) {
    var pages = [];
    var chain = Promise.resolve();
    var dpr     = Math.min(window.devicePixelRatio || 1, 2);
    var targetW = 1200 * dpr * MAX_ZOOM; // one leaf, retina, fully zoomed in
    for (var i = 1; i <= pdf.numPages; i++) {
      (function (num) {
        chain = chain.then(function () {
          return pdf.getPage(num).then(function (page) {
            var natural = page.getViewport({ scale: 1 });
            var scale   = Math.min(6, Math.max(3, targetW / natural.width));
            var vp      = page.getViewport({ scale: scale });
            var canvas  = document.createElement('canvas');
            canvas.width  = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d');
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            return page.render({ canvasContext: ctx, viewport: vp }).promise
              .then(function () {
                pages.push({ src: canvas.toDataURL('image/png'), ratio: vp.height / vp.width });
              });
          });
        });
      })(i);
    }
    return chain.then(function () { return pages; });
  }

  pdfjsLib.getDocument(url).promise
    .then(renderPages)
    .then(function (pages) {
      if (!pages.length) { fallback(); return; }

      var ratio = pages[0].ratio || 1.294;

      // On mobile we show one full page at a time (portrait); on larger screens
      // it's a two-page magazine spread.
      var isMobile = window.matchMedia('(max-width: 768px)').matches;

      // showCover floats the FRONT cover alone on the right (empty left leaf).
      // For the BACK cover to sit alone on the left the total page count must be
      // even — if it's odd, slip a blank leaf in before the back cover. This is
      // only meaningful for the two-page spread, so skip it in single-page mode.
      if (!isMobile && pages.length > 2 && pages.length % 2 === 1) {
        var bw = 1000, bh = Math.round(bw * ratio);
        var bc = document.createElement('canvas');
        bc.width = bw; bc.height = bh;
        var bctx = bc.getContext('2d');
        bctx.fillStyle = '#ffffff';
        bctx.fillRect(0, 0, bw, bh);
        pages.splice(pages.length - 1, 0, { src: bc.toDataURL('image/jpeg', 0.82), ratio: ratio });
      }
      var baseW = 600;
      var baseH = Math.round(baseW * ratio);

      el.innerHTML = '';
      var flip = new St.PageFlip(el, {
        width: baseW,
        height: baseH,
        size: 'stretch',
        minWidth: 350,
        maxWidth: 1200,
        minHeight: Math.round(350 * ratio),
        maxHeight: Math.round(1200 * ratio),
        maxShadowOpacity: 0.5,
        showCover: true,
        mobileScrollSupport: false,
        // Force one full page per view on phones; two-page spread on desktop.
        usePortrait: isMobile
      });

      flip.loadFromImages(pages.map(function (p) { return p.src; }));

      // ── Page controls ───────────────────────────────────
      if (controls) {
        controls.hidden = false;
        var total = flip.getPageCount();
        var update = function () {
          var cur = flip.getCurrentPageIndex() + 1;
          if (pageLabel) pageLabel.textContent = cur + ' / ' + total;
          if (prevBtn) prevBtn.disabled = cur <= 1;
          if (nextBtn) nextBtn.disabled = cur >= total;
        };
        flip.on('flip', update);
        if (prevBtn) prevBtn.addEventListener('click', function () { flip.flipPrev(); });
        if (nextBtn) nextBtn.addEventListener('click', function () { flip.flipNext(); });
        update();
      }

      // ── Zoom + pan ──────────────────────────────────────
      var scale = 1, tx = 0, ty = 0;
      var MIN = 1, MAX = MAX_ZOOM, STEP = 0.5;
      var dragging = false, startX = 0, startY = 0;
      var pinching = false, pinchStartDist = 0, pinchStartScale = 1;

      function touchDist(e) {
        var a = e.touches[0], b = e.touches[1];
        var dx = a.clientX - b.clientX, dy = a.clientY - b.clientY;
        return Math.sqrt(dx * dx + dy * dy);
      }

      function apply() {
        el.style.transformOrigin = 'center center';
        el.style.transform = 'translate(' + tx + 'px,' + ty + 'px) scale(' + scale + ')';
        el.style.cursor = scale > 1 ? (dragging ? 'grabbing' : 'grab') : '';
        el.style.touchAction = scale > 1 ? 'none' : '';
        if (zoomOutBtn) zoomOutBtn.disabled = scale <= MIN;
        if (zoomInBtn)  zoomInBtn.disabled  = scale >= MAX;
      }
      function clamp() {
        if (!viewport) return;
        var maxX = Math.max(0, (el.offsetWidth  * scale - viewport.clientWidth)  / 2);
        var maxY = Math.max(0, (el.offsetHeight * scale - viewport.clientHeight) / 2);
        tx = Math.max(-maxX, Math.min(maxX, tx));
        ty = Math.max(-maxY, Math.min(maxY, ty));
      }
      function zoom(dir) {
        scale = Math.max(MIN, Math.min(MAX, scale + dir * STEP));
        if (scale === 1) { tx = 0; ty = 0; }
        clamp();
        apply();
      }
      if (zoomInBtn)  zoomInBtn.addEventListener('click', function () { zoom(1); });
      if (zoomOutBtn) zoomOutBtn.addEventListener('click', function () { zoom(-1); });

      function point(e) {
        return e.touches && e.touches[0] ? e.touches[0] : e;
      }
      // Capture phase so we intercept the gesture BEFORE StPageFlip.
      function onDown(e) {
        // Two fingers → pinch-zoom (works at any zoom level).
        if (e.touches && e.touches.length === 2) {
          e.stopPropagation();
          e.preventDefault();
          pinching = true;
          dragging = false;
          pinchStartDist = touchDist(e);
          pinchStartScale = scale;
          el.style.touchAction = 'none';
          return;
        }
        if (scale <= 1) return;          // single finger at 1× → let StPageFlip turn pages
        e.stopPropagation();
        e.preventDefault();
        dragging = true;
        var p = point(e);
        startX = p.clientX - tx;
        startY = p.clientY - ty;
        apply();
      }
      function onMove(e) {
        if (pinching && e.touches && e.touches.length === 2) {
          e.stopPropagation();
          e.preventDefault();
          var ratio = touchDist(e) / (pinchStartDist || 1);
          scale = Math.max(MIN, Math.min(MAX, pinchStartScale * ratio));
          if (scale <= 1) { scale = 1; tx = 0; ty = 0; }
          clamp();
          apply();
          return;
        }
        if (!dragging) return;
        e.preventDefault();
        var p = point(e);
        tx = p.clientX - startX;
        ty = p.clientY - startY;
        clamp();
        apply();
      }
      function onUp(e) {
        if (pinching) {
          if (!e || !e.touches || e.touches.length < 2) {
            pinching = false;
            if (scale <= 1) { scale = 1; tx = 0; ty = 0; }
            apply();          // restores touch-action based on final scale
          }
          return;
        }
        if (!dragging) return;
        dragging = false;
        apply();
      }
      el.addEventListener('mousedown', onDown, true);
      el.addEventListener('touchstart', onDown, { capture: true, passive: false });
      window.addEventListener('mousemove', onMove, true);
      window.addEventListener('touchmove', onMove, { capture: true, passive: false });
      window.addEventListener('mouseup', onUp, true);
      window.addEventListener('touchend', onUp, true);

      apply();
    })
    .catch(function (err) {
      fallback();
      if (window.console && console.warn) console.warn('pdf-flip:', err);
    });
})();
