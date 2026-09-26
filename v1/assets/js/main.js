import { initTheme } from './modules/theme.js';
import { initHeader, initMenu, initActiveNav } from './modules/nav.js';
import { initIntro } from './modules/intro.js';
import { initReveal } from './modules/reveal.js';
import { initCare } from './modules/care.js';
import { initConsult } from './modules/consult.js';
import { initParallax } from './modules/parallax.js';

window.__siteBooted = true;

initTheme();
initHeader();
initMenu();
initActiveNav();
initCare();
initConsult();

// The page choreography starts once the opening letter has been dismissed
// (or immediately, for returning visitors).
initIntro({
  onReveal() {
    document.documentElement.classList.add('is-ready');
    initReveal();
    initParallax();
  },
});
