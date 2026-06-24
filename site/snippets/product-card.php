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
$prodData = [
    'id'    => $product->id(),
    'title' => $product->title()->value(),
    'url'   => $product->url(),
    'img'   => $cover ? $cover->resize(420)->url() : '',
    'price' => mv_eur($priceFrom, $code),
    'ps'    => $product->powerPs()->value(),
    'kw'    => $product->powerKw()->value(),
    'kg'    => $product->weightKg()->value(),
    'brand' => $product->brand()->value(),
    'drive' => $isElectric ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol' : 'Benzin'),
    'avail' => $availLabel,
];
?>
<article class="pcard" data-product='<?= htmlspecialchars(json_encode($prodData, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_TAG), ENT_QUOTES) ?>'>
    <div class="pcard__tools">
        <button type="button" class="ptool ptool--wl" data-wl-toggle aria-pressed="false" aria-label="<?= $en ? 'Save to wishlist' : 'Zur Merkliste' ?>" title="<?= $en ? 'Wishlist' : 'Merkliste' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
        </button>
        <button type="button" class="ptool ptool--cmp" data-cmp-toggle aria-pressed="false" aria-label="<?= $en ? 'Add to comparison' : 'Zum Vergleich' ?>" title="<?= $en ? 'Compare' : 'Vergleichen' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h7M3 12h7M3 18h7"/><path d="M17 4v16M14 7l3-3 3 3M14 17l3 3 3-3"/></svg>
        </button>
    </div>
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
        <img src="<?= $cover->resize(540)->url() ?>" srcset="<?= $cover->srcset([280, 420, 540, 760]) ?>" sizes="(max-width: 640px) 45vw, 260px" alt="<?= $cover->alt()->or($product->title())->esc() ?>" loading="lazy" width="540" height="405">
<?php else: ?>
        <span class="pcard__ph"><?= $en ? 'Product photo follows' : 'Produktfoto folgt' ?></span>
<?php endif ?>
    </a>
    <div class="pcard__body">
        <?php snippet('brand-logo', ['brand' => $product->brand()->value(), 'class' => 'brand-logo--card']) ?>
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
