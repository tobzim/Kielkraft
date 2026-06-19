<?php
/**
 * Dev utility: downscale + convert downloaded Higgsfield PNGs to optimised WebP
 * for the public asset pipeline (heroes/banners/textures served directly by nginx).
 * Run with the portable php83 toolchain:
 *   php83\php.exe scripts\convert-webp.php
 */
$imgDir = dirname(__DIR__) . '/public/assets/img/';
$jobs = [
    ['hero-petrol-src.png',     'hero-petrol.webp',     2000],
    ['hero-electric-src.png',   'hero-electric.webp',   2000],
    ['banner-petrol-src.png',   'banner-petrol.webp',   2400],
    ['banner-electric-src.png', 'banner-electric.webp', 2400],
    ['texture-water-src.png',   'texture-water.webp',   1800],
    ['texture-alu-src.png',     'texture-alu.webp',     1800],
    ['lifestyle-src.png',       'lifestyle.webp',       2000],
];
function mv_load($path)
{
    $info = @getimagesize($path);
    if (!$info) { return false; }
    switch ($info[2]) {
        case IMAGETYPE_PNG:  return @imagecreatefrompng($path);
        case IMAGETYPE_JPEG: return @imagecreatefromjpeg($path);
        case IMAGETYPE_WEBP: return @imagecreatefromwebp($path);
        default: return false;
    }
}

foreach ($jobs as [$in, $out, $maxw]) {
    $inp = $imgDir . $in;
    if (!is_file($inp)) { echo "skip (missing): $in\n"; continue; }
    $src = mv_load($inp);
    if (!$src) { echo "fail load: $in\n"; continue; }
    $w = imagesx($src); $h = imagesy($src);
    if ($w > $maxw) {
        $nh = (int) round($h * $maxw / $w);
        $dst = imagecreatetruecolor($maxw, $nh);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxw, $nh, $w, $h);
    } else {
        $dst = $src;
    }
    imagewebp($dst, $imgDir . $out, 82);
    printf("ok: %s -> %s (%dx%d, %d KB)\n", $in, $out, imagesx($dst), imagesy($dst), (int) (filesize($imgDir . $out) / 1024));
}
