<?php
/** @var \Kirby\Cms\Page $product */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$fmt  = fn($n) => $en ? number_format((float) $n, 0, '.', ',') : number_format((float) $n, 0, ',', '.');

$cover      = $product->image();
$priceFrom  = $product->priceFrom()->or($product->price());
$isElectric = $product->antrieb()->value() === 'elektro';
?>
<article class="card product-card">
    <a href="<?= $product->url() ?>" class="product-card__media" aria-label="<?= $product->title()->esc() ?>">
<?php if ($cover): ?>
        <img src="<?= $cover->resize(560)->url() ?>" alt="<?= $cover->alt()->or($product->title())->esc() ?>" loading="lazy" width="560" height="420">
<?php else: ?>
        <span class="product-card__ph"><?= $en ? 'Image follows' : 'Bild folgt' ?></span>
<?php endif ?>
        <div class="product-card__badges">
<?php if ($product->bestseller()->toBool()): ?>
            <span class="badge badge--bestseller"><?= t('product.bestseller') ?></span>
<?php endif ?>
<?php if ($isElectric): ?>
            <span class="badge badge--electric"><?= $en ? 'Electric' : 'Elektro' ?></span>
<?php endif ?>
        </div>
    </a>
    <div class="product-card__body">
        <span class="product-card__brand"><?= $product->brand() ?></span>
        <a href="<?= $product->url() ?>"><h3 class="product-card__title"><?= $product->title() ?></h3></a>
        <span class="product-card__meta"><?= $product->powerPs() ?> PS &middot; <?= $product->weightKg() ?> kg</span>
        <div class="product-card__foot">
            <div class="price">
                <span class="price__from"><?= t('product.from') ?></span>
                <?= $fmt($priceFrom->value()) ?>&nbsp;&euro;
                <small><?= t('product.incl_vat') ?></small>
            </div>
            <a class="btn btn--dark" href="<?= $product->url() ?>"><?= t('product.details') ?></a>
        </div>
    </div>
</article>
