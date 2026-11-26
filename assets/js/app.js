document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.main-nav');
  if (toggle && nav) toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });
  document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const message = form.getAttribute('data-confirm') || 'Are you sure?';
      if (!window.confirm(message)) event.preventDefault();
    });
  });
  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const input = document.getElementById(button.dataset.passwordToggle);
      if (input) input.type = input.type === 'password' ? 'text' : 'password';
    });
  });
});

// Subtle scroll reveals and header elevation; honours reduced-motion preferences.
document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.site-header');
  const revealTargets = document.querySelectorAll('.hero-grid > div, .section-heading, .feature-grid > *, .grid-3 > *, .grid-2 > *, .stat-card, .wireframe-cta > *');
  revealTargets.forEach((el, index) => {
    el.classList.add('reveal');
    el.style.transitionDelay = `${Math.min(index % 4, 3) * 70}ms`;
  });
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealTargets.forEach(el => observer.observe(el));
  } else {
    revealTargets.forEach(el => el.classList.add('is-visible'));
  }
  const updateHeader = () => header?.classList.toggle('scrolled', window.scrollY > 8);
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });
});

// Homepage counters animate only when the statistic cards enter the viewport.
document.addEventListener('DOMContentLoaded', () => {
  const counters = document.querySelectorAll('.count-up[data-count]');
  const animateCounter = (el) => {
    const target = Number.parseInt(el.dataset.count || '0', 10);
    if (!Number.isFinite(target) || target <= 0) { el.textContent = String(Math.max(0, target || 0)); return; }
    const duration = 1150; const start = performance.now();
    const tick = (now) => {
      const progress = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(target * eased).toLocaleString();
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    counters.forEach(el => { el.textContent = Number(el.dataset.count || 0).toLocaleString(); });
  } else if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => entries.forEach(entry => {
      if (entry.isIntersecting) { animateCounter(entry.target); observer.unobserve(entry.target); }
    }), { threshold: .6 });
    counters.forEach(el => observer.observe(el));
  } else counters.forEach(animateCounter);
});
