<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$u = fn(string $de, string $enp) => url($en ? 'en/' . $enp : $de);
$urlE = $u('elektro-aussenborder', 'electric-outboards');
$urlP = $u('benzin-aussenborder', 'petrol-outboards');
$urlAdv = $u('kaufberater', 'buying-advisor');

$catE = page('elektro-aussenborder');
$catP = page('benzin-aussenborder');
$products = $site->index()->filterBy('intendedTemplate', 'product')->listed();
$featured = $products->filterBy('bestseller', true)->merge($products)->limit(4);

$pf = function ($pr) use ($code) {
    $v = $pr->variants()->toStructure()->first();
    $price = $v && $v->price()->isNotEmpty() ? $v->price()->value() : $pr->priceFrom()->value();
    return mv_eur($price, $code);
};
?>
<?php snippet('header') ?>

<section class="promo">
    <img class="promo__img" src="<?= url('assets/img/hero-petrol.webp') ?>" alt="" fetchpriority="high">
    <div class="promo__scrim"></div>
    <div class="container">
        <div class="promo__inner">
            <div class="promo__content">
                <span class="promo__kicker"><?= $page->heroEyebrow() ?></span>
                <h1><?= $page->heroHeadline() ?></h1>
                <p class="promo__sub"><?= $page->heroLead() ?></p>
                <div class="promo__cta">
                    <a class="btn btn--on-navy btn--lg" href="<?= $urlP ?>"><?= $en ? 'Browse all models' : 'Alle Modelle ansehen' ?></a>
                    <a class="btn btn--ghost btn--lg" href="<?= $urlAdv ?>"><?= t('nav.advisor') ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="trustbar">
    <div class="container trustbar__row">
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?= t('trust.new_goods') ?> &amp; <?= $en ? 'warranty' : 'Garantie' ?></span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg><?= t('trust.invoice') ?></span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg><?= t('trust.shipping') ?></span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg><?= $en ? '14-day withdrawal' : '14 Tage Widerruf' ?></span>
        <span class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><?= t('trust.secure') ?></span>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section__head"><h2 class="section__title"><?= $page->sectionCatsTitle() ?></h2></div>
        <div class="catgrid">
            <a class="cat-card" href="<?= $urlE ?>">
                <img class="cat-card__img" src="<?= url('assets/img/banner-electric.webp') ?>" alt="" loading="lazy">
                <div class="cat-card__scrim"></div>
                <div class="cat-card__body">
                    <span class="cat-card__tag"><b style="background:#46b3ff"></b> ePropulsion · <?= $en ? 'Electric' : 'Elektro' ?></span>
                    <h3><?= $page->catElectricTitle() ?></h3>
                    <p><?= $page->catElectricText() ?></p>
                    <span class="cat-card__link"><?= $en ? 'View electric outboards' : 'Elektro-Außenborder ansehen' ?></span>
                </div>
            </a>
            <a class="cat-card" href="<?= $urlP ?>">
                <img class="cat-card__img" src="<?= url('assets/img/banner-petrol.webp') ?>" alt="" loading="lazy">
                <div class="cat-card__scrim"></div>
                <div class="cat-card__body">
                    <span class="cat-card__tag"><b style="background:var(--gold)"></b> Tohatsu · <?= $en ? 'Petrol 4-stroke' : 'Benzin 4-Takt' ?></span>
                    <h3><?= $page->catPetrolTitle() ?></h3>
                    <p><?= $page->catPetrolText() ?></p>
                    <span class="cat-card__link"><?= $en ? 'View petrol outboards' : 'Benzin-Außenborder ansehen' ?></span>
                </div>
            </a>
        </div>
    </div>
</section>

<?php if ($featured->count() > 0): ?>
<section class="section--tight bg-soft">
    <div class="container">
        <div class="section__head">
            <h2 class="section__title"><?= $page->sectionBestTitle() ?></h2>
            <a class="section__more" href="<?= $urlP ?>"><?= $en ? 'All models' : 'Alle Modelle' ?> &rarr;</a>
        </div>
        <div class="pgrid">
<?php foreach ($featured as $product): ?>
            <?php snippet('product-card', ['product' => $product]) ?>
<?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<?php snippet('tiefpreis') ?>

<section class="section">
    <div class="container">
        <div class="section__head"><h2 class="section__title"><?= $en ? 'Our brands' : 'Unsere Marken' ?></h2></div>
        <div class="brands">
            <div class="brand-block">
                <span class="brand-block__tag">Tohatsu · <?= $en ? 'Petrol 4-stroke' : 'Benzin 4-Takt' ?></span>
                <?php snippet('brand-logo', ['brand' => 'Tohatsu', 'class' => 'brand-logo--block']) ?>
                <p><?= $en ? 'Proven Japanese 4-stroke engineering from 3.5 to 15 hp – reliable, economical, durable.' : 'Bewährte japanische 4-Takt-Technik von 3,5 bis 15 PS – zuverlässig, sparsam, langlebig.' ?></p>
                <div class="brand-block__models">
<?php if ($catP): foreach ($catP->children()->listed()->sortBy('powerPs', 'asc')->limit(4) as $pr): ?>
                    <a href="<?= $pr->url() ?>"><span><?= $pr->title() ?></span><span class="p"><?= $pf($pr) ?></span></a>
<?php endforeach; endif ?>
                </div>
                <a class="btn btn--ghost" href="<?= $urlP ?>"><?= $en ? 'All Tohatsu models' : 'Alle Tohatsu-Modelle' ?></a>
            </div>
            <div class="brand-block">
                <span class="brand-block__tag">ePropulsion · <?= $en ? 'Electric' : 'Elektro' ?></span>
                <?php snippet('brand-logo', ['brand' => 'ePropulsion', 'class' => 'brand-logo--block']) ?>
                <p><?= $en ? 'Modern electric drives with lithium batteries – quiet, low-maintenance, zero-emission.' : 'Moderne Elektroantriebe mit Lithium-Akku – leise, wartungsarm, emissionsfrei.' ?></p>
                <div class="brand-block__models">
<?php if ($catE): foreach ($catE->children()->listed()->sortBy('powerPs', 'asc') as $pr): ?>
                    <a href="<?= $pr->url() ?>"><span><?= $pr->title() ?></span><span class="p"><?= $pf($pr) ?></span></a>
<?php endforeach; endif ?>
                </div>
                <a class="btn btn--ghost" href="<?= $urlE ?>"><?= $en ? 'All ePropulsion models' : 'Alle ePropulsion-Modelle' ?></a>
            </div>
        </div>
    </div>
</section>

<?php snippet('partner-trust') ?>

<?php snippet('boostboards-banner') ?>

<section class="section">
    <div class="container">
        <div class="advisor-cta">
            <div class="advisor-cta__text">
                <h2><?= $page->advisorTitle() ?></h2>
                <p><?= $page->advisorText() ?></p>
            </div>
            <a class="btn btn--on-navy btn--lg advisor-cta__btn" href="<?= $urlAdv ?>"><?= t('nav.advisor') ?></a>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
