/**
 * Newsletter page-flip viewer.
 * Renders each PDF page with pdf.js, then feeds the images into StPageFlip
 * for a realistic magazine-style flip. Loaded only on newsletter posts.
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
      var baseW = 420;
      var baseH = Math.round(baseW * ratio);

      el.innerHTML = '';
      var flip = new St.PageFlip(el, {
        width: baseW,
        height: baseH,
        size: 'stretch',
        minWidth: 260,
        maxWidth: 560,
        minHeight: Math.round(260 * ratio),
        maxHeight: Math.round(560 * ratio),
        maxShadowOpacity: 0.5,
        showCover: true,
        mobileScrollSupport: false,
        usePortrait: false
      });

      flip.loadFromImages(pages.map(function (p) { return p.src; }));

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
