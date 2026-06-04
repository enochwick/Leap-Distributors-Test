// Leap Distributors — site interactions

(() => {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Current year
  document.querySelectorAll('[data-year]').forEach(el => {
    el.textContent = String(new Date().getFullYear());
  });

  // Active nav link based on current path
  const path = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('[data-nav-link]').forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    const target = href.split('/').pop();
    if (target === path) link.setAttribute('aria-current', 'page');
  });

  // Mobile drawer
  const nav = document.querySelector('[data-nav]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const overlay = document.querySelector('[data-nav-overlay]');
  const closeBtn = document.querySelector('[data-nav-close]');

  const openDrawer = () => {
    if (!nav) return;
    nav.classList.add('is-open');
    toggle?.setAttribute('aria-expanded', 'true');
    document.body.classList.add('is-locked');
  };
  const closeDrawer = () => {
    if (!nav) return;
    nav.classList.remove('is-open');
    toggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('is-locked');
  };

  toggle?.addEventListener('click', () => {
    nav?.classList.contains('is-open') ? closeDrawer() : openDrawer();
  });
  overlay?.addEventListener('click', closeDrawer);
  closeBtn?.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && nav?.classList.contains('is-open')) closeDrawer();
  });
  // Close drawer when a link inside it is tapped
  nav?.querySelectorAll('.nav__drawer-link, .nav__drawer-cta').forEach(a => {
    a.addEventListener('click', closeDrawer);
  });

  // Reveal on scroll
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length && 'IntersectionObserver' in window && !reducedMotion) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('is-visible'));
  }

  // Better. Together. motion
  const bt = document.querySelector('[data-better-together]');
  if (bt) {
    if (reducedMotion) {
      bt.classList.add('is-visible');
    } else if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            bt.classList.add('is-visible');
            io.unobserve(bt);
          }
        });
      }, { threshold: 0.35 });
      io.observe(bt);
    } else {
      bt.classList.add('is-visible');
    }
  }

  // Founder selector
  const founders = document.querySelector('[data-founders]');
  if (founders) {
    const tabs = founders.querySelectorAll('[data-founder-tab]');
    const panels = founders.querySelectorAll('[data-founder-panel]');
    const indicator = founders.querySelector('[data-founder-indicator]');

    const setActive = (idx) => {
      tabs.forEach((tab, i) => {
        const isActive = i === idx;
        tab.setAttribute('aria-selected', String(isActive));
        tab.setAttribute('tabindex', isActive ? '0' : '-1');
      });
      panels.forEach((panel, i) => panel.classList.toggle('is-active', i === idx));
      if (indicator) {
        const active = tabs[idx];
        const rect = active.getBoundingClientRect();
        const parentRect = active.parentElement.getBoundingClientRect();
        indicator.style.left = `${rect.left - parentRect.left}px`;
        indicator.style.width = `${rect.width}px`;
      }
    };

    tabs.forEach((tab, i) => {
      tab.addEventListener('click', () => setActive(i));
      tab.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
          e.preventDefault();
          const next = (i + 1) % tabs.length;
          tabs[next].focus(); setActive(next);
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
          e.preventDefault();
          const prev = (i - 1 + tabs.length) % tabs.length;
          tabs[prev].focus(); setActive(prev);
        } else if (e.key === 'Home') {
          e.preventDefault();
          tabs[0].focus(); setActive(0);
        } else if (e.key === 'End') {
          e.preventDefault();
          tabs[tabs.length - 1].focus(); setActive(tabs.length - 1);
        }
      });
    });

    requestAnimationFrame(() => setActive(0));
    window.addEventListener('resize', () => {
      const activeIdx = Array.from(tabs).findIndex(t => t.getAttribute('aria-selected') === 'true');
      if (activeIdx >= 0) setActive(activeIdx);
    });
  }

  // Contact form
  const contactForm = document.querySelector('[data-contact-form]');
  if (contactForm) {
    const fields = contactForm.querySelectorAll('[data-field]');
    const confirm = document.querySelector('[data-contact-confirm]');

    const validateField = (input) => {
      const errorEl = input.parentElement.querySelector('.field__error');
      let error = '';
      if (input.required && !input.value.trim()) {
        if (input.name === 'name') error = "We need a name to know who to reply to.";
        else if (input.name === 'email') error = "We need an email to reach you.";
        else if (input.name === 'message') error = "Add a line or two so we know what to follow up on.";
        else if (input.name === 'audience') error = "Pick the option that fits best.";
        else error = "This one's required.";
      } else if (input.type === 'email' && input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
        error = "That email doesn't look quite right. Mind double-checking?";
      }
      if (errorEl) errorEl.textContent = error;
      input.setAttribute('aria-invalid', error ? 'true' : 'false');
      return !error;
    };

    fields.forEach(input => {
      input.addEventListener('blur', () => validateField(input));
      input.addEventListener('input', () => {
        if (input.getAttribute('aria-invalid') === 'true') validateField(input);
      });
    });

    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      let valid = true;
      fields.forEach(input => { if (!validateField(input)) valid = false; });
      if (!valid) {
        const firstInvalid = contactForm.querySelector('[aria-invalid="true"]');
        if (firstInvalid) firstInvalid.focus();
        return;
      }
      contactForm.style.display = 'none';
      if (confirm) {
        confirm.classList.add('is-visible');
        confirm.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  // Newsletter forms
  document.querySelectorAll('[data-newsletter-form]').forEach(form => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const success = form.querySelector('[data-newsletter-success]');
      const rows = form.querySelectorAll('[data-newsletter-row]');
      const emailInput = form.querySelector('input[type="email"]');
      if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        if (success) {
          success.textContent = "That email doesn't look quite right. Mind double-checking?";
          success.style.color = 'var(--accent-persimmon)';
          success.style.display = 'block';
        }
        return;
      }
      rows.forEach(r => r.style.display = 'none');
      if (success) {
        success.textContent = "You're in. Look for us next month.";
        success.style.color = '';
        success.style.display = 'block';
      }
    });
  });
})();
