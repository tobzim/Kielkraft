<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$articles = $page->children()->listed()->sortBy('date', 'desc');
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <div style="max-width:60ch;margin-bottom:var(--sp-6)">
            <span class="eyebrow"><?= t('nav.guide') ?></span>
            <h1><?= $page->title() ?></h1>
            <p class="section__lead"><?= $page->intro() ?></p>
        </div>

        <div class="guide-grid">
<?php foreach ($articles as $a): $cov = $a->image(); ?>
            <a class="guide-card" href="<?= $a->url() ?>">
                <div class="guide-card__media">
                    <img src="<?= $cov ? $cov->resize(560)->url() : url('assets/img/texture-water.webp') ?>" alt="" loading="lazy">
                </div>
                <div class="guide-card__body">
                    <span class="guide-card__meta"><?= t('nav.guide') ?><?php if ($a->date()->isNotEmpty()): ?> · <?= $a->date()->toDate($en ? 'M j, Y' : 'd.m.Y') ?><?php endif ?></span>
                    <h3><?= $a->title() ?></h3>
                    <p><?= $a->excerpt() ?></p>
                    <span class="guide-card__more"><?= $en ? 'Read more' : 'Weiterlesen' ?> &rarr;</span>
                </div>
            </a>
<?php endforeach ?>
        </div>
<?php if ($articles->count() === 0): ?>
        <p class="section__lead"><?= $en ? 'Articles coming soon.' : 'Beiträge folgen in Kürze.' ?></p>
<?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
