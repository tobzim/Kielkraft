<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $page->antrieb()->value() === 'elektro';
$axis = $isElectric ? 'axis-cool' : 'axis-warm';
$banner = $isElectric ? 'assets/img/banner-electric.webp' : 'assets/img/banner-petrol.webp';
$products = $page->children()->listed()->filterBy('intendedTemplate', 'product')->sortBy('powerPs', 'asc');
?>
<?php snippet('header') ?>

<section class="cat-banner <?= $axis ?>">
    <img class="cat-banner__img" src="<?= url($banner) ?>" alt="" loading="eager" fetchpriority="high">
    <div class="cat-banner__scrim"></div>
    <div class="container cat-banner__inner">
        <div class="section__label" style="color:#fff"><?= $page->brand() ?></div>
        <h1><?= $page->title() ?></h1>
        <p><?= $page->intro() ?></p>
    </div>
</section>

<section class="section">
    <div class="container <?= $axis ?>" data-listing>
        <div class="filterbar">
            <label for="sort"><?= $en ? 'Sort' : 'Sortieren' ?></label>
            <select id="sort" data-sort>
                <option value="power"><?= $en ? 'Power (low to high)' : 'Leistung (aufsteigend)' ?></option>
                <option value="price-asc"><?= $en ? 'Price (low to high)' : 'Preis (aufsteigend)' ?></option>
                <option value="price-desc"><?= $en ? 'Price (high to low)' : 'Preis (absteigend)' ?></option>
            </select>
            <label for="steer"><?= t('product.control') ?></label>
            <select id="steer" data-filter-steuerung>
                <option value=""><?= $en ? 'All' : 'Alle' ?></option>
                <option value="Pinne"><?= $en ? 'Tiller' : 'Pinne' ?></option>
                <option value="Fernbedienung"><?= $en ? 'Remote' : 'Fernbedienung' ?></option>
            </select>
            <span class="filter-count" data-count><?= $products->count() ?> <?= $en ? 'models' : 'Modelle' ?></span>
        </div>

        <div class="grid grid--3" data-listing-grid>
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
</section>

<?php if ($page->description()->isNotEmpty()): ?>
<section class="section--tight bg-panel">
    <div class="container container--narrow prose">
        <?= $page->description()->kt() ?>
    </div>
</section>
<?php endif ?>

<?php snippet('footer') ?>
