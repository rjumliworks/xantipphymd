<?php
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/icons.php';
$c = require __DIR__ . '/config.php';

$p         = $c['person'];
$fullName  = trim("{$p['title']} {$p['first_name']} {$p['last_name']}");
$location  = "{$p['city']}, {$p['region']}";
$pageTitle = "{$fullName} — {$p['role']} in {$p['city']}";
$metaDesc  = "{$fullName} is an internal medicine physician in {$location}, caring for adults — prevention, everyday concerns and long-term conditions.";
$year      = date('Y');

$nav = [
    'about'        => 'About',
    'care'         => 'Care',
    'education'    => 'Education',
    'consultation' => 'Consultation',
    'contact'      => 'Contact',
];

$email     = $c['contact']['email'];
$emailLink = is_placeholder($email) ? null : 'mailto:' . $email;

$jsonLd = array_filter([
    '@context'    => 'https://schema.org',
    '@type'       => 'Physician',
    'name'        => $fullName,
    'description' => $metaDesc,
    'url'         => $c['site']['url'] ?: null,
    'address'     => is_placeholder($p['city']) ? null : [
        '@type'           => 'PostalAddress',
        'addressLocality' => $p['city'],
        'addressRegion'   => $p['region'],
    ],
]);
?><!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<?php if ($c['site']['url']): ?>
<link rel="canonical" href="<?= e($c['site']['url']) ?>">
<meta property="og:url" content="<?= e($c['site']['url']) ?>">
<meta property="og:image" content="<?= e(rtrim($c['site']['url'], '/') . '/' . $c['site']['og_image']) ?>">
<?php endif; ?>
<meta property="og:type" content="profile">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDesc) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#FDF7F8" data-theme-color>
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">

<script>
/* Runs before paint: theme (stored → system) and whether the opening letter plays. */
(function () {
  var d = document.documentElement, t, seen;
  d.classList.add('js');
  var pass = false, q = location.search;
  try { t = localStorage.getItem('theme'); } catch (e) {}
  /* Every visit (and every refresh) starts at the anniversary gate. Finishing the gate
     leaves a one-time pass; it is used up here, so the next refresh goes back to the gate.
     ?nogate / ?nointro bypass it for previews. */
  try { pass = sessionStorage.getItem('gate-pass') === '1'; sessionStorage.removeItem('gate-pass'); } catch (e) {}
  if (!pass && !/[?&](nogate|nointro)\b/.test(q)) { location.replace('gate.php'); return; }
  if (t !== 'light' && t !== 'dark') t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  d.setAttribute('data-theme', t);
  /* The love letter plays every time she comes through the gate (or with ?letter). */
  if ((pass || /[?&]letter\b/.test(q)) && !/[?&]nointro\b/.test(q)) d.classList.add('intro-on');
  /* Safety net: if scripts fail to load, never leave content hidden. */
  setTimeout(function () { if (!window.__siteBooted) d.classList.remove('js', 'intro-on'); }, 4000);
})();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,300..500;1,6..72,300..500&display=swap">
<link rel="stylesheet" href="<?= asset('assets/css/main.css') ?>">
<script type="module" src="<?= asset('assets/js/main.js') ?>"></script>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body>

<!-- ============================================================
     Opening letter
     ============================================================ -->
<div class="intro" id="intro" role="dialog" aria-modal="true" aria-labelledby="intro-name" tabindex="-1">
  <div class="intro__ambient" aria-hidden="true"></div>

  <p class="intro__corner intro__corner--left"><?= t($c['intro']['corner']) ?></p>
  <p class="intro__corner intro__corner--right"><?= t($c['intro']['date']) ?></p>

  <div class="intro__stage">
    <div class="intro__lines">
      <?php foreach ($c['intro']['lines'] as $line): ?>
        <p class="intro__line" data-intro-line><?= t($line) ?></p>
      <?php endforeach; ?>
    </div>

    <div class="intro__finale">
      <p class="intro__name" id="intro-name">
        <?php foreach (explode(' ', $c['intro']['name'], 2) as $i => $part): ?>
          <span class="intro__mask"><span style="--i:<?= $i ?>"><?= t($part) ?></span></span>
        <?php endforeach; ?>
      </p>
      <button class="intro__enter" type="button" data-intro-enter>
        <span>Open</span>
        <span class="intro__enter-line" aria-hidden="true"></span>
      </button>
    </div>
  </div>

  <button class="intro__skip" type="button" data-intro-skip>Skip intro</button>
</div>

<a class="skip-link" href="#main">Skip to content</a>

<!-- ============================================================
     Header
     ============================================================ -->
<header class="site-header" data-site data-header>
  <div class="wrap site-header__inner">
    <a class="brand" href="#top" aria-label="<?= e($fullName) ?> — back to top">
      <span class="brand__name"><?= t($p['first_name']) ?></span>
      <span class="brand__suffix"><?= t($p['suffix']) ?></span>
    </a>

    <nav class="nav" aria-label="Primary">
      <ul class="nav__list">
        <?php foreach ($nav as $id => $label): ?>
          <li><a class="nav__link" href="#<?= $id ?>" data-nav-link="<?= $id ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="site-header__tools">
      <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark theme"><?= theme_icon() ?></button>
      <a class="btn btn--sm header-cta" href="#contact"><span>Book a consult</span></a>
      <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu">
        <span class="menu-toggle__label">Menu</span>
        <span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span></span>
      </button>
    </div>
  </div>
</header>

<div class="mobile-menu" id="mobile-menu" hidden>
  <div class="wrap mobile-menu__inner">
    <nav aria-label="Mobile">
      <ol class="mobile-menu__list">
        <?php $n = 0; foreach ($nav as $id => $label): $n++; ?>
          <li style="--i:<?= $n ?>"><a href="#<?= $id ?>" data-nav-link="<?= $id ?>"><span class="mobile-menu__num"><?= sprintf('%02d', $n) ?></span><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ol>
    </nav>
    <div class="mobile-menu__foot">
      <a class="btn" href="#contact"><span>Request a consultation</span><?= arrow() ?></a>
      <button class="theme-toggle theme-toggle--text" type="button" data-theme-toggle aria-label="Switch to dark theme"><?= theme_icon() ?><span data-theme-label>Dark</span></button>
    </div>
  </div>
</div>

<main id="main" data-site>

  <!-- ============================================================
       Hero
       ============================================================ -->
  <section class="hero" id="top" aria-labelledby="hero-title">
    <div class="hero__shape" aria-hidden="true"></div>

    <div class="wrap hero__grid">
      <div class="hero__media" data-hero="img">
        <?= media($c['hero']['image'], "Portrait of {$fullName}", 'Portrait — cut-out PNG', true, 'cutout') ?>
      </div>

      <div class="hero__text">
        <p class="kicker" data-hero="1"><?= t($c['hero']['kicker']) ?></p>
        <h1 class="hero__title" id="hero-title" data-hero="2">
          Make time for your health with <em><?= e($p['title']) ?>&nbsp;<?= t($p['first_name']) ?></em>
        </h1>
        <p class="hero__lede" data-hero="3"><?= t($c['hero']['lede']) ?></p>
        <div class="hero__actions" data-hero="4">
          <a class="btn" href="#contact"><span>Request a consultation</span><?= arrow() ?></a>
          <a class="btn btn--ghost" href="#about"><span>About Dr. <?= t($p['first_name']) ?></span></a>
        </div>
      </div>
    </div>

    <div class="wrap">
      <div class="glance" data-hero="5">
        <p class="glance__title">At a <em>glance</em></p>
        <dl class="glance__list">
          <?php foreach ($c['glance'] as $term => $value): ?>
            <div>
              <dt><?= e($term) ?></dt>
              <dd><?= t($value) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </section>

  <!-- ============================================================
       About
       ============================================================ -->
  <section class="about section" id="about" aria-labelledby="about-title" data-nav-section="about">
    <div class="wrap about__grid">
      <div class="about__text">
        <p class="kicker" data-reveal="meta">About Dr. <?= t($p['first_name']) ?></p>
        <h2 class="h2" id="about-title" data-reveal="heading"><?= t_em($c['about']['heading']) ?></h2>
        <div class="prose" data-reveal="text">
          <?php foreach ($c['about']['bio'] as $para): ?>
            <p><?= t($para) ?></p>
          <?php endforeach; ?>
        </div>
        <div class="about__actions" data-reveal="text" style="--delay:.15s">
          <a class="btn" href="#contact"><span>Request a consultation</span><?= arrow() ?></a>
        </div>
      </div>

      <figure class="about__figure" data-reveal="image">
        <span class="about__ring" aria-hidden="true"></span>
        <div class="about__circle">
          <?= media($c['about']['image'], $c['about']['image_alt'], 'Photograph') ?>
        </div>
      </figure>
    </div>
  </section>

  <!-- ============================================================
       Care
       ============================================================ -->
  <section class="care section section--tint" id="care" aria-labelledby="care-title" data-nav-section="care">
    <div class="wrap">
      <header class="section-head section-head--center">
        <p class="kicker" data-reveal="meta">Internal Medicine</p>
        <h2 class="h2" id="care-title" data-reveal="heading">How I can <em>help</em></h2>
        <p class="section-head__intro" data-reveal="text"><?= t($c['care']['intro']) ?></p>
      </header>

      <ul class="services">
        <?php foreach ($c['care']['items'] as $i => [$ico, $title, $text]): ?>
          <li class="service" data-reveal="text" style="--delay:<?= ($i % 3) * 0.08 ?>s">
            <span class="service__icon"><?= icon($ico) ?></span>
            <h3 class="service__title"><?= t($title) ?></h3>
            <p class="service__text"><?= t($text) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================================================
       Consultation
       ============================================================ -->
  <section class="consult section" id="consultation" aria-labelledby="consult-title" data-nav-section="consultation">
    <div class="wrap consult__grid">
      <figure class="consult__figure" data-reveal="image">
        <div class="consult__frame">
          <?= media($c['consultation']['image'], $c['consultation']['image_alt'], 'Photograph') ?>
        </div>
        <p class="consult__badge">
          <span class="status__dot" aria-hidden="true"></span>
          Free initial consultation
        </p>
      </figure>

      <div class="consult__text">
        <p class="kicker" data-reveal="meta"><?= t($c['consultation']['status']) ?></p>
        <h2 class="h2" id="consult-title" data-reveal="heading">Need someone to talk to about your <em>health?</em></h2>
        <p class="lead" data-reveal="text"><?= t($c['consultation']['body']) ?></p>

        <ol class="steps">
          <?php foreach ($c['consultation']['steps'] as $i => [$step, $desc]): ?>
            <li class="step" data-reveal="text" style="--delay:<?= 0.08 * $i ?>s">
              <span class="step__num"><?= sprintf('%02d', $i + 1) ?></span>
              <div>
                <h3 class="step__title"><?= t($step) ?></h3>
                <p><?= t($desc) ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>

        <div class="consult__cta" data-reveal="text">
          <a class="btn" href="#contact"><span>Request a free consultation</span><?= arrow() ?></a>
        </div>
        <p class="fine-print" data-reveal="meta"><?= t($c['consultation']['disclaimer']) ?></p>
      </div>
    </div>
  </section>

  <!-- ============================================================
       Behind the physician
       ============================================================ -->
  <section class="letter section" id="letter" aria-labelledby="letter-title">
    <div class="letter__shape" aria-hidden="true"></div>
    <div class="wrap letter__grid">
      <div class="letter__body">
        <p class="kicker" data-reveal="meta"><?= t($c['letter']['label']) ?></p>
        <h2 class="h2" id="letter-title" data-reveal="heading"><?= t($c['letter']['title']) ?></h2>

        <div class="letter__card" data-reveal="text">
          <p class="letter__salutation"><?= t($c['letter']['salutation']) ?></p>
          <?php foreach ($c['letter']['body'] as $para): ?>
            <p><?= t($para) ?></p>
          <?php endforeach; ?>

          <div class="letter__sign">
            <svg class="pulse" viewBox="0 0 200 44" aria-hidden="true" focusable="false">
              <path pathLength="1" d="M0 26H66l5-9 6 20 6-30 5 19H136c-7-8-2-18 7-18 5 0 7 4 7 6 0-2 2-6 7-6 9 0 14 10 7 18l-14 12L136 26"/>
            </svg>
            <p class="letter__closing"><?= t($c['letter']['closing']) ?></p>
            <p class="letter__signature"><?= t($c['letter']['signature']) ?></p>
            <p class="letter__date"><?= t($c['letter']['date']) ?></p>
          </div>
        </div>
      </div>

      <div class="letter__media" data-reveal="image">
        <?= media($c['letter']['image'], $c['letter']['image_alt'], 'Photograph — cut-out PNG', false, 'cutout') ?>
      </div>
    </div>
  </section>

  <!-- ============================================================
       Health notes
       ============================================================ -->
  <section class="notes section" id="education" aria-labelledby="notes-title" data-nav-section="education">
    <div class="wrap">
      <header class="section-head section-head--center">
        <p class="kicker" data-reveal="meta">Education</p>
        <h2 class="h2" id="notes-title" data-reveal="heading">Health <em>Notes</em></h2>
        <p class="section-head__intro" data-reveal="text">
          Short, plain-language notes on the questions that come up most often.
          <span class="tag">Sample content</span>
        </p>
      </header>

      <ul class="notes-grid">
        <?php foreach ($c['notes'] as $i => $note): ?>
          <li data-reveal="text" style="--delay:<?= ($i % 2) * 0.08 ?>s">
            <article class="note">
              <div class="note__thumb">
                <?= media($note['image'], '', 'Image') ?>
              </div>
              <div class="note__body">
                <p class="note__meta"><?= e($note['category']) ?> <span class="tag">Sample</span></p>
                <h3 class="note__title">
                  <?php if ($note['url']): ?>
                    <a href="<?= e($note['url']) ?>"><?= t($note['title']) ?></a>
                  <?php else: ?>
                    <?= t($note['title']) ?>
                  <?php endif; ?>
                </h3>
                <p class="note__desc"><?= t($c['notes_summary']) ?></p>
                <p class="note__read" aria-hidden="true"><?= $note['url'] ? 'Read more' : 'Coming soon' ?><?= arrow() ?></p>
              </div>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================================================
       Contact — the form is a VISUAL PROTOTYPE: nothing is sent or stored.
       ============================================================ -->
  <section class="contact section" id="contact" aria-labelledby="contact-title" data-nav-section="contact">
    <div class="contact__shape" aria-hidden="true"></div>
    <div class="wrap contact__grid">
      <div class="contact__media" data-reveal="image">
        <?= media($c['contact']['image'], "Portrait of {$fullName}", 'Portrait — cut-out PNG', false, 'cutout') ?>
      </div>

      <div class="contact__panel">
        <p class="kicker" data-reveal="meta">Contact</p>
        <h2 class="h2" id="contact-title" data-reveal="heading">Get in <em>touch</em></h2>
        <p class="contact__intro" data-reveal="text">
          Leave a few lines and a way to reach you. For anything urgent, please call your local emergency number.
        </p>

        <form class="form" novalidate data-prototype-form data-reveal="text">
          <p class="form__notice" role="note">
            <strong>Preview.</strong> This form isn’t connected yet — nothing you type is sent or saved.
          </p>
          <div class="form__row">
            <div class="field">
              <label for="f-name">Full name</label>
              <input id="f-name" name="name" type="text" autocomplete="off" required>
            </div>
            <div class="field">
              <label for="f-email">Email</label>
              <input id="f-email" name="email" type="email" autocomplete="off" required>
            </div>
          </div>
          <div class="field">
            <label for="f-phone">Phone <span>(optional)</span></label>
            <input id="f-phone" name="phone" type="tel" autocomplete="off">
          </div>
          <div class="field">
            <label for="f-msg">What would you like to talk about? <span>(no medical details yet)</span></label>
            <textarea id="f-msg" name="message" rows="4" autocomplete="off"></textarea>
          </div>
          <div class="field field--check">
            <input id="f-ack" name="ack" type="checkbox" required>
            <label for="f-ack">I understand this is not for emergencies.</label>
          </div>
          <div class="form__actions">
            <button class="btn" type="submit"><span>Send request</span><?= arrow() ?></button>
          </div>
          <p class="form__status" data-status tabindex="-1" hidden>
            Thank you. This is a preview, so nothing was sent — and the form has been cleared.
          </p>
        </form>
      </div>
    </div>
  </section>
</main>

<!-- ============================================================
     Footer
     ============================================================ -->
<footer class="site-footer" data-site>
  <div class="wrap">
    <div class="footer__grid">
      <div class="footer__id">
        <p class="footer__brand"><?= t($p['first_name']) ?> <span><?= t($p['suffix']) ?></span></p>
        <p><?= t($fullName) ?> — <?= e($p['role']) ?>, <?= t($location) ?>.</p>
      </div>

      <div class="footer__col">
        <p class="footer__head">Reach her</p>
        <ul>
          <li><?php if ($emailLink): ?><a class="link" href="<?= e($emailLink) ?>"><?= e($email) ?></a><?php else: ?><?= t($email) ?><?php endif; ?></li>
          <li><?= t($c['contact']['phone']) ?></li>
          <li><?= t($c['contact']['clinic']) ?></li>
          <li><?= t($c['contact']['address']) ?></li>
          <li><?= t($c['contact']['hours']) ?></li>
        </ul>
      </div>

      <div class="footer__col">
        <p class="footer__head">Elsewhere</p>
        <ul>
          <?php foreach ($c['contact']['social'] as $name => $url): ?>
            <li>
              <?php if (is_placeholder($url)): ?>
                <?= e($name) ?> <?= t($url) ?>
              <?php else: ?>
                <a class="link" href="<?= e($url) ?>" rel="me noopener"><?= e($name) ?></a>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="footer__legal">
      <p id="disclaimer"><strong>Medical disclaimer.</strong> <?= t($c['consultation']['disclaimer']) ?></p>
      <p id="privacy"><strong>Privacy.</strong> <?= t($c['legal']['privacy']) ?></p>
    </div>

    <div class="footer__bar">
      <p>© <?= $year ?> <?= t($fullName) ?></p>
      <ul class="footer__links">
        <li><a class="link" href="#privacy">Privacy</a></li>
        <li><a class="link" href="#disclaimer">Medical disclaimer</a></li>
        <li><a class="link" href="gate.php">Replay the surprise</a></li>
        <li><button class="theme-toggle theme-toggle--text" type="button" data-theme-toggle aria-label="Switch to dark theme"><?= theme_icon() ?><span data-theme-label>Dark</span></button></li>
      </ul>
    </div>
  </div>
</footer>

</body>
</html>
