<?php

use Kirby\Cms\App as Kirby;

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

Kirby::plugin('marvento/core', [
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

                return new \Kirby\Http\Response($xml, 'application/xml');
            },
        ],
    ],
]);
