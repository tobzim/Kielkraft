<?php
/**
 * Freistellen: remove the connected white studio background from product
 * photos via a border-seeded flood fill. Interior whites (logos, numbers)
 * are preserved because they are not connected to the image border.
 * Output: public/assets/img/products/<slug>-cut.webp (with alpha).
 *
 * Run inside the app container (GD + WebP):
 *   docker exec marvento-app-1 php -d memory_limit=512M scripts/freistellen.php
 */

$root   = dirname(__DIR__);
$outDir = "$root/public/assets/img/products";
@mkdir($outDir, 0775, true);

$cats = ['content/1_elektro-aussenborder', 'content/2_benzin-aussenborder'];
$tol  = 55.0;            // distance-to-white threshold (0..441)
$tol2 = $tol * $tol;

$isBg = function (int $rgb) use ($tol2): bool {
    $dr = 255 - (($rgb >> 16) & 0xFF);
    $dg = 255 - (($rgb >> 8) & 0xFF);
    $db = 255 - ($rgb & 0xFF);
    return ($dr * $dr + $dg * $dg + $db * $db) <= $tol2;
};

foreach ($cats as $cat) {
    foreach (glob("$root/$cat/*", GLOB_ONLYDIR) as $dir) {
        $slug = preg_replace('/^\d+_/', '', basename($dir));
        $src  = "$dir/$slug.webp";
        if (!is_file($src)) { echo "skip (no cover): $slug\n"; continue; }

        $im = @imagecreatefromwebp($src);
        if (!$im) { echo "fail load: $slug\n"; continue; }
        $w = imagesx($im);
        $h = imagesy($im);

        $out = imagecreatetruecolor($w, $h);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagecopy($out, $im, 0, 0, 0, 0, $w, $h);
        imagedestroy($im);

        $vis   = new SplFixedArray($w * $h);
        $stack = [];
        $seed = function (int $x, int $y) use (&$stack, $vis, $w, $out, $isBg): void {
            $i = $y * $w + $x;
            if ($vis[$i]) { return; }
            $vis[$i] = true;
            if ($isBg(imagecolorat($out, $x, $y) & 0xFFFFFF)) { $stack[] = $i; }
        };
        for ($x = 0; $x < $w; $x++) { $seed($x, 0); $seed($x, $h - 1); }
        for ($y = 0; $y < $h; $y++) { $seed(0, $y); $seed($w - 1, $y); }

        $trans = imagecolorallocatealpha($out, 0, 0, 0, 127);
        $removed = 0;
        while ($stack) {
            $i = array_pop($stack);
            $x = $i % $w;
            $y = intdiv($i, $w);
            imagesetpixel($out, $x, $y, $trans);
            $removed++;
            foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as $d) {
                $nx = $x + $d[0];
                $ny = $y + $d[1];
                if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) { continue; }
                $j = $ny * $w + $nx;
                if ($vis[$j]) { continue; }
                $vis[$j] = true;
                if ($isBg(imagecolorat($out, $nx, $ny) & 0xFFFFFF)) { $stack[] = $j; }
            }
        }

        imagesavealpha($out, true);
        $dest = "$outDir/$slug-cut.webp";
        imagewebp($out, $dest, 92);
        imagedestroy($out);
        $pctCut = round($removed / ($w * $h) * 100);
        echo "ok: $slug ({$w}x{$h})  bg removed {$pctCut}%  -> assets/img/products/$slug-cut.webp\n";
    }
}
echo "done\n";
