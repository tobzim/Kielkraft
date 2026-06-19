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

Kirby::plugin('marvento/core', []);
