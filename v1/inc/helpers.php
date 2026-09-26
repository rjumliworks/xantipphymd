<?php

/** Escape for HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Escape, then mark any [PLACEHOLDER] so it is visible until replaced. */
function t($value): string
{
    return preg_replace('/\[([^\]]+)\]/', '<span class="ph">[$1]</span>', e($value));
}

/** Like t(), but allows the <em> tag used for editorial emphasis. */
function t_em($value): string
{
    return str_replace(['&lt;em&gt;', '&lt;/em&gt;'], ['<em>', '</em>'], t($value));
}

function is_placeholder($value): bool
{
    return str_contains((string) $value, '[');
}

/** Cache-busted asset URL. */
function asset(string $path): string
{
    $file = __DIR__ . '/../' . $path;
    return e($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/**
 * An <img> when the file exists, otherwise a labelled placeholder frame
 * of the same proportions — so the layout is honest before photos arrive.
 */
function media(string $src, string $alt, string $label, bool $eager = false): string
{
    $file = __DIR__ . '/../' . $src;

    if (is_file($file)) {
        $size  = @getimagesize($file);
        $dims  = $size ? sprintf(' width="%d" height="%d"', $size[0], $size[1]) : '';
        $load  = $eager ? ' loading="eager" fetchpriority="high"' : ' loading="lazy"';
        return sprintf('<img src="%s" alt="%s"%s%s decoding="async">', asset($src), e($alt), $dims, $load);
    }

    return sprintf(
        '<div class="ph-media" role="img" aria-label="%s"><span class="ph-media__label">%s</span><code class="ph-media__path">%s</code></div>',
        e($alt), e($label), e($src)
    );
}

function arrow(): string
{
    return '<svg class="arrow" viewBox="0 0 22 10" aria-hidden="true" focusable="false"><path d="M0 5h20.5M16.5 1l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.1"/></svg>';
}

function theme_icon(): string
{
    return '<svg class="theme-toggle__icon" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">'
         . '<circle cx="8" cy="8" r="6.25" fill="none" stroke="currentColor" stroke-width="1.1"/>'
         . '<path class="theme-toggle__half" d="M8 1.75a6.25 6.25 0 0 1 0 12.5z" fill="currentColor"/></svg>';
}
