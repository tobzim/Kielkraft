<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <header style="max-width:60ch;margin-bottom:var(--sp-6)">
            <h1><?= $page->title() ?></h1>
            <p class="section__lead"><?= $page->intro() ?></p>
        </header>

        <div class="cmp-wrap" data-cmp-mount hidden></div>

        <div class="emptybox" data-cmp-empty>
            <p><?= $en ? 'No models selected yet. Tap the compare icon on up to three models.' : 'Noch keine Modelle ausgewählt. Tippe bei bis zu drei Modellen auf das Vergleichs-Symbol.' ?></p>
            <div class="emptybox__cta">
                <a class="btn btn--cta" href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>"><?= $en ? 'Petrol models' : 'Benzin-Modelle' ?></a>
                <a class="btn btn--ghost" href="<?= url($en ? 'en/electric-outboards' : 'elektro-aussenborder') ?>"><?= $en ? 'Electric models' : 'Elektro-Modelle' ?></a>
            </div>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
