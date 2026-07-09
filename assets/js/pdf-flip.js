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
  var pager     = controls ? controls.querySelector('.pdf-flip__pager') : null;
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

  // Render every page to a JPEG data URL (sequentially, to keep memory sane).
  function renderPages(pdf) {
    var pages = [];
    var chain = Promise.resolve();
    for (var i = 1; i <= pdf.numPages; i++) {
      (function (num) {
        chain = chain.then(function () {
          return pdf.getPage(num).then(function (page) {
            var vp     = page.getViewport({ scale: 1.6 });
            var canvas = document.createElement('canvas');
            canvas.width  = vp.width;
            canvas.height = vp.height;
            return page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise
              .then(function () {
                pages.push({ src: canvas.toDataURL('image/jpeg', 0.82), ratio: vp.height / vp.width });
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
      var baseW = 600;
      var baseH = Math.round(baseW * ratio);

      el.innerHTML = '';
      var flip = new St.PageFlip(el, {
        width: baseW,
        height: baseH,
        size: 'stretch',
        // Two-page spread stretches to the full container on desktop; below
        // ~2×minWidth (i.e. phones) StPageFlip auto-collapses to one full page.
        minWidth: 350,
        maxWidth: 1200,
        minHeight: Math.round(350 * ratio),
        maxHeight: Math.round(1200 * ratio),
        maxShadowOpacity: 0.5,
        showCover: true,
        mobileScrollSupport: false,
        usePortrait: false
      });

      flip.loadFromImages(pages.map(function (p) { return p.src; }));

      // ── Pagination (same style as the News page: numbers + arrows) ──
      var total    = flip.getPageCount();
      var pageBtns = [];
      var navPrev, navNext;
      if (pager) {
        var nav = document.createElement('nav');
        nav.className = 'news-pagination';
        nav.setAttribute('aria-label', 'Newsletter pages');

        navPrev = document.createElement('button');
        navPrev.type = 'button';
        navPrev.className = 'news-pagination__arrow';
        navPrev.innerHTML = '<span aria-hidden="true">←</span>';
        navPrev.setAttribute('aria-label', 'Previous page');
        navPrev.addEventListener('click', function () { flip.flipPrev(); });

        var pagesDiv = document.createElement('div');
        pagesDiv.className = 'news-pagination__pages';
        for (var i = 0; i < total; i++) {
          (function (idx) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'news-pagination__page';
            b.textContent = String(idx + 1);
            b.setAttribute('aria-label', 'Page ' + (idx + 1));
            b.addEventListener('click', function () { flip.turnToPage(idx); });
            pagesDiv.appendChild(b);
            pageBtns.push(b);
          })(i);
        }

        navNext = document.createElement('button');
        navNext.type = 'button';
        navNext.className = 'news-pagination__arrow';
        navNext.innerHTML = '<span aria-hidden="true">→</span>';
        navNext.setAttribute('aria-label', 'Next page');
        navNext.addEventListener('click', function () { flip.flipNext(); });

        nav.append(navPrev, pagesDiv, navNext);
        pager.appendChild(nav);
      }
      var updatePager = function () {
        var cur = flip.getCurrentPageIndex();
        pageBtns.forEach(function (b, i) { b.classList.toggle('is-active', i === cur); });
        if (navPrev) navPrev.disabled = cur <= 0;
        if (navNext) navNext.disabled = cur >= total - 1;
      };
      flip.on('flip', updatePager);
      if (controls) controls.hidden = false;
      updatePager();

      // ── Zoom + pan ──────────────────────────────────────
      var scale = 1, tx = 0, ty = 0;
      var MIN = 1, MAX = 2.5, STEP = 0.5;
      var dragging = false, startX = 0, startY = 0;

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
      // Capture phase so we intercept the drag BEFORE StPageFlip when zoomed.
      function onDown(e) {
        if (scale <= 1) return;          // let StPageFlip handle page turns
        e.stopPropagation();
        e.preventDefault();
        dragging = true;
        var p = point(e);
        startX = p.clientX - tx;
        startY = p.clientY - ty;
        apply();
      }
      function onMove(e) {
        if (!dragging) return;
        e.preventDefault();
        var p = point(e);
        tx = p.clientX - startX;
        ty = p.clientY - startY;
        clamp();
        apply();
      }
      function onUp() {
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
