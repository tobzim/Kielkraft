<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$err  = fn(string $k) => isset($invalid[$k]) ? ' has-error' : '';
?>
<?php snippet('header') ?>

<section class="section auth-section">
    <div class="container container--narrow">
        <div class="auth-card auth-card--wide">
            <span class="eyebrow"><?= t('auth.register.title') ?></span>
            <h1><?= $page->title() ?></h1>
            <p class="auth-card__lead"><?= t('auth.register.intro') ?></p>

<?php if ($alert === 'email-taken'): ?>
            <div class="form-alert form-alert--err"><?= t('auth.register.taken') ?> <a href="<?= url($en ? 'en/login' : 'anmelden') ?>"><?= t('auth.register.login_now') ?></a></div>
<?php elseif ($alert === 'failed'): ?>
            <div class="form-alert form-alert--err"><?= t('auth.register.failed') ?></div>
<?php elseif ($alert === 'csrf'): ?>
            <div class="form-alert form-alert--err"><?= $en ? 'Your session expired. Please try again.' : 'Sitzung abgelaufen. Bitte erneut versuchen.' ?></div>
<?php elseif ($alert === 'invalid'): ?>
            <div class="form-alert form-alert--err"><?= t('auth.error.required') ?></div>
<?php endif ?>

            <form class="form" method="post" novalidate data-register>
                <input type="hidden" name="csrf" value="<?= csrf() ?>">

                <div class="seg-toggle" role="radiogroup" aria-label="<?= t('auth.register.type') ?>">
                    <label class="seg-toggle__opt">
                        <input type="radio" name="type" value="private" data-account-type<?= $data['type'] !== 'business' ? ' checked' : '' ?>>
                        <span><?= t('auth.register.private') ?></span>
                    </label>
                    <label class="seg-toggle__opt">
                        <input type="radio" name="type" value="business" data-account-type<?= $data['type'] === 'business' ? ' checked' : '' ?>>
                        <span><?= t('auth.register.business') ?></span>
                    </label>
                </div>

                <div class="form-grid">
                    <div class="field<?= $err('firstname') ?>">
                        <label for="r-first"><?= t('auth.firstname') ?> *</label>
                        <input id="r-first" name="firstname" value="<?= esc($data['firstname']) ?>" autocomplete="given-name" required>
                    </div>
                    <div class="field<?= $err('lastname') ?>">
                        <label for="r-last"><?= t('auth.lastname') ?> *</label>
                        <input id="r-last" name="lastname" value="<?= esc($data['lastname']) ?>" autocomplete="family-name" required>
                    </div>
                </div>

                <div class="business-fields"<?= $data['type'] === 'business' ? '' : ' hidden' ?> data-business-fields>
                    <div class="form-grid">
                        <div class="field<?= $err('company') ?>">
                            <label for="r-company"><?= t('auth.company') ?> *</label>
                            <input id="r-company" name="company" value="<?= esc($data['company']) ?>" autocomplete="organization">
                        </div>
                        <div class="field">
                            <label for="r-vat"><?= t('auth.vatid') ?></label>
                            <input id="r-vat" name="vatId" value="<?= esc($data['vatId']) ?>">
                        </div>
                    </div>
                </div>

                <div class="field<?= $err('email') ?>">
                    <label for="r-email"><?= t('auth.email') ?> *</label>
                    <input id="r-email" type="email" name="email" value="<?= esc($data['email']) ?>" autocomplete="email" required>
<?php if (isset($invalid['email'])): ?><span class="field-err"><?= $en ? 'Please enter a valid email address.' : 'Bitte gib eine gültige E-Mail-Adresse an.' ?></span><?php endif ?>
                </div>

                <div class="form-grid">
                    <div class="field<?= $err('password') ?>">
                        <label for="r-pass"><?= t('auth.password') ?> *</label>
                        <input id="r-pass" type="password" name="password" autocomplete="new-password" minlength="8" required>
<?php if (isset($invalid['password'])): ?><span class="field-err"><?= t('auth.error.password_len') ?></span><?php endif ?>
                    </div>
                    <div class="field<?= $err('password_confirm') ?>">
                        <label for="r-pass2"><?= t('auth.password_confirm') ?> *</label>
                        <input id="r-pass2" type="password" name="password_confirm" autocomplete="new-password" required>
<?php if (isset($invalid['password_confirm'])): ?><span class="field-err"><?= t('auth.error.password_match') ?></span><?php endif ?>
                    </div>
                </div>

                <div class="field">
                    <label for="r-street"><?= t('auth.street') ?></label>
                    <input id="r-street" name="street" value="<?= esc($data['street']) ?>" autocomplete="street-address">
                </div>

                <div class="form-grid form-grid--zip">
                    <div class="field">
                        <label for="r-zip"><?= t('auth.zip') ?></label>
                        <input id="r-zip" name="zip" value="<?= esc($data['zip']) ?>" autocomplete="postal-code" inputmode="numeric" data-zip data-zip-country="de">
                    </div>
                    <div class="field">
                        <label for="r-city"><?= t('auth.city') ?></label>
                        <input id="r-city" name="city" value="<?= esc($data['city']) ?>" autocomplete="address-level2" data-city>
                    </div>
                </div>

                <div class="field">
                    <label for="r-country"><?= t('auth.country') ?></label>
                    <input id="r-country" name="country" value="<?= esc($data['country'] ?: 'Deutschland') ?>" autocomplete="country-name">
                </div>

                <div class="field">
                    <label for="r-phone"><?= t('auth.phone') ?></label>
                    <input id="r-phone" type="tel" name="phone" value="<?= esc($data['phone']) ?>" autocomplete="tel">
                </div>

                <label class="checkbox"><input type="checkbox" name="newsletter" value="1"<?= $data['newsletter'] !== '' ? ' checked' : '' ?>><span><?= t('auth.newsletter') ?></span></label>

                <label class="checkbox<?= isset($invalid['terms']) ? ' has-error' : '' ?>"><input type="checkbox" name="terms" value="1" required><span><?= $en ? 'I accept the' : 'Ich akzeptiere die' ?> <a href="<?= url($en ? 'en/terms' : 'agb') ?>"><?= t('footer.terms') ?></a> <?= $en ? 'and the' : 'und die' ?> <a href="<?= url($en ? 'en/privacy' : 'datenschutz') ?>"><?= t('footer.privacy') ?></a>.</span></label>

                <button class="btn btn--cta btn--lg btn--block" type="submit" name="submit" value="1"><?= t('auth.register.submit') ?></button>
            </form>

            <p class="auth-card__alt"><?= t('auth.register.has_account') ?> <a href="<?= url($en ? 'en/login' : 'anmelden') ?>"><?= t('auth.register.login_now') ?></a></p>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
