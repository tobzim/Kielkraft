<?php
/**
 * Generate branded Open-Graph share images (1200x630):
 *   - public/assets/img/og-default.jpg          (site-wide default)
 *   - public/assets/img/og/<slug>.jpg           (one per product)
 * Navy CI background + freigestellt motor (from *-cut.webp) + wordmark/title.
 * Run in the app container (GD + dompdf bundled fonts):
 *   docker exec kielkraft-app-1 php scripts/og-images.php
 */

$root   = dirname(__DIR__);
$outDir = "$root/public/assets/img/og";
@mkdir($outDir, 0775, true);
$ttfB = "$root/vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf";
$ttfR = "$root/vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf";

function og_canvas(int $W, int $H)
{
    $c = imagecreatetruecolor($W, $H);
    imagefilledrectangle($c, 0, 0, $W, $H, imagecolorallocate($c, 11, 47, 85));
    imagefilledpolygon($c, [$W, 0, $W, $H, $W - 460, 0], imagecolorallocatealpha($c, 22, 80, 143, 90));
    imagealphablending($c, true);
    return $c;
}

function og_motor($c, string $cut, int $W, int $H): void
{
    $m = @imagecreatefromwebp($cut);
    if (!$m) { return; }
    $mw = imagesx($m); $mh = imagesy($m);
    $th = 540; $tw = (int) ($mw * $th / $mh);
    imagecopyresampled($c, $m, $W - $tw - 80, (int) (($H - $th) / 2), 0, 0, $tw, $th, $mw, $mh);
    imagedestroy($m);
}

// Shrink-to-fit: largest font size where the text width stays <= $maxW
function og_fit(string $ttf, string $text, int $start, int $maxW): int
{
    for ($s = $start; $s >= 20; $s -= 2) {
        $bb = imagettfbbox($s, 0, $ttf, $text);
        if (($bb[2] - $bb[0]) <= $maxW) { return $s; }
    }
    return 20;
}

// ---- Default OG ----
$c = og_canvas(1200, 630);
og_motor($c, "$root/public/assets/img/products/tohatsu-mfs6-dd-cut.webp", 1200, 630);
$white = imagecolorallocate($c, 255, 255, 255);
$mut   = imagecolorallocate($c, 176, 201, 224);
imagettftext($c, 72, 0, 80, 300, $white, $ttfB, 'Kielkraft');
imagettftext($c, 30, 0, 84, 358, $mut, $ttfR, 'Außenbordmotoren');
imagettftext($c, 24, 0, 84, 408, $mut, $ttfR, 'Tohatsu · ePropulsion');
imagejpeg($c, "$root/public/assets/img/og-default.jpg", 86);
imagedestroy($c);
echo "og-default.jpg\n";

// ---- Per product ----
foreach (['content/1_elektro-aussenborder', 'content/2_benzin-aussenborder'] as $cat) {
    foreach (glob("$root/$cat/*", GLOB_ONLYDIR) as $dir) {
        $slug = preg_replace('/^\d+_/', '', basename($dir));
        $cut  = "$root/public/assets/img/products/$slug-cut.webp";
        if (!is_file($cut)) { continue; }
        $txt = (string) @file_get_contents("$dir/product.de.txt");
        preg_match('/^Title:\s*(.+)$/m', $txt, $mt);
        preg_match('/^PriceFrom:\s*([0-9.]+)/m', $txt, $mp);
        $title = trim($mt[1] ?? $slug);
        $price = $mp[1] ?? '';

        $c = og_canvas(1200, 630);
        og_motor($c, $cut, 1200, 630);
        $white = imagecolorallocate($c, 255, 255, 255);
        $mut   = imagecolorallocate($c, 176, 201, 224);
        imagettftext($c, 19, 0, 84, 150, $mut, $ttfB, 'KIELKRAFT');
        $fs = og_fit($ttfB, $title, 54, 640);
        imagettftext($c, $fs, 0, 80, 290, $white, $ttfB, $title);
        if ($price !== '') {
            imagettftext($c, 28, 0, 84, 360, $mut, $ttfR, 'ab ' . number_format((float) $price, 0, ',', '.') . ' € · inkl. MwSt.');
        }
        imagejpeg($c, "$outDir/$slug.jpg", 86);
        imagedestroy($c);
        echo "og/$slug.jpg\n";
    }
}
echo "done\n";
