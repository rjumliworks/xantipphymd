const KEY = 'theme';
const root = document.documentElement;
const systemDark = matchMedia('(prefers-color-scheme: dark)');
const META_COLORS = { light: '#F4F0E8', dark: '#151513' };

let animTimer;

function stored() {
  try { return localStorage.getItem(KEY); } catch { return null; }
}

function apply(theme, animate) {
  if (animate) {
    root.classList.add('theme-anim');
    clearTimeout(animTimer);
    animTimer = setTimeout(() => root.classList.remove('theme-anim'), 700);
  }

  root.dataset.theme = theme;
  const next = theme === 'dark' ? 'light' : 'dark';

  document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.setAttribute('aria-label', `Switch to ${next} theme`);
  });
  document.querySelectorAll('[data-theme-label]').forEach((el) => {
    el.textContent = next === 'dark' ? 'Dark' : 'Light';
  });
  document.querySelector('[data-theme-color]')?.setAttribute('content', META_COLORS[theme]);
}

export function initTheme() {
  apply(root.dataset.theme === 'dark' ? 'dark' : 'light', false);

  document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
      apply(next, true);
      try { localStorage.setItem(KEY, next); } catch { /* private mode: still works for this visit */ }
    });
  });

  // Follow the OS until the visitor makes an explicit choice.
  systemDark.addEventListener('change', (e) => {
    if (!stored()) apply(e.matches ? 'dark' : 'light', true);
  });
}
