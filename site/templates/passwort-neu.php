<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section auth-section">
    <div class="container container--narrow">
        <div class="auth-card">
            <span class="eyebrow"><?= t('reset.new.title') ?></span>
            <h1><?= $page->title() ?></h1>

<?php if ($success): ?>
            <div class="form-alert form-alert--ok"><?= t('reset.new.success') ?></div>
            <p class="auth-card__alt"><a class="btn btn--cta" href="<?= url($en ? 'en/account' : 'konto') ?>"><?= t('account.title') ?></a></p>
<?php elseif (!$valid): ?>
            <div class="form-alert form-alert--err"><?= t('reset.new.invalid') ?></div>
            <p class="auth-card__alt"><a href="<?= url($en ? 'en/reset-password' : 'passwort-reset') ?>"><?= t('reset.title') ?></a></p>
<?php else: ?>
            <p class="auth-card__lead"><?= t('reset.new.intro') ?></p>
<?php if ($alert === 'invalid-token'): ?>
            <div class="form-alert form-alert--err"><?= t('reset.new.invalid') ?></div>
<?php elseif ($alert === 'failed'): ?>
            <div class="form-alert form-alert--err"><?= $en ? 'Something went wrong. Please request a new link.' : 'Etwas ist schiefgelaufen. Bitte fordere einen neuen Link an.' ?></div>
<?php elseif ($alert === 'csrf'): ?>
            <div class="form-alert form-alert--err"><?= $en ? 'Your session expired. Please try again.' : 'Sitzung abgelaufen. Bitte erneut versuchen.' ?></div>
<?php endif ?>
            <form class="form" method="post" novalidate>
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <input type="hidden" name="token" value="<?= esc($token) ?>">
                <input type="hidden" name="email" value="<?= esc($email) ?>">

                <div class="field<?= isset($invalid['password']) ? ' has-error' : '' ?>">
                    <label for="np-pass"><?= t('reset.new.password') ?> *</label>
                    <input id="np-pass" type="password" name="password" minlength="8" autocomplete="new-password" required autofocus>
<?php if (isset($invalid['password'])): ?><span class="field-err"><?= t('auth.error.password_len') ?></span><?php endif ?>
                </div>

                <div class="field<?= isset($invalid['password_confirm']) ? ' has-error' : '' ?>">
                    <label for="np-pass2"><?= t('auth.password_confirm') ?> *</label>
                    <input id="np-pass2" type="password" name="password_confirm" autocomplete="new-password" required>
<?php if (isset($invalid['password_confirm'])): ?><span class="field-err"><?= t('auth.error.password_match') ?></span><?php endif ?>
                </div>

                <button class="btn btn--cta btn--lg btn--block" type="submit" name="submit" value="1"><?= t('reset.new.submit') ?></button>
            </form>
<?php endif ?>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
