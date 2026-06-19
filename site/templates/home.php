<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$u = fn(string $de, string $enp) => url($en ? 'en/' . $enp : $de);
$urlElectric = $u('elektro-aussenborder', 'electric-outboards');
$urlPetrol   = $u('benzin-aussenborder', 'petrol-outboards');
$urlAdvisor  = $u('kaufberater', 'buying-advisor');

$products = $site->index()->filterBy('intendedTemplate', 'product')->listed();
$featured = $products->filterBy('bestseller', true)->merge($products)->limit(4);
?>
<?php snippet('header') ?>

<section class="hero-ed">
    <img class="hero-ed__img" src="<?= url('assets/img/hero-petrol.webp') ?>" alt="" fetchpriority="high">
    <div class="hero-ed__scrim"></div>
    <div class="container">
        <div class="hero-ed__inner">
            <div class="hero-ed__block">
                <span class="hero-ed__kicker">
                    <?= $page->heroEyebrow() ?>
                    <span class="pole"><b class="w"></b><b class="c"></b></span>
                </span>
                <h1><?= $page->heroHeadline() ?></h1>
                <p class="hero-ed__vp"><?= $page->heroLead() ?></p>
                <div class="hero-ed__cta">
                    <a class="btn btn--primary btn--lg" href="#modelle"><?= $en ? 'Discover models' : 'Modelle entdecken' ?></a>
                    <a class="link-quiet" href="<?= $urlAdvisor ?>"><?= $en ? 'Not sure? Use the buying advisor' : 'Unsicher? Zum Kaufberater' ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="trust-strip">
    <div class="container trust-strip__row">
        <span class="trust-strip__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><?= t('trust.secure') ?></span>
        <span class="trust-strip__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?= t('trust.new_goods') ?></span>
        <span class="trust-strip__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.5 13.5 7 22l5-3 5 3-1.5-8.5"/></svg><?= t('trust.warranty') ?></span>
        <span class="trust-strip__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg><?= $en ? '14-day right of withdrawal' : '14 Tage Widerrufsrecht' ?></span>
        <span class="trust-strip__item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg><?= t('trust.shipping') ?></span>
    </div>
</div>

<section class="section" id="modelle">
    <div class="container">
        <div class="section__head">
            <span class="section__label"><?= $page->sectionCatsEyebrow() ?></span>
            <h2><?= $page->sectionCatsTitle() ?></h2>
        </div>
        <div class="cat-split">
            <a class="cat-tile axis-cool" href="<?= $urlElectric ?>">
                <img class="cat-tile__img" src="<?= url('assets/img/banner-electric.webp') ?>" alt="" loading="lazy">
                <div class="cat-tile__scrim"></div>
                <div class="cat-tile__body">
                    <span class="axis-tag"><b></b> ePropulsion · <?= $en ? 'Electric' : 'Elektro' ?></span>
                    <h3><?= $page->catElectricTitle() ?></h3>
                    <p><?= $page->catElectricText() ?></p>
                    <span class="btn btn--primary" style="margin-top:var(--sp-4)"><?= t('product.details') ?></span>
                </div>
            </a>
            <a class="cat-tile axis-warm" href="<?= $urlPetrol ?>">
                <img class="cat-tile__img" src="<?= url('assets/img/banner-petrol.webp') ?>" alt="" loading="lazy">
                <div class="cat-tile__scrim"></div>
                <div class="cat-tile__body">
                    <span class="axis-tag"><b></b> Tohatsu · <?= $en ? 'Petrol 4-stroke' : 'Benzin 4-Takt' ?></span>
                    <h3><?= $page->catPetrolTitle() ?></h3>
                    <p><?= $page->catPetrolText() ?></p>
                    <span class="btn btn--warm" style="margin-top:var(--sp-4)"><?= t('product.details') ?></span>
                </div>
            </a>
        </div>
    </div>
</section>

<?php if ($featured->count() > 0): ?>
<section class="section--tight">
    <div class="container">
        <div class="section__head"><h2><?= $page->sectionBestTitle() ?></h2></div>
        <div class="grid grid--4">
<?php foreach ($featured as $product): ?>
            <?php snippet('product-card', ['product' => $product]) ?>
<?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<section class="section">
    <div class="container">
        <div class="section__head"><h2><?= $page->uspTitle() ?></h2></div>
        <div class="featurelist">
            <div class="feature"><h3><span class="num">01</span> <?= $page->usp1Title() ?></h3><p><?= $page->usp1Text() ?></p></div>
            <div class="feature"><h3><span class="num">02</span> <?= $page->usp2Title() ?></h3><p><?= $page->usp2Text() ?></p></div>
            <div class="feature"><h3><span class="num">03</span> <?= $page->usp3Title() ?></h3><p><?= $page->usp3Text() ?></p></div>
        </div>
    </div>
</section>

<section class="section--tight">
    <div class="container">
        <div class="advisor-cta">
            <h2><?= $page->advisorTitle() ?></h2>
            <p><?= $page->advisorText() ?></p>
            <a class="btn btn--primary btn--lg" style="margin-top:var(--sp-5)" href="<?= $urlAdvisor ?>"><?= t('nav.advisor') ?></a>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
