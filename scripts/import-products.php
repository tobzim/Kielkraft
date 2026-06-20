<?php
/**
 * One-off: import real product photos from the source product pages,
 * flatten onto white, downscale, save as WebP into each product folder.
 * Run: php83\php.exe scripts\import-products.php
 *
 * NOTE: these are the supplier's product shots used per the project brief.
 * For production, replace with properly licensed manufacturer press images.
 */
$root = dirname(__DIR__);
$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36';

$items = [
    ['https://boostboards.de/wp-content/uploads/2025/07/1-1-1.png',                 'content/1_elektro-aussenborder/1_epropulsion-spirit-1-0-plus/epropulsion-spirit-1-0-plus.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/1-1.png',                   'content/1_elektro-aussenborder/2_epropulsion-elite/epropulsion-elite.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/6ps-blau.png',              'content/2_benzin-aussenborder/1_tohatsu-mfs6-dd/tohatsu-mfs6-dd.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/35ps-1024x1352.png',        'content/2_benzin-aussenborder/2_tohatsu-mfs-3-5c/tohatsu-mfs-3-5c.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/SailPro.png',               'content/2_benzin-aussenborder/3_tohatsu-mfs6-d-sail-pro/tohatsu-mfs6-d-sail-pro.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/8PS-1-1024x1169.png',       'content/2_benzin-aussenborder/4_tohatsu-mfs8c/tohatsu-mfs8c.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/9.9-Weiss-1024x816.png',    'content/2_benzin-aussenborder/5_tohatsu-mfs9-9cy/tohatsu-mfs9-9cy.webp'],
    ['https://boostboards.de/wp-content/uploads/2025/07/15PS-Pinne-scaled-1-1024x816.png', 'content/2_benzin-aussenborder/6_tohatsu-mfs15e/tohatsu-mfs15e.webp'],
];

function fetch_url(string $url, string $ua): string|false
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 60,
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ($data !== false && $code >= 200 && $code < 300) ? $data : false;
}

$maxw = 1280;
foreach ($items as [$url, $dest]) {
    $data = fetch_url($url, $ua);
    if ($data === false) { echo "FAIL download: $url\n"; continue; }
    $src = @imagecreatefromstring($data);
    if (!$src) { echo "FAIL decode: $url\n"; continue; }
    $w = imagesx($src); $h = imagesy($src);
    $tw = min($w, $maxw);
    $th = (int) round($h * $tw / $w);
    $dst = imagecreatetruecolor($tw, $th);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefilledrectangle($dst, 0, 0, $tw, $th, $white);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
    $out = $root . '/' . $dest;
    imagewebp($dst, $out, 86);
    printf("ok: %-42s %dx%d  %d KB\n", basename($dest), $tw, $th, (int) (filesize($out) / 1024));
    imagedestroy($src); imagedestroy($dst);
}
