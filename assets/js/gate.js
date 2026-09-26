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

  $$('[data-progress]').forEach((li) => {
    const i = +li.dataset.progress;
    li.classList.toggle('is-current', i === n);
    li.classList.toggle('is-done', i < n);
    if (i === n && li.classList.contains('is-locked')) {
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

  // The envelope: tap to open his message.
  const openBtn = $('[data-envelope-open]');
  const paper = $('#his-message');
  const finishWrap = $('[data-finish-wrap]');
  openBtn.addEventListener('click', () => {
    openBtn.setAttribute('aria-expanded', 'true');
    openBtn.hidden = true;
    paper.hidden = false;
    paper.classList.add('is-opening');
    paper.tabIndex = -1;
    paper.focus({ preventScroll: true });
    paper.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
    confetti(60);
    setTimeout(() => { finishWrap.hidden = false; }, reduced ? 0 : 1200);
  });

  // On to the love letter, then her site.
  $('[data-finish]').addEventListener('click', () => {
    // A one-time pass: the site lets her in once, then any refresh starts over at the gate.
    try { sessionStorage.setItem('gate-pass', '1'); } catch { /* fine */ }
    location.assign('./');
  });
}

initQuestion();
initWishlist();
initNda();
initGift();
