<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $page->antrieb()->value() === 'elektro';
$banner = $isElectric ? 'assets/img/banner-electric.webp' : 'assets/img/banner-petrol.webp';
$products = $page->children()->listed()->filterBy('intendedTemplate', 'product')->sortBy('powerPs', 'asc');

// --- Build per-product facet data + aggregate which filters are relevant ---
$rows = [];
$steuSet = [];   // value => count
$shaftSet = [];  // code  => count
$anySale = false; $anyNotInStock = false;
foreach ($products as $p) {
    $vs    = $p->variants()->toStructure();
    $first = $vs->first();
    $price = $first && $first->price()->isNotEmpty() ? (float) $first->price()->value() : (float) $p->priceFrom()->value();
    $uvp   = $p->uvp()->isNotEmpty() ? (float) $p->uvp()->value() : null;
    $sale  = $uvp && $uvp > $price;
    $avail = $p->availability()->or('instock')->value();
    if ($sale) { $anySale = true; }
    if ($avail !== 'instock') { $anyNotInStock = true; }

    $steu = [];
    if ($p->steuerung()->isNotEmpty()) { $steu[] = (string) $p->steuerung(); }
    foreach ($vs as $v) { if ($v->steuerung()->isNotEmpty()) { $steu[] = (string) $v->steuerung(); } }
    $steu = array_values(array_unique($steu));

    $shaft = [];
    foreach ($vs as $v) {
        if (preg_match('/^\s*([A-Za-z]+)/', (string) $v->shaft(), $m)) { $shaft[] = strtoupper($m[1]); }
    }
    $shaft = array_values(array_unique($shaft));

    foreach ($steu as $x)  { $steuSet[$x]  = ($steuSet[$x]  ?? 0) + 1; }
    foreach ($shaft as $x) { $shaftSet[$x] = ($shaftSet[$x] ?? 0) + 1; }

    $rows[] = ['p' => $p, 'power' => (float) $p->powerPs()->value(), 'price' => $price,
               'weight' => (float) $p->weightKg()->value(), 'steu' => $steu, 'shaft' => $shaft,
               'avail' => $avail, 'sale' => $sale];
}

// Range facets (only buckets that actually contain products are shown)
$powerBuckets = [[0, 5, $en ? 'up to 5 hp' : 'bis 5 PS'], [6, 10, '5–10 PS'], [11, 15, '10–15 PS'], [16, 9999, $en ? 'over 15 hp' : 'über 15 PS']];
$priceBuckets = [[0, 999.99, $en ? 'up to €1,000' : 'bis 1.000 €'], [1000, 1999.99, '1.000–2.000 €'], [2000, 2999.99, '2.000–3.000 €'], [3000, 9999999, $en ? 'over €3,000' : 'ab 3.000 €']];
$bucketCount = function ($buckets, $key) use ($rows) {
    $out = [];
    foreach ($buckets as $b) {
        $n = 0;
        foreach ($rows as $r) { if ($r[$key] >= $b[0] && $r[$key] <= $b[1]) { $n++; } }
        if ($n > 0) { $out[] = [$b[0], $b[1], $b[2], $n]; }
    }
    return $out;
};
$powerFacets = $bucketCount($powerBuckets, 'power');
$priceFacets = $bucketCount($priceBuckets, 'price');
ksort($steuSet); ksort($shaftSet);
$shaftLabels = ['S' => $en ? 'Short shaft (S)' : 'Kurzschaft (S)', 'L' => $en ? 'Long shaft (L)' : 'Langschaft (L)', 'UL' => $en ? 'Ultra-long (UL)' : 'Ultralang (UL)', 'XL' => 'XL'];
$steuLabels  = ['Pinne' => $en ? 'Tiller' : 'Pinne', 'Fernbedienung' => $en ? 'Remote control' : 'Fernbedienung'];
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
            <details class="filters" open data-filters>
                <summary class="filters__toggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg><?= $en ? 'Filter' : 'Filter' ?></summary>
                <div class="filters__head">
                    <h3><?= $en ? 'Filter' : 'Filter' ?></h3>
                    <button type="button" class="filters__reset" data-filter-reset hidden><?= $en ? 'Reset' : 'Zurücksetzen' ?></button>
                </div>

<?php if (count($powerFacets) > 1): ?>
                <fieldset class="filter-group">
                    <legend><?= $en ? 'Power' : 'Leistung' ?></legend>
<?php foreach ($powerFacets as $b): ?>
                    <label class="facet"><input type="checkbox" data-filter="power" data-min="<?= $b[0] ?>" data-max="<?= $b[1] ?>"><span class="facet__l"><?= $b[2] ?></span><span class="facet__n"><?= $b[3] ?></span></label>
<?php endforeach ?>
                </fieldset>
<?php endif ?>

<?php if (count($steuSet) > 1): ?>
                <fieldset class="filter-group">
                    <legend><?= t('product.control') ?></legend>
<?php foreach ($steuSet as $val => $n): ?>
                    <label class="facet"><input type="checkbox" data-filter="steuerung" data-value="<?= esc($val) ?>"><span class="facet__l"><?= esc($steuLabels[$val] ?? $val) ?></span><span class="facet__n"><?= $n ?></span></label>
<?php endforeach ?>
                </fieldset>
<?php endif ?>

<?php if (count($shaftSet) > 1): ?>
                <fieldset class="filter-group">
                    <legend><?= $en ? 'Shaft length' : 'Schaftlänge' ?></legend>
<?php foreach ($shaftSet as $code => $n): ?>
                    <label class="facet"><input type="checkbox" data-filter="shaft" data-value="<?= esc($code) ?>"><span class="facet__l"><?= esc($shaftLabels[$code] ?? $code) ?></span><span class="facet__n"><?= $n ?></span></label>
<?php endforeach ?>
                </fieldset>
<?php endif ?>

<?php if (count($priceFacets) > 1): ?>
                <fieldset class="filter-group">
                    <legend><?= $en ? 'Price' : 'Preis' ?></legend>
<?php foreach ($priceFacets as $b): ?>
                    <label class="facet"><input type="checkbox" data-filter="price" data-min="<?= $b[0] ?>" data-max="<?= $b[1] ?>"><span class="facet__l"><?= $b[2] ?></span><span class="facet__n"><?= $b[3] ?></span></label>
<?php endforeach ?>
                </fieldset>
<?php endif ?>

<?php if ($anyNotInStock || $anySale): ?>
                <fieldset class="filter-group">
                    <legend><?= $en ? 'Availability' : 'Verfügbarkeit' ?></legend>
<?php if ($anyNotInStock): ?>
                    <label class="facet"><input type="checkbox" data-filter="avail" data-value="instock"><span class="facet__l"><?= $en ? 'In stock only' : 'Nur sofort lieferbar' ?></span></label>
<?php endif ?>
<?php if ($anySale): ?>
                    <label class="facet"><input type="checkbox" data-filter="sale" data-value="1"><span class="facet__l"><?= $en ? 'On offer only' : 'Nur Angebote' ?></span></label>
<?php endif ?>
                </fieldset>
<?php endif ?>

                <div class="filter-group filter-group--cta">
                    <p class="filter-help"><?= $en ? 'Not sure which one fits?' : 'Unsicher bei der Wahl?' ?></p>
                    <a class="btn btn--ghost btn--sm btn--block" href="<?= url($en ? 'en/buying-advisor' : 'kaufberater') ?>"><?= t('nav.advisor') ?></a>
                </div>
            </details>

            <div>
                <div class="shop-toolbar">
                    <span class="count" data-count><?= $products->count() ?> <?= $en ? 'models' : 'Modelle' ?></span>
                    <span class="spacer"></span>
                    <label for="sort"><?= $en ? 'Sort' : 'Sortieren' ?></label>
                    <select id="sort" data-sort style="padding:8px 10px;border:1px solid var(--line);border-radius:var(--r-sm)">
                        <option value="power"><?= $en ? 'Power (low to high)' : 'Leistung (aufsteigend)' ?></option>
                        <option value="price-asc"><?= $en ? 'Price (low to high)' : 'Preis (aufsteigend)' ?></option>
                        <option value="price-desc"><?= $en ? 'Price (high to low)' : 'Preis (absteigend)' ?></option>
                        <option value="weight"><?= $en ? 'Weight (light first)' : 'Gewicht (leicht zuerst)' ?></option>
                    </select>
                </div>

                <div class="pgrid" data-listing-grid>
<?php foreach ($rows as $r): $product = $r['p']; ?>
                    <div class="listing-item"
                         data-price="<?= $r['price'] ?>"
                         data-power="<?= $r['power'] ?>"
                         data-weight="<?= $r['weight'] ?>"
                         data-steuerung="<?= esc(implode(' ', $r['steu'])) ?>"
                         data-shaft="<?= esc(implode(' ', $r['shaft'])) ?>"
                         data-avail="<?= esc($r['avail']) ?>"
                         data-sale="<?= $r['sale'] ? '1' : '0' ?>">
                        <?php snippet('product-card', ['product' => $product]) ?>
                    </div>
<?php endforeach ?>
                </div>
                <p class="account-empty" data-listing-empty<?= $products->count() === 0 ? '' : ' hidden' ?>><?= $products->count() === 0 ? ($en ? 'Products are being added.' : 'Produkte werden gerade ergänzt.') : ($en ? 'No models match this selection.' : 'Keine Modelle für diese Auswahl. Filter anpassen oder zurücksetzen.') ?></p>
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
