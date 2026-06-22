<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section auth-section">
    <div class="container container--narrow">
        <div class="auth-card">
            <span class="eyebrow"><?= t('auth.login.title') ?></span>
            <h1><?= $page->title() ?></h1>
            <p class="auth-card__lead"><?= t('auth.login.intro') ?></p>

<?php if ($alert === 'invalid'): ?>
            <div class="form-alert form-alert--err"><?= t('auth.login.failed') ?></div>
<?php elseif ($alert === 'csrf'): ?>
            <div class="form-alert form-alert--err"><?= $en ? 'Your session expired. Please try again.' : 'Sitzung abgelaufen. Bitte erneut versuchen.' ?></div>
<?php endif ?>

            <form class="form" method="post" novalidate>
                <input type="hidden" name="csrf" value="<?= csrf() ?>">

                <div class="field">
                    <label for="l-email"><?= t('auth.email') ?> *</label>
                    <input id="l-email" type="email" name="email" value="<?= esc($email) ?>" autocomplete="email" required autofocus>
                </div>

                <div class="field">
                    <label for="l-pass"><?= t('auth.password') ?> *</label>
                    <input id="l-pass" type="password" name="password" autocomplete="current-password" required>
                </div>

                <div class="form-row form-row--between">
                    <label class="checkbox"><input type="checkbox" name="remember" value="1"><span><?= t('auth.remember') ?></span></label>
                    <a class="link-muted" href="<?= url($en ? 'en/reset-password' : 'passwort-reset') ?>"><?= t('auth.login.forgot') ?></a>
                </div>

                <button class="btn btn--cta btn--lg btn--block" type="submit" name="submit" value="1"><?= t('auth.login.submit') ?></button>
            </form>

            <p class="auth-card__alt"><?= t('auth.login.no_account') ?> <a href="<?= url($en ? 'en/register' : 'registrieren') ?>"><?= t('auth.login.register_now') ?></a></p>
        </div>

        <div class="auth-benefits">
            <h2><?= t('account.benefits.title') ?></h2>
            <ul>
                <li><?= t('account.benefits.orders') ?></li>
                <li><?= t('account.benefits.checkout') ?></li>
                <li><?= t('account.benefits.invoice') ?></li>
                <li><?= t('account.benefits.manage') ?></li>
            </ul>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
