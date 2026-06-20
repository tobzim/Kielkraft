<?php
$lang = $kirby->language();
$code = $lang ? $lang->code() : 'de';
$en   = $code === 'en';

$metaTitle = $page->metaTitle()->or($page->isHomePage() ? $site->title() : $page->title() . ' | ' . $site->title());
$metaDesc  = $page->metaDescription()->or($site->description());

$u = fn(string $de, string $enp) => url($en ? 'en/' . $enp : $de);
$navE = $u('elektro-aussenborder', 'electric-outboards');
$navP = $u('benzin-aussenborder', 'petrol-outboards');
$navAdv = $u('kaufberater', 'buying-advisor');
$navGuide = $u('ratgeber', 'guide');
$navContact = $u('kontakt', 'contact');
$navCart = $u('warenkorb', 'cart');
$navAccount = $u('konto', 'account');

$catElectric = page('elektro-aussenborder');
$catPetrol   = page('benzin-aussenborder');
$fmt = fn($p) => function_exists('mv_eur') ? mv_eur($p, $code) : $p;
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
    <!-- Tier 1: trust / service bar -->
    <div class="topbar">
        <div class="container topbar__row">
            <span class="topbar__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg><a href="<?= $navContact ?>"><?= $en ? 'Advice: +49 (0) …' : 'Beratung: +49 (0) …' ?></a></span>
            <span class="topbar__item topbar__item--hide-sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg><?= $en ? 'Invoice purchase for returning customers' : 'Kauf auf Rechnung für Bestandskunden' ?></span>
            <span class="topbar__spacer"></span>
            <span class="topbar__item topbar__item--hide-sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?= $en ? 'Transparent freight – no hidden costs' : 'Transparente Fracht – keine versteckten Kosten' ?></span>
            <span class="lang-switch">
<?php foreach ($kirby->languages() as $l): ?>
                <a href="<?= $page->url($l->code()) ?>"<?= $l->code() === $code ? ' aria-current="true"' : '' ?>><?= strtoupper($l->code()) ?></a>
<?php endforeach; ?>
            </span>
        </div>
    </div>

    <!-- Tier 2: logo + search + actions -->
    <div class="headbar">
        <div class="container headbar__row">
            <a class="brand" href="<?= $site->url($code) ?>" aria-label="<?= $site->title()->or('Marvento') ?>">
                <img class="brand__mark" src="<?= url('assets/img/logo.svg') ?>" alt="" width="34" height="34">
                <span class="brand__name"><?= $site->title()->or('Marvento') ?></span>
            </a>

            <form class="search" action="<?= $navP ?>" method="get" role="search">
                <select class="search__cat" name="cat" aria-label="<?= $en ? 'Category' : 'Kategorie' ?>">
                    <option value=""><?= $en ? 'All' : 'Alle' ?></option>
                    <option value="elektro"><?= t('nav.electric') ?></option>
                    <option value="benzin"><?= t('nav.petrol') ?></option>
                </select>
                <input class="search__input" type="search" name="q" placeholder="<?= $en ? 'Search for model, hp, brand…' : 'Modell, PS, Marke suchen…' ?>" aria-label="<?= t('marvento.search') ?>">
                <button class="search__btn" type="submit" aria-label="<?= t('marvento.search') ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></button>
            </form>

            <div class="header-actions">
                <a class="iconlink" href="<?= $navAccount ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span><?= t('nav.account') ?></span></a>
                <a class="cart-btn" href="<?= $navCart ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/></svg>
                    <span class="cart-btn__meta"><span class="cart-btn__label"><?= t('cart.title') ?></span><span class="cart-btn__total price">0,00 €</span></span>
                    <span class="cart-btn__count" data-cart-count>0</span>
                </a>
                <button class="nav-toggle" type="button" aria-label="<?= t('marvento.menu') ?>" data-nav-toggle><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
            </div>
        </div>
    </div>

    <!-- Tier 3: mega navigation -->
    <nav class="mega" aria-label="<?= $en ? 'Main navigation' : 'Hauptnavigation' ?>">
        <div class="container mega__row">
            <ul>
                <li>
                    <a href="<?= $navE ?>" data-axis="cool"><span class="dot"></span><?= t('nav.electric') ?></a>
                    <div class="mega__panel">
                        <div class="mega__cols">
                            <div class="mega__col">
                                <h4>ePropulsion</h4>
                                <ul>
<?php if ($catElectric): foreach ($catElectric->children()->listed() as $pr): ?>
                                    <a href="<?= $pr->url() ?>"><?= $pr->title() ?> <small><?= $pr->powerPs() ?> PS</small></a>
<?php endforeach; endif ?>
                                </ul>
                            </div>
                            <div class="mega__col">
                                <h4><?= $en ? 'Help' : 'Hilfe' ?></h4>
                                <ul>
                                    <a href="<?= $navAdv ?>"><?= t('nav.advisor') ?></a>
                                    <a href="<?= $navE ?>"><?= $en ? 'All electric models' : 'Alle Elektro-Modelle' ?></a>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <a href="<?= $navP ?>" data-axis="warm"><span class="dot"></span><?= t('nav.petrol') ?></a>
                    <div class="mega__panel">
                        <div class="mega__cols">
                            <div class="mega__col">
                                <h4>Tohatsu</h4>
                                <ul>
<?php if ($catPetrol): foreach ($catPetrol->children()->listed()->sortBy('powerPs', 'asc') as $pr): ?>
                                    <a href="<?= $pr->url() ?>"><?= $pr->title() ?> <small><?= $pr->powerPs() ?> PS</small></a>
<?php endforeach; endif ?>
                                </ul>
                            </div>
                            <div class="mega__col">
                                <h4><?= $en ? 'Help' : 'Hilfe' ?></h4>
                                <ul>
                                    <a href="<?= $navAdv ?>"><?= t('nav.advisor') ?></a>
                                    <a href="<?= $navP ?>"><?= $en ? 'All petrol models' : 'Alle Benzin-Modelle' ?></a>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="<?= $navAdv ?>"><?= t('nav.advisor') ?></a></li>
                <li><a href="<?= $navGuide ?>"><?= t('nav.guide') ?></a></li>
                <li><a href="<?= $navContact ?>"><?= t('nav.contact') ?></a></li>
            </ul>
        </div>
    </nav>
</header>

<main id="main">
