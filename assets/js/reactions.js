/**
 * Husband reactions for the gate.
 *
 *   react('cry', 'land')   — slides in from a random edge, sobs, tears stream, 💧 rain
 *   react('happy')         — pops in relieved / delighted, sparkles
 *   react('faint')         — tips over
 *
 * Uses assets/img/me*.png when present (see config.php → gate), otherwise an emoji face.
 */
const data = JSON.parse(document.getElementById('reaction-data')?.textContent || '{}');
const photos = data.photos || {};
const eyes = data.eyes || [[38, 44], [62, 44]];
const stage = document.querySelector('[data-reactions]');
const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

const LINES = {
  jewelry: ['My savings… 💸', 'Can it be a Ring Pop? 🍭', 'The jeweler knows my name now…'],
  land:    ['LAND?! I can’t even afford a plant pot…', 'Does a sandbox count? 🏖️', 'Selling a kidney… oh wait, you’re the doctor.'],
  car:     ['Can I at least drive it on weekends?', 'Hot Wheels is a car, right? 🚗', 'There goes my motorcycle fund…'],
  money:   ['Take my wallet… take it all.', 'Bank balance: 0.00', 'I’ll be in the corner, crying in coins. 🪙'],
  happy:   ['Phew 😮‍💨 thank you, love!', 'My wallet says thank you 🥹', 'You’re too kind 🥹'],
  faint:   ['*faints* 😵', 'ALL FOUR?! Calling a doctor… oh wait.'],
  default: ['😭😭😭'],
};
const EMOJI = { cry: '😭', happy: '🥹', faint: '😵' };
const SIDES = ['left', 'right', 'bottom', 'top'];
const STAY = { cry: 2800, happy: 2000, faint: 3000 };

let active = null;
let lastSide = null;

const pick = (list) => list[Math.floor(Math.random() * list.length)];

function dismiss(el, fast = false) {
  if (!el || el.classList.contains('is-out')) return;
  el.classList.remove('is-in');
  el.classList.add('is-out');
  setTimeout(() => el.remove(), fast ? 250 : 600);
}

export function react(mood = 'cry', key = 'default', line) {
  if (!stage) return;
  dismiss(active, true);

  let side;
  do { side = pick(SIDES); } while (side === lastSide);
  lastSide = side;

  const photo = photos[mood === 'cry' ? key : mood] || photos.default;
  const text = line || pick(LINES[mood === 'cry' ? key : mood] || LINES.default);

  const el = document.createElement('div');
  el.className = `me me--${side} me--${mood}`;
  // Spread along the edge it enters from, keeping the whole face on screen.
  const half = 110;
  if (side === 'left' || side === 'right') {
    el.style.top = `${half + Math.random() * Math.max(0, innerHeight - half * 2)}px`;
  } else {
    el.style.left = `${half + Math.random() * Math.max(0, innerWidth - half * 2)}px`;
  }

  const face = photo
    ? `<img src="${photo}" alt="">`
    : `<span class="me__emoji">${EMOJI[mood] || '😭'}</span>`;

  const tears = mood === 'cry'
    ? eyes.map(([x, y], i) => `<span class="tear" style="left:${x}%;top:${y}%;--i:${i}"></span>`).join('')
    : '';

  const extras = mood === 'cry' && !reduced
    ? Array.from({ length: 8 }, (_, i) =>
        `<span class="drop" style="--dx:${(Math.random() - .5) * 160}px;--d:${(i * .22).toFixed(2)}s;--x:${20 + Math.random() * 60}%">💧</span>`).join('')
    : mood === 'happy' && !reduced
      ? Array.from({ length: 5 }, (_, i) =>
          `<span class="spark" style="--a:${i * 72}deg;--d:${(i * .08).toFixed(2)}s">✨</span>`).join('')
      : '';

  el.innerHTML = `
    <div class="me__inner">
      <div class="me__face">${face}${tears}${extras}</div>
      <p class="me__bubble">${text}</p>
    </div>`;

  stage.appendChild(el);
  active = el;
  void el.offsetWidth;
  el.classList.add('is-in');

  setTimeout(() => { dismiss(el); if (active === el) active = null; }, STAY[mood] || 2600);
}
