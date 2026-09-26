/**
 * Little cartoon sound effects for the gate — all synthesized with the Web Audio
 * API (no files). Browsers only allow sound after a tap, so the first tap anywhere
 * unlocks it. A 🔊/🔇 button (remembered in the browser) mutes everything.
 */
const KEY = 'gate-sound';
let ctx = null;
let muted = false;
try { muted = localStorage.getItem(KEY) === 'off'; } catch { /* fine */ }

export const soundEnabled = () => !muted && document.body.dataset.sound !== '0';

export function getCtx() {
  if (!soundEnabled()) return null;
  const AC = window.AudioContext || window.webkitAudioContext;
  if (!ctx && AC) ctx = new AC();
  if (ctx?.state === 'suspended') ctx.resume();
  return ctx;
}

/* Building blocks ------------------------------------------------------- */

function tone({ freq = 440, to = null, type = 'sine', dur = 0.15, vol = 0.18, at = 0, attack = 0.005 }) {
  const c = getCtx();
  if (!c) return;
  const t = c.currentTime + at;
  const o = c.createOscillator();
  const g = c.createGain();
  o.type = type;
  o.frequency.setValueAtTime(freq, t);
  if (to) o.frequency.exponentialRampToValueAtTime(to, t + dur);
  g.gain.setValueAtTime(0.0001, t);
  g.gain.exponentialRampToValueAtTime(vol, t + attack);
  g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
  o.connect(g).connect(c.destination);
  o.start(t);
  o.stop(t + dur + 0.03);
  return o;
}

function noise({ dur = 0.3, vol = 0.2, at = 0, from = 3000, to = 800, q = 1 }) {
  const c = getCtx();
  if (!c) return;
  const t = c.currentTime + at;
  const len = Math.max(1, Math.floor(c.sampleRate * dur));
  const buf = c.createBuffer(1, len, c.sampleRate);
  const d = buf.getChannelData(0);
  for (let i = 0; i < len; i++) d[i] = Math.random() * 2 - 1;
  const src = c.createBufferSource();
  src.buffer = buf;
  const f = c.createBiquadFilter();
  f.type = 'bandpass';
  f.Q.value = q;
  f.frequency.setValueAtTime(from, t);
  f.frequency.exponentialRampToValueAtTime(to, t + dur);
  const g = c.createGain();
  g.gain.setValueAtTime(0.0001, t);
  g.gain.exponentialRampToValueAtTime(vol, t + 0.01);
  g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
  src.connect(f).connect(g).connect(c.destination);
  src.start(t);
  src.stop(t + dur + 0.02);
}

/* The effects ------------------------------------------------------------ */

export const sfx = {
  // the runaway "Yes" button
  boing() {
    const o = tone({ freq: 180, to: 520, type: 'triangle', dur: 0.28, vol: 0.2 });
    const c = getCtx();
    if (o && c) {   // wobble
      const lfo = c.createOscillator(); const amt = c.createGain();
      lfo.frequency.value = 18; amt.gain.value = 40;
      lfo.connect(amt).connect(o.frequency);
      lfo.start(); lfo.stop(c.currentTime + 0.3);
    }
  },
  // ticking a box, opening a card
  pop() {
    tone({ freq: 900, to: 380, type: 'sine', dur: 0.09, vol: 0.22 });
  },
  // un-ticking
  unpop() {
    tone({ freq: 380, to: 900, type: 'sine', dur: 0.09, vol: 0.16 });
  },
  // a happy little arpeggio
  yay() {
    [523, 659, 784, 1047].forEach((f, i) => tone({ freq: f, type: 'triangle', dur: 0.18, vol: 0.14, at: i * 0.08 }));
  },
  // relieved / sweet
  chime() {
    tone({ freq: 880, type: 'sine', dur: 0.35, vol: 0.12 });
    tone({ freq: 1320, type: 'sine', dur: 0.45, vol: 0.08, at: 0.09 });
  },
  // his crying: three wobbly falling "wahh"s
  sob() {
    [0, 0.32, 0.64].forEach((at, i) => {
      tone({ freq: 520 - i * 40, to: 300 - i * 30, type: 'sawtooth', dur: 0.28, vol: 0.06, at, attack: 0.03 });
      tone({ freq: 523 - i * 40, to: 302 - i * 30, type: 'triangle', dur: 0.28, vol: 0.08, at, attack: 0.03 });
    });
  },
  // fainting: a long slide-whistle down, then a thud
  faint() {
    tone({ freq: 1200, to: 120, type: 'sine', dur: 0.9, vol: 0.16 });
    tone({ freq: 90, to: 40, type: 'sine', dur: 0.25, vol: 0.5, at: 0.9 });
  },
  // wrong answer
  buzz() {
    tone({ freq: 140, type: 'square', dur: 0.14, vol: 0.09 });
    tone({ freq: 110, type: 'square', dur: 0.2, vol: 0.09, at: 0.15 });
  },
  // right answer
  ding() {
    tone({ freq: 1568, type: 'sine', dur: 0.6, vol: 0.14 });
    tone({ freq: 2093, type: 'sine', dur: 0.7, vol: 0.08, at: 0.06 });
  },
  // page turn / spin
  whoosh() {
    noise({ dur: 0.35, vol: 0.18, from: 600, to: 3200, q: 0.8 });
  },
  // envelope paper
  rustle() {
    [0, 0.12, 0.26].forEach((at) => noise({ dur: 0.12, vol: 0.14, at, from: 4000, to: 2500, q: 2 }));
  },
  // fingerprint scanner
  scan() {
    [0, 0.25, 0.5, 0.75, 1.0].forEach((at, i) => tone({ freq: 1400 + i * 120, type: 'square', dur: 0.06, vol: 0.05, at }));
  },
  // loading bar tick
  tick() {
    tone({ freq: 1200, type: 'square', dur: 0.03, vol: 0.04 });
  },
  // loading bar "error"
  error() {
    tone({ freq: 220, to: 110, type: 'sawtooth', dur: 0.35, vol: 0.08 });
  },
};

/* Unlock on first tap + the mute button ------------------------------------ */

export function initSound() {
  const unlock = () => { getCtx(); };
  window.addEventListener('pointerdown', unlock, { once: true, capture: true });
  window.addEventListener('keydown', unlock, { once: true, capture: true });

  if (document.body.dataset.sound === '0') return;

  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'sound-toggle';
  const render = () => {
    btn.textContent = muted ? '🔇' : '🔊';
    btn.setAttribute('aria-label', muted ? 'Turn sound on' : 'Turn sound off');
    btn.setAttribute('aria-pressed', String(!muted));
  };
  btn.addEventListener('click', () => {
    muted = !muted;
    try { localStorage.setItem(KEY, muted ? 'off' : 'on'); } catch { /* fine */ }
    if (muted) ctx?.suspend(); else { getCtx(); sfx.pop(); }
    render();
  });
  render();
  document.body.appendChild(btn);
}
