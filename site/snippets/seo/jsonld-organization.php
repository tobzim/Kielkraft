<?php
/** Organization JSON-LD - consistent NAP entity signal for SEO/GEO. */
$org = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'      => $site->title()->or('Kielkraft')->value(),
    'legalName' => 'Boostboards GmbH & Co. KG',
    'url'       => $site->url(),
    'logo'      => url('assets/img/logo.svg'),
    'description' => (string) t('kielkraft.tagline'),
    'email'     => 'info@boostboards.de',
    'telephone' => '+49 40 60 90 199 69',
    'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Groten Hoff 21',
        'postalCode'      => '22359',
        'addressLocality' => 'Hamburg',
        'addressCountry'  => 'DE',
    ],
    'sameAs'   => [],
    'contactPoint' => [
        '@type'             => 'ContactPoint',
        'contactType'       => 'customer service',
        'telephone'         => '+49 40 60 90 199 69',
        'email'             => 'info@boostboards.de',
        'availableLanguage' => ['de', 'en'],
    ],
];
?>
<script type="application/ld+json"><?= json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
