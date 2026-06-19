<?php
$lang = $kirby->language();
$code = $lang ? $lang->code() : 'de';
$en   = $code === 'en';

$metaTitle = $page->metaTitle()->or(
    $page->isHomePage() ? $site->title() : $page->title() . ' | ' . $site->title()
);
$metaDesc = $page->metaDescription()->or($site->description());

// Language-aware section URLs (pages are added in later phases)
$nav = [
    'electric' => $en ? 'en/electric-outboards' : 'elektro-aussenborder',
    'petrol'   => $en ? 'en/petrol-outboards'   : 'benzin-aussenborder',
    'advisor'  => $en ? 'en/buying-advisor'      : 'kaufberater',
    'guide'    => $en ? 'en/guide'               : 'ratgeber',
    'service'  => $en ? 'en/service'             : 'service',
    'cart'     => $en ? 'en/cart'                : 'warenkorb',
];
?><!doctype html>
<html lang="<?= $code ?>" dir="<?= $lang ? $lang->direction() : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $metaTitle->html() ?></title>
    <meta name="description" content="<?= $metaDesc->html() ?>">
    <meta name="robots" content="<?= $page->isHomePage() || $page->isListed() ? 'index, follow' : 'noindex, follow' ?>">
    <link rel="canonical" href="<?= $page->url() ?>">
<?php foreach ($kirby->languages() as $l): ?>
    <link rel="alternate" hreflang="<?= $l->code() ?>" href="<?= $page->url($l->code()) ?>">
<?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= $page->url($kirby->defaultLanguage()->code()) ?>">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= $site->title()->html() ?>">
    <meta property="og:title" content="<?= $metaTitle->html() ?>">
    <meta property="og:description" content="<?= $metaDesc->html() ?>">
    <meta property="og:url" content="<?= $page->url() ?>">
    <meta property="og:locale" content="<?= $en ? 'en_US' : 'de_DE' ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="<?= url('assets/img/favicon.svg') ?>" type="image/svg+xml">
    <?= css(['assets/css/tokens.css', 'assets/css/app.css']) ?>
    <?php snippet('seo/jsonld-organization') ?>
</head>
<body>
<a class="skip-link" href="#main"><?= t('marvento.skip') ?></a>

<header class="site-header" data-header>
    <div class="container site-header__bar">
        <a class="brand" href="<?= $site->url($code) ?>" aria-label="<?= $site->title()->or('Marvento') ?>">
            <img class="brand__mark" src="<?= url('assets/img/logo.svg') ?>" alt="" width="34" height="34">
            <span class="brand__name"><?= $site->title()->or('Marvento') ?></span>
        </a>

        <nav class="main-nav" aria-label="<?= $en ? 'Main navigation' : 'Hauptnavigation' ?>">
            <ul>
                <li><a href="<?= url($nav['electric']) ?>" data-axis="cool"><span class="dot"></span><?= t('nav.electric') ?></a></li>
                <li><a href="<?= url($nav['petrol']) ?>" data-axis="warm"><span class="dot"></span><?= t('nav.petrol') ?></a></li>
                <li><a href="<?= url($nav['advisor']) ?>"><?= t('nav.advisor') ?></a></li>
                <li><a href="<?= url($nav['guide']) ?>"><?= t('nav.guide') ?></a></li>
                <li><a href="<?= url($nav['service']) ?>"><?= t('nav.service') ?></a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <div class="lang-switch" aria-label="<?= $en ? 'Language' : 'Sprache' ?>">
<?php foreach ($kirby->languages() as $l): ?>
                <a href="<?= $page->url($l->code()) ?>"<?= $l->code() === $code ? ' aria-current="true"' : '' ?>><?= strtoupper($l->code()) ?></a>
<?php endforeach; ?>
            </div>
            <a class="cart-btn" href="<?= url($nav['cart']) ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                <span><?= t('cart.title') ?></span>
                <span class="cart-btn__count" data-cart-count>0</span>
            </a>
            <button class="nav-toggle" type="button" aria-label="<?= t('marvento.menu') ?>" data-nav-toggle>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
        </div>
    </div>
</header>

<main id="main">
