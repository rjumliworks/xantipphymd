/** A single, light parallax on the hero portrait. Moves at most 8% of its frame. */
export function initParallax() {
  const layer = document.querySelector('[data-parallax]');
  if (!layer || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const frame = layer.parentElement;
  let max = 0;
  let ticking = false;

  const measure = () => { max = frame.offsetHeight * 0.08; };
  const update = () => {
    const y = Math.min(window.scrollY * 0.1, max);
    layer.style.transform = `translate3d(0, ${y.toFixed(1)}px, 0)`;
    ticking = false;
  };

  measure();
  update();
  window.addEventListener('resize', measure, { passive: true });
  window.addEventListener('scroll', () => {
    if (window.scrollY > window.innerHeight * 1.2) return;
    if (!ticking) { requestAnimationFrame(update); ticking = true; }
  }, { passive: true });
}
