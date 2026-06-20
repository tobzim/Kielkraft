<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section auth-section">
    <div class="container container--narrow">
        <div class="auth-card">
            <span class="eyebrow"><?= t('reset.title') ?></span>
            <h1><?= $page->title() ?></h1>

<?php if ($sent): ?>
            <div class="form-alert form-alert--ok"><?= t('reset.sent') ?></div>
            <p class="auth-card__alt"><a href="<?= url($en ? 'en/login' : 'anmelden') ?>"><?= t('reset.back') ?></a></p>
<?php else: ?>
            <p class="auth-card__lead"><?= t('reset.intro') ?></p>
<?php if ($alert === 'csrf'): ?>
            <div class="form-alert form-alert--err"><?= $en ? 'Your session expired. Please try again.' : 'Sitzung abgelaufen. Bitte erneut versuchen.' ?></div>
<?php endif ?>
            <form class="form" method="post" novalidate>
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <div class="field">
                    <label for="rp-email"><?= t('auth.email') ?> *</label>
                    <input id="rp-email" type="email" name="email" value="<?= esc($email) ?>" autocomplete="email" required autofocus>
                </div>
                <button class="btn btn--cta btn--lg btn--block" type="submit" name="submit" value="1"><?= t('reset.submit') ?></button>
            </form>
            <p class="auth-card__alt"><a href="<?= url($en ? 'en/login' : 'anmelden') ?>"><?= t('reset.back') ?></a></p>
<?php endif ?>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
