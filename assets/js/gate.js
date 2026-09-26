/**
 * The anniversary gate: 1) "Are you still angry?" (Yes runs away)
 * 2) the wishlist, 3) the NDA. Signing sends her on to the love letter + site.
 * Nothing here is sent anywhere; only a "gate-done" flag is kept in the browser.
 */
import { react } from './reactions.js';

const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

/* Screens ---------------------------------------------------------------- */

let current = 1;

function go(n) {
  const from = $(`[data-screen="${current}"]`);
  const to = $(`[data-screen="${n}"]`);
  from.hidden = true;
  from.classList.remove('is-active');
  to.hidden = false;
  to.classList.add('is-active', 'is-entering');
  to.addEventListener('animationend', () => to.classList.remove('is-entering'), { once: true });

  const step = Math.min(n, 4);   // screens 4 & 5 both live under the "Gift" pill
  $$('[data-progress]').forEach((li) => {
    const i = +li.dataset.progress;
    li.classList.toggle('is-current', i === step);
    li.classList.toggle('is-done', i < step);
    if (i === step && li.classList.contains('is-locked')) {
      li.classList.replace('is-locked', 'is-unlocked');
      const lock = li.querySelector('[data-lock]');
      if (lock) lock.textContent = '🎁';
    }
  });

  window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
  const heading = $('h1, h2', to);
  heading.tabIndex = -1;
  heading.focus({ preventScroll: true });
  current = n;
  to.dispatchEvent(new Event('screen:enter'));
}

function say(el, text) {
  el.textContent = text;
  el.classList.remove('is-pop');
  void el.offsetWidth;
  el.classList.add('is-pop');
}

/* Confetti --------------------------------------------------------------- */

const COLORS = ['#FF6FA8', '#FFD23F', '#5AA9FF', '#36CF8A', '#FF8C42', '#FFFFFF'];

function confetti(count = 60) {
  if (reduced) return;
  const box = $('[data-confetti]');
  for (let i = 0; i < count; i++) {
    const bit = document.createElement('i');
    bit.style.left = `${Math.random() * 100}%`;
    bit.style.background = COLORS[i % COLORS.length];
    bit.style.setProperty('--dx', `${(Math.random() - 0.5) * 240}px`);
    bit.style.setProperty('--rot', `${(Math.random() - 0.5) * 1440}deg`);
    bit.style.setProperty('--dur', `${1.8 + Math.random() * 1.6}s`);
    bit.style.animationDelay = `${Math.random() * 0.4}s`;
    if (i % 3 === 0) bit.style.borderRadius = '50%';
    box.appendChild(bit);
    setTimeout(() => bit.remove(), 4200);
  }
}

/* The "No" celebration --------------------------------------------------- */

const HEARTS = ['💖', '💕', '💗', '💓', '😍', '🥰', '💘', '✨'];

function celebrate(then) {
  const overlay = $('[data-celebrate]');
  if (!overlay) { then(); return; }
  const hearts = $('[data-hearts]', overlay);
  const btn = $('[data-celebrate-go]', overlay);
  let done = false;
  let heartTimer;

  overlay.hidden = false;
  confetti(160);
  setTimeout(() => confetti(90), 900);

  // Hearts keep floating up for as long as the party lasts.
  const spawn = () => {
    if (reduced) return;
    for (let i = 0; i < 4; i++) {
      const h = document.createElement('span');
      h.className = 'heart';
      h.textContent = HEARTS[Math.floor(Math.random() * HEARTS.length)];
      h.style.setProperty('--x', `${Math.random() * 100}%`);
      h.style.setProperty('--s', `${1.4 + Math.random() * 2}rem`);
      h.style.setProperty('--dx', `${(Math.random() - .5) * 180}px`);
      h.style.setProperty('--r', `${(Math.random() - .5) * 90}deg`);
      h.style.setProperty('--dur', `${2.6 + Math.random() * 1.8}s`);
      hearts.appendChild(h);
      setTimeout(() => h.remove(), 4600);
    }
  };
  spawn();
  heartTimer = setInterval(spawn, 260);

  const finish = () => {
    if (done) return;
    done = true;
    clearInterval(heartTimer);
    overlay.classList.add('is-leaving');
    setTimeout(() => {
      overlay.hidden = true;
      overlay.classList.remove('is-leaving');
      hearts.replaceChildren();
      then();
    }, 400);
  };

  overlay.addEventListener('click', finish);
  document.addEventListener('keydown', function onKey(e) {
    if (done) { document.removeEventListener('keydown', onKey); return; }
    if (e.key === 'Escape') finish();
  });
  setTimeout(() => btn.focus({ preventScroll: true }), 50);
  setTimeout(finish, 5200);   // moves on by itself if she just watches
}

/* Screen 1 — the runaway "Yes" ------------------------------------------- */

function initQuestion() {
  const yes = $('[data-yes]');
  const no = $('[data-no]');
  const toast = $('[data-toast]');
  const lines = [
    'Nope 🙃', 'Too slow!', 'Wrong button, love.', 'Try the green one →',
    'Error 404: anger not found', 'Nice try, Doc.', 'That button is on vacation.',
    'The other one is right there…', 'Are you sure? Like, sure sure?', 'Just press No 💕',
  ];
  let count = 0;

  function flee(pointerX, pointerY) {
    const r = yes.getBoundingClientRect();

    // First escape: lift it out of the layout exactly where it stands.
    if (!yes.classList.contains('is-running')) {
      yes.style.left = `${r.left}px`;
      yes.style.top = `${r.top}px`;
      yes.classList.add('is-running');
      void yes.offsetWidth;
    }

    const pad = 16;
    const maxX = window.innerWidth - r.width - pad;
    const maxY = window.innerHeight - r.height - pad;
    const noRect = no.getBoundingClientRect();
    let x, y, tries = 0;

    // Pick a spot away from the pointer and not on top of the "No" button.
    do {
      x = pad + Math.random() * Math.max(0, maxX - pad);
      y = pad + Math.random() * Math.max(0, maxY - pad);
      tries++;
    } while (
      tries < 40 && (
        Math.hypot(x + r.width / 2 - pointerX, y + r.height / 2 - pointerY) < 200 ||
        (x < noRect.right + 20 && x + r.width > noRect.left - 20 && y < noRect.bottom + 20 && y + r.height > noRect.top - 20)
      )
    );

    yes.style.left = `${x}px`;
    yes.style.top = `${y}px`;
    say(toast, lines[count++ % lines.length]);
  }

  // Mouse: dodge as soon as the pointer gets close, before it can even hover.
  document.addEventListener('pointermove', (e) => {
    if (current !== 1 || e.pointerType !== 'mouse') return;
    const r = yes.getBoundingClientRect();
    const dist = Math.hypot(e.clientX - (r.left + r.width / 2), e.clientY - (r.top + r.height / 2));
    if (dist < Math.max(r.width, r.height) * 0.9) flee(e.clientX, e.clientY);
  });
  yes.addEventListener('pointerenter', (e) => flee(e.clientX, e.clientY));

  // Touch: it jumps away the moment a finger lands on it.
  yes.addEventListener('pointerdown', (e) => {
    e.preventDefault();
    flee(e.clientX, e.clientY);
  });

  // Keyboard (or a lucky click): still not accepted.
  yes.addEventListener('click', (e) => {
    e.preventDefault();
    const r = yes.getBoundingClientRect();
    flee(r.left, r.top);
  });

  window.addEventListener('resize', () => {
    if (!yes.classList.contains('is-running')) return;
    yes.style.left = `${Math.min(parseFloat(yes.style.left), window.innerWidth - yes.offsetWidth - 16)}px`;
    yes.style.top = `${Math.min(parseFloat(yes.style.top), window.innerHeight - yes.offsetHeight - 16)}px`;
  });

  no.addEventListener('click', () => {
    say(toast, 'Yay! 🎉');
    celebrate(() => go(2));
  });
}

/* Screen 2 — wishlist ---------------------------------------------------- */

function initWishlist() {
  const boxes = $$('input[name="wish"]');
  const toast = $('[data-wish-toast]');
  const next = $('[data-to-nda]');

  boxes.forEach((box) => {
    box.addEventListener('change', () => {
      say(toast, box.checked ? box.dataset.reply : `Removed ${box.value}. The wallet says thank you.`);
      const any = boxes.some((b) => b.checked);
      next.disabled = !any;

      if (boxes.every((b) => b.checked)) {
        say(toast, 'ALL of them?! Okay. Bold. I love that for you.');
        react('faint');
      } else if (box.checked) {
        react('cry', box.value.toLowerCase());
      } else {
        react('happy');
      }
    });
  });

  next.addEventListener('click', () => {
    const picked = boxes.filter((b) => b.checked).map((b) => b.value.toLowerCase());
    const list = picked.length > 1
      ? `${picked.slice(0, -1).join(', ')} and ${picked.at(-1)}`
      : picked[0];
    $('[data-wish-list]').textContent = list;

    // A little card from him first, then the NDA.
    const note = $('[data-note]');
    if (note && typeof note.showModal === 'function') note.showModal();
    else go(3);
  });

  $$('[data-note-answer]').forEach((btn) => {
    btn.addEventListener('click', () => {
      $('[data-note]').close();
      if (btn.dataset.noteAnswer === 'love') {
        confetti(90);
        react('happy', null, 'Knew it 🥹💕');
      } else {
        react('cry', 'default', 'BOTH?! …okay. Noted. 😭');
      }
      setTimeout(() => go(3), reduced ? 0 : 500);
    });
  });
}

/* Screen 3 — the NDA ----------------------------------------------------- */

function initNda() {
  const form = $('[data-sign]');
  const name = $('#sig-name');
  const read = $('[data-sig-read]');
  const btn = $('[data-sign-btn]');
  const preview = $('[data-sig-preview]');
  const toast = $('[data-sign-toast]');
  const rejections = [
    'Rejection rejected. 😌',
    'Legal department says: no.',
    'This button is decorative.',
    'Please see Clause 4.',
    'Objection overruled.',
  ];
  let r = 0;

  // The clause list scrolls on its own; the hint cheers once she reaches the end.
  const box = $('[data-clauses]');
  const hint = $('[data-clauses-hint]');
  box.addEventListener('scroll', () => {
    if (hint.classList.contains('is-done')) return;
    if (box.scrollTop + box.clientHeight >= box.scrollHeight - 8) {
      hint.textContent = '✓ You actually read it all. Impressive.';
      hint.classList.add('is-done');
    }
  }, { passive: true });

  // Only the Wife can sign: her name, big or small X, with or without "Dr.".
  const sigHint = $('[data-sig-hint]');
  const normalize = (s) => s.toLowerCase().replace(/^\s*dr\.?\s*/, '').replace(/[^a-z]/g, '');
  const expected = normalize(name.dataset.expected || '');
  const isHer = () => expected !== '' && normalize(name.value) === expected;

  const update = () => {
    preview.textContent = name.value;
    const typed = normalize(name.value);
    const ok = isHer();

    name.classList.toggle('is-valid', ok);
    name.classList.toggle('is-wrong', typed.length >= 3 && !ok && !expected.startsWith(typed));
    if (ok) sigHint.textContent = '✓ Identity confirmed. Hi, love 💕';
    else if (!typed) sigHint.textContent = '';
    else if (expected.startsWith(typed)) sigHint.textContent = 'Keep going… ✍️';
    else sigHint.textContent = 'Hmm 🤨 only the Wife can sign this. Type your name.';

    btn.disabled = !(ok && read.checked);
  };
  name.addEventListener('input', update);
  read.addEventListener('change', update);

  const begging = ['Please sign 🙏', 'I’ll do the dishes for a month!', 'Pleeeease 🥺', 'I’ll give you the last slice. Forever.'];
  $('[data-reject]').addEventListener('click', () => {
    say(toast, rejections[r % rejections.length]);
    react('cry', 'default', begging[r % begging.length]);
    r++;
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (btn.disabled) return;
    btn.disabled = true;
    say(toast, 'Signed, sealed, delivered. 💌 Unlocking your gift…');
    confetti(140);
    setTimeout(() => go(4), reduced ? 300 : 1600);
  });
}

/* Screen 4 — the scrapbook, the photo viewer, his message ---------------- */

function initGift() {
  const memories = JSON.parse($('#memories-data')?.textContent || '[]');
  const box = $('[data-lightbox]');
  const frame = $('[data-lb-frame]');
  const cap = $('[data-lb-cap]');
  const count = $('[data-lb-count]');
  let index = 0;
  let opener = null;

  function show(i) {
    index = (i + memories.length) % memories.length;
    const m = memories[index];
    frame.replaceChildren();
    if (m.src) {
      const img = new Image();
      img.src = m.src;
      img.alt = m.caption || `Photo ${index + 1}`;
      img.className = 'is-swapping';
      frame.appendChild(img);
    } else {
      const ph = document.createElement('span');
      ph.className = 'polaroid__ph';
      ph.textContent = `Photo ${index + 1}`;
      frame.appendChild(ph);
    }
    cap.textContent = m.caption || '';
    count.textContent = `${index + 1} / ${memories.length}`;
    const many = memories.length > 1;
    $('[data-lb-prev]').hidden = !many;
    $('[data-lb-next]').hidden = !many;
  }

  $$('[data-photo]').forEach((btn) => {
    btn.addEventListener('click', () => {
      opener = btn;
      show(+btn.dataset.photo);
      box.showModal();
    });
  });
  $('[data-lb-prev]').addEventListener('click', () => show(index - 1));
  $('[data-lb-next]').addEventListener('click', () => show(index + 1));
  $('[data-lb-close]').addEventListener('click', () => box.close());
  box.addEventListener('click', (e) => { if (e.target === box) box.close(); });
  box.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') show(index - 1);
    if (e.key === 'ArrowRight') show(index + 1);
  });
  box.addEventListener('close', () => opener?.focus({ preventScroll: true }));

  // Swipe left/right on phones.
  let startX = null;
  box.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
  box.addEventListener('touchend', (e) => {
    if (startX === null) return;
    const dx = e.changedTouches[0].clientX - startX;
    if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
    startX = null;
  });

  initPager();
}

/* Scrapbook pages — 3 photos at a time, no scrolling --------------------- */

function initPager() {
  const book = $('[data-scrapbook]');
  const items = $$('li', book);
  const pages = +book.dataset.pages || 1;
  const prev = $('[data-page-prev]');
  const next = $('[data-page-next]');
  const label = $('[data-page-label]');
  let page = 0;
  let busy = false;

  function render(p, dir) {
    book.dataset.dir = dir;
    items.forEach((li) => {
      const on = +li.dataset.page === p;
      li.hidden = !on;
      li.classList.toggle('is-entering', on);
    });
    $$('[data-dot]').forEach((d) => d.classList.toggle('is-on', +d.dataset.dot === p));
    if (label) label.textContent = `Page ${p + 1} of ${pages} · tap a photo to zoom`;
    prev.disabled = p === 0;
    const last = p === pages - 1;
    next.textContent = last ? 'Read my message 💌' : 'Next ›';
    next.classList.toggle('is-final', last);
    next.setAttribute('aria-label', last ? 'Read his message' : 'Next photos');
    page = p;
  }

  function turn(to, dir) {
    if (busy || to < 0 || to >= pages || to === page) return;
    busy = true;
    book.dataset.dir = dir;
    const leaving = items.filter((li) => +li.dataset.page === page);
    leaving.forEach((li) => { li.classList.remove('is-entering'); li.classList.add('is-leaving'); });
    setTimeout(() => {
      leaving.forEach((li) => li.classList.remove('is-leaving'));
      render(to, dir);
      busy = false;
    }, reduced ? 0 : 260);
  }

  prev.addEventListener('click', () => turn(page - 1, 'prev'));
  next.addEventListener('click', () => {
    if (page === pages - 1) go(5);
    else turn(page + 1, 'next');
  });
  document.addEventListener('keydown', (e) => {
    if (current !== 4 || $('[data-lightbox]').open) return;
    if (e.key === 'ArrowRight') next.click();
    if (e.key === 'ArrowLeft') prev.click();
  });

  // Swipe between pages on phones.
  let sx = null;
  book.addEventListener('touchstart', (e) => { sx = e.touches[0].clientX; }, { passive: true });
  book.addEventListener('touchend', (e) => {
    if (sx === null) return;
    const dx = e.changedTouches[0].clientX - sx;
    if (Math.abs(dx) > 50) (dx < 0 ? () => turn(page + 1, 'next') : () => turn(page - 1, 'prev'))();
    sx = null;
  });

  // First page drops in when the gift screen opens.
  $('[data-screen="4"]').addEventListener('screen:enter', () => render(0, 'next'));
}

/* Screen 5 — the very dramatic message unboxing -------------------------- */

const LOADING = [
  [8,   '📁', 'Opening a very important file…'],
  [23,  '🗜️', 'Compressing 4 years of feelings…'],
  [41,  '🧹', 'Removing snoring evidence…'],
  [58,  '💘', 'Adding extra kilig…'],
  [74,  '💥', 'ERROR: too much love detected. Retrying…', 'error'],
  [52,  '🔁', 'Retrying (with less love)… just kidding, same amount.'],
  [86,  '🔤', 'Spell-checking “Pannal ko ba…”'],
  [97,  '🙏', 'Almost there… please don’t be angry again'],
  [100, '💌', 'Done!'],
];

function heartBurst(x, y, count = 16) {
  if (reduced) return;
  for (let i = 0; i < count; i++) {
    const h = document.createElement('span');
    h.className = 'heart-pop';
    h.textContent = HEARTS[i % HEARTS.length];
    const a = (Math.PI * 2 * i) / count;
    const dist = 90 + Math.random() * 120;
    h.style.left = `${x}px`;
    h.style.top = `${y}px`;
    h.style.setProperty('--dx', `${Math.cos(a) * dist}px`);
    h.style.setProperty('--dy', `${Math.sin(a) * dist - 40}px`);
    h.style.setProperty('--r', `${(Math.random() - .5) * 120}deg`);
    h.style.setProperty('--s', `${1.2 + Math.random() * 1.2}rem`);
    document.body.appendChild(h);
    setTimeout(() => h.remove(), 1500);
  }
}

function initMessage() {
  const screen = $('[data-screen="5"]');
  const loader = $('[data-loader]');
  const fill = $('[data-loader-fill]');
  const bar = $('[data-loader-bar]');
  const pct = $('[data-loader-pct]');
  const icon = $('[data-loader-icon]');
  const status = $('[data-loader-status]');
  const room = $('[data-mailroom]');
  const env = $('[data-env]');
  const toast = $('[data-env-toast]');
  const hint = $('[data-env-hint]');
  const letter = $('[data-letter]');
  const finishWrap = $('[data-finish-wrap]');
  let started = false;
  let taps = 0;
  let opening = false;

  const say = (text) => {
    toast.textContent = text;
    toast.classList.remove('is-pop');
    void toast.offsetWidth;
    toast.classList.add('is-pop');
  };

  // 1) the fake loading bar
  function runLoader() {
    const stepMs = reduced ? 150 : 750;
    LOADING.forEach(([p, ico, text, mood], i) => {
      setTimeout(() => {
        fill.style.width = `${p}%`;
        pct.textContent = `${p}%`;
        bar.setAttribute('aria-valuenow', p);
        icon.textContent = ico;
        status.textContent = text;
        loader.classList.toggle('is-error', mood === 'error');
        if (i === LOADING.length - 1) setTimeout(showMailroom, reduced ? 100 : 700);
      }, i * stepMs);
    });
  }

  // 2) the envelope arrives
  function showMailroom() {
    loader.classList.add('is-leaving');
    setTimeout(() => {
      loader.hidden = true;
      room.hidden = false;
      say('You’ve got mail 💌');
      env.focus({ preventScroll: true });
    }, reduced ? 0 : 350);
  }

  // …and plays hard to get
  // Jump sideways, shrinking a bit, but always stay inside the window.
  function dodge() {
    const body = env.closest('.window__body').getBoundingClientRect();
    const scale = 0.75;
    const room = Math.max(0, (body.width - env.offsetWidth * scale) / 2 - 12);
    const x = (Math.random() > .5 ? 1 : -1) * (room * (0.6 + Math.random() * 0.4));
    const y = 20 + Math.random() * 30;
    env.style.setProperty('--x', `${x.toFixed(0)}px`);
    env.style.setProperty('--y', `${y.toFixed(0)}px`);
    env.style.setProperty('--r', `${((Math.random() - .5) * 30).toFixed(0)}deg`);
    env.style.setProperty('--s', String(scale));
  }

  env.addEventListener('click', () => {
    if (opening) return;
    taps++;

    if (taps === 1) {
      dodge();
      say('Hmm… too easy. Catch me first 😏');
      hint.textContent = 'Tap it again';
      react('happy', null, 'Hehe, pakipot muna 😜');
      return;
    }

    if (taps === 2) {
      env.style.setProperty('--x', '0px');
      env.style.setProperty('--y', '0px');
      env.style.setProperty('--r', '0deg');
      env.style.setProperty('--s', '1');
      env.classList.remove('is-spin');
      void env.offsetWidth;
      env.classList.add('is-spin');
      say('Wait — are you sure? It’s VERY cheesy 🧀');
      hint.textContent = 'Tap if you’re ready for the cheese';
      return;
    }

    // 3rd tap: identity check, then open
    opening = true;
    env.classList.remove('is-spin');
    env.classList.add('is-scanning', 'is-shake');
    say('Scanning fingerprint… 🔍');
    hint.textContent = 'Please hold still (or don’t)';
    setTimeout(() => {
      env.classList.remove('is-scanning', 'is-shake');
      say('Access granted: Wife detected ✅');
      hint.textContent = '';
    }, reduced ? 200 : 1600);
    setTimeout(openEnvelope, reduced ? 400 : 2500);
  });

  // 3) open it up
  function openEnvelope() {
    env.classList.add('is-open');
    say('Opening… 💞');
    const r = env.getBoundingClientRect();
    setTimeout(() => {
      heartBurst(r.left + r.width / 2, r.top + r.height / 3, 20);
      confetti(90);
    }, reduced ? 0 : 500);

    setTimeout(() => {
      room.hidden = true;
      letter.hidden = false;
      $('#his-message').focus({ preventScroll: true });
      setTimeout(() => { finishWrap.hidden = false; }, reduced ? 0 : 1500);
    }, reduced ? 300 : 1900);
  }

  screen.addEventListener('screen:enter', () => {
    if (started) return;
    started = true;
    runLoader();
  });

  // One more hurdle before the gift: the final question.
  $('[data-to-quiz]').addEventListener('click', () => go(6));
}

/* Screen 6 — the final question ------------------------------------------ */

function initQuiz() {
  const form = $('[data-quiz]');
  const input = $('[data-quiz-input]');
  const toast = $('[data-quiz-toast]');
  const tries = $('[data-quiz-tries]');
  const hint = $('[data-quiz-hint]');
  const done = $('[data-quiz-done]');
  const lock = $('[data-quiz-lock]');
  const hints = JSON.parse($('#quiz-hints')?.textContent || '[]');
  const clean = (s) => s.toLowerCase().replace(/[^a-z]/g, '');
  let answer = '';
  try { answer = clean(atob(input.dataset.answer || '')); } catch { /* fine */ }

  const nopes = [
    'Nope 🙅‍♀️',
    'Wrong! Think harder, Doc 🩺',
    'Hmm… are you sure you’re married to me? 🤨',
    'Not even close 😂',
    'Try again, love 😏',
    'The system is judging you 👀',
    'Incorrect. Your husband is disappointed 😭',
  ];
  let wrong = 0;
  let solved = false;

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (solved) return;
    const guess = clean(input.value);
    if (!guess) { say(toast, 'Type something first 😅'); input.focus(); return; }

    if (guess === answer) {
      solved = true;
      input.classList.remove('is-wrong');
      input.classList.add('is-right');
      input.readOnly = true;
      lock.textContent = '🔓';
      lock.classList.add('is-open');
      say(toast, 'Correct! ✅');
      tries.textContent = wrong ? `Solved after ${wrong} wrong ${wrong === 1 ? 'try' : 'tries'} 😏` : 'First try?! 😳';
      hint.hidden = true;
      done.hidden = false;
      confetti(150);
      react('happy', null, 'Hehe 🙈 same, love');
      warmAudio();   // unlock sound now, while we still have her click
      setTimeout(() => $('[data-finish]').focus({ preventScroll: true }), 300);
      // …and then the prank begins by itself.
      setTimeout(startScare, reduced ? 800 : 2400);
      return;
    }

    wrong++;
    input.classList.remove('is-wrong');
    void input.offsetWidth;
    input.classList.add('is-wrong');
    say(toast, nopes[(wrong - 1) % nopes.length]);
    tries.textContent = `Wrong answers: ${wrong}`;
    if (wrong % 3 === 0) react('cry', 'default', wrong >= 6 ? 'You really forgot?! 😭' : 'Seriously?! 😭');

    // A hint after every 3 wrong answers, each more obvious than the last.
    const level = Math.floor(wrong / 3);
    if (level > 0 && hints.length) {
      hint.textContent = hints[Math.min(level, hints.length) - 1];
      hint.hidden = false;
      hint.classList.remove('is-new');
      void hint.offsetWidth;
      hint.classList.add('is-new');
    }
    input.select();
  });

  $('[data-screen="6"]').addEventListener('screen:enter', () => {
    setTimeout(() => input.focus({ preventScroll: true }), 400);
  });

  // "Open my gift" starts the prank right away (it also starts by itself).
  $('[data-finish]').addEventListener('click', () => { warmAudio(); startScare(); });

  // After the laugh: on to the love letter, then her site.
  $('[data-finish-real]').addEventListener('click', () => {
    // A one-time pass: the site lets her in once, then any refresh starts over at the gate.
    try { sessionStorage.setItem('gate-pass', '1'); } catch { /* fine */ }
    location.assign('./');
  });
}

/* The prank: 5-4-3-2-1 → BOO! → HAHAHA ------------------------------------ */

let audio = null;
const soundOn = () => $('[data-scare]')?.dataset.sound === '1';

function warmAudio() {
  if (!soundOn()) return;
  const AC = window.AudioContext || window.webkitAudioContext;
  if (!audio && AC) audio = new AC();
  if (audio?.state === 'suspended') audio.resume();
}

function beep(freq, dur = 0.09, vol = 0.15, type = 'square') {
  if (!audio) return;
  const t = audio.currentTime;
  const o = audio.createOscillator();
  const g = audio.createGain();
  o.type = type;
  o.frequency.value = freq;
  g.gain.setValueAtTime(vol, t);
  g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
  o.connect(g).connect(audio.destination);
  o.start(t);
  o.stop(t + dur + 0.02);
}

function thump(at = 0, vol = 0.6) {
  if (!audio) return;
  const t = audio.currentTime + at;
  const o = audio.createOscillator();
  const g = audio.createGain();
  o.type = 'sine';
  o.frequency.setValueAtTime(110, t);
  o.frequency.exponentialRampToValueAtTime(40, t + 0.25);
  g.gain.setValueAtTime(vol, t);
  g.gain.exponentialRampToValueAtTime(0.0001, t + 0.3);
  o.connect(g).connect(audio.destination);
  o.start(t);
  o.stop(t + 0.35);
}

// A cartoon scream: a noise burst sweeping down, a wobbling screech and a low hit.
function scream() {
  if (!audio) return;
  const t = audio.currentTime;
  const master = audio.createGain();
  master.gain.value = 0.55;
  master.connect(audio.destination);

  const len = Math.floor(audio.sampleRate * 1.2);
  const buf = audio.createBuffer(1, len, audio.sampleRate);
  const data = buf.getChannelData(0);
  for (let i = 0; i < len; i++) data[i] = Math.random() * 2 - 1;
  const noise = audio.createBufferSource();
  noise.buffer = buf;
  const band = audio.createBiquadFilter();
  band.type = 'bandpass';
  band.Q.value = 1.3;
  band.frequency.setValueAtTime(2600, t);
  band.frequency.exponentialRampToValueAtTime(650, t + 1.1);
  const ng = audio.createGain();
  ng.gain.setValueAtTime(0.0001, t);
  ng.gain.exponentialRampToValueAtTime(0.8, t + 0.03);
  ng.gain.exponentialRampToValueAtTime(0.0001, t + 1.2);
  noise.connect(band).connect(ng).connect(master);
  noise.start(t);
  noise.stop(t + 1.25);

  const screech = audio.createOscillator();
  screech.type = 'sawtooth';
  screech.frequency.setValueAtTime(1150, t);
  screech.frequency.exponentialRampToValueAtTime(300, t + 1.1);
  const wobble = audio.createOscillator();
  const wobbleAmt = audio.createGain();
  wobble.frequency.value = 26;
  wobbleAmt.gain.value = 70;
  wobble.connect(wobbleAmt).connect(screech.frequency);
  const sg = audio.createGain();
  sg.gain.setValueAtTime(0.0001, t);
  sg.gain.exponentialRampToValueAtTime(0.35, t + 0.03);
  sg.gain.exponentialRampToValueAtTime(0.0001, t + 1.15);
  screech.connect(sg).connect(master);
  screech.start(t);
  wobble.start(t);
  screech.stop(t + 1.2);
  wobble.stop(t + 1.2);

  thump(0, 1);
}

let scaring = false;

function startScare() {
  if (scaring) return;
  scaring = true;

  const overlay = $('[data-scare]');
  const countWrap = $('[data-count-wrap]');
  const num = $('[data-count]');
  const label = $('.scare__label', overlay);
  const boo = $('[data-boo]');
  const lol = $('[data-lol]');

  overlay.hidden = false;
  document.body.style.overflow = 'hidden';

  const show = (n) => {
    num.textContent = n;
    num.classList.remove('is-tick');
    void num.offsetWidth;
    num.classList.add('is-tick');
    num.classList.toggle('is-late', n <= 2);
    overlay.style.setProperty('--dark', ((5 - n) / 4).toFixed(2));
    overlay.classList.toggle('is-heartbeat', n <= 3 && !reduced);
    beep(520 - (5 - n) * 50, 0.1, 0.12);
    if (n <= 3) { thump(0, 0.35); thump(0.18, 0.25); }
  };

  let n = 5;
  show(n);
  const timer = setInterval(() => {
    n--;
    if (n >= 1) { show(n); return; }
    clearInterval(timer);

    // The pause after "1"… too quiet…
    num.textContent = '';
    label.textContent = '…';
    overlay.classList.remove('is-heartbeat');
    setTimeout(goBoo, reduced ? 400 : 1500);
  }, 1000);

  function goBoo() {
    countWrap.hidden = true;
    boo.hidden = false;
    overlay.classList.add('is-boo');
    scream();
    try { navigator.vibrate?.([250, 80, 350]); } catch { /* fine */ }
    setTimeout(goLol, reduced ? 1200 : 1900);
  }

  function goLol() {
    boo.hidden = true;
    overlay.classList.remove('is-boo');
    overlay.classList.add('is-lol');
    lol.hidden = false;
    confetti(170);
    setTimeout(() => confetti(90), 900);
    beep(660, 0.12, 0.1, 'triangle');
    setTimeout(() => beep(880, 0.16, 0.1, 'triangle'), 130);
    setTimeout(() => $('[data-finish-real]').focus({ preventScroll: true }), 900);
  }
}

initQuestion();
initWishlist();
initNda();
initGift();
initMessage();
initQuiz();
