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

// Curated, varied hero set: bestseller/sale first, then guarantee an
// electric + the strongest petrol + a second electric, then fill. merge() dedupes.
$heroSel = $products->filterBy('bestseller', true)
    ->merge($products->filterBy('antrieb', 'elektro')->limit(1))
    ->merge($products->filterBy('antrieb', 'benzin')->sortBy('powerPs', 'desc')->limit(1))
    ->merge($products->filterBy('antrieb', 'elektro'))
    ->merge($products)
    ->limit(4);
$heroN = $heroSel->count();

$pf = function ($pr) use ($code) {
    $v = $pr->variants()->toStructure()->first();
    $price = $v && $v->price()->isNotEmpty() ? $v->price()->value() : $pr->priceFrom()->value();
    return mv_eur($price, $code);
};
?>
<?php snippet('header') ?>

<?php if ($heroN > 0): ?>
<section class="hero" data-hero aria-roledescription="carousel" aria-label="<?= $en ? 'Featured outboards' : 'Empfohlene Außenborder' ?>">
    <h1 class="sr-only"><?= $page->heroHeadline()->or($site->title()) ?></h1>
    <div class="hero__viewport">
        <div class="hero__track" data-hero-track>
            <article class="hero__slide hero__slide--brand is-active" data-hero-slide role="group" aria-roledescription="slide" aria-label="1 / <?= $heroN + 1 ?>">
                <div class="hero__brandmedia"><img src="<?= url('assets/img/hero-petrol.webp') ?>" alt="" fetchpriority="high"></div>
                <div class="hero__brandscrim"></div>
                <div class="container hero__brand-inner">
                    <div class="hero__brand-content">
                        <span class="hero__eyebrow hero__eyebrow--sale"><?= $page->heroEyebrow() ?></span>
                        <h2 class="hero__title"><?= $page->heroHeadline() ?></h2>
                        <p class="hero__lead"><?= $page->heroLead() ?></p>
                        <div class="hero__cta">
                            <a class="btn btn--on-navy btn--lg" href="<?= $urlP ?>"><?= $en ? 'Browse all models' : 'Alle Modelle ansehen' ?></a>
                            <a class="btn btn--ghost btn--lg" href="<?= $urlAdv ?>"><?= t('nav.advisor') ?></a>
                        </div>
                    </div>
                </div>
            </article>
<?php $hi = 0; foreach ($heroSel as $pr): $hi++;
            $cover = $pr->image();
            $hv    = $pr->variants()->toStructure()->first();
            $price = $hv && $hv->price()->isNotEmpty() ? (float) $hv->price()->value() : (float) $pr->priceFrom()->value();
            $uvp   = $pr->uvp()->isNotEmpty() ? (float) $pr->uvp()->value() : null;
            $pct   = ($uvp && $uvp > $price) ? (int) round((1 - $price / $uvp) * 100) : 0;
            $hship = (float) $pr->shippingCost()->or(0)->value();
            $isE   = $pr->antrieb()->value() === 'elektro';
?>
            <article class="hero__slide hero__slide--<?= $isE ? 'cool' : 'warm' ?>" data-hero-slide role="group" aria-roledescription="slide" aria-label="<?= $hi + 1 ?> / <?= $heroN + 1 ?>" aria-hidden="true">
                <div class="container hero__inner">
                    <div class="hero__content">
<?php if ($pct > 0): ?>
                        <span class="hero__eyebrow hero__eyebrow--sale"><?= $en ? 'Deal' : 'Aktion' ?> &middot; &minus;<?= $pct ?>&nbsp;%</span>
<?php elseif ($pr->bestseller()->toBool()): ?>
                        <span class="hero__eyebrow hero__eyebrow--top"><?= t('product.bestseller') ?></span>
<?php else: ?>
                        <span class="hero__eyebrow hero__eyebrow--<?= $isE ? 'cool' : 'warm' ?>"><?= $pr->brand()->esc() ?> &middot; <?= $isE ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol 4-stroke' : 'Benzin 4-Takt') ?></span>
<?php endif ?>
                        <h2 class="hero__title"><?= $pr->title()->esc() ?></h2>
                        <p class="hero__lead"><?= $isE
                            ? ($en ? 'Quiet, low-maintenance and emission-free – ideal for tenders, sailing yachts and calm waters.' : 'Leise, wartungsarm und emissionsfrei – ideal für Tender, Segelyachten und ruhige Reviere.')
                            : ($en ? 'Proven 4-stroke power with long range – reliable on every tour.' : 'Bewährte 4-Takt-Power mit hoher Reichweite – zuverlässig auf jeder Tour.') ?></p>
                        <div class="hero__specs">
                            <span><?= $pr->powerPs() ?> PS</span>
                            <span><?= $pr->powerKw() ?> kW</span>
                            <span><?= $pr->weightKg() ?> kg</span>
                        </div>
                        <div class="hero__price">
<?php if ($uvp && $uvp > $price): ?>
                            <span class="hero__old"><?= mv_eur($uvp, $code) ?></span>
<?php endif ?>
                            <span class="hero__now"><?= mv_eur($price, $code) ?></span>
                            <span class="hero__vat"><?= t('product.incl_vat') ?> &middot; <?= $hship > 0 ? ($en ? 'plus freight ' : 'zzgl. Fracht ') . mv_eur($hship, $code) : ($en ? 'free freight' : 'frachtfrei') ?></span>
                        </div>
                        <div class="hero__cta">
                            <a class="btn btn--on-navy btn--lg" href="<?= $pr->url() ?>"><?= $en ? 'View model' : 'Modell ansehen' ?></a>
                            <a class="btn btn--ghost btn--lg" href="<?= $urlAdv ?>"><?= t('nav.advisor') ?></a>
                        </div>
                    </div>
                    <div class="hero__media">
<?php
                        $cutRel = 'assets/img/products/' . $pr->slug() . '-cut.webp';
                        $cutAbs = kirby()->root('index') . '/' . $cutRel;
                        $imgSrc = is_file($cutAbs) ? mv_asset($cutRel) : ($cover ? $cover->resize(900)->url() : null);
?>
<?php if ($imgSrc): ?>
                        <img src="<?= $imgSrc ?>" alt="<?= $cover ? $cover->alt()->or($pr->title())->esc() : $pr->title()->esc() ?>" loading="lazy">
<?php else: ?>
                        <span class="hero__ph"><?= $pr->title()->esc() ?></span>
<?php endif ?>
                    </div>
                </div>
            </article>
<?php endforeach ?>
        </div>
    </div>
<?php if ($heroN > 1): ?>
    <button class="hero__nav hero__nav--prev" type="button" data-hero-prev aria-label="<?= $en ? 'Previous' : 'Vorheriges' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg></button>
    <button class="hero__nav hero__nav--next" type="button" data-hero-next aria-label="<?= $en ? 'Next' : 'Nächstes' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg></button>
    <div class="hero__dots" data-hero-dots role="tablist" aria-label="<?= $en ? 'Slides' : 'Folien' ?>">
<?php for ($d = 0; $d < $heroN + 1; $d++): ?>
        <button class="hero__dot<?= $d === 0 ? ' is-active' : '' ?>" type="button" data-hero-dot="<?= $d ?>" aria-label="<?= ($en ? 'Slide ' : 'Folie ') . ($d + 1) ?>"<?= $d === 0 ? ' aria-current="true"' : '' ?>></button>
<?php endfor ?>
    </div>
<?php endif ?>
</section>
<?php else: ?>
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
<?php endif ?>

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
