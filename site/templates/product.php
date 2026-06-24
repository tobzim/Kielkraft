<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $page->antrieb()->value() === 'elektro';
$axis = $isElectric ? 'axis-cool' : 'axis-warm';
$cat  = $page->parent();
$gallery = $page->gallery()->toFiles();
$cover = $page->image();
$cross = $page->crossSell()->toPages();
$crossAuto = false;
if ($cross->count() === 0) {
    $cross = kk_related_products($page, 4);
    $crossAuto = true;
}
?>
<?php snippet('header') ?>
<?php $ga4Item = kk_ga4_item($page); ?>
<script type="application/json" id="kk-ecom"><?= json_encode(['event' => 'view_item', 'params' => ['currency' => 'EUR', 'value' => $ga4Item['price'], 'items' => [$ga4Item]]], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

<div class="container <?= $axis ?>" style="padding-top:var(--sp-5)">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= $site->url($code) ?>"><?= $en ? 'Home' : 'Start' ?></a> <span>/</span>
<?php if ($cat): ?>
        <a href="<?= $cat->url() ?>"><?= $cat->title() ?></a> <span>/</span>
<?php endif ?>
        <span><?= $page->title() ?></span>
    </nav>
</div>

<section class="pdp <?= $axis ?>">
    <div class="container">
        <header style="max-width:60ch;margin-bottom:var(--sp-6)">
            <?php snippet('brand-logo', ['brand' => $page->brand()->value(), 'class' => 'brand-logo--pdp']) ?>
            <div class="eyebrow"><?= $page->brand() ?> · <?= $isElectric ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol 4-stroke' : 'Benzin 4-Takt') ?></div>
            <h1><?= $page->title() ?></h1>
            <p class="section__lead"><?= $page->intro() ?></p>
        </header>

        <div class="pdp__grid">
            <div class="pdp__main-col">
                <div class="gallery">
                    <div class="gallery__main">
<?php if ($cover): ?>
                        <img src="<?= $cover->resize(1100)->url() ?>" srcset="<?= $cover->srcset([560, 800, 1100]) ?>" sizes="(max-width: 900px) 92vw, 560px" alt="<?= $cover->alt()->or($page->title())->esc() ?>" width="1100" height="825" fetchpriority="high">
<?php else: ?>
                        <div class="gallery__ph">
                            <strong><?= $page->title() ?></strong><br>
                            <?= $en ? 'Real product photo follows (placeholder).' : 'Reales Produktfoto folgt (Platzhalter).' ?><br>
                            <small><?= $en ? 'Only genuine manufacturer photos are used here.' : 'Hier kommen ausschließlich echte Hersteller-Fotos zum Einsatz.' ?></small>
                        </div>
<?php endif ?>
                    </div>
<?php if ($gallery->count() > 1): ?>
                    <div class="gallery__thumbs">
<?php foreach ($gallery->limit(5) as $g): ?>
                        <button type="button"><img src="<?= $g->resize(160)->url() ?>" alt=""></button>
<?php endforeach ?>
                    </div>
<?php endif ?>
                </div>

                <!-- Gauge readout (specs as instrument cluster) -->
                <div class="gauges" style="margin-top:var(--sp-6)">
                    <div class="gauge"><div class="gauge__label"><?= t('product.power') ?></div><div class="gauge__value"><?= $page->powerPs() ?></div><div class="gauge__unit">PS</div></div>
                    <div class="gauge"><div class="gauge__label">kW</div><div class="gauge__value"><?= $page->powerKw() ?></div><div class="gauge__unit">kW</div></div>
                    <div class="gauge"><div class="gauge__label"><?= t('product.weight') ?></div><div class="gauge__value"><?= $page->weightKg() ?></div><div class="gauge__unit">kg</div></div>
                    <div class="gauge"><div class="gauge__label"><?= $isElectric ? ($en ? 'Battery' : 'Akku') : ($en ? 'Displacement' : 'Hubraum') ?></div><div class="gauge__value mono" style="font-size:var(--fs-500)"><?= $page->displacement()->or('–') ?></div><div class="gauge__unit">&nbsp;</div></div>
                </div>

                <div class="prose" style="margin-top:var(--sp-7)">
                    <h2><?= $en ? 'About this engine' : 'Über diesen Motor' ?></h2>
                    <?= $page->description()->kt() ?>
                </div>

                <h2 style="margin-top:var(--sp-8);margin-bottom:var(--sp-5)"><?= t('product.specs') ?></h2>
                <table class="spectable">
                    <tbody>
                        <tr><th><?= $en ? 'Brand / Model' : 'Marke / Modell' ?></th><td><?= $page->brand() ?> <?= $page->modelcode() ?></td></tr>
                        <tr><th><?= t('product.power') ?></th><td><?= $page->powerPs() ?> PS · <?= $page->powerKw() ?> kW</td></tr>
                        <tr><th><?= t('product.weight') ?></th><td><?= $page->weightKg() ?> kg</td></tr>
<?php foreach ($page->specs()->toStructure() as $s): ?>
                        <tr><th><?= $s->label()->esc() ?></th><td><?= $s->value()->esc() ?></td></tr>
<?php endforeach ?>
                        <tr><th><?= t('product.warranty') ?></th><td><?= $page->warrantyYears() ?> <?= $en ? 'years (manufacturer)' : 'Jahre (Hersteller)' ?></td></tr>
                    </tbody>
                </table>

<?php if ($page->faq()->toStructure()->count() > 0): ?>
                <h2 style="margin-top:var(--sp-8);margin-bottom:var(--sp-5)">FAQ</h2>
                <div class="faq">
<?php foreach ($page->faq()->toStructure() as $f): ?>
                    <details class="faq__item">
                        <summary><?= $f->q()->esc() ?></summary>
                        <p><?= $f->a()->kt() ?></p>
                    </details>
<?php endforeach ?>
                </div>
<?php endif ?>
            </div>

            <div class="pdp__buy-col">
                <?php snippet('konsole', ['product' => $page]) ?>
            </div>
        </div>
    </div>
</section>

<!-- Risk reversal -->
<section class="section--tight bg-soft">
    <div class="container">
        <div class="featurelist">
            <div class="feature"><h3><?= $en ? '14-day right of withdrawal' : '14 Tage Widerrufsrecht' ?></h3><p><?= $en ? 'Buy with confidence. Return within 14 days under the statutory terms.' : 'Kaufe ohne Risiko. Rückgabe innerhalb von 14 Tagen nach den gesetzlichen Bedingungen.' ?></p></div>
            <div class="feature"><h3><?= $en ? 'Manufacturer warranty' : 'Herstellergarantie' ?></h3><p><?= $en ? 'Official new goods with the full manufacturer warranty.' : 'Offizielle Neuware mit voller Herstellergarantie.' ?></p></div>
            <div class="feature"><h3><?= $en ? 'Real advice' : 'Echte Beratung' ?></h3><p><?= $en ? 'Not sure about shaft length? Talk to a human by phone or WhatsApp.' : 'Unsicher bei der Schaftlänge? Sprich per Telefon oder WhatsApp mit einem Menschen.' ?></p></div>
        </div>
    </div>
</section>

<?php if ($cross->count() > 0): ?>
<section class="section--tight">
    <div class="container">
        <div class="section__head"><h2 class="section__title"><?= $crossAuto ? ($en ? 'Similar models' : 'Ähnliche Modelle') : ($en ? 'Goes well with' : 'Passendes Zubehör') ?></h2></div>
        <div class="pgrid">
<?php foreach ($cross->limit(4) as $c): ?>
            <?php snippet('product-card', ['product' => $c]) ?>
<?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<!-- Mini console (mobile / on scroll) -->
<div class="mini-konsole <?= $axis ?>" data-mini-konsole>
    <div class="container mini-konsole__row">
        <span class="mini-konsole__name"><?= $page->title() ?></span>
        <span class="mini-konsole__price mono" data-mini-price><?= mv_eur(($page->variants()->toStructure()->first()?->price()?->value()) ?? $page->priceFrom()->value(), $code) ?></span>
        <button type="button" class="btn btn--primary" data-add-to-cart><?= t('product.add_to_cart') ?></button>
    </div>
</div>

<?php snippet('seo/jsonld-product', ['product' => $page]) ?>
<?php snippet('footer') ?>
