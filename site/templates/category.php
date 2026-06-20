<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $page->antrieb()->value() === 'elektro';
$banner = $isElectric ? 'assets/img/banner-electric.webp' : 'assets/img/banner-petrol.webp';
$products = $page->children()->listed()->filterBy('intendedTemplate', 'product')->sortBy('powerPs', 'asc');
?>
<?php snippet('header') ?>

<div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= $site->url($code) ?>"><?= $en ? 'Home' : 'Start' ?></a> <span>/</span>
        <span><?= $page->title() ?></span>
    </nav>
</div>

<section class="cat-banner">
    <img class="cat-banner__img" src="<?= url($banner) ?>" alt="" loading="eager" fetchpriority="high">
    <div class="cat-banner__scrim"></div>
    <div class="container cat-banner__inner">
        <span class="brand-chip" style="margin-bottom:var(--sp-3)"><?php snippet('brand-logo', ['brand' => $page->brand()->value()]) ?></span>
        <h1><?= $page->title() ?></h1>
        <p><?= $page->intro() ?></p>
    </div>
</section>

<section class="section">
    <div class="container" data-listing>
        <div class="shop-layout">
            <aside class="filters">
                <h3><?= $en ? 'Filter' : 'Filter' ?></h3>
                <div class="filter-group">
                    <label for="steer"><?= t('product.control') ?></label>
                    <select id="steer" data-filter-steuerung>
                        <option value=""><?= $en ? 'All' : 'Alle' ?></option>
                        <option value="Pinne"><?= $en ? 'Tiller' : 'Pinne' ?></option>
                        <option value="Fernbedienung"><?= $en ? 'Remote' : 'Fernbedienung' ?></option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><?= $en ? 'Drive' : 'Antrieb' ?></label>
                    <p style="font-size:var(--fs-200);color:var(--muted);margin:0"><?= $isElectric ? ($en ? 'Electric (ePropulsion)' : 'Elektro (ePropulsion)') : ($en ? 'Petrol 4-stroke (Tohatsu)' : 'Benzin 4-Takt (Tohatsu)') ?></p>
                </div>
                <div class="filter-group">
                    <label><?= $en ? 'Need help choosing?' : 'Unsicher bei der Wahl?' ?></label>
                    <a class="btn btn--ghost btn--sm btn--block" href="<?= url($en ? 'en/buying-advisor' : 'kaufberater') ?>"><?= t('nav.advisor') ?></a>
                </div>
            </aside>

            <div>
                <div class="shop-toolbar">
                    <span class="count" data-count><?= $products->count() ?> <?= $en ? 'models' : 'Modelle' ?></span>
                    <span class="spacer"></span>
                    <label for="sort"><?= $en ? 'Sort' : 'Sortieren' ?></label>
                    <select id="sort" data-sort style="padding:8px 10px;border:1px solid var(--line);border-radius:var(--r-sm)">
                        <option value="power"><?= $en ? 'Power (low to high)' : 'Leistung (aufsteigend)' ?></option>
                        <option value="price-asc"><?= $en ? 'Price (low to high)' : 'Preis (aufsteigend)' ?></option>
                        <option value="price-desc"><?= $en ? 'Price (high to low)' : 'Preis (absteigend)' ?></option>
                    </select>
                </div>

                <div class="pgrid" data-listing-grid>
<?php foreach ($products as $product): ?>
                    <div class="listing-item" data-price="<?= (float) $product->priceFrom()->or($product->variants()->toStructure()->first()?->price())->value() ?>" data-power="<?= (float) $product->powerPs()->value() ?>" data-steuerung="<?= $product->steuerung()->esc() ?>">
                        <?php snippet('product-card', ['product' => $product]) ?>
                    </div>
<?php endforeach ?>
                </div>
<?php if ($products->count() === 0): ?>
                <p class="section__lead"><?= $en ? 'Products are being added.' : 'Produkte werden gerade ergänzt.' ?></p>
<?php endif ?>
            </div>
        </div>

<?php if ($page->description()->isNotEmpty()): ?>
        <div class="prose" style="margin-top:var(--sp-7)">
            <?= $page->description()->kt() ?>
        </div>
<?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
