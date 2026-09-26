<?php
require __DIR__ . '/inc/helpers.php';
$c = require __DIR__ . '/config.php';

$p        = $c['person'];
$fullName = trim("{$p['title']} {$p['first_name']} {$p['last_name']}");
$location = "{$p['city']}, {$p['region']}";
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

$email      = $c['contact']['email'];
$emailLink  = is_placeholder($email) ? null : 'mailto:' . $email;

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
<?php endif; ?>
<meta property="og:type" content="profile">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDesc) ?>">
<?php if ($c['site']['url']): ?>
<meta property="og:url" content="<?= e($c['site']['url']) ?>">
<meta property="og:image" content="<?= e(rtrim($c['site']['url'], '/') . '/' . $c['site']['og_image']) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#F4F0E8" data-theme-color>
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">

<script>
/* Runs before paint: theme (stored → system) and whether the opening letter plays. */
(function () {
  var d = document.documentElement, t, seen;
  d.classList.add('js');
  try { t = localStorage.getItem('theme'); seen = localStorage.getItem('intro-seen'); } catch (e) {}
  if (t !== 'light' && t !== 'dark') t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  d.setAttribute('data-theme', t);
  /* ?letter replays the opening; ?nointro previews the site without it */
  if ((!seen || /[?&]letter\b/.test(location.search)) && !/[?&]nointro\b/.test(location.search)) d.classList.add('intro-on');
  /* Safety net: if scripts fail to load, never leave content hidden. */
  setTimeout(function () { if (!window.__siteBooted) d.classList.remove('js', 'intro-on'); }, 4000);
})();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&family=Newsreader:ital,opsz,wght@0,6..72,200..600;1,6..72,200..500&display=swap">
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
      <span class="brand__name"><?= t($p['first_name']) ?> <?= t($p['last_name']) ?></span>
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
      <p><?= t($location) ?></p>
      <button class="theme-toggle theme-toggle--text" type="button" data-theme-toggle aria-label="Switch to dark theme"><?= theme_icon() ?><span data-theme-label>Dark</span></button>
    </div>
  </div>
</div>

<main id="main" data-site>

  <!-- ============================================================
       Hero
       ============================================================ -->
  <section class="hero" id="top" aria-labelledby="hero-title">
    <div class="wrap hero__grid">
      <div class="hero__text">
        <h1 class="hero__title" id="hero-title">
          <span class="hero__line"><span><?= e($p['title']) ?> <?= t($p['first_name']) ?></span></span>
          <span class="hero__line"><span><?= t($p['last_name']) ?></span></span>
        </h1>
        <p class="hero__role" data-hero="1"><?= e($p['role']) ?></p>
        <p class="hero__lede" data-hero="2"><?= t($c['hero']['lede']) ?></p>
        <div class="hero__actions" data-hero="3">
          <a class="btn" href="#consultation"><span>Request a free consultation</span><?= arrow() ?></a>
          <a class="link link--arrow" href="#about"><span>About Dr. <?= t($p['first_name']) ?></span><?= arrow() ?></a>
        </div>
      </div>

      <figure class="hero__figure">
        <div class="hero__frame media-hover">
          <div class="hero__parallax" data-parallax>
            <?= media($c['hero']['portrait'], "Portrait of {$fullName}", 'Portrait', true) ?>
          </div>
        </div>
        <figcaption class="plate" data-hero="4"><span class="plate__num">Pl. 01</span><span><?= t($c['hero']['plate']) ?></span></figcaption>
      </figure>
    </div>

    <div class="wrap">
      <div class="hero__foot" data-hero="5">
        <p><?= t($location) ?></p>
        <ul class="hero__creds" aria-label="Training and certification">
          <?php foreach ($c['hero']['credentials'] as $cred): ?>
            <li><?= t($cred) ?></li>
          <?php endforeach; ?>
        </ul>
        <p class="hero__avail"><?= t($c['hero']['availability']) ?></p>
      </div>
    </div>
  </section>

  <!-- ============================================================
       About
       ============================================================ -->
  <section class="about" id="about" aria-labelledby="about-title" data-nav-section="about">
    <div class="wrap about__grid">
      <p class="label label--rule about__label" data-reveal="meta">About</p>

      <h2 class="about__statement" id="about-title" data-reveal="heading">
        Medicine begins <br class="br-desk">with <em>listening.</em>
      </h2>

      <div class="about__bio prose" data-reveal="text">
        <?php foreach ($c['about']['bio'] as $i => $para): ?>
          <p<?= $i === 0 ? ' class="lead"' : '' ?>><?= t($para) ?></p>
        <?php endforeach; ?>
      </div>

      <dl class="facts" data-reveal="text" style="--delay:.25s">
        <?php foreach ($c['about']['facts'] as $term => $value): ?>
          <div class="facts__row">
            <dt><?= e($term) ?></dt>
            <dd><?= t($value) ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <!-- ============================================================
       Internal Medicine
       ============================================================ -->
  <section class="care" id="care" aria-labelledby="care-title" data-nav-section="care">
    <div class="wrap care__grid">
      <header class="care__head">
        <p class="label label--rule" data-reveal="meta">Internal Medicine</p>
        <h2 class="h2" id="care-title" data-reveal="heading">Care for adults, <em>seen whole.</em></h2>
        <p class="care__intro" data-reveal="text"><?= t($c['care']['intro']) ?></p>
      </header>

      <ol class="care-list" data-care>
        <?php foreach ($c['care']['items'] as $i => [$title, $text]): $n = sprintf('%02d', $i + 1); ?>
          <li class="care-item" data-reveal="meta" style="--delay:<?= $i * 0.06 ?>s">
            <h3 class="care-item__heading">
              <button class="care-item__btn" type="button" id="care-btn-<?= $n ?>" aria-controls="care-panel-<?= $n ?>" aria-expanded="false">
                <span class="care-item__num"><?= $n ?></span>
                <span class="care-item__title"><?= t($title) ?></span>
                <span class="care-item__icon" aria-hidden="true"></span>
              </button>
            </h3>
            <div class="care-item__panel" id="care-panel-<?= $n ?>" role="region" aria-labelledby="care-btn-<?= $n ?>">
              <div><p><?= t($text) ?></p></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>

      <aside class="care-preview" data-care-preview aria-hidden="true">
        <div class="care-preview__body" data-care-preview-body>
          <p class="care-preview__num"><?= '01' ?></p>
          <p class="care-preview__title"><?= t($c['care']['items'][0][0]) ?></p>
          <p class="care-preview__text"><?= t($c['care']['items'][0][1]) ?></p>
        </div>
        <a class="link link--arrow care-preview__link" href="#consultation" tabindex="-1"><span>Ask about this</span><?= arrow() ?></a>
      </aside>
    </div>
  </section>

  <!-- ============================================================
       Approach
       ============================================================ -->
  <section class="approach" id="approach" aria-labelledby="approach-title" data-nav-section="care">
    <div class="wrap">
      <p class="label label--rule" data-reveal="meta">Approach</p>
      <h2 class="statement" id="approach-title" data-reveal="heading"><?= t_em($c['approach']['statement']) ?></h2>

      <div class="approach__grid">
        <figure class="approach__figure">
          <div class="media-hover reveal-image" data-reveal="image">
            <?= media($c['approach']['image'], $c['approach']['image_alt'], 'Photograph') ?>
            <figcaption class="media-caption"><?= t($c['approach']['caption']) ?></figcaption>
          </div>
        </figure>

        <div class="approach__body">
          <p class="lead" data-reveal="text"><?= t($c['approach']['lead']) ?></p>
          <dl class="principles">
            <?php $i = 0; foreach ($c['approach']['principles'] as $term => $desc): $i++; ?>
              <div class="principles__row" data-reveal="text" style="--delay:<?= 0.1 * $i ?>s">
                <dt><span><?= sprintf('%02d', $i) ?></span><?= e($term) ?></dt>
                <dd><?= t($desc) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       Free consultation
       ============================================================ -->
  <section class="consult" id="consultation" aria-labelledby="consult-title" data-nav-section="consultation">
    <div class="wrap consult__grid">
      <div class="consult__head">
        <p class="label" data-reveal="meta">
          <span>Free initial consultation</span>
          <span class="status"><span class="status__dot" aria-hidden="true"></span><?= t($c['consultation']['status']) ?></span>
        </p>
        <h2 class="consult__title" id="consult-title" data-reveal="heading">Need someone to talk to about your <em>health?</em></h2>
      </div>

      <div class="consult__body">
        <p class="lead" data-reveal="text"><?= t($c['consultation']['body']) ?></p>

        <ol class="steps">
          <?php foreach ($c['consultation']['steps'] as $i => [$step, $desc]): ?>
            <li class="steps__item" data-reveal="text" style="--delay:<?= 0.08 * $i ?>s">
              <span class="steps__num"><?= sprintf('%02d', $i + 1) ?></span>
              <p><strong><?= t($step) ?></strong> <?= t($desc) ?></p>
            </li>
          <?php endforeach; ?>
        </ol>

        <div class="consult__cta" data-reveal="text">
          <button class="btn" type="button" data-open-consult><span>Request a free consultation</span><?= arrow() ?></button>
          <p class="consult__note">Preview — requests are not being accepted yet.</p>
        </div>
      </div>

      <p class="consult__disclaimer" data-reveal="meta">
        <span class="consult__disclaimer-label">Please note</span>
        <?= t($c['consultation']['disclaimer']) ?>
      </p>
    </div>
  </section>

  <!-- ============================================================
       Health notes
       ============================================================ -->
  <section class="notes" id="education" aria-labelledby="notes-title" data-nav-section="education">
    <div class="wrap">
      <header class="notes__head">
        <div>
          <p class="label label--rule" data-reveal="meta">Education</p>
          <h2 class="h2" id="notes-title" data-reveal="heading">Health Notes</h2>
        </div>
        <p class="notes__intro" data-reveal="text">
          Short, plain-language notes on the questions that come up most often.
          <span class="tag">Sample content</span>
        </p>
      </header>

      <ul class="notes-list">
        <?php foreach ($c['notes'] as $i => $note): ?>
          <li data-reveal="text" style="--delay:<?= 0.06 * $i ?>s">
            <article class="note<?= $note['url'] ? ' note--link' : '' ?>">
              <p class="note__date"><?= t($note['date']) ?></p>
              <div class="note__main">
                <h3 class="note__title">
                  <?php if ($note['url']): ?>
                    <a href="<?= e($note['url']) ?>"><?= t($note['title']) ?></a>
                  <?php else: ?>
                    <?= t($note['title']) ?>
                  <?php endif; ?>
                </h3>
                <p class="note__desc"><?= t($c['notes_summary']) ?></p>
              </div>
              <p class="note__cat"><?= e($note['category']) ?> <span class="tag">Sample</span></p>
              <p class="note__read" aria-hidden="true"><?= $note['url'] ? 'Read article' : 'Coming soon' ?><?= arrow() ?></p>
            </article>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <!-- ============================================================
       Behind the physician
       ============================================================ -->
  <section class="letter" id="letter" aria-labelledby="letter-title">
    <div class="wrap letter__grid">
      <figure class="letter__figure">
        <div class="reveal-image" data-reveal="image">
          <?= media($c['letter']['image'], $c['letter']['image_alt'], 'Photograph') ?>
        </div>
        <figcaption class="plate"><span class="plate__num">Pl. 02</span><span><?= t($c['letter']['caption']) ?></span></figcaption>
      </figure>

      <div class="letter__body">
        <p class="label label--rule" data-reveal="meta"><?= t($c['letter']['label']) ?></p>
        <h2 class="letter__title" id="letter-title" data-reveal="heading"><?= t($c['letter']['title']) ?></h2>

        <div class="letter__text" data-reveal="text">
          <p class="letter__salutation"><?= t($c['letter']['salutation']) ?></p>
          <?php foreach ($c['letter']['body'] as $para): ?>
            <p><?= t($para) ?></p>
          <?php endforeach; ?>
        </div>

        <div class="letter__sign" data-reveal="meta">
          <svg class="pulse" viewBox="0 0 200 44" aria-hidden="true" focusable="false">
            <path pathLength="1" d="M0 26H66l5-9 6 20 6-30 5 19H136c-7-8-2-18 7-18 5 0 7 4 7 6 0-2 2-6 7-6 9 0 14 10 7 18l-14 12L136 26"/>
          </svg>
          <p class="letter__closing"><?= t($c['letter']['closing']) ?></p>
          <p class="letter__signature"><?= t($c['letter']['signature']) ?></p>
          <p class="letter__date"><?= t($c['letter']['date']) ?></p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       Contact
       ============================================================ -->
  <section class="contact" id="contact" aria-labelledby="contact-title" data-nav-section="contact">
    <div class="wrap contact__grid">
      <div class="contact__head">
        <p class="label label--rule" data-reveal="meta">Contact</p>
        <h2 class="visually-hidden" id="contact-title">Contact</h2>
        <p class="contact__email" data-reveal="heading">
          <?php if ($emailLink): ?>
            <a class="link" href="<?= e($emailLink) ?>"><?= e($email) ?></a>
          <?php else: ?>
            <?= t($email) ?>
          <?php endif; ?>
        </p>
      </div>

      <dl class="contact__list" data-reveal="text">
        <div><dt>Phone</dt><dd><?= t($c['contact']['phone']) ?></dd></div>
        <div><dt>Clinic</dt><dd><?= t($c['contact']['clinic']) ?></dd></div>
        <div><dt>Location</dt><dd><?= t($c['contact']['address']) ?></dd></div>
        <div><dt>Hours</dt><dd><?= t($c['contact']['hours']) ?></dd></div>
        <div>
          <dt>Elsewhere</dt>
          <dd>
            <ul class="contact__social">
              <?php foreach ($c['contact']['social'] as $name => $url): ?>
                <li>
                  <?php if (is_placeholder($url)): ?>
                    <?= e($name) ?> <?= t($url) ?>
                  <?php else: ?>
                    <a class="link link--arrow" href="<?= e($url) ?>" rel="me noopener"><span><?= e($name) ?></span><?= arrow() ?></a>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </dd>
        </div>
      </dl>
    </div>
  </section>
</main>

<!-- ============================================================
     Footer
     ============================================================ -->
<footer class="site-footer" data-site>
  <div class="wrap footer__grid">
    <div class="footer__id">
      <p class="footer__name"><?= t($fullName) ?></p>
      <p><?= e($p['specialty']) ?> · <?= t($location) ?></p>
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
        <li><a class="link" href="?letter">Replay introduction</a></li>
        <li><button class="theme-toggle theme-toggle--text" type="button" data-theme-toggle aria-label="Switch to dark theme"><?= theme_icon() ?><span data-theme-label>Dark</span></button></li>
      </ul>
    </div>
  </div>
</footer>

<!-- ============================================================
     Consultation request — VISUAL PROTOTYPE ONLY.
     Nothing is sent, stored or logged. Replace with a real,
     secure backend before accepting any patient information.
     ============================================================ -->
<dialog class="dialog" id="consult-dialog" aria-labelledby="dialog-title">
  <div class="dialog__inner">
    <header class="dialog__head">
      <p class="label">Free initial consultation</p>
      <button class="dialog__close" type="button" data-close aria-label="Close">
        <span aria-hidden="true"></span>
      </button>
    </header>

    <h2 class="dialog__title" id="dialog-title">Request a consultation</h2>

    <p class="dialog__notice" role="note">
      <strong>Design preview.</strong> This form is not connected yet — nothing you type is sent or saved.
      Please don’t enter medical details.
    </p>

    <form class="form" novalidate data-prototype-form>
      <div class="field">
        <label for="f-name">Name</label>
        <input id="f-name" name="name" type="text" autocomplete="off" required>
      </div>
      <div class="field">
        <label for="f-contact">Email or phone</label>
        <input id="f-contact" name="contact" type="text" autocomplete="off" required>
      </div>
      <div class="field">
        <label for="f-topic">In a sentence, what would you like to talk about? <span>(optional)</span></label>
        <textarea id="f-topic" name="topic" rows="3" autocomplete="off"></textarea>
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

    <p class="dialog__disclaimer"><?= t($c['consultation']['disclaimer']) ?></p>
  </div>
</dialog>

</body>
</html>
