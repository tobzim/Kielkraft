<?php
/** Organization JSON-LD - consistent NAP entity signal for SEO/GEO. */
$org = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => $site->title()->or('Marvento')->value(),
    'url'      => $site->url(),
    'logo'     => url('assets/img/logo.svg'),
    'description' => (string) t('marvento.tagline'),
    'sameAs'   => [],
    'contactPoint' => [
        '@type'             => 'ContactPoint',
        'contactType'       => 'customer service',
        'email'             => 'info@marvento.de',
        'availableLanguage' => ['de', 'en'],
    ],
];
?>
<script type="application/ld+json"><?= json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
