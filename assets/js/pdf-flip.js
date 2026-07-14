/**
 * Newsletter page-flip viewer (lightweight).
 * Renders each PDF page with pdf.js at screen resolution, then feeds the
 * images into StPageFlip for a magazine-style page turn. Loaded only on
 * newsletter posts.
 *
 * Kept intentionally simple: page-turn + prev/next only (no zoom / pan / pinch),
 * and pages render as screen-sized JPEGs so the viewer loads fast and stays light.
 */
(function () {
  var el = document.querySelector('.pdf-flip');
  if (!el || typeof pdfjsLib === 'undefined' || typeof St === 'undefined' || !St.PageFlip) return;

  var url       = el.getAttribute('data-pdf');
  var wrap      = el.closest('.pdf-flip-wrap');
  var controls  = wrap ? wrap.querySelector('.pdf-flip__controls') : null;
  var pageLabel = controls ? controls.querySelector('[data-flip-page]') : null;
  var prevBtn   = controls ? controls.querySelector('[data-flip-prev]') : null;
  var nextBtn   = controls ? controls.querySelector('[data-flip-next]') : null;

  if (!url) return;

  if (window.leapPdf && leapPdf.worker) {
    pdfjsLib.GlobalWorkerOptions.workerSrc = leapPdf.worker;
  }

  function fallback() {
    el.innerHTML =
      '<div class="pdf-flip__loading">The viewer couldn’t load. ' +
      '<a href="' + url + '" target="_blank" rel="noopener">Open the PDF</a> instead.</div>';
  }

  var isMobile = window.matchMedia('(max-width: 768px)').matches;

  // Render each page to a JPEG sized for the screen (no zoom headroom). One leaf
  // displays at ~500 CSS px, so ~2x that on retina is plenty sharp. JPEG + a
  // modest resolution keeps rendering fast and the images small.
  function renderPages(pdf) {
    var pages = [];
    var chain = Promise.resolve();
    var dpr     = Math.min(window.devicePixelRatio || 1, 2);
    var targetW = 640 * dpr; // one leaf at device resolution
    for (var i = 1; i <= pdf.numPages; i++) {
      (function (num) {
        chain = chain.then(function () {
          return pdf.getPage(num).then(function (page) {
            var natural = page.getViewport({ scale: 1 });
            var scale   = Math.min(2.5, Math.max(1, targetW / natural.width));
            var vp      = page.getViewport({ scale: scale });
            var canvas  = document.createElement('canvas');
            canvas.width  = vp.width;
            canvas.height = vp.height;
            var ctx = canvas.getContext('2d');
            return page.render({ canvasContext: ctx, viewport: vp }).promise
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

      // On desktop it's a two-page spread. For the back cover to sit alone on the
      // left the page count must be even — if it's odd, slip in a blank leaf.
      if (!isMobile && pages.length > 2 && pages.length % 2 === 1) {
        var bw = 800, bh = Math.round(bw * ratio);
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
        minWidth: 320,
        maxWidth: 1000,
        minHeight: Math.round(320 * ratio),
        maxHeight: Math.round(1000 * ratio),
        maxShadowOpacity: 0.4,
        showCover: true,
        mobileScrollSupport: false,
        // One full page per view on phones; two-page spread on desktop.
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
    })
    .catch(function (err) {
      fallback();
      if (window.console && console.warn) console.warn('pdf-flip:', err);
    });
})();
