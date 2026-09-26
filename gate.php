<?php
require __DIR__ . '/inc/helpers.php';
$c = require __DIR__ . '/config.php';
$g = $c['gate'];

$n   = (int) $g['years'];
$suf = ($n % 100 >= 11 && $n % 100 <= 13) ? 'th' : (['th', 'st', 'nd', 'rd'][$n % 10] ?? 'th');
$wife    = $g['wife'];
$husband = $g['husband'];
$nick    = $g['husband_nick'] ?? '';

/* Photos for the husband reactions: per-mood file if present, else me.*, else null (emoji stand-in). */
$findPhoto = function (string $base) {
    foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
        $path = "assets/img/{$base}.{$ext}";
        if (is_file(__DIR__ . '/' . $path)) return asset($path);
    }
    return null;
};
$defaultPhoto = is_file(__DIR__ . '/' . $g['me_photo']) ? asset($g['me_photo']) : $findPhoto('me');
$reactionData = ['eyes' => $g['eyes'], 'photos' => ['default' => $defaultPhoto]];
foreach (['jewelry', 'land', 'car', 'money', 'happy', 'faint'] as $mood) {
    $reactionData['photos'][$mood] = $findPhoto("me-{$mood}") ?? $defaultPhoto;
}

/* Step 4 scrapbook: every photo in assets/img/us/, or placeholders until there are some. */
require_once __DIR__ . '/inc/images.php';
$captions = $g['captions'] ?? [];
$memories = [];
foreach (photos_in('assets/img/us') as $file) {
    $memories[] = [
        'thumb'   => resized($file, 700),
        'full'    => resized($file, 1800),
        'caption' => $captions[basename($file)] ?? '',
    ];
}
if (!$memories) {
    foreach (array_values($captions) ?: array_fill(0, 6, '[Caption]') as $cap) {
        $memories[] = ['thumb' => null, 'full' => null, 'caption' => $cap];
    }
}

/* NDA "evidence" photos, clipped to clauses. */
$exhibit = function (int $clause) use ($g) {
    $x = $g['exhibits'][$clause] ?? null;
    if (!$x) return '';
    $img = is_file(__DIR__ . '/' . $x['file'])
        ? '<img src="' . resized($x['file'], 400) . '" alt="' . e($x['label'] . ': ' . $x['caption']) . '" loading="lazy">'
        : '<span class="exhibit__ph">[' . e($x['label']) . ' photo]</span>';
    return '<figure class="exhibit"><span class="exhibit__clip" aria-hidden="true"></span>' . $img
         . '<figcaption><b>' . e($x['label']) . '</b> ' . e($x['caption']) . '</figcaption></figure>';
};
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Happy <?= $n . $suf ?> Anniversary, <?= e($wife) ?>!</title>
<meta name="robots" content="noindex">
<meta name="theme-color" content="#FFF6E5">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bagel+Fat+One&family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Homemade+Apple&family=VT323&display=swap">
<link rel="stylesheet" href="<?= asset('assets/css/gate.css') ?>">
<link rel="stylesheet" href="<?= asset('assets/css/gate-gift.css') ?>">
<script type="module" src="<?= asset('assets/js/gate.js') ?>"></script>
</head>
<body>

<div class="deco" aria-hidden="true">
  <span class="deco__star deco__star--1">✦</span>
  <span class="deco__star deco__star--2">✦</span>
  <span class="deco__dot deco__dot--1"></span>
  <span class="deco__dot deco__dot--2"></span>
  <span class="deco__squiggle"></span>
</div>

<main class="stage">
  <ol class="progress" aria-label="Progress">
    <li class="is-current" data-progress="1"><span>1</span> Hello</li>
    <li data-progress="2"><span>2</span> Wishlist</li>
    <li data-progress="3"><span>3</span> Legal</li>
    <li class="is-locked" data-progress="4"><span aria-hidden="true" data-lock>🔒</span> Gift</li>
  </ol>

  <!-- ==========================================================
       Screen 1 — greeting + the only acceptable answer
       ========================================================== -->
  <section class="screen is-active" data-screen="1" aria-labelledby="s1-title">
    <div class="window window--pink">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">anniversary.exe</span>
      </div>
      <div class="window__body">
        <p class="sticker sticker--yellow" aria-hidden="true"><?= $n ?><sup><?= $suf ?></sup></p>
        <h1 class="big" id="s1-title">
          Happy <?= $n . $suf ?> Anniversary,<br>
          <span class="big__name"><?= t($wife) ?>!</span>
        </h1>
        <p class="sub"><?= $n ?> years, zero regrets.<sup>*</sup></p>
        <p class="tiny">*Terms and conditions apply. See page 3.</p>

        <div class="question">
          <p class="question__text">Are you still angry?</p>
          <div class="question__buttons">
            <button class="btn btn--no" type="button" data-no>No 😊</button>
            <button class="btn btn--yes" type="button" data-yes>Yes 😠</button>
          </div>
          <p class="toast" data-toast role="status" aria-live="polite"></p>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Screen 2 — the wishlist
       ========================================================== -->
  <section class="screen" data-screen="2" aria-labelledby="s2-title" hidden>
    <div class="window window--blue">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">wishlist.exe</span>
      </div>
      <div class="window__body">
        <p class="kicker">Yay! Knew it. 💕</p>
        <h2 class="big big--md" id="s2-title">What do you want for today?</h2>
        <p class="sub">Pick as many as you like. <span class="tiny tiny--inline">(Budget not guaranteed.)</span></p>

        <fieldset class="wishes">
          <legend class="visually-hidden">Choose your gifts</legend>

          <label class="wish wish--pink">
            <input type="checkbox" name="wish" value="Jewelry" data-reply="Shiny. Noted. The wallet is sweating.">
            <span class="wish__emoji" aria-hidden="true">💍</span>
            <span class="wish__name">Jewelry</span>
          </label>
          <label class="wish wish--green">
            <input type="checkbox" name="wish" value="Land" data-reply="A whole piece of LAND?! Opening Google Maps…">
            <span class="wish__emoji" aria-hidden="true">🏡</span>
            <span class="wish__name">Land</span>
          </label>
          <label class="wish wish--orange">
            <input type="checkbox" name="wish" value="Car" data-reply="Vroom vroom. Does a toy car count?">
            <span class="wish__emoji" aria-hidden="true">🚗</span>
            <span class="wish__name">Car</span>
          </label>
          <label class="wish wish--yellow">
            <input type="checkbox" name="wish" value="Money" data-reply="Straight to the point. Respect.">
            <span class="wish__emoji" aria-hidden="true">💰</span>
            <span class="wish__name">Money</span>
          </label>
        </fieldset>

        <p class="toast toast--left" data-wish-toast role="status" aria-live="polite">Nothing selected yet… don’t be shy.</p>

        <div class="actions">
          <button class="btn btn--primary" type="button" data-to-nda disabled>Submit my wishlist →</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Screen 3 — the (very official) NDA
       ========================================================== -->
  <section class="screen" data-screen="3" aria-labelledby="s3-title" hidden>
    <div class="window window--paper">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">NDA_final_FINAL_v4(1).pdf</span>
      </div>
      <div class="window__body doc">

        <h2 class="doc__title" id="s3-title">Non-Disclosure Agreement</h2>
        <p class="doc__subtitle">(Mutual*) &nbsp;·&nbsp; <em>*mostly one-way</em></p>

        <p class="stamp" aria-hidden="true">Top<br>Secret</p>
        <div class="clauses-box" tabindex="0" role="region" aria-label="Agreement clauses (scrollable)" data-clauses>
        <p class="doc__preamble">
          This Agreement is entered into on <strong><?= t($g['date']) ?></strong>, the <?= $n . $suf ?> anniversary, by and between
          <strong><?= t($husband) ?></strong> (a.k.a. “<strong><?= e($nick) ?></strong>”, hereinafter “<strong>the Husband</strong>” or “<strong>the Always-Right Party</strong>”) and
          <strong>Dr. <?= t($wife) ?></strong> (hereinafter “<strong>the Wife</strong>” or “<strong>the Boss, Technically</strong>”).
        </p>

        <ol class="clauses">
          <li>
            <?= $exhibit(1) ?>
            <strong>Confidential Information.</strong> The Wife shall not disclose to any third party — including but not limited to her mother, her friends, her co-workers and The Group Chat — that the Husband snores, sings off-key in the shower, or cried during a movie he insists was “just dusty.”
          </li>
          <li>
            <strong>Current Emotional Status.</strong> The Wife confirms that she answered “No” to “Are you still angry?” of her own free will, without pressure, and while the “Yes” button was running away purely by coincidence.
          </li>
          <li>
            <strong>Past Issues.</strong> All previous disagreements, including “that thing from last year,” are hereby closed and may not be brought up during future disagreements, car rides, or at 2:00 a.m.
          </li>
          <li>
            <strong>Being Right.</strong> In any disagreement, the Husband shall be deemed right. Should the Husband be proven wrong, the matter shall be referred back to this Clause.
          </li>
          <li>
            <strong>The Remote Control.</strong> The TV remote is the sole property of the Husband on weekends, public holidays, and during any game involving a ball.
          </li>
          <li>
            <?= $exhibit(6) ?>
            <strong>The Last Slice.</strong> The last slice of pizza, the last piece of chicken, and the last spoon of dessert belong to the Husband, unless the Wife says “are you going to eat that?”, in which case it immediately belongs to the Wife.
          </li>
          <li>
            <?= $exhibit(7) ?>
            <strong>Medical Services.</strong> The Husband is entitled to unlimited free consultations for all conditions, including paper cuts, “man flu,” and pains he cannot describe, delivered with full bedside manner and zero eye-rolling.
          </li>
          <li>
            <strong>“I’m Fine.”</strong> The phrase “I’m fine” shall be legally interpreted as “I am fine” and shall not be used as a trap.
          </li>
          <li>
            <strong>The Wishlist.</strong> The Wife’s request for <strong data-wish-list>—</strong> has been received and shall be processed within 5–7 business <em>years</em>, subject to the availability of funds, the stock market, and the Husband’s mood.
          </li>
          <li>
            <strong>Term.</strong> This Agreement lasts forever, renews automatically every anniversary, and cannot be terminated — only extended, preferably with kisses.
          </li>
          <li>
            <strong>Penalties.</strong> Any breach by the Husband is punishable by 100 hugs, one date night (paid by the Husband), and one sincere apology. <span class="doc__aside">(This clause was added by the Wife’s lawyer.)</span>
          </li>
        </ol>

        <p class="doc__fineprint">
          Fine print: Notwithstanding anything written above, the Husband loves the Wife far more than this document can legally express,
          and would lose every argument on purpose if it made her smile. 💗
        </p>
        </div>
        <p class="clauses-hint" data-clauses-hint aria-hidden="true">Scroll to read all 11 clauses ↓</p>

        <form class="sign" data-sign novalidate>
          <div class="sign__grid">
            <div class="sign__block">
              <p class="sign__label">The Always-Right Party</p>
              <p class="sign__line sign__line--done"><span class="script"><?= e(is_placeholder($husband) ? 'The Husband' : $husband) ?></span></p>
              <p class="sign__who"><?= t($husband) ?> (<?= e($nick) ?>)</p>
            </div>
            <div class="sign__block">
              <label class="sign__label" for="sig-name">The Wife — sign here</label>
              <p class="sign__line"><span class="script" data-sig-preview aria-hidden="true"></span></p>
              <input class="sign__input" id="sig-name" type="text" autocomplete="off" autocapitalize="words" spellcheck="false"
                     placeholder="Your name" required data-expected="<?= e($wife) ?>" aria-describedby="sig-hint">
              <p class="sign__hint" id="sig-hint" data-sig-hint aria-live="polite"></p>
            </div>
          </div>

          <label class="check">
            <input type="checkbox" data-sig-read required>
            <span>I have read this agreement <em>(or at least pretended to)</em>.</span>
          </label>

          <div class="actions actions--split">
            <button class="btn btn--ghost" type="button" data-reject>I disagree</button>
            <button class="btn btn--primary" type="submit" data-sign-btn disabled><span>Sign &amp; open <span class="hide-sm">my </span>gift →</span></button>
          </div>
          <p class="toast toast--left" data-sign-toast role="status" aria-live="polite"></p>
        </form>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Screen 4 — gift unlocked: the scrapbook + his message
       ========================================================== -->
  <section class="screen screen--wide" data-screen="4" aria-labelledby="s4-title" hidden>
    <div class="window window--green">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">our_<?= $n ?>_years.zip — extracted ✓</span>
      </div>
      <div class="window__body">
        <p class="kicker">Signed, sealed, delivered 💌</p>
        <h2 class="big big--md" id="s4-title"><?= e($g['gift_title']) ?></h2>
        <p class="sub"><?= e($g['gift_subtitle']) ?></p>

        <?php $perPage = 3; $pages = (int) ceil(count($memories) / $perPage); ?>
        <ul class="scrapbook" data-scrapbook data-pages="<?= $pages ?>" aria-live="polite">
          <?php foreach ($memories as $i => $m): ?>
            <li class="k<?= $i % $perPage ?>" style="--i:<?= $i ?>;--k:<?= $i % $perPage ?>" data-page="<?= intdiv($i, $perPage) ?>"<?= $i >= $perPage ? ' hidden' : '' ?>>
              <button class="polaroid polaroid--<?= ['pink', 'yellow', 'blue', 'green'][$i % 4] ?>" type="button" data-photo="<?= $i ?>"
                      aria-label="Open photo <?= $i + 1 ?><?= $m['caption'] ? ': ' . e($m['caption']) : '' ?>">
                <span class="polaroid__tape" aria-hidden="true"></span>
                <span class="polaroid__img">
                  <?php if ($m['thumb']): ?>
                    <img src="<?= $m['thumb'] ?>" alt="" loading="lazy" decoding="async">
                  <?php else: ?>
                    <span class="polaroid__ph">Photo <?= $i + 1 ?><small>assets/img/us/<?= sprintf('%02d', $i + 1) ?>.jpg</small></span>
                  <?php endif; ?>
                </span>
                <?php if ($m['caption']): ?>
                  <span class="polaroid__cap"><?= t($m['caption']) ?></span>
                <?php endif; ?>
              </button>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="pager">
          <button class="btn btn--ghost pager__btn" type="button" data-page-prev aria-label="Previous photos">‹ Back</button>
          <div class="pager__mid">
            <p class="pager__dots" aria-hidden="true">
              <?php for ($p = 0; $p < $pages; $p++): ?><i data-dot="<?= $p ?>"<?= $p === 0 ? ' class="is-on"' : '' ?>></i><?php endfor; ?>
            </p>
            <p class="pager__label" data-page-label>Page 1 of <?= $pages ?> · tap a photo to zoom</p>
          </div>
          <button class="btn btn--primary pager__btn" type="button" data-page-next>Next ›</button>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Screen 5 — his message, with a very dramatic unboxing
       ========================================================== -->
  <section class="screen screen--msg" data-screen="5" aria-labelledby="s5-title" hidden>
    <div class="window window--pink">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">message_from_<?= e(strtolower($nick ?: 'hubby')) ?>.txt</span>
      </div>
      <div class="window__body msg">
        <h2 class="visually-hidden" id="s5-title"><?= e($g['message_title']) ?></h2>

        <!-- 1) the fake loading screen -->
        <div class="loader" data-loader>
          <p class="kicker">Opening a very important file…</p>
          <p class="loader__icon" aria-hidden="true" data-loader-icon>📁</p>
          <div class="loader__bar" role="progressbar" aria-label="Loading your message" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-loader-bar>
            <span data-loader-fill></span>
          </div>
          <p class="loader__pct" data-loader-pct>0%</p>
          <p class="loader__status" data-loader-status aria-live="polite">Starting…</p>
        </div>

        <!-- 2) the envelope that plays hard to get -->
        <div class="mailroom" data-mailroom hidden>
          <p class="mailroom__toast" data-env-toast aria-live="polite">You’ve got mail 💌</p>
          <div class="mailroom__floor">
            <button class="env" type="button" data-env aria-label="Open the envelope">
              <span class="env__back" aria-hidden="true"></span>
              <span class="env__letter" aria-hidden="true"><i></i><i></i><i></i></span>
              <span class="env__front" aria-hidden="true"></span>
              <span class="env__flap" aria-hidden="true"></span>
              <span class="env__seal" aria-hidden="true">💗</span>
              <span class="env__scan" aria-hidden="true"></span>
            </button>
          </div>
          <p class="mailroom__hint" data-env-hint>Tap the envelope</p>
        </div>

        <!-- 3) the letter itself -->
        <div class="letter-view" data-letter hidden>
          <div class="letterpaper" id="his-message" tabindex="-1">
            <p class="letterpaper__to"><?= e($g['message_title']) ?> 💌</p>
            <?php foreach ($g['message'] as $para): ?>
              <p><?= t($para) ?></p>
            <?php endforeach; ?>
            <p class="letterpaper__sign"><?= e($g['message_sign']) ?></p>
          </div>
          <div class="actions" data-finish-wrap hidden>
            <button class="btn btn--primary" type="button" data-to-quiz>One last surprise →</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================
       Screen 6 — one final question before the gift opens
       ========================================================== -->
  <section class="screen" data-screen="6" aria-labelledby="s6-title" hidden>
    <div class="window window--blue">
      <div class="window__bar">
        <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="window__title">final_security_question.exe</span>
      </div>
      <div class="window__body quiz">
        <p class="kicker">Security check 🔐 — last one, promise</p>
        <p class="quiz__lock" aria-hidden="true" data-quiz-lock>🔒</p>
        <h2 class="big big--md" id="s6-title"><?= e($g['quiz_question']) ?></h2>

        <form class="quiz__form" data-quiz novalidate autocomplete="off">
          <label class="visually-hidden" for="quiz-answer">Your answer</label>
          <input class="quiz__input" id="quiz-answer" type="text" autocapitalize="off" autocorrect="off" spellcheck="false"
                 placeholder="Type your answer…" data-quiz-input
                 data-answer="<?= e(base64_encode(strtolower($g['quiz_answer']))) ?>">
          <button class="btn btn--primary" type="submit">Submit</button>
        </form>

        <p class="toast" data-quiz-toast role="status" aria-live="polite"></p>
        <p class="quiz__tries" data-quiz-tries></p>
        <p class="quiz__hint" data-quiz-hint aria-live="polite" hidden></p>
        <script type="application/json" id="quiz-hints"><?= json_encode($g['quiz_hints'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

        <div class="actions" data-quiz-done hidden>
          <p class="quiz__win" data-quiz-win><?= e($g['quiz_correct']) ?></p>
          <button class="btn btn--primary" type="button" data-finish>Open my gift 🎁 →</button>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Photo viewer for the scrapbook -->
<dialog class="lightbox" data-lightbox aria-label="Photo viewer">
  <figure class="lightbox__figure">
    <div class="lightbox__frame" data-lb-frame></div>
    <figcaption class="lightbox__cap" data-lb-cap></figcaption>
  </figure>
  <button class="lightbox__btn lightbox__btn--prev" type="button" data-lb-prev aria-label="Previous photo">‹</button>
  <button class="lightbox__btn lightbox__btn--next" type="button" data-lb-next aria-label="Next photo">›</button>
  <button class="lightbox__btn lightbox__btn--close" type="button" data-lb-close aria-label="Close">×</button>
  <p class="lightbox__count" data-lb-count></p>
</dialog>
<script type="application/json" id="memories-data"><?= json_encode(array_map(fn ($m) => ['src' => $m['full'] ? html_entity_decode($m['full']) : null, 'caption' => $m['caption']], $memories), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

<!-- The celebration after she answers "No" -->
<div class="celebrate" data-celebrate hidden>
  <div class="celebrate__hearts" aria-hidden="true" data-hearts></div>
  <div class="celebrate__card">
    <div class="celebrate__face" aria-hidden="true">
      <?php $partyPhoto = $reactionData['photos']['happy'] ?? $reactionData['photos']['default']; ?>
      <?php if ($partyPhoto): ?>
        <img src="<?= $partyPhoto ?>" alt="">
      <?php else: ?>
        <span>😍</span>
      <?php endif; ?>
    </div>
    <p class="celebrate__text" role="status">
      <?php foreach (preg_split('/\s+/', trim($g['no_line'])) as $i => $word): ?>
        <span style="--w:<?= $i ?>"><?= e($word) ?></span>
      <?php endforeach; ?>
    </p>
    <?php if ($nick): ?><p class="celebrate__from">— your <?= e($nick) ?> 😘</p><?php endif; ?>
    <button class="btn btn--primary celebrate__go" type="button" data-celebrate-go>Yay! Continue 💕</button>
  </div>
</div>

<!-- The little card from him, shown after the wishlist is submitted -->
<dialog class="note-card" data-note aria-labelledby="note-title">
  <div class="window window--yellow">
    <div class="window__bar">
      <span class="window__dots" aria-hidden="true"><i></i><i></i><i></i></span>
      <span class="window__title">message_from_<?= e(strtolower($nick ?: 'hubby')) ?>.txt</span>
    </div>
    <div class="window__body note-card__body">
      <div class="note-card__face" aria-hidden="true">
        <?php $notePhoto = $reactionData['photos']['happy'] ?? $reactionData['photos']['default']; ?>
        <?php if ($notePhoto): ?>
          <img src="<?= $notePhoto ?>" alt="">
        <?php else: ?>
          <span>🥺</span>
        <?php endif; ?>
      </div>
      <p class="kicker" id="note-title">Wait, before you go…</p>
      <p class="note-card__message">“<?= e($g['wish_note']) ?>”</p>
      <p class="note-card__sign">— your <?= e($nick) ?> 💕</p>
      <div class="note-card__answers">
        <button class="btn btn--primary" type="button" data-note-answer="love"><?= e($g['wish_answers'][0]) ?></button>
        <button class="btn btn--ghost" type="button" data-note-answer="both"><?= e($g['wish_answers'][1]) ?></button>
      </div>
    </div>
  </div>
</dialog>

<div class="confetti" aria-hidden="true" data-confetti></div>
<div class="reactions" aria-hidden="true" data-reactions></div>
<script type="application/json" id="reaction-data"><?= json_encode($reactionData, JSON_UNESCAPED_SLASHES) ?></script>

</body>
</html>
