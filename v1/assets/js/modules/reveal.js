/** Adds .is-in to [data-reveal] elements as they enter the viewport. The CSS decides how each kind moves. */
export function initReveal() {
  const els = document.querySelectorAll('[data-reveal]');

  if (!('IntersectionObserver' in window)) {
    els.forEach((el) => el.classList.add('is-in'));
    return;
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-in');
      io.unobserve(entry.target);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

  els.forEach((el) => io.observe(el));
}
