import { initTheme } from './modules/theme.js';
import { initHeader, initMenu, initActiveNav } from './modules/nav.js';
import { initIntro } from './modules/intro.js';
import { initReveal } from './modules/reveal.js';
import { initForm } from './modules/form.js';

window.__siteBooted = true;

initTheme();
initHeader();
initMenu();
initActiveNav();
initForm();

// The page choreography starts once the opening letter has been dismissed
// (or immediately, for returning visitors).
initIntro({
  onReveal() {
    document.documentElement.classList.add('is-ready');
    initReveal();
  },
});
