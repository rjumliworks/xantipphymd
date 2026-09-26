<?php

/** Simple 24px line icons, drawn for this site. They inherit currentColor. */
function icon(string $name): string
{
    $paths = [
        'shield'      => '<path d="M12 3l7 3v5c0 4.6-3 8.3-7 10-4-1.7-7-5.4-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
        'stethoscope' => '<path d="M6 3v6a4 4 0 008 0V3"/><path d="M10 13v2a5 5 0 0010 0v-2"/><circle cx="20" cy="11" r="2"/><path d="M5 3h2M13 3h2"/>',
        'pulse'       => '<path d="M3 12h4l2-5 4 10 2-5h6"/>',
        'clipboard'   => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="M9 10h6M9 14h6M9 18h3"/>',
        'chat'        => '<path d="M4 5h16v11H9l-5 4V5z"/><path d="M8 10h8M8 13h5"/>',
        'leaf'        => '<path d="M5 19c0-8 5-13 14-14 0 9-5 14-13 14"/><path d="M5 19c3-4 6-6 9-8"/>',
    ];

    return '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">'
         . ($paths[$name] ?? '') . '</svg>';
}
