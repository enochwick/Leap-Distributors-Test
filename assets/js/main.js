/* Leap Distributors — main.js */

// ── Mesh Background — runs on every .mesh-canvas found ────
function createMeshBackground(canvas) {
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  let W = 0, H = 0, dpr = 1;
  let time = 0;
  let raf = null;
  const mouse = { x: -9999, y: -9999 };
  let scrollRatio = 0;

  // Gradient blobs — normalized 0-1 positions, drift via sine
  const blobs = [
    { px: 0.22, py: 0.28, r: 0.72, color: [42, 125, 225], a: 0.22, sx: 1.0, sy: 0.8, ph: 0.0 },
    { px: 0.78, py: 0.68, r: 0.62, color: [0,  56,  77],  a: 0.42, sx: 0.7, sy: 1.1, ph: 1.5 },
    { px: 0.50, py: 0.52, r: 0.78, color: [42, 125, 225], a: 0.14, sx: 1.2, sy: 0.9, ph: 2.8 },
    { px: 0.12, py: 0.78, r: 0.46, color: [0,  183, 200], a: 0.16, sx: 0.9, sy: 0.7, ph: 0.7 },
    { px: 0.88, py: 0.22, r: 0.50, color: [230, 83,  0],  a: 0.05, sx: 1.1, sy: 1.3, ph: 3.9 },
    { px: 0.62, py: 0.12, r: 0.42, color: [42, 125, 225], a: 0.13, sx: 0.8, sy: 0.6, ph: 5.2 },
    { px: 0.38, py: 0.88, r: 0.52, color: [0,  77,  102], a: 0.26, sx: 1.0, sy: 0.9, ph: 4.4 },
  ];

  const GRID  = 40;  // grid spacing px
  const WARP  = 8;  // max grid warp px
  const DRIFT = 0.065; // blob drift amplitude (fraction of canvas)

  function resize() {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    W = canvas.offsetWidth;
    H = canvas.offsetHeight;
    canvas.width  = W * dpr;
    canvas.height = H * dpr;
    ctx.scale(dpr, dpr);
  }

  // Return current world-space position of a blob
  function blobPos(b) {
    return {
      x: (b.px + Math.sin(time * b.sx * 0.38 + b.ph)             * DRIFT
               + Math.cos(time * b.sy * 0.22 + b.ph + 1.1)       * DRIFT * 0.45) * W,
      y: (b.py + Math.cos(time * b.sy * 0.33 + b.ph + 2.0)       * DRIFT
               + Math.sin(time * b.sx * 0.52 + b.ph + 0.6)       * DRIFT * 0.4
               + scrollRatio * 0.14) * H,
    };
  }

  // Compute displacement at a grid point from blobs + mouse
  function warpAt(gx, gy, blobPositions) {
    let wx = 0, wy = 0;

    for (let i = 0; i < blobs.length; i++) {
      const pos = blobPositions[i];
      const dx  = gx - pos.x;
      const dy  = gy - pos.y;
      const dist = Math.hypot(dx, dy);
      const rad  = blobs[i].r * Math.min(W, H) * 0.55;
      if (dist < rad && dist > 0.5) {
        const t = 1 - dist / rad;
        const f = t * t * WARP * 0.6;
        wx -= (dx / dist) * f;
        wy -= (dy / dist) * f;
      }
    }

    // Mouse push (repulsion)
    const mdx  = gx - mouse.x;
    const mdy  = gy - mouse.y;
    const mdist = Math.hypot(mdx, mdy);
    const mRad  = 180;
    if (mdist < mRad && mdist > 0.5) {
      const t = 1 - mdist / mRad;
      const f = t * t * WARP * 4;
      wx += (mdx / mdist) * f;
      wy += (mdy / mdist) * f;
    }

    return { wx, wy };
  }

  function drawFrame() {
    ctx.clearRect(0, 0, W, H);

    // ── 1. Gradient blobs ───────────────────────────────────
    const blobPositions = blobs.map(blobPos);

    ctx.save();
    ctx.globalCompositeOperation = 'source-over';
    for (let i = 0; i < blobs.length; i++) {
      const b   = blobs[i];
      const pos = blobPositions[i];
      const rad = b.r * Math.min(W, H) * 0.75;
      const [r, g, bl] = b.color;
      const gr  = ctx.createRadialGradient(pos.x, pos.y, 0, pos.x, pos.y, rad);
      gr.addColorStop(0,   `rgba(${r},${g},${bl},${b.a})`);
      gr.addColorStop(0.45,`rgba(${r},${g},${bl},${b.a * 0.3})`);
      gr.addColorStop(1,   `rgba(${r},${g},${bl},0)`);
      ctx.fillStyle = gr;
      ctx.fillRect(0, 0, W, H);
    }
    ctx.restore();

    // ── 2. Warped mesh grid ─────────────────────────────────
    const cols = Math.ceil(W / GRID) + 2;
    const rows = Math.ceil(H / GRID) + 2;
    // Slow vertical drift for the grid itself
    const driftY = (time * 6) % GRID;

    // Pre-build warped point array
    const pts = [];
    for (let row = 0; row <= rows; row++) {
      pts[row] = [];
      for (let col = 0; col <= cols; col++) {
        const gx = (col - 1) * GRID;
        const gy = (row - 1) * GRID + driftY;
        const { wx, wy } = warpAt(gx, gy, blobPositions);
        pts[row][col] = { x: gx + wx, y: gy + wy, wx, wy };
      }
    }

    // Horizontal lines
    ctx.beginPath();
    for (let row = 0; row <= rows; row++) {
      ctx.moveTo(pts[row][0].x, pts[row][0].y);
      for (let col = 1; col <= cols; col++) {
        ctx.lineTo(pts[row][col].x, pts[row][col].y);
      }
    }
    // Vertical lines
    for (let col = 0; col <= cols; col++) {
      ctx.moveTo(pts[0][col].x, pts[0][col].y);
      for (let row = 1; row <= rows; row++) {
        ctx.lineTo(pts[row][col].x, pts[row][col].y);
      }
    }
    ctx.strokeStyle = 'rgba(42, 125, 225, 0.10)';
    ctx.lineWidth   = 0.8;
    ctx.stroke();

    // Intersection dots — size scales with warp intensity
    for (let row = 0; row <= rows; row++) {
      for (let col = 0; col <= cols; col++) {
        const p = pts[row][col];
        const warpMag = Math.min(Math.hypot(p.wx, p.wy) / (WARP * 2), 1);
        const dotR = 1.2 + warpMag * 2.2;
        const alpha = 0.18 + warpMag * 0.35;

        ctx.beginPath();
        ctx.arc(p.x, p.y, dotR, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(42, 125, 225, ${alpha})`;
        ctx.fill();
      }
    }

    time += 0.004;
    raf = requestAnimationFrame(drawFrame);
  }

  // ── Events ─────────────────────────────────────────────────
  const sectionEl = canvas.closest('.hero, .page-hero, .phg-section');

  if (sectionEl) {
    sectionEl.addEventListener('mousemove', e => {
      const rect = canvas.getBoundingClientRect();
      mouse.x = e.clientX - rect.left;
      mouse.y = e.clientY - rect.top;
    });
    sectionEl.addEventListener('mouseleave', () => {
      mouse.x = -9999;
      mouse.y = -9999;
    });
  }

  const onScroll = () => {
    if (!sectionEl) return;
    const rect = sectionEl.getBoundingClientRect();
    scrollRatio = Math.max(0, -rect.top) / (H || 1);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', resize, { passive: true });

  // Pause when off-screen
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        if (!raf) drawFrame();
      } else {
        cancelAnimationFrame(raf);
        raf = null;
      }
    });
  }, { threshold: 0.01 });

  if (sectionEl) observer.observe(sectionEl);

  resize();
  drawFrame();
}

// Init on every .mesh-canvas element on this page
document.querySelectorAll('.mesh-canvas').forEach(createMeshBackground);


// ── Desktop pill nav (sliding highlight) ──────────────────
document.addEventListener('DOMContentLoaded', () => {
  const pill = document.querySelector('.nav-pill');
  if (!pill) return;

  const list   = pill.querySelector('.nav-pill__list');
  const cursor = pill.querySelector('.nav-pill__cursor');
  const links  = Array.from(pill.querySelectorAll('.nav-pill__link'));
  if (!list || !cursor || !links.length) return;

  // Mark the link matching the current page.
  // Also match on the last path segment — WP may serve a nav URL like
  // /partnerships/hospitals/ from /hospitals/ depending on page nesting.
  const here = location.pathname.replace(/\/+$/, '');
  const lastSeg = p => p.split('/').filter(Boolean).pop() || '';
  const activeLink = links.find(a => {
    const path = new URL(a.href).pathname.replace(/\/+$/, '');
    if (!path) return false;
    return path === here || here.startsWith(path + '/') || (here && lastSeg(path) === lastSeg(here));
  }) || null;
  if (activeLink) activeLink.setAttribute('aria-current', 'page');

  const moveTo = (el, instant = false) => {
    links.forEach(a => a.classList.toggle('is-on', a === el));
    if (instant) {
      cursor.style.transition = 'none';
      // flush so the jump isn't animated, then restore the CSS transition
      requestAnimationFrame(() => requestAnimationFrame(() => { cursor.style.transition = ''; }));
    }
    if (!el) { cursor.style.opacity = '0'; return; }
    const item = el.parentElement; // the <li>
    cursor.style.left    = item.offsetLeft + 'px';
    cursor.style.width   = item.offsetWidth + 'px';
    cursor.style.opacity = '1';
  };

  // The white pill always rests on the active page. Hover/focus on other
  // links is handled purely in CSS (their own dark-teal background).
  const rest = (instant = false) => moveTo(activeLink, instant);

  window.addEventListener('resize', () => rest(true), { passive: true });

  // Place instantly on load, and re-place once webfonts settle link widths
  rest(true);
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => rest(true));
  }
  window.addEventListener('load', () => rest(true));
});

document.addEventListener('DOMContentLoaded', () => {
  if (typeof gsap === 'undefined') return;

  gsap.registerPlugin(ScrollTrigger);

  // ── Header scroll state ───────────────────────────────────
  const header = document.getElementById('site-header');
  let lastScrollY = 0;
  let ticking = false;

  function updateHeader() {
    const scrollY = window.scrollY;
    header.classList.toggle('is-scrolled', scrollY > 40);
    header.classList.toggle('is-hidden', scrollY > lastScrollY && scrollY > 200);
    lastScrollY = scrollY;
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) { requestAnimationFrame(updateHeader); ticking = true; }
  }, { passive: true });

  updateHeader();

  // ── Overlay nav toggle ────────────────────────────────────
  const navToggle  = document.getElementById('nav-toggle');
  const navOverlay = document.getElementById('nav-overlay');

  if (navToggle && navOverlay) {
    navToggle.addEventListener('click', () => {
      const isOpen = navOverlay.classList.toggle('is-open');
      navToggle.classList.toggle('is-open', isOpen);
      header.classList.toggle('nav-is-open', isOpen);
      navToggle.setAttribute('aria-expanded', String(isOpen));
      navOverlay.setAttribute('aria-hidden', String(!isOpen));
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on link click
    navOverlay.querySelectorAll('.nav-overlay__link, .nav-overlay__cta').forEach(link => {
      link.addEventListener('click', () => {
        navOverlay.classList.remove('is-open');
        navToggle.classList.remove('is-open');
        header.classList.remove('nav-is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        navOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      });
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && navOverlay.classList.contains('is-open')) {
        navOverlay.classList.remove('is-open');
        navToggle.classList.remove('is-open');
        header.classList.remove('nav-is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        navOverlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        navToggle.focus();
      }
    });
  }

  // ── Nav Search ───────────────────────────────────────────
  const navSearch    = document.getElementById('nav-search');
  const searchToggle = document.getElementById('nav-search-toggle');

  if (navSearch && searchToggle) {
    const searchInput    = navSearch.querySelector('.nav-search__input');
    const searchClose    = navSearch.querySelector('.nav-search__close');
    const searchDropdown = document.getElementById('nav-search-dropdown');
    const index          = typeof leapSearchIndex !== 'undefined' ? leapSearchIndex : [];
    let activeIdx = -1;

    function openSearch() {
      navSearch.classList.add('is-open');
      searchToggle.setAttribute('aria-expanded', 'true');
      searchInput.focus();
    }

    function closeSearch() {
      navSearch.classList.remove('is-open');
      searchToggle.setAttribute('aria-expanded', 'false');
      searchInput.value = '';
      hideDropdown();
      searchToggle.focus();
    }

    function hideDropdown() {
      searchDropdown.innerHTML = '';
      searchDropdown.classList.remove('is-visible');
      activeIdx = -1;
    }

    function queryIndex(q) {
      const terms = q.toLowerCase().trim().split(/\s+/).filter(Boolean);
      if (!terms.length) return [];
      return index.filter(item => {
        const hay = (item.title + ' ' + item.description + ' ' + item.keywords).toLowerCase();
        return terms.every(t => hay.includes(t));
      }).slice(0, 6);
    }

    function highlight(text, q) {
      const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      return text.replace(new RegExp(`(${escaped})`, 'gi'), '<mark>$1</mark>');
    }

    function renderDropdown(results, q) {
      if (!results.length) {
        searchDropdown.innerHTML = '<p class="nav-search__empty">No results found</p>';
        searchDropdown.classList.add('is-visible');
        return;
      }
      searchDropdown.innerHTML = results.map((r, i) =>
        `<a class="nav-search__result" href="${r.url}" data-idx="${i}">
          <span class="nav-search__result-type">${r.type}</span>
          <span class="nav-search__result-title">${highlight(r.title, q)}</span>
          <span class="nav-search__result-desc">${highlight(r.description.slice(0, 80), q)}…</span>
        </a>`
      ).join('');
      searchDropdown.classList.add('is-visible');
      activeIdx = -1;
    }

    searchInput.addEventListener('input', () => {
      const q = searchInput.value.trim();
      if (q.length < 2) { hideDropdown(); return; }
      const results = queryIndex(q);
      renderDropdown(results, q);
    });

    // Keyboard navigation in dropdown
    searchInput.addEventListener('keydown', e => {
      const items = searchDropdown.querySelectorAll('.nav-search__result');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIdx = Math.min(activeIdx + 1, items.length - 1);
        items.forEach((el, i) => el.classList.toggle('is-active', i === activeIdx));
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIdx = Math.max(activeIdx - 1, -1);
        items.forEach((el, i) => el.classList.toggle('is-active', i === activeIdx));
      } else if (e.key === 'Enter' && activeIdx >= 0) {
        e.preventDefault();
        items[activeIdx].click();
      }
    });

    searchToggle.addEventListener('click', openSearch);
    searchClose.addEventListener('click', closeSearch);

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && navSearch.classList.contains('is-open')) closeSearch();
    });

    document.addEventListener('click', e => {
      if (navSearch.classList.contains('is-open') && !navSearch.contains(e.target)) closeSearch();
    });
  }

  // ── Hero entrance ─────────────────────────────────────────
  const heroBadge    = document.querySelector('.hero__badge');
  const heroHeadline = document.querySelector('.hero__headline');
  const heroSubtext  = document.querySelector('.hero__subtext');
  const heroActions  = document.querySelector('.hero__actions');

  if (heroHeadline) {
    const tl = gsap.timeline({ delay: 0.1 });

    if (heroBadge) {
      gsap.set(heroBadge, { opacity: 0, y: 16 });
      tl.to(heroBadge, { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, 0);
    }

    gsap.set(heroHeadline, { opacity: 0, y: 40 });
    tl.to(heroHeadline, { opacity: 1, y: 0, duration: 1.0, ease: 'power3.out' }, 0.2);

    if (heroSubtext) {
      gsap.set(heroSubtext, { opacity: 0, y: 20 });
      tl.to(heroSubtext, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out' }, 0.55);
    }

    if (heroActions) {
      gsap.set(heroActions, { opacity: 0, y: 20 });
      tl.to(heroActions, { opacity: 1, y: 0, duration: 0.75, ease: 'power3.out' }, 0.7);
    }
  }

  // ── Counter animation ─────────────────────────────────────
  function animateCounter(el) {
    const target = parseFloat(el.dataset.count);
    const suffix = el.dataset.suffix || '';
    const prefix = el.dataset.prefix || '';
    const decimals = el.dataset.decimals ? parseInt(el.dataset.decimals) : 0;
    const duration = 2;
    let start = null;

    function update(timestamp) {
      if (!start) start = timestamp;
      const progress = Math.min((timestamp - start) / (duration * 1000), 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const current = eased * target;
      el.textContent = prefix + current.toFixed(decimals) + suffix;

      if (progress < 1) requestAnimationFrame(update);
      else el.textContent = prefix + target.toFixed(decimals) + suffix;
    }

    requestAnimationFrame(update);
  }

  document.querySelectorAll('[data-count]').forEach(el => {
    ScrollTrigger.create({
      trigger: el,
      start: 'top 85%',
      once: true,
      onEnter: () => animateCounter(el),
    });
  });

  // ── Scroll reveals ────────────────────────────────────────
  gsap.utils.toArray('.reveal').forEach(el => {
    gsap.to(el, {
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
      },
      opacity: 1,
      y: 0,
      duration: 0.75,
      ease: 'power3.out',
    });
  });

  gsap.utils.toArray('.reveal-left').forEach(el => {
    gsap.to(el, {
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
      },
      opacity: 1,
      x: 0,
      duration: 0.75,
      ease: 'power3.out',
    });
  });

  gsap.utils.toArray('.reveal-right').forEach(el => {
    gsap.to(el, {
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
      },
      opacity: 1,
      x: 0,
      duration: 0.75,
      ease: 'power3.out',
    });
  });

  gsap.utils.toArray('.reveal-scale').forEach(el => {
    gsap.to(el, {
      scrollTrigger: {
        trigger: el,
        start: 'top 88%',
      },
      opacity: 1,
      scale: 1,
      duration: 0.75,
      ease: 'power3.out',
    });
  });

  // Staggered children reveals
  gsap.utils.toArray('[data-stagger]').forEach(parent => {
    const children = parent.querySelectorAll('[data-stagger-child]');
    gsap.fromTo(children,
      { opacity: 0, y: 32 },
      {
        scrollTrigger: {
          trigger: parent,
          start: 'top 85%',
        },
        opacity: 1,
        y: 0,
        duration: 0.65,
        stagger: 0.1,
        ease: 'power3.out',
      }
    );
  });

  // ── Scroll-driven video frames ────────────────────────────
  const heroVideoCanvas = document.getElementById('hero-canvas');
  if (heroVideoCanvas && typeof leapData !== 'undefined') {
    const ctx         = heroVideoCanvas.getContext('2d');
    const totalFrames = 139;
    const frames      = new Array(totalFrames);
    let   loadedCount = 0;
    let   currentFrame = 0;

    function resizeVideoCanvas() {
      heroVideoCanvas.width  = window.innerWidth;
      heroVideoCanvas.height = window.innerHeight;
      drawVideoFrame(currentFrame);
    }

    function drawVideoFrame(index) {
      const img = frames[index];
      if (!img || !img.complete || !img.naturalWidth) return;
      const cw = heroVideoCanvas.width, ch = heroVideoCanvas.height;
      const iw = img.naturalWidth, ih = img.naturalHeight;

      // Skip letterbox bars baked into source (16.25% top, 10.42% bottom)
      const barTop  = ih * 0.1625;
      const barBot  = ih * 0.1042;
      const contentY = barTop;
      const contentH = ih - barTop - barBot;

      // Cover-fit the content area to fill the canvas
      const scale = Math.max(cw / iw, ch / contentH);
      const sw = cw / scale;
      const sh = ch / scale;
      const sx = (iw - sw) / 2;
      const sy = contentY + (contentH - sh) / 2;

      ctx.clearRect(0, 0, cw, ch);
      ctx.drawImage(img, sx, sy, sw, sh, 0, 0, cw, ch);
    }

    const baseUrl = leapData.themeUrl + '/assets/images/hero-frames/';
    for (let i = 0; i < totalFrames; i++) {
      const img = new Image();
      img.src = baseUrl + 'frame_' + String(i + 1).padStart(3, '0') + '.jpg';
      img.onload = () => { loadedCount++; if (loadedCount === 1) resizeVideoCanvas(); };
      frames[i] = img;
    }

    window.addEventListener('resize', resizeVideoCanvas, { passive: true });
    resizeVideoCanvas();

    gsap.to({ f: 0 }, {
      f: totalFrames - 1,
      ease: 'none',
      scrollTrigger: {
        trigger: '.hero-scroll-container',
        start: 'top top',
        end:   'bottom bottom',
        scrub: true,
        onUpdate: self => {
          const frame = Math.round(self.progress * (totalFrames - 1));
          if (frame !== currentFrame) { currentFrame = frame; drawVideoFrame(frame); }
        },
      },
    });
  }

  // ── Platform Dashboard Hero ───────────────────────────────
  const phgScroll  = document.getElementById('phg-scroll');
  const phgGallery = document.getElementById('phg-gallery');
  const phgFades   = document.querySelectorAll('.phg-fade');

  if (phgScroll && phgGallery) {
    // Staggered fade-in for header text
    phgFades.forEach((el, i) => {
      setTimeout(() => el.classList.add('is-visible'), 200 + i * 150);
    });

    // Scroll-driven tilt is a desktop effect only — on mobile the pinned
    // full-height section leaves a big empty gap, so we skip it and let the
    // laptop render inline (see the matching mobile CSS).
    if (window.matchMedia('(min-width: 769px)').matches &&
        !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      // Tilt flattens after first scroll — no y offset so image is never cropped
      gsap.set(phgGallery, { rotateX: 32, scale: 0.92, transformOrigin: 'center center' });

      gsap.to(phgGallery, {
        rotateX: 0,
        scale: 1,
        ease: 'none',
        scrollTrigger: {
          trigger: '.phg-section',
          start: 'top top',
          end: 'top+=15% top',
          scrub: 0.4,
        },
      });
    }
  }

  // ── Platform capabilities — sticky stacking cards ─────────
  const platformStack = document.getElementById('platform-stack');
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (platformStack && window.matchMedia('(min-width: 901px)').matches && !prefersReducedMotion) {
    const cards = gsap.utils.toArray('.platform-stack__card', platformStack);

    cards.forEach((card, i) => {
      // The last card sits on top and never scales/dims.
      if (i === cards.length - 1) return;

      // Mirrors the reference: cards further down the stack shrink more.
      const targetScale = Math.max(0.86, 1 - (cards.length - i - 1) * 0.05);

      gsap.fromTo(
        card,
        { scale: 1, '--stack-dim': 0 },
        {
          scale: targetScale,
          '--stack-dim': 0.4,
          ease: 'none',
          scrollTrigger: {
            trigger: card,
            start: 'top 140px',          // when the card reaches its pinned spot
            endTrigger: cards[i + 1],
            end: 'top 140px',            // finishes once the next card pins over it
            scrub: 0.4,
          },
        }
      );
    });

    ScrollTrigger.refresh();
  }

  // ── Horizontal marquee (if used) ──────────────────────────
  const marquees = document.querySelectorAll('.marquee-track');
  marquees.forEach(track => {
    const items = track.querySelectorAll('.marquee-item');
    if (!items.length) return;

    const clone = track.cloneNode(true);
    track.parentElement.appendChild(clone);

    gsap.to([track, clone], {
      xPercent: -100,
      repeat: -1,
      duration: 20,
      ease: 'none',
    });
  });

  // ── Founders carousel: optional arrow buttons scroll the CSS swipe track ──
  (function () {
    var mosaic = document.getElementById('founders');
    if (!mosaic) return;
    var nav = mosaic.querySelector('.founders-nav');
    if (!nav) return;
    var firstTile = mosaic.querySelector('.founder-tile');
    function scrollByCard(dir) {
      var step = firstTile ? firstTile.offsetWidth + 16 : mosaic.clientWidth;
      mosaic.scrollBy({ left: dir * step, behavior: 'smooth' });
    }
    nav.querySelectorAll('[data-dir]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        scrollByCard(btn.getAttribute('data-dir') === 'next' ? 1 : -1);
      });
    });
  })();

  // ── Trey video: muted autoplay when scrolled into view ────
  (function () {
    var vid = document.getElementById('trey-video');
    if (!vid || !('IntersectionObserver' in window)) return;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          var p = vid.play();
          if (p && p.catch) p.catch(function () {});
        } else {
          vid.pause();
        }
      });
    }, { threshold: 0.4 });
    io.observe(vid);
  })();

  // ── Vertical Tabs (Built For) ─────────────────────────────
  const vtSection = document.querySelector('.vt-section');
  if (vtSection) {
    const tabs   = vtSection.querySelectorAll('.vt-tab');
    const slides = vtSection.querySelectorAll('.vt-slide');
    const gallery = vtSection.querySelector('.vt-gallery');
    const DURATION = 5000;
    let current = 0;
    let timer   = null;
    let paused  = false;

    function vtGoTo(index, dir) {
      const prev = current;
      current = ((index % slides.length) + slides.length) % slides.length;
      if (prev === current) return;

      const outSlide = slides[prev];
      const inSlide  = slides[current];

      // Plain crossfade — no movement
      inSlide.classList.add('is-active');
      setTimeout(() => outSlide.classList.remove('is-active'), 500);

      // Update tabs
      tabs.forEach((tab, i) => {
        const wasActive = tab.classList.contains('is-active');
        tab.classList.toggle('is-active', i === current);
        // Reset progress bar animation
        if (i === current) {
          const bar = tab.querySelector('.vt-tab__progress');
          if (bar) { bar.style.animation = 'none'; bar.offsetHeight; bar.style.animation = ''; }
        }
      });
    }

    function vtNext() { vtGoTo(current + 1,  1); }
    function vtPrev() { vtGoTo(current - 1, -1); }

    function vtStart() {
      clearInterval(timer);
      timer = setInterval(() => { if (!paused) vtNext(); }, DURATION);
    }

    gallery.addEventListener('mouseenter', () => { paused = true; });
    gallery.addEventListener('mouseleave', () => { paused = false; });

    vtSection.querySelector('#vt-next').addEventListener('click', () => { vtNext(); vtStart(); });
    vtSection.querySelector('#vt-prev').addEventListener('click', () => { vtPrev(); vtStart(); });

    tabs.forEach((tab, i) => {
      tab.addEventListener('click', () => {
        if (i !== current) { vtGoTo(i, i > current ? 1 : -1); vtStart(); }
      });
    });

    // Init — first slide already visible via CSS is-active
    vtStart();
  }

  // ── Spotlight glow cards ───────────────────────────────────
  document.querySelectorAll('[data-glow]').forEach(card => {
    card.addEventListener('pointermove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = (e.clientX - rect.left).toFixed(2);
      const y = (e.clientY - rect.top).toFixed(2);
      const xp = (x / rect.width).toFixed(2);
      const yp = (y / rect.height).toFixed(2);
      card.style.setProperty('--x', x);
      card.style.setProperty('--y', y);
      card.style.setProperty('--xp', xp);
      card.style.setProperty('--yp', yp);
      card.style.setProperty('--opacity', '1');
    }, { passive: true });
    card.addEventListener('pointerleave', () => {
      card.style.setProperty('--opacity', '0');
    });
  });
});

// ── News list pagination ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-paginate]').forEach(list => {
    const perPage = parseInt(list.dataset.paginate, 10) || 4;
    const cards = Array.from(list.children).filter(el => el.classList.contains('news-card'));
    const pageCount = Math.ceil(cards.length / perPage);
    if (pageCount <= 1) return;

    let current = 0;

    const nav = document.createElement('nav');
    nav.className = 'news-pagination';
    nav.setAttribute('aria-label', 'More articles');

    const prev = document.createElement('button');
    prev.type = 'button';
    prev.className = 'news-pagination__arrow';
    prev.innerHTML = '<span aria-hidden="true">←</span>';
    prev.setAttribute('aria-label', 'Previous articles');

    const pages = document.createElement('div');
    pages.className = 'news-pagination__pages';

    const next = document.createElement('button');
    next.type = 'button';
    next.className = 'news-pagination__arrow';
    next.innerHTML = '<span aria-hidden="true">→</span>';
    next.setAttribute('aria-label', 'Next articles');

    const pageBtns = [];
    for (let i = 0; i < pageCount; i++) {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'news-pagination__page';
      b.textContent = String(i + 1);
      b.setAttribute('aria-label', 'Page ' + (i + 1));
      b.addEventListener('click', () => show(i, true));
      pages.appendChild(b);
      pageBtns.push(b);
    }

    prev.addEventListener('click', () => show(current - 1, true));
    next.addEventListener('click', () => show(current + 1, true));

    nav.append(prev, pages, next);
    list.after(nav);

    function show(page, scroll) {
      current = Math.max(0, Math.min(pageCount - 1, page));
      cards.forEach((card, i) => {
        card.style.display = Math.floor(i / perPage) === current ? '' : 'none';
      });
      pageBtns.forEach((b, i) => b.classList.toggle('is-active', i === current));
      prev.disabled = current === 0;
      next.disabled = current === pageCount - 1;
      if (scroll) list.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    show(0, false);
  });
});

// ── AI Chat Widget ────────────────────────────────────────────
(function initLeapChat() {
  const widget   = document.getElementById('leap-chat');
  const toggle   = document.getElementById('lc-toggle');
  const panel    = document.getElementById('lc-panel');
  const messages = document.getElementById('lc-messages');
  const form     = document.getElementById('lc-form');
  const input    = document.getElementById('lc-input');
  if (!widget || !toggle) return;

  let isOpen = false, isLoading = false;
  const history = [];

  function openChat()  {
    isOpen = true;
    widget.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    input.focus();
  }
  function closeChat() {
    isOpen = false;
    widget.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
  }

  toggle.addEventListener('click', () => isOpen ? closeChat() : openChat());
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && isOpen) closeChat(); });

  // Clicking Trey (image or speech bubble) opens the chat.
  var trey = widget.querySelector('.lc__trey');
  if (trey) trey.addEventListener('click', () => { if (!isOpen) openChat(); });

  // Alternate Trey's speech bubble between two prompts.
  var bubble = widget.querySelector('.lc__trey-bubble');
  if (bubble) {
    const bubbleMsgs = ["Hi, I'm Trey! 👋", 'Ask me about Leap 👋'];
    let bi = 0;
    setInterval(() => {
      if (isOpen) return;
      bi = (bi + 1) % bubbleMsgs.length;
      bubble.style.opacity = '0';
      setTimeout(() => { bubble.textContent = bubbleMsgs[bi]; bubble.style.opacity = '1'; }, 300);
    }, 4500);
  }

  function appendMsg(role, text) {
    const div = document.createElement('div');
    div.className = 'lc__msg lc__msg--' + (role === 'user' ? 'user' : 'ai');
    const p = document.createElement('p');
    p.textContent = text;
    div.appendChild(p);
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
  }

  function showTyping() {
    const div = document.createElement('div');
    div.className = 'lc__msg lc__typing';
    div.innerHTML = '<p><span class="lc__dot"></span><span class="lc__dot"></span><span class="lc__dot"></span></p>';
    messages.appendChild(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
  }

  // Auto-grow the textarea with its content (capped by CSS max-height).
  function autoGrow() {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 120) + 'px';
  }
  input.addEventListener('input', autoGrow);

  // Enter sends, Shift+Enter inserts a newline.
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      form.requestSubmit();
    }
  });

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg || isLoading) return;
    input.value = '';
    autoGrow();
    isLoading = true;
    form.querySelector('.lc__send').disabled = true;
    appendMsg('user', msg);
    history.push({ role: 'user', content: msg });
    const typing = showTyping();
    try {
      if (typeof leapChat === 'undefined') throw new Error('not configured');
      const data = new FormData();
      data.append('action',  'leap_ai_chat');
      data.append('nonce',   leapChat.nonce);
      data.append('message', msg);
      data.append('history', JSON.stringify(history.slice(-8)));
      const res  = await fetch(leapChat.ajaxUrl, { method: 'POST', body: data });
      const json = await res.json();
      typing.remove();
      if (json.success && json.data.reply) {
        appendMsg('ai', json.data.reply);
        history.push({ role: 'assistant', content: json.data.reply });
      } else {
        appendMsg('ai', 'Sorry, I had trouble responding. Please try again or contact info@leapdistributors.com.');
      }
    } catch (_) {
      typing.remove();
      appendMsg('ai', 'Connection error. Please try again.');
    }
    isLoading = false;
    form.querySelector('.lc__send').disabled = false;
    input.focus();
  });

  // ── Human handover ──────────────────────────────────────
  const handoverBtn = document.getElementById('lc-handover');
  if (handoverBtn) {
    let handoverOpen = false;
    handoverBtn.addEventListener('click', () => {
      if (handoverOpen) return;
      handoverOpen = true;

      const wrap = document.createElement('div');
      wrap.className = 'lc__handover';
      wrap.innerHTML =
        '<p class="lc__handover-title">Talk to a person</p>' +
        '<input class="lc__handover-field" type="text" id="lc-ho-name" placeholder="Your name" autocomplete="name">' +
        '<input class="lc__handover-field" type="email" id="lc-ho-email" placeholder="Email" autocomplete="email" required>' +
        '<textarea class="lc__handover-field" id="lc-ho-msg" rows="2" placeholder="How can we help?" required></textarea>' +
        '<button class="lc__handover-send" type="button" id="lc-ho-send">Send to the team</button>';
      messages.appendChild(wrap);
      messages.scrollTop = messages.scrollHeight;

      document.getElementById('lc-ho-send').addEventListener('click', async () => {
        const name  = document.getElementById('lc-ho-name').value.trim();
        const email = document.getElementById('lc-ho-email').value.trim();
        const note  = document.getElementById('lc-ho-msg').value.trim();
        if (!email || !note) { return; }

        const sendBtn = document.getElementById('lc-ho-send');
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending…';

        const transcript = history.map(h =>
          (h.role === 'user' ? 'Visitor: ' : 'Leap AI: ') + h.content).join('\n');

        try {
          const data = new FormData();
          data.append('action',     'leap_chat_handover');
          data.append('nonce',      leapChat.nonce);
          data.append('name',       name);
          data.append('email',      email);
          data.append('message',    note);
          data.append('transcript', transcript);
          const res  = await fetch(leapChat.ajaxUrl, { method: 'POST', body: data });
          const json = await res.json();
          wrap.remove();
          handoverOpen = false;
          appendMsg('ai', (json.success && json.data.reply)
            ? json.data.reply
            : (json.data || 'Something went wrong — please email info@leapdistributors.com.'));
        } catch (_) {
          sendBtn.disabled = false;
          sendBtn.textContent = 'Send to the team';
          appendMsg('ai', 'Connection error — please email info@leapdistributors.com.');
        }
      });
    });
  }
})();


// ── Floating search (mobile/tablet) ────────────────────────
(function initFabSearch() {
  const wrap    = document.getElementById('leap-fab-search');
  const toggle  = document.getElementById('fabs-toggle');
  const panel   = document.getElementById('fabs-panel');
  const input   = document.getElementById('fabs-input');
  const results = document.getElementById('fabs-results');
  if (!wrap || !toggle || !input) return;

  const index = typeof leapSearchIndex !== 'undefined' ? leapSearchIndex : [];

  function open() {
    wrap.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    setTimeout(() => input.focus(), 50);
  }
  function close() {
    wrap.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
  }

  function queryIndex(q) {
    const terms = q.toLowerCase().trim().split(/\s+/).filter(Boolean);
    if (!terms.length) return [];
    return index.filter(item => {
      const hay = (item.title + ' ' + item.description + ' ' + item.keywords).toLowerCase();
      return terms.every(t => hay.includes(t));
    }).slice(0, 6);
  }
  function highlight(text, q) {
    const esc = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return text.replace(new RegExp(`(${esc})`, 'gi'), '<mark>$1</mark>');
  }
  function render(list, q) {
    if (!list.length) {
      results.innerHTML = '<p class="nav-search__empty">No results found</p>';
      return;
    }
    results.innerHTML = list.map(r =>
      `<a class="nav-search__result" href="${r.url}">
        <span class="nav-search__result-type">${r.type}</span>
        <span class="nav-search__result-title">${highlight(r.title, q)}</span>
        <span class="nav-search__result-desc">${highlight(r.description.slice(0, 80), q)}…</span>
      </a>`
    ).join('');
  }

  toggle.addEventListener('click', () => wrap.classList.contains('is-open') ? close() : open());

  input.addEventListener('input', () => {
    const q = input.value.trim();
    if (q.length < 2) { results.innerHTML = ''; return; }
    render(queryIndex(q), q);
  });

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && wrap.classList.contains('is-open')) close();
  });
  document.addEventListener('click', e => {
    if (wrap.classList.contains('is-open') && !wrap.contains(e.target)) close();
  });
})();


// ── Mobile card carousels — dot indicators + scroll sync ────
// The horizontal swipe/snap behaviour is pure CSS (≤640px). This
// only adds dot indicators and keeps the active dot in sync. Dots
// are hidden by CSS above 640px, so this is a no-op on desktop.
(function () {
  const SELECTORS = [
    '.news-grid',
    '.news-list',
    '.capability-grid',
    '.why-leap__grid',
    '.reason-grid',
  ];

  function initCarousel(track) {
    const cards = Array.from(track.children);
    if (cards.length < 2) return;

    // Build the dots once; CSS controls whether they're visible.
    const dots = document.createElement('div');
    dots.className = 'carousel-dots';

    // Position of a card's left edge relative to the track's content
    // origin (accounts for current scroll). Independent of offsetParent.
    function cardLeft(card) {
      return card.getBoundingClientRect().left - track.getBoundingClientRect().left + track.scrollLeft;
    }

    cards.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'carousel-dots__dot';
      dot.setAttribute('aria-label', 'Go to card ' + (i + 1));
      dot.addEventListener('click', () => {
        const padLeft = parseFloat(getComputedStyle(track).paddingLeft) || 0;
        track.scrollTo({ left: cardLeft(cards[i]) - padLeft, behavior: 'smooth' });
      });
      dots.appendChild(dot);
    });

    track.insertAdjacentElement('afterend', dots);
    const dotEls = Array.from(dots.children);

    function syncActive() {
      const mid = track.clientWidth / 2;
      const trackLeft = track.getBoundingClientRect().left;
      let active = 0;
      cards.forEach((card, i) => {
        if (card.getBoundingClientRect().left - trackLeft <= mid) active = i;
      });
      dotEls.forEach((d, i) => d.classList.toggle('is-active', i === active));
    }

    let ticking = false;
    track.addEventListener('scroll', () => {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(() => { syncActive(); ticking = false; });
    }, { passive: true });

    syncActive();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(SELECTORS.join(',')).forEach(initCarousel);
  });
})();
