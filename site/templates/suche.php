<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$count = $results->count();
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <div class="search-head">
            <span class="eyebrow"><?= t('kielkraft.search') ?></span>
            <h1><?= $q !== '' ? ($en ? 'Search results' : 'Suchergebnisse') : ($en ? 'Search' : 'Suche') ?></h1>

            <form class="search search--page" action="<?= $page->url() ?>" method="get" role="search">
                <select class="search__cat" name="cat" aria-label="<?= $en ? 'Category' : 'Kategorie' ?>">
                    <option value=""<?= $cat === '' ? ' selected' : '' ?>><?= $en ? 'All' : 'Alle' ?></option>
                    <option value="elektro"<?= $cat === 'elektro' ? ' selected' : '' ?>><?= t('nav.electric') ?></option>
                    <option value="benzin"<?= $cat === 'benzin' ? ' selected' : '' ?>><?= t('nav.petrol') ?></option>
                </select>
                <input class="search__input" type="search" name="q" value="<?= esc($q) ?>" placeholder="<?= $en ? 'Search for model, hp, brand…' : 'Modell, PS, Marke suchen…' ?>" aria-label="<?= t('kielkraft.search') ?>" autofocus>
                <button class="search__btn" type="submit" aria-label="<?= t('kielkraft.search') ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg></button>
            </form>

<?php if ($q !== ''): ?>
            <p class="search-count"><?= $count ?> <?= $count === 1 ? ($en ? 'result for' : 'Treffer für') : ($en ? 'results for' : 'Treffer für') ?> „<?= esc($q) ?>"</p>
<?php endif ?>
        </div>

<?php if ($count > 0): ?>
        <div class="pgrid">
<?php foreach ($results as $product): ?>
            <?php snippet('product-card', ['product' => $product]) ?>
<?php endforeach ?>
        </div>
<?php else: ?>
        <div class="account-empty">
            <p><?= $en ? 'No products found. Try a different term or browse all models.' : 'Keine Produkte gefunden. Versuche einen anderen Begriff oder sieh dir alle Modelle an.' ?></p>
            <a class="btn btn--cta" href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>"><?= $en ? 'Browse all models' : 'Alle Modelle ansehen' ?></a>
        </div>
<?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
