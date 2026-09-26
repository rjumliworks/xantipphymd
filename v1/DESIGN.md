# Dr. Xantipphy — Design Concept

An editorial physician website that opens as a letter.

---

## 0. Stack (and why)

| Layer | Choice | Why |
|---|---|---|
| Serving | **Plain PHP templates** (`index.php`), served by Herd at `http://xantipphymd.test` | No build step. One config file holds every piece of copy, so replacing placeholders never means touching markup. Easy to lift into Laravel later (the template maps 1:1 to a Blade view). |
| Styles | **One hand-written CSS file** with design tokens | Two themes are just two token sets. No framework, so no generic look. |
| Behaviour | **Vanilla ES modules** (`assets/js/modules/*`) | Each interaction is its own small file. The motion is CSS transitions plus a little Web Animations API, which is enough for this restraint. GSAP/Motion would add weight without adding anything visible. |
| Dependencies | **Zero.** Two Google Fonts. | Nothing to update, nothing to break. |

Future production (consultation requests) should be a proper backend: Laravel with encrypted storage, audit logging and a compliant email/telehealth provider. The current form is explicitly a **visual prototype**: it sends nothing and stores nothing.

---

## 1. Design concept: *"The Quiet Chart"*

A physician's notes are precise, spare and humane. The site borrows that idea: **typography carries the personality**, hairline rules create structure, and whitespace does the work that decoration usually does.

- Editorial grid (12 columns), used asymmetrically. Most sections break the grid in a different way.
- Numbers and small uppercase labels act as quiet metadata, the way a medical journal or a gallery catalogue uses them.
- There is one accent colour, used rarely: a muted clinical sage. It avoids hospital blue.
- The romance is kept to the opening letter and a single "Behind the physician" section near the end. Everything else is a credible professional site.

## 2. Colour system

| Token | Light | Dark | Use |
|---|---|---|---|
| `--bg` | `#F4F0E8` ivory | `#151513` warm charcoal | page |
| `--bg-2` | `#ECE7DC` | `#1C1C19` | hover wash, placeholders |
| `--paper` | `#EFE9DD` | `#191916` | the personal letter |
| `--ink` | `#1C1B18` | `#ECE7DE` | headings, primary text |
| `--ink-2` | `#45423B` | `#BEB8AC` | body copy |
| `--muted` | `#6F6A60` | `#8F897D` | metadata (≥ 4.5 : 1) |
| `--rule` | `#D9D3C6` | `#2C2B27` | hairlines |
| `--accent` | `#3E5B4D` sage | `#A3BDAD` pale sage | focus, numbers, one heart |
| `--inv-*` | charcoal block | raised charcoal | consultation section |

Dark mode is not an inversion. The accent gets lighter and less saturated, body copy drops to a warm grey so white text never glares, and the consultation block becomes a raised surface instead of an inverted one.

## 3. Typography

- **Newsreader** (variable, optical sizes) for display and editorial text. It is a real reading serif with a beautiful italic. Weight 300 at large sizes and italic for emphasis (`listening.`).
- **Hanken Grotesk** for the interface and body text. It is neutral but warm, has good figures and suits the Swiss feel.
- Scale: display 8rem → statement 5rem → h2 3.5rem → h3 1.9rem → lead 1.375rem → body 1.0625rem → meta 0.72rem uppercase tracked. Only the hero name and one statement per page go huge.
- Line lengths are capped at 34–38ch for ledes and about 64ch for prose. Headings are left-aligned. Only the opening letter is centred as a composition, and its text is still left-aligned.

## 4. Navigation

`Xantipphy [Surname] MD` ——— `About  Care  Education  Consultation  Contact  |  ◐`

- Links underline themselves from left to right on hover. The current section keeps its underline.
- Past 40px of scroll the bar compacts from 84px to 62px and gains a hairline and a solid background. There's no blur and no shadow.
- Theme switch: a 16px half-filled circle whose half rotates 180°.
- Mobile: a `Menu` text button whose two lines morph into ×. It opens a full-screen sheet with numbered serif links that rise in sequence.

## 5. Page structure (each section uses a different composition)

1. **Opening letter**: a full-screen overlay (first visit only, can be replayed with `?letter`)
2. **Hero**: editorial split. Name set huge on the left and slightly overlapping the portrait on the right. A metadata row gives location and credentials (trust signals, placeholders for now).
3. **About**: a label in the margin, one large statement, a narrow bio, and a *facts* table in the far column
4. **Internal Medicine**: a numbered list with a live preview panel *(signature interaction)*
5. **Approach**: a full-width statement, then an offset photograph with three principles
6. **Free consultation**: an inverted block, three steps, a disclaimer, and the prototype form in a modal
7. **Health Notes**: an index-style table of articles, all marked SAMPLE CONTENT
8. **Behind the physician**: a paper-toned letter, a photograph, and one drawn line that ends in a heart
9. **Contact**: a large email address and a definition list
10. **Footer**: name, disclaimer, privacy note, replay link and theme toggle

## 6. Opening animation

Near-black warm screen. A faint pool of light drifts slowly across it over 18 seconds, so the screen feels alive without showing anything that moves.

```
0.9s   "For the woman who spends her days caring for others."   (rises 0.6em, fades in)
3.8s   first line dims to 38%; "And the woman who means the world to me."
7.2s   both lines lift away; "Dr. Xantipphy" rises out of a mask, line by line
8.8s   "Open" appears; a hairline draws itself out from the word
click  the text lifts 2rem and fades; the whole dark sheet wipes upward (clip-path),
       revealing the ivory site. The hero name rises line by line, the portrait
       unmasks bottom-up while it settles from 1.15× to 1×, and the navigation fades in last.
```

The skip button is present from the first second, and `Esc` also skips. With `prefers-reduced-motion`, everything becomes opacity-only and the wipe becomes a crossfade.

## 7. Signature interactions (five, and only five)

1. **The letter → site transition** (described above).
2. **Care index**. On desktop, hovering or focusing a row shifts the title 12px, colours the number, draws the row's rule in ink and dims the other rows to 40%. A sticky panel on the right swaps in that item's description. On touch it becomes an accessible accordion.
3. **Buttons**. A sage fill rises from the bottom on hover and leaves through the top. The arrow travels 4px.
4. **Links**. A hairline underline is always faintly present, and on hover an ink line draws over it.
5. **Images**. They unmask on entry. On hover the crop tightens slowly (1.025× over 1.2s) and a caption slides up.

Scroll reveals are intentionally *unequal*. Headings rise 24px over 1.2s, body text rises 12px over 1s with a short delay, metadata only fades, and images unmask. The hero portrait has a light parallax (8%). There is no custom cursor, because it wouldn't improve anything here.

## 8. Light mode
Ivory paper, near-black ink, sage used sparingly. It feels like a printed monograph.

## 9. Dark mode
Warm charcoal, never pure black, with warm white type. It feels like the same monograph read by lamplight.

## 10. Mobile

- The hero is re-composed rather than stacked: the name comes first at 3.1rem, then the portrait inset from the left (an asymmetric 18% margin) with its plate caption, then the full-width consultation button.
- The intro keeps the same text, with type sized for a single-hand read. The Open control and Skip sit in the thumb zone.
- The care index becomes an accordion with a + that turns into −.
- Health Notes rows stack: metadata, then title, then description.
- Every hover effect has a focus-visible or tap equivalent, and nothing depends on hover to be understood.

---

## Replacing content

Everything is in **`config.php`**. Any text in `[BRACKETS]` is a placeholder and renders in sage with a dotted underline, so nothing unfinished can slip through unnoticed.

### Photographs (`assets/img/`)
| File | Where | Suggested export |
|---|---|---|
| `portrait.jpg` | Hero | 1600 × 2000, JPG q≈78 (~250 KB) |
| `approach.jpg` | Approach section | 1400 × 1750 |
| `together.jpg` | Behind the physician | 1200 × 1500 |
| `og.jpg` | Social share image | 1200 × 630 |

If a file is missing, a labelled placeholder frame renders in its place. Width and height are read automatically, which prevents layout shift. Images below the fold are lazy-loaded.

### Must be reviewed before publishing
- All `[PLACEHOLDER]` fields (surname, credentials, city, contact).
- **Draft copy written for layout.** This is not her words and needs her approval: the hero lede, the Internal Medicine descriptions, the Approach statement and principles, and the consultation text.
- The medical disclaimer and privacy wording (legal review).
- `MD`, taken from the folder name. Please confirm it.
