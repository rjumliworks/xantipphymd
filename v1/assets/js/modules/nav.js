const root = document.documentElement;

/** Compact the header once the page has moved. */
export function initHeader() {
  const header = document.querySelector('[data-header]');
  if (!header) return;

  let ticking = false;
  const update = () => {
    header.classList.toggle('is-compact', window.scrollY > 40);
    ticking = false;
  };

  window.addEventListener('scroll', () => {
    if (!ticking) { requestAnimationFrame(update); ticking = true; }
  }, { passive: true });
  update();
}

/** Full-screen menu for small screens. */
export function initMenu() {
  const btn = document.querySelector('[data-menu-toggle]');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;

  const background = [document.getElementById('main'), document.querySelector('.site-footer')];
  const desktop = matchMedia('(min-width: 60rem)');
  let hideTimer;

  const isOpen = () => btn.getAttribute('aria-expanded') === 'true';

  function open() {
    clearTimeout(hideTimer);
    menu.hidden = false;
    menu.offsetHeight; // commit the closed state so the reveal transitions
    menu.classList.add('is-open');
    btn.setAttribute('aria-expanded', 'true');
    btn.querySelector('.menu-toggle__label').textContent = 'Close';
    root.classList.add('menu-open');
    background.forEach((el) => el && (el.inert = true));
    menu.querySelector('a')?.focus({ preventScroll: true });
  }

  function close({ restoreFocus = true } = {}) {
    if (!isOpen()) return;
    menu.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    btn.querySelector('.menu-toggle__label').textContent = 'Menu';
    root.classList.remove('menu-open');
    background.forEach((el) => el && (el.inert = false));
    hideTimer = setTimeout(() => { menu.hidden = true; }, 750);
    if (restoreFocus) btn.focus({ preventScroll: true });
  }

  btn.addEventListener('click', () => (isOpen() ? close() : open()));
  menu.addEventListener('click', (e) => {
    if (e.target.closest('a')) close({ restoreFocus: false });
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
  desktop.addEventListener('change', (e) => {
    if (e.matches) close({ restoreFocus: false });
  });
}

/** Mark the nav link for the section currently being read. */
export function initActiveNav() {
  const sections = document.querySelectorAll('[data-nav-section]');
  const links = document.querySelectorAll('[data-nav-link]');
  if (!sections.length || !('IntersectionObserver' in window)) return;

  const setCurrent = (id) => {
    links.forEach((a) => {
      if (a.dataset.navLink === id) a.setAttribute('aria-current', 'true');
      else a.removeAttribute('aria-current');
    });
  };

  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) setCurrent(entry.target.dataset.navSection);
    });
  }, { rootMargin: '-45% 0px -50% 0px' });

  sections.forEach((s) => io.observe(s));

  // Nothing is "current" while the hero or the letter is in view.
  const neutral = document.querySelectorAll('#top, #letter');
  const io2 = new IntersectionObserver((entries) => {
    entries.forEach((entry) => { if (entry.isIntersecting) setCurrent(null); });
  }, { rootMargin: '-45% 0px -50% 0px' });
  neutral.forEach((s) => io2.observe(s));
}
