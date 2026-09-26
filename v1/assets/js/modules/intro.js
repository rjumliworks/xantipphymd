/**
 * The opening letter.
 *
 *   0.9s  first line
 *   3.8s  first line dims, second line
 *   7.2s  lines lift away, her name rises out of a mask
 *   8.8s  "Open" is offered
 *
 * Enter, Skip or Escape dismisses it: the text lifts, the dark sheet wipes
 * upward, and onReveal() starts the page's own choreography underneath.
 */
const TIMELINE = [
  [900,  (el) => el.lines[0]?.classList.add('is-in')],
  [3800, (el) => { el.lines[0]?.classList.add('is-dim'); el.lines[1]?.classList.add('is-in'); }],
  [7200, (el) => el.intro.classList.add('is-name')],
  [8800, (el) => el.intro.classList.add('is-invite')],
];

export function initIntro({ onReveal }) {
  const root = document.documentElement;
  const intro = document.getElementById('intro');

  if (!root.classList.contains('intro-on') || !intro) {
    intro?.remove();
    requestAnimationFrame(onReveal);
    return;
  }

  const el = {
    intro,
    lines: intro.querySelectorAll('[data-intro-line]'),
    enter: intro.querySelector('[data-intro-enter]'),
    skip: intro.querySelector('[data-intro-skip]'),
  };
  const site = document.querySelectorAll('[data-site], .skip-link');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

  site.forEach((node) => { node.inert = true; });
  // Focus the letter itself (no ring); Tab then reaches Skip / Open.
  intro.focus({ preventScroll: true });
  window.scrollTo(0, 0);

  const timers = TIMELINE.map(([at, step]) => setTimeout(() => step(el), at));
  let leaving = false;

  function leave() {
    if (leaving) return;
    leaving = true;
    timers.forEach(clearTimeout);
    document.removeEventListener('keydown', onKey);

    try { localStorage.setItem('intro-seen', '1'); } catch { /* fine */ }
    if (location.search.includes('letter')) history.replaceState(null, '', location.pathname + location.hash);

    intro.classList.add('is-leaving');
    site.forEach((node) => { node.inert = false; });

    // Start the page underneath as the sheet begins to lift.
    setTimeout(onReveal, reduced ? 100 : 550);
    setTimeout(() => {
      root.classList.remove('intro-on');
      intro.remove();
    }, reduced ? 800 : 1700);
  }

  function onKey(e) {
    if (e.key === 'Escape') leave();
    // keep focus inside the letter while it is open
    if (e.key === 'Tab') {
      const focusables = [el.enter, el.skip].filter((b) => b.offsetParent !== null && getComputedStyle(b).visibility !== 'hidden');
      const i = focusables.indexOf(document.activeElement);
      e.preventDefault();
      const next = focusables[(i + (e.shiftKey ? -1 : 1) + focusables.length) % focusables.length];
      next?.focus();
    }
  }

  el.enter.addEventListener('click', leave);
  el.skip.addEventListener('click', leave);
  document.addEventListener('keydown', onKey);
}
