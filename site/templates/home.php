<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';

$u = fn(string $de, string $enp) => url($en ? 'en/' . $enp : $de);
$urlElectric = $u('elektro-aussenborder', 'electric-outboards');
$urlPetrol   = $u('benzin-aussenborder', 'petrol-outboards');
$urlAdvisor  = $u('kaufberater', 'buying-advisor');

// Real products if the catalog already exists (Phase 2); empty otherwise.
$products    = $site->index()->filterBy('intendedTemplate', 'product')->listed();
$bestsellers = $products->filterBy('bestseller', true);
if ($bestsellers->count() === 0) {
    $bestsellers = $products;
}
$bestsellers = $bestsellers->limit(4);
?>
<?php snippet('header') ?>

<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <span class="hero__eyebrow"><?= $page->heroEyebrow() ?></span>
            <h1><?= $page->heroHeadline() ?></h1>
            <p class="hero__lead"><?= $page->heroLead() ?></p>
            <div class="hero__cta">
                <a class="btn btn--primary btn--lg" href="<?= $urlAdvisor ?>"><?= t('nav.advisor') ?></a>
                <a class="btn btn--ghost btn--lg" style="color:#fff;border-color:rgba(255,255,255,.32)" href="<?= $urlPetrol ?>"><?= $en ? 'Browse all models' : 'Alle Modelle ansehen' ?></a>
            </div>
            <div class="hero__split">
                <a href="<?= $urlElectric ?>"><strong><?= t('nav.electric') ?></strong><span>ePropulsion</span></a>
                <a href="<?= $urlPetrol ?>"><strong><?= t('nav.petrol') ?></strong><span>Tohatsu 4-Takt</span></a>
            </div>
        </div>
        <div class="hero__media">
            <div class="hero__placeholder"><?= $en ? 'Product image to follow (placeholder)' : 'Produktbild folgt (Platzhalter)' ?></div>
        </div>
    </div>
</section>

<div class="trustbar">
    <div class="container trustbar__grid">
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span><?= t('trust.secure') ?></span></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg><span><?= t('trust.new_goods') ?></span></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.5 13.5L7 22l5-3 5 3-1.5-8.5"/></svg><span><?= t('trust.warranty') ?></span></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg><span><?= t('trust.invoice') ?></span></div>
        <div class="trust-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1zM16 8h4l3 3v5h-7"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></svg><span><?= t('trust.shipping') ?></span></div>
    </div>
</div>

<section class="section container">
    <div class="section__head">
        <span class="section__eyebrow"><?= $page->sectionCatsEyebrow() ?></span>
        <h2><?= $page->sectionCatsTitle() ?></h2>
    </div>
    <div class="grid grid--2" style="grid-template-columns:1fr 1fr">
        <article class="cat-teaser cat-teaser--electric">
            <div>
                <span class="badge badge--electric"><?= $en ? 'Electric' : 'Elektro' ?> &middot; ePropulsion</span>
                <h3 style="margin-top:var(--sp-3)"><?= $page->catElectricTitle() ?></h3>
                <p><?= $page->catElectricText() ?></p>
                <a class="btn btn--primary" href="<?= $urlElectric ?>"><?= t('product.details') ?></a>
            </div>
        </article>
        <article class="cat-teaser">
            <div>
                <span class="badge badge--soft">4-Takt &middot; Tohatsu</span>
                <h3 style="margin-top:var(--sp-3)"><?= $page->catPetrolTitle() ?></h3>
                <p><?= $page->catPetrolText() ?></p>
                <a class="btn btn--primary" href="<?= $urlPetrol ?>"><?= t('product.details') ?></a>
            </div>
        </article>
    </div>
</section>

<?php if ($bestsellers->count() > 0): ?>
<section class="section section--tight container">
    <div class="section__head"><h2><?= $page->sectionBestTitle() ?></h2></div>
    <div class="grid grid--4">
<?php foreach ($bestsellers as $product): ?>
        <?php snippet('product-card', ['product' => $product]) ?>
<?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section container">
    <div class="section__head"><h2><?= $page->uspTitle() ?></h2></div>
    <div class="usp">
        <div class="usp__item"><h3><?= $page->usp1Title() ?></h3><p><?= $page->usp1Text() ?></p></div>
        <div class="usp__item"><h3><?= $page->usp2Title() ?></h3><p><?= $page->usp2Text() ?></p></div>
        <div class="usp__item"><h3><?= $page->usp3Title() ?></h3><p><?= $page->usp3Text() ?></p></div>
    </div>
</section>

<section class="section--tight container">
    <div class="advisor">
        <h2><?= $page->advisorTitle() ?></h2>
        <p><?= $page->advisorText() ?></p>
        <a class="btn btn--primary btn--lg" style="margin-top:var(--sp-5)" href="<?= $urlAdvisor ?>"><?= t('nav.advisor') ?></a>
    </div>
</section>

<?php snippet('footer') ?>
