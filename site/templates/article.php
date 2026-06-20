<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$parent = $page->parent();
$cover = $page->image();
$urlAdv = url($en ? 'en/buying-advisor' : 'kaufberater');
$mins = max(1, (int) round(str_word_count(strip_tags((string) $page->text()->kt())) / 200));
?>
<?php snippet('header') ?>

<div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= $site->url($code) ?>"><?= $en ? 'Home' : 'Start' ?></a> <span>/</span>
<?php if ($parent): ?>
        <a href="<?= $parent->url() ?>"><?= $parent->title() ?></a> <span>/</span>
<?php endif ?>
        <span><?= $page->title() ?></span>
    </nav>
</div>

<article class="section">
    <div class="container container--narrow">
        <span class="eyebrow"><?= t('nav.guide') ?></span>
        <h1><?= $page->title() ?></h1>
        <p class="section__lead"><?= $page->excerpt() ?></p>
        <p class="article-meta">
<?php if ($page->date()->isNotEmpty()): ?><?= $page->date()->toDate($en ? 'M j, Y' : 'd.m.Y') ?> · <?php endif ?>
            <?= $mins ?> <?= $en ? 'min read' : 'Min. Lesezeit' ?>
        </p>
<?php if ($cover): ?>
        <img class="article-cover" src="<?= $cover->resize(1100)->url() ?>" alt="<?= $cover->alt()->or($page->title())->esc() ?>">
<?php endif ?>

        <div class="prose">
            <?= $page->text()->kt() ?>
        </div>

<?php if ($page->faq()->toStructure()->count() > 0): ?>
        <h2 style="margin-top:var(--sp-7)">FAQ</h2>
        <div class="faq">
<?php foreach ($page->faq()->toStructure() as $f): ?>
            <details class="faq__item">
                <summary><?= $f->q()->esc() ?></summary>
                <p><?= $f->a()->kt() ?></p>
            </details>
<?php endforeach ?>
        </div>
<?php endif ?>

        <div class="article-cta">
            <h3><?= $en ? 'Not sure which model fits?' : 'Unsicher, welches Modell passt?' ?></h3>
            <p><?= $en ? 'Our buying advisor recommends a concrete model in two minutes.' : 'Unser Kaufberater empfiehlt dir in zwei Minuten ein konkretes Modell.' ?></p>
            <a class="btn btn--cta btn--lg" href="<?= $urlAdv ?>"><?= t('nav.advisor') ?></a>
        </div>
    </div>
</article>

<?php snippet('seo/jsonld-article', ['article' => $page]) ?>
<?php snippet('footer') ?>
