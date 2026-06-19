<?php
/** @var \Kirby\Cms\Page $product */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $product->antrieb()->value() === 'elektro';
$axis = $isElectric ? 'axis-cool' : 'axis-warm';
$cover = $product->image();
$priceFrom = $product->priceFrom()->or($product->variants()->toStructure()->first()?->price());
$multi = $product->variants()->toStructure()->count() > 1;
?>
<article class="card product-card <?= $axis ?>">
    <div class="product-card__axisbar"></div>
    <a href="<?= $product->url() ?>" class="product-card__media" aria-label="<?= $product->title()->esc() ?>">
<?php if ($cover): ?>
        <img src="<?= $cover->resize(560)->url() ?>" alt="<?= $cover->alt()->or($product->title())->esc() ?>" loading="lazy" width="560" height="420">
<?php else: ?>
        <span class="product-card__ph"><?= $en ? 'Photo follows' : 'Foto folgt' ?></span>
<?php endif ?>
        <div class="product-card__badges">
<?php if ($product->bestseller()->toBool()): ?>
            <span class="badge badge--bestseller"><?= t('product.bestseller') ?></span>
<?php endif ?>
            <span class="badge <?= $isElectric ? 'badge--electric' : 'badge--petrol' ?>"><?= $isElectric ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol' : 'Benzin') ?></span>
        </div>
    </a>
    <div class="product-card__body">
        <span class="product-card__brand"><?= $product->brand() ?></span>
        <a href="<?= $product->url() ?>"><h3 class="product-card__title"><?= $product->title() ?></h3></a>
        <span class="product-card__meta"><?= $product->powerPs() ?> PS · <?= $product->powerKw() ?> kW · <?= $product->weightKg() ?> kg</span>
        <div class="product-card__foot">
            <div class="price">
<?php if ($multi): ?><span class="price__from"><?= t('product.from') ?></span> <?php endif ?><?= mv_eur($priceFrom->value(), $code) ?>
                <small><?= t('product.incl_vat') ?></small>
            </div>
            <a class="btn btn--dark" href="<?= $product->url() ?>"><?= t('product.details') ?></a>
        </div>
    </div>
</article>
