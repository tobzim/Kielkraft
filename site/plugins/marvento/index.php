<?php

use Kirby\Cms\App as Kirby;
use Kirby\Http\Response;

/**
 * Format a price for the active (or given) language.
 * de: 1.395  ·  en: 1,395  (decimals only when present)
 */
if (!function_exists('mv_price')) {
    function mv_price($value, ?string $lang = null): string
    {
        $lang = $lang ?? (kirby()->language() ? kirby()->language()->code() : 'de');
        $n = (float) $value;
        $dec = fmod($n, 1.0) !== 0.0 ? 2 : 0;
        return $lang === 'en'
            ? number_format($n, $dec, '.', ',')
            : number_format($n, $dec, ',', '.');
    }
}

/** Price including the euro sign. */
if (!function_exists('mv_eur')) {
    function mv_eur($value, ?string $lang = null): string
    {
        return mv_price($value, $lang) . ' €';
    }
}

/** Versioned asset URL (cache-busting via file mtime; beats immutable caching). */
if (!function_exists('mv_asset')) {
    function mv_asset(string $path): string
    {
        $file = kirby()->root('index') . '/' . ltrim($path, '/');
        $v = is_file($file) ? filemtime($file) : 1;
        return url($path) . '?v=' . $v;
    }
}

/**
 * Normalised product-feed items (one per orderable variant) for all export
 * channels (Google Shopping, Meta/Facebook, Idealo, generic CSV).
 */
if (!function_exists('kk_feed_items')) {
    function kk_feed_items(): array
    {
        $kirby = kirby();
        $site  = $kirby->site();
        $catDefault = $site->googleProductCategory()->or('Vehicles & Parts > Vehicle Parts & Accessories > Watercraft Parts & Accessories')->value();
        $availMap = ['instock' => 'in_stock', 'short' => 'in_stock', 'preorder' => 'preorder'];

        $items = [];
        foreach ($site->index()->filterBy('intendedTemplate', 'product')->listed() as $p) {
            $img   = $p->image();
            $imgUrl = $img ? $img->resize(1200)->url() : '';
            $avail = $availMap[$p->availability()->or('instock')->value()] ?? 'in_stock';
            $brand = $p->brand()->value();
            $desc  = trim(strip_tags((string) $p->intro()->or($p->metaDescription())));
            $gcat  = $p->googleProductCategory()->or($catDefault)->value();
            $gtin  = trim((string) $p->gtin());
            $ship  = (float) $p->shippingCost()->or(0)->value();
            $variants = $p->variants()->toStructure();

            $rows = [];
            if ($variants->count() > 0) {
                foreach ($variants as $i => $v) {
                    $rows[] = [
                        'id'    => $v->sku()->isNotEmpty() ? $v->sku()->value() : ($p->slug() . '-' . ($i + 1)),
                        'group' => $p->slug(),
                        'title' => $p->title()->value() . ' – ' . $v->label()->value(),
                        'price' => (float) $v->price()->value(),
                        'mpn'   => ($p->mpn()->or($p->modelcode())->value()),
                    ];
                }
            } else {
                $rows[] = [
                    'id'    => $p->slug(),
                    'group' => null,
                    'title' => $p->title()->value(),
                    'price' => (float) $p->priceFrom()->value(),
                    'mpn'   => ($p->mpn()->or($p->modelcode())->value()),
                ];
            }

            foreach ($rows as $r) {
                $items[] = [
                    'id'          => $r['id'],
                    'item_group'  => $r['group'],
                    'title'       => $r['title'],
                    'description' => $desc !== '' ? $desc : $r['title'],
                    'link'        => $p->url(),
                    'image_link'  => $imgUrl,
                    'availability' => $avail,
                    'price'       => number_format($r['price'], 2, '.', '') . ' EUR',
                    'brand'       => $brand,
                    'condition'   => 'new',
                    'gtin'        => $gtin,
                    'mpn'         => $r['mpn'],
                    'category'    => $gcat,
                    'shipping_country' => 'DE',
                    'shipping_price'   => number_format($ship, 2, '.', '') . ' EUR',
                ];
            }
        }
        return $items;
    }
}

Kirby::plugin('kielkraft/core', [
    'routes' => [
        [
            'pattern' => 'sitemap.xml',
            'action'  => function () {
                $kirby = kirby();
                $langs = $kirby->languages();
                $def   = $kirby->defaultLanguage()->code();
                $pages = $kirby->site()->index()->listed();

                $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
                foreach ($pages as $p) {
                    $xml .= '  <url><loc>' . htmlspecialchars($p->url($def)) . '</loc>';
                    foreach ($langs as $l) {
                        $xml .= '<xhtml:link rel="alternate" hreflang="' . $l->code() . '" href="' . htmlspecialchars($p->url($l->code())) . '"/>';
                    }
                    $xml .= '<xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($p->url($def)) . '"/>';
                    $xml .= '</url>' . "\n";
                }
                $xml .= '</urlset>';

                return new Response($xml, 'application/xml');
            },
        ],

        // Google Shopping / Merchant Center feed (RSS 2.0 + g: namespace)
        [
            'pattern' => 'feeds/google.xml',
            'action'  => function () {
                $site = kirby()->site();
                $e = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8');

                $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"><channel>' . "\n";
                $xml .= '<title>' . $e($site->title()->or('Kielkraft')) . '</title>';
                $xml .= '<link>' . $e($site->url()) . '</link>';
                $xml .= '<description>' . $e($site->description()) . '</description>' . "\n";

                foreach (kk_feed_items() as $it) {
                    $xml .= '<item>';
                    $xml .= '<g:id>' . $e($it['id']) . '</g:id>';
                    if (!empty($it['item_group'])) {
                        $xml .= '<g:item_group_id>' . $e($it['item_group']) . '</g:item_group_id>';
                    }
                    $xml .= '<g:title>' . $e($it['title']) . '</g:title>';
                    $xml .= '<g:description>' . $e($it['description']) . '</g:description>';
                    $xml .= '<g:link>' . $e($it['link']) . '</g:link>';
                    if ($it['image_link'] !== '') {
                        $xml .= '<g:image_link>' . $e($it['image_link']) . '</g:image_link>';
                    }
                    $xml .= '<g:availability>' . $e($it['availability']) . '</g:availability>';
                    $xml .= '<g:price>' . $e($it['price']) . '</g:price>';
                    $xml .= '<g:brand>' . $e($it['brand']) . '</g:brand>';
                    $xml .= '<g:condition>' . $e($it['condition']) . '</g:condition>';
                    if ($it['gtin'] !== '') {
                        $xml .= '<g:gtin>' . $e($it['gtin']) . '</g:gtin>';
                    }
                    if ($it['mpn'] !== '') {
                        $xml .= '<g:mpn>' . $e($it['mpn']) . '</g:mpn>';
                    }
                    if ($it['gtin'] === '' && $it['mpn'] === '') {
                        $xml .= '<g:identifier_exists>false</g:identifier_exists>';
                    }
                    $xml .= '<g:google_product_category>' . $e($it['category']) . '</g:google_product_category>';
                    $xml .= '<g:shipping><g:country>' . $e($it['shipping_country']) . '</g:country><g:price>' . $e($it['shipping_price']) . '</g:price></g:shipping>';
                    $xml .= '</item>' . "\n";
                }

                $xml .= '</channel></rss>';
                return new Response($xml, 'application/xml');
            },
        ],

        // Generic CSV feed (Meta/Facebook catalogue, Idealo, billiger.de, ...)
        [
            'pattern' => 'feeds/products.csv',
            'action'  => function () {
                $cols = ['id', 'item_group_id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand', 'gtin', 'mpn', 'google_product_category', 'shipping'];
                $out = fopen('php://temp', 'r+');
                fputcsv($out, $cols);
                foreach (kk_feed_items() as $it) {
                    fputcsv($out, [
                        $it['id'], $it['item_group'], $it['title'], $it['description'],
                        $it['availability'], $it['condition'], $it['price'], $it['link'],
                        $it['image_link'], $it['brand'], $it['gtin'], $it['mpn'],
                        $it['category'], $it['shipping_country'] . ':::' . $it['shipping_price'],
                    ]);
                }
                rewind($out);
                $csv = stream_get_contents($out);
                fclose($out);
                return new Response($csv, 'text/csv');
            },
        ],
    ],
]);
