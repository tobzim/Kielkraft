<?php

/**
 * Kielkraft - Kirby configuration.
 * Driven by environment variables (Docker). See .env.example for all keys.
 */

if (!function_exists('mv_env')) {
    function mv_env(string $key, $default = null)
    {
        $v = getenv($key);
        return $v === false ? $default : $v;
    }
}

if (!function_exists('mv_bool')) {
    function mv_bool(string $key, bool $default = false): bool
    {
        $v = getenv($key);
        if ($v === false) {
            return $default;
        }
        return in_array(strtolower((string) $v), ['1', 'true', 'on', 'yes'], true);
    }
}

return [
    'debug' => mv_bool('KIRBY_DEBUG', false),
    'url'   => mv_env('APP_URL', null),

    // Multi-language: DE (default) + EN
    'languages' => true,

    // Panel: allow first-user creation only outside production
    'panel' => [
        'install' => mv_env('APP_ENV', 'production') !== 'production',
    ],

    // Pages cache (enable in production via KIRBY_CACHE=true)
    'cache' => [
        'pages' => [
            'active' => mv_bool('KIRBY_CACHE', false),
        ],
    ],

    // Image pipeline: GD with WebP output (AVIF available in the image)
    'thumbs' => [
        'driver'  => 'gd',
        'quality' => 82,
        'format'  => 'webp',
    ],

    // Localised date formatting
    'date' => [
        'handler' => 'intl',
    ],

    // Transactional mail (ESP in prod; Mailpit in dev). Never PHP mail().
    'email' => [
        'transport' => [
            'type'     => mv_env('MAIL_TRANSPORT', 'smtp'),
            'host'     => mv_env('MAIL_HOST', 'mailpit'),
            'port'     => (int) mv_env('MAIL_PORT', 1025),
            'security' => mv_env('MAIL_SECURITY', '') ?: false,
            'auth'     => (bool) mv_env('MAIL_USER', false),
            'username' => mv_env('MAIL_USER', null),
            'password' => mv_env('MAIL_PASSWORD', null),
        ],
    ],

    // App-level settings consumed by Kielkraft code (shop, invoicing, partner)
    'kielkraft' => [
        'env'             => mv_env('APP_ENV', 'production'),
        'invoiceProvider' => mv_env('INVOICE_PROVIDER', 'fallback'),
        'mailFrom'        => mv_env('MAIL_FROM', 'bestellung@kielkraft.de'),
        'mailFromName'    => mv_env('MAIL_FROM_NAME', 'Kielkraft'),
        'contactTo'       => mv_env('MAIL_CONTACT_TO', 'info@boostboards.de'),
        'partnerBcc'      => mv_env('PARTNER_ORDER_BCC', null),
    ],

    // Routes & hooks (shop, Stripe webhooks, sitemap) are registered by the
    // site/plugins/kielkraft plugin in later build phases.
];
