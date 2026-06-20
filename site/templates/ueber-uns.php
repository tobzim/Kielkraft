<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
?>
<?php snippet('header') ?>

<section class="cat-banner axis-cool">
    <img class="cat-banner__img" src="<?= url('assets/img/lifestyle.webp') ?>" alt="" loading="eager">
    <div class="cat-banner__scrim"></div>
    <div class="container cat-banner__inner">
        <div class="eyebrow" style="color:#bcd0e4"><?= $en ? 'About us' : 'Über uns' ?></div>
        <h1><?= $page->title() ?></h1>
        <p><?= $page->intro() ?></p>
    </div>
</section>

<section class="section">
    <div class="container container--narrow prose">
        <?= $page->text()->kt() ?>
<?php if ($page->isPlaceholder()->toBool()): ?>
        <div class="callout" style="margin-top:var(--sp-6)"><strong><?= t('footer.placeholder') ?></strong></div>
<?php endif ?>
    </div>
</section>

<section class="section--tight bg-soft">
    <div class="container">
        <div class="featurelist">
            <div class="feature"><h3><?= $en ? 'Specialists, not a general store' : 'Spezialisten, kein Gemischtwarenladen' ?></h3><p><?= $en ? 'We focus on Tohatsu and ePropulsion – so we actually know the products and can advise honestly.' : 'Wir konzentrieren uns auf Tohatsu und ePropulsion – deshalb kennen wir die Produkte wirklich und beraten ehrlich.' ?></p></div>
            <div class="feature"><h3><?= $en ? 'Reachable people' : 'Echte Erreichbarkeit' ?></h3><p><?= $en ? 'Phone, WhatsApp and e-mail with a stated response time – before and after your purchase.' : 'Telefon, WhatsApp und E-Mail mit angegebener Reaktionszeit – vor und nach dem Kauf.' ?></p></div>
            <div class="feature"><h3><?= $en ? 'Honest by default' : 'Ehrlich als Prinzip' ?></h3><p><?= $en ? 'Official new goods, transparent freight, real warranty terms – no fabricated ratings or fake urgency.' : 'Offizielle Neuware, transparente Fracht, echte Garantiebedingungen – keine erfundenen Bewertungen oder Fake-Dringlichkeit.' ?></p></div>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
