const toggle = document.querySelector('.menu');
toggle?.addEventListener('click', () => {
  const open = toggle.getAttribute('aria-expanded') !== 'true';
  toggle.setAttribute('aria-expanded', String(open));
  document.querySelector('#navigation').classList.toggle('open', open);
});

// Reveal content once, with a short stagger for adjacent catalogue items.
(() => {
  const scope = document.querySelector('.dp-home, body.page-template-template-products #main, body.post-type-archive-dp_product #main, body.tax-dp_category #main');
  if (!scope) return;
  scope.classList.add('dp-motion');
  const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (preference.matches || !('IntersectionObserver' in window)) return;
  const targets = scope.querySelectorAll('.home-heading, .section-head, .service-intro, .intro-image, .category-tile, .card, .news-card, .brand-names > div, .service-accordion details, .cta, .catalog-head, .page-heading, .page-lead, .filters');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(({ target, isIntersecting }) => {
      if (!isIntersecting) return;
      target.classList.add('dp-revealed');
      observer.unobserve(target);
    });
  }, { threshold: 0.08 });
  targets.forEach((target) => {
    const siblings = [...target.parentElement.children];
    target.style.setProperty('--reveal-delay', `${Math.min(siblings.indexOf(target) % 4, 3) * 70}ms`);
    target.classList.add('dp-reveal');
    observer.observe(target);
  });
  // Keyboard focus must never land on visually hidden content.
  scope.addEventListener('focusin', (event) => {
    const target = event.target.closest('.dp-reveal');
    if (target) {
      target.classList.add('dp-revealed');
      observer.unobserve(target);
    }
  });
  preference.addEventListener('change', ({ matches }) => {
    if (matches) {
      targets.forEach((target) => target.classList.add('dp-revealed'));
      observer.disconnect();
    }
  });
})();

document.querySelectorAll('.home-slider').forEach((slider) => {
  const slides = [...slider.querySelectorAll('.home-slide')];
  const dots = [...slider.querySelectorAll('[data-slide-to]')];
  if (slides.length < 2) return;
  let current = 0;
  let timer;
  const schedule = () => {
    window.clearTimeout(timer);
    if (document.hidden || slider.querySelector(':focus-visible')) return;
    timer = window.setTimeout(() => show(current + 1), 3000);
  };
  const show = (index) => {
    current = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => { slide.hidden = i !== current; });
    dots.forEach((dot, i) => dot.setAttribute('aria-pressed', String(i === current)));
    schedule();
  };
  slider.addEventListener('focusin', schedule);
  slider.addEventListener('focusout', () => window.setTimeout(schedule, 0));
  document.addEventListener('visibilitychange', schedule);
  slider.querySelector('[data-slide-prev]')?.addEventListener('click', () => show(current - 1));
  slider.querySelector('[data-slide-next]')?.addEventListener('click', () => show(current + 1));
  dots.forEach((dot, i) => dot.addEventListener('click', () => show(i)));
  slider.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
      event.preventDefault();
      show(current + (event.key === 'ArrowRight' ? 1 : -1));
    }
  });
  schedule();
});
