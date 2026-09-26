<?php

/**
 * Resized, cached copies of photos so big phone pictures load fast.
 *
 * resized('assets/img/us/01.jpg', 900) → 'assets/img/_cache/us-01-900.jpg?v=…'
 * The copy is made once (on first view) with GD, rotated upright using the
 * photo's EXIF orientation, and re-made if the original changes.
 * If anything fails, the original file is used instead.
 */
function resized(string $rel, int $maxW): string
{
    $root = realpath(__DIR__ . '/..');
    $src  = $root . '/' . $rel;
    if (!is_file($src) || !extension_loaded('gd')) {
        return asset($rel);
    }

    $name  = preg_replace('/[^a-z0-9]+/i', '-', pathinfo(str_replace('assets/img/', '', $rel), PATHINFO_DIRNAME) . '-' . pathinfo($rel, PATHINFO_FILENAME));
    $name  = trim($name, '-.');
    $cache = "assets/img/_cache/{$name}-{$maxW}.jpg";
    $dst   = $root . '/' . $cache;

    if (is_file($dst) && filemtime($dst) >= filemtime($src)) {
        return asset($cache);
    }

    try {
        $info = @getimagesize($src);
        if (!$info) return asset($rel);

        $img = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => @imagecreatefrompng($src),
            IMAGETYPE_WEBP => @imagecreatefromwebp($src),
            default        => false,
        };
        if (!$img) return asset($rel);

        // Phones store photos sideways and rely on EXIF to rotate them.
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($src);
            $img = match ((int) ($exif['Orientation'] ?? 1)) {
                3 => imagerotate($img, 180, 0),
                6 => imagerotate($img, -90, 0),
                8 => imagerotate($img, 90, 0),
                default => $img,
            };
        }

        $w = imagesx($img);
        $h = imagesy($img);
        if ($w > $maxW) {
            $nh  = (int) round($h * $maxW / $w);
            $out = imagecreatetruecolor($maxW, $nh);
            // flatten transparency onto white (polaroid paper)
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
            imagecopyresampled($out, $img, 0, 0, 0, 0, $maxW, $nh, $w, $h);
            $img = $out;
        } elseif ($info[2] !== IMAGETYPE_JPEG) {
            $out = imagecreatetruecolor($w, $h);
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
            imagecopy($out, $img, 0, 0, 0, 0, $w, $h);
            $img = $out;
        }

        if (!is_dir(dirname($dst))) @mkdir(dirname($dst), 0775, true);
        imageinterlace($img, true);
        imagejpeg($img, $dst, 80);

        return is_file($dst) ? asset($cache) : asset($rel);
    } catch (Throwable) {
        return asset($rel);
    }
}

/** Photos in a folder, in natural filename order (01, 02, … 10). */
function photos_in(string $dir): array
{
    $root  = realpath(__DIR__ . '/..');
    $files = array_filter(
        glob($root . '/' . trim($dir, '/') . '/*') ?: [],
        fn ($f) => is_file($f) && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)
    );
    natcasesort($files);
    return array_values(array_map(fn ($f) => trim($dir, '/') . '/' . basename($f), $files));
}
