<?php snippet('header') ?>

<section class="section container container--narrow">
    <div style="margin-bottom:var(--sp-5)">
        <h1><?= $page->title() ?></h1>
<?php if ($page->intro()->isNotEmpty()): ?>
        <p class="section__lead"><?= $page->intro() ?></p>
<?php endif ?>
    </div>

    <div class="prose">
        <?= $page->text()->kt() ?>
    </div>

<?php if ($page->isPlaceholder()->toBool()): ?>
    <div class="callout" style="margin-top:var(--sp-6)">
        <strong><?= t('footer.placeholder') ?></strong>
    </div>
<?php endif ?>
</section>

<?php snippet('footer') ?>
