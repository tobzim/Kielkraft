<?php

use Kirby\Cms\App as Kirby;
use Kirby\Http\Response;

require_once __DIR__ . '/invoice.php';

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

/* ---------------------------------------------------------------------------
 * Transactional e-mail building blocks (CI-branded, email-client safe:
 * table layout, inline styles, web-safe fonts, light background)
 * ------------------------------------------------------------------------- */
if (!function_exists('kk_email_shell')) {
    function kk_email_shell(string $heading, string $bodyHtml, string $preheader = ''): string
    {
        $imp = url('impressum'); $dat = url('datenschutz'); $agb = url('agb');
        $pre = $preheader !== ''
            ? '<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;opacity:0;">' . htmlspecialchars($preheader) . '</div>'
            : '';
        return <<<HTML
<!doctype html>
<html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="x-apple-disable-message-reformatting"></head>
<body style="margin:0;padding:0;background:#eef2f6;">
$pre
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f6;"><tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:100%;background:#ffffff;border:1px solid #e1e8ee;border-radius:12px;overflow:hidden;">
<tr><td style="background:#0B2F55;padding:20px 28px;">
<span style="font-family:Arial,Helvetica,sans-serif;font-weight:800;font-size:22px;letter-spacing:-0.5px;color:#ffffff;">Kielkraft<sup style="font-size:11px;font-weight:700;">&trade;</sup></span>
</td></tr>
<tr><td style="padding:30px 28px 6px;font-family:Arial,Helvetica,sans-serif;">
<h1 style="margin:0 0 14px;font-size:21px;line-height:1.3;color:#16263a;font-weight:800;">$heading</h1>
$bodyHtml
</td></tr>
<tr><td style="padding:22px 28px 30px;font-family:Arial,Helvetica,sans-serif;">
<div style="border-top:1px solid #edf1f5;padding-top:16px;">
<p style="margin:0 0 6px;font-size:12px;line-height:1.6;color:#6b7c8c;">Kielkraft ist eine Marke der Boostboards GmbH &amp; Co. KG &middot; Groten Hoff 21 &middot; 22359 Hamburg</p>
<p style="margin:0 0 6px;font-size:12px;line-height:1.6;color:#6b7c8c;">Tel: +49 40 60 90 199 69 &middot; <a href="mailto:info@boostboards.de" style="color:#0f4e97;text-decoration:none;">info@boostboards.de</a></p>
<p style="margin:0;font-size:12px;line-height:1.6;color:#6b7c8c;"><a href="$imp" style="color:#0f4e97;text-decoration:none;">Impressum</a> &middot; <a href="$dat" style="color:#0f4e97;text-decoration:none;">Datenschutz</a> &middot; <a href="$agb" style="color:#0f4e97;text-decoration:none;">AGB</a></p>
</div>
</td></tr>
</table>
</td></tr></table>
</body></html>
HTML;
    }
}

if (!function_exists('kk_email_button')) {
    function kk_email_button(string $label, string $href): string
    {
        $href = htmlspecialchars($href, ENT_QUOTES);
        return '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px 0 6px;"><tr>'
            . '<td style="border-radius:8px;background:#0B2F55;"><a href="' . $href . '" '
            . 'style="display:inline-block;padding:13px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:8px;">'
            . $label . '</a></td></tr></table>';
    }
}

if (!function_exists('kk_email_panel')) {
    function kk_email_panel(string $html): string
    {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" '
            . 'style="background:#f7fafc;border:1px solid #e1e8ee;border-radius:8px;margin:6px 0 14px;"><tr>'
            . '<td style="padding:14px 16px;font-family:Arial,Helvetica,sans-serif;font-size:14px;line-height:1.7;color:#16263a;">'
            . $html . '</td></tr></table>';
    }
}

/* ---------------------------------------------------------------------------
 * Product search (used by the /suche page and the /search.json autocomplete).
 * AND-matches every query word against title, brand, drive, power and meta.
 * ------------------------------------------------------------------------- */
if (!function_exists('kk_search_products')) {
    function kk_search_products(string $q, string $cat = '')
    {
        $products = kirby()->site()->index()->filterBy('intendedTemplate', 'product')->listed();
        if ($cat === 'elektro' || $cat === 'benzin') {
            $products = $products->filterBy('antrieb', $cat);
        }
        $q = trim($q);
        if ($q === '') {
            return $products->sortBy('powerPs', 'asc');
        }
        $words = array_filter(preg_split('/\s+/', mb_strtolower($q)));
        return $products->filter(function ($p) use ($words) {
            $hay = mb_strtolower(implode(' ', [
                (string) $p->title(), (string) $p->brand(), (string) $p->antrieb(),
                $p->powerPs() . ' ps', $p->powerKw() . ' kw',
                (string) $p->metaDescription(), (string) $p->tagline(),
            ]));
            foreach ($words as $w) {
                if (mb_strpos($hay, $w) === false) { return false; }
            }
            return true;
        })->sortBy('powerPs', 'asc');
    }
}

/* ---------------------------------------------------------------------------
 * Session cart (lightweight; no commercial shop plugin required yet)
 * ------------------------------------------------------------------------- */
if (!function_exists('kk_cart_get')) {
    function kk_cart_get(): array { return (array) kirby()->session()->get('kk.cart', []); }
    function kk_cart_save(array $c): void { kirby()->session()->set('kk.cart', $c); }
    function kk_cart_count(): int { $n = 0; foreach (kk_cart_get() as $i) { $n += (int) $i['qty']; } return $n; }
    function kk_cart_subtotal(): float { $t = 0; foreach (kk_cart_get() as $i) { $t += (float) $i['price'] * (int) $i['qty']; } return $t; }
    // Outboards ship by forwarder: one consolidated shipment = the highest freight in the cart.
    function kk_cart_shipping(): float { $s = 0; foreach (kk_cart_get() as $i) { $s = max($s, (float) $i['shipping']); } return $s; }
    function kk_cart_total(): float { return kk_cart_subtotal() + kk_cart_shipping(); }
    function kk_cart_url(): string { return get('lang') === 'en' ? 'en/cart' : 'warenkorb'; }
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

        // ---- Cart actions ----
        [
            'pattern' => 'cart/add',
            'method'  => 'POST',
            'action'  => function () {
                if (csrf(get('csrf')) !== true) { go('/'); }
                $p = page(get('product'));
                if (!$p) { go(kk_cart_url()); }
                $qty = max(1, (int) get('qty', 1));
                $sku = (string) get('sku');
                $v = $p->variants()->toStructure()->filter(fn ($x) => (string) $x->sku()->value() === $sku)->first();
                $price = $v ? (float) $v->price()->value() : (float) $p->priceFrom()->value();
                $label = $v ? $v->label()->value() : '';
                if ($sku === '') { $sku = $p->slug(); }
                $img = $p->image();
                $cart = kk_cart_get();
                if (isset($cart[$sku])) {
                    $cart[$sku]['qty'] += $qty;
                } else {
                    $cart[$sku] = [
                        'sku' => $sku, 'product' => $p->id(), 'title' => $p->title()->value(),
                        'variant' => $label, 'price' => $price,
                        'shipping' => (float) $p->shippingCost()->or(0)->value(),
                        'qty' => $qty, 'url' => $p->url(),
                        'img' => $img ? $img->resize(160)->url() : '',
                    ];
                }
                kk_cart_save($cart);
                go(kk_cart_url());
            },
        ],
        [
            'pattern' => 'cart/update',
            'method'  => 'POST',
            'action'  => function () {
                if (csrf(get('csrf')) === true) {
                    $cart = kk_cart_get();
                    $sku  = (string) get('sku');
                    $qty  = (int) get('qty', 1);
                    if (isset($cart[$sku])) {
                        if ($qty < 1) { unset($cart[$sku]); } else { $cart[$sku]['qty'] = $qty; }
                    }
                    kk_cart_save($cart);
                }
                go(kk_cart_url());
            },
        ],
        [
            'pattern' => 'cart/remove',
            'method'  => 'POST',
            'action'  => function () {
                if (csrf(get('csrf')) === true) {
                    $cart = kk_cart_get();
                    unset($cart[(string) get('sku')]);
                    kk_cart_save($cart);
                }
                go(kk_cart_url());
            },
        ],
        [
            'pattern' => 'cart/count.json',
            'action'  => function () {
                return Response::json([
                    'count'     => kk_cart_count(),
                    'subtotal'  => kk_cart_subtotal(),
                    'total'     => kk_cart_total(),
                    'formatted' => mv_eur(kk_cart_subtotal()),
                ]);
            },
        ],

        // ---- Search autocomplete (JSON) ----
        [
            'pattern' => 'search.json',
            'action'  => function () {
                $code = kirby()->language() ? kirby()->language()->code() : 'de';
                $hits = kk_search_products((string) get('q'), (string) get('cat'))->limit(6);
                $out = [];
                foreach ($hits as $p) {
                    $slug = $p->slug();
                    $cut  = kirby()->root('index') . '/assets/img/products/' . $slug . '-cut.webp';
                    $cover = $p->image();
                    $img = is_file($cut)
                        ? url('assets/img/products/' . $slug . '-cut.webp')
                        : ($cover ? $cover->resize(120)->url() : '');
                    $v = $p->variants()->toStructure()->first();
                    $price = $v && $v->price()->isNotEmpty() ? (float) $v->price()->value() : (float) $p->priceFrom()->value();
                    $out[] = [
                        'title' => (string) $p->title(),
                        'brand' => (string) $p->brand(),
                        'ps'    => (string) $p->powerPs(),
                        'price' => mv_eur($price, $code),
                        'url'   => $p->url(),
                        'img'   => $img,
                    ];
                }
                return Response::json(['results' => $out]);
            },
        ],

        // ---- Account: logout ----
        [
            'pattern' => 'logout',
            'action'  => function () {
                if ($u = kirby()->user()) { $u->logout(); }
                go(get('lang') === 'en' ? 'en/login' : 'anmelden');
            },
        ],
    ],

    'hooks' => [
        // Auto-unlock "Kauf auf Rechnung" once a customer's order is set to delivered.
        'page.update:after' => function ($newPage) {
            if ($newPage->intendedTemplate()->name() !== 'order') { return; }
            if ((string) $newPage->content()->get('orderStatus') !== 'delivered') { return; }
            $email = (string) $newPage->customerEmail();
            if ($email === '') { return; }
            $user = kirby()->users()->findBy('email', $email);
            if ($user && $user->invoiceEligible()->toBool() === false) {
                kirby()->impersonate('kirby');
                $user->update(['invoiceEligible' => 'true']);
            }
        },
    ],
]);
