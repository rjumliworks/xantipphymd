/**
 * The Internal Medicine index.
 *
 * Desktop with a fine pointer: hovering or focusing a row makes it "active" —
 * the row shifts, its rule is drawn, neighbours dim, and the sticky preview
 * on the right swaps in its description. (The same text also sits, visually
 * hidden, beneath each row for screen readers.)
 *
 * Touch / small screens: a plain accordion with aria-expanded.
 */
const PREVIEW_MQ = '(min-width: 64rem) and (hover: hover) and (pointer: fine)';

export function initCare() {
  const list = document.querySelector('[data-care]');
  if (!list) return;

  const items = [...list.querySelectorAll('.care-item')];
  const preview = document.querySelector('[data-care-preview-body]');
  const mq = matchMedia(PREVIEW_MQ);
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  let activeIndex = -1;

  function setActive(index) {
    if (index === activeIndex) return;
    activeIndex = index;
    items.forEach((item, i) => item.classList.toggle('is-active', i === index));

    const item = items[index];
    if (!preview || !item) return;

    preview.querySelector('.care-preview__num').textContent = item.querySelector('.care-item__num').textContent;
    preview.querySelector('.care-preview__title').innerHTML = item.querySelector('.care-item__title').innerHTML;
    preview.querySelector('.care-preview__text').innerHTML = item.querySelector('.care-item__panel p').innerHTML;

    if (!reduced.matches) {
      preview.animate(
        [{ opacity: 0, transform: 'translate3d(0, 10px, 0)' }, { opacity: 1, transform: 'none' }],
        { duration: 650, easing: 'cubic-bezier(.16, 1, .3, 1)' }
      );
    }
  }

  function toggle(item, force) {
    const btn = item.querySelector('.care-item__btn');
    const open = force ?? !item.classList.contains('is-open');
    item.classList.toggle('is-open', open);
    btn.setAttribute('aria-expanded', String(open));
  }

  function setMode() {
    items.forEach((item) => toggle(item, false));
    list.classList.remove('is-hovering');
    activeIndex = -1;
    items.forEach((item) => item.classList.remove('is-active'));

    items.forEach((item) => {
      const btn = item.querySelector('.care-item__btn');
      if (mq.matches) btn.removeAttribute('aria-expanded');
      else btn.setAttribute('aria-expanded', 'false');
    });

    if (mq.matches) setActive(0);
  }

  items.forEach((item, i) => {
    const btn = item.querySelector('.care-item__btn');

    item.addEventListener('pointerenter', () => {
      if (!mq.matches) return;
      list.classList.add('is-hovering');
      setActive(i);
    });
    btn.addEventListener('focus', () => { if (mq.matches) setActive(i); });
    btn.addEventListener('click', () => {
      if (mq.matches) setActive(i);
      else toggle(item);
    });
  });

  list.addEventListener('pointerleave', () => list.classList.remove('is-hovering'));
  mq.addEventListener('change', setMode);
  setMode();
}
