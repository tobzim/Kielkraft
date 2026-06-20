<?php
/** @var \Kirby\Cms\Page $product */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $product->antrieb()->value() === 'elektro';
$cover = $product->image();
$variants = $product->variants()->toStructure();
$first = $variants->first();
$priceFrom = $first && $first->price()->isNotEmpty() ? (float) $first->price()->value() : (float) $product->priceFrom()->value();
$uvp = $product->uvp()->isNotEmpty() ? (float) $product->uvp()->value() : null;
$ship = (float) $product->shippingCost()->or(0)->value();
$multi = $variants->count() > 1;
$avail = $product->availability()->or('instock')->value();
$pct = ($uvp && $uvp > $priceFrom) ? (int) round((1 - $priceFrom / $uvp) * 100) : 0;
$availLabel = $avail === 'short' ? ($en ? 'Few in stock' : 'Wenige verfügbar') : ($avail === 'preorder' ? ($en ? 'Pre-order' : 'Vorbestellung') : ($en ? 'In stock' : 'Lieferbar'));
?>
<article class="pcard">
    <div class="pcard__badges">
<?php if ($pct > 0): ?>
        <span class="badge badge--sale">-<?= $pct ?>&nbsp;%</span>
<?php endif ?>
<?php if ($product->bestseller()->toBool()): ?>
        <span class="badge badge--top"><?= t('product.bestseller') ?></span>
<?php endif ?>
        <span class="badge <?= $isElectric ? 'badge--electric' : 'badge--petrol' ?>"><?= $isElectric ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol' : 'Benzin') ?></span>
    </div>
    <a class="pcard__media" href="<?= $product->url() ?>" aria-label="<?= $product->title()->esc() ?>">
<?php if ($cover): ?>
        <img src="<?= $cover->resize(540)->url() ?>" alt="<?= $cover->alt()->or($product->title())->esc() ?>" loading="lazy" width="540" height="405">
<?php else: ?>
        <span class="pcard__ph"><?= $en ? 'Product photo follows' : 'Produktfoto folgt' ?></span>
<?php endif ?>
    </a>
    <div class="pcard__body">
        <span class="pcard__brand"><?= $product->brand() ?></span>
        <h3 class="pcard__title"><a href="<?= $product->url() ?>"><?= $product->title() ?></a></h3>
        <div class="pcard__specs">
            <span><?= $product->powerPs() ?> PS</span>
            <span><?= $product->powerKw() ?> kW</span>
            <span><?= $product->weightKg() ?> kg</span>
        </div>
        <div class="pcard__price">
            <div>
<?php if ($uvp && $uvp > $priceFrom): ?>
                <span class="price__old"><?= mv_eur($uvp, $code) ?></span>
<?php endif ?>
<?php if ($multi): ?><span class="price__from"><?= t('product.from') ?></span> <?php endif ?>
                <span class="price__now <?= $pct > 0 ? 'is-sale' : '' ?>"><?= mv_eur($priceFrom, $code) ?></span>
            </div>
            <div class="price__vat"><?= t('product.incl_vat') ?> ·
<?php if ($ship > 0): ?>
                <span class="price__freight"><?= $en ? 'plus freight' : 'zzgl. Fracht' ?> <b><?= mv_eur($ship, $code) ?></b></span>
<?php else: ?>
                <span class="price__freight"><?= $en ? 'freight included' : 'frachtfrei' ?></span>
<?php endif ?>
            </div>
        </div>
        <div class="pcard__foot">
            <span class="stock <?= $avail === 'short' ? 'stock--short' : '' ?>"><span class="dot"></span><?= $availLabel ?></span>
            <a class="btn btn--cta btn--sm" href="<?= $product->url() ?>" style="margin-inline-start:auto"><?= t('product.details') ?></a>
        </div>
    </div>
</article>
