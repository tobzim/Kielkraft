<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';

$first      = $user->content()->get('firstname')->or($user->name())->or($user->email());
$accountType = $user->content()->get('accountType')->or('private')->value();
$invoiceOk  = $user->content()->get('invoiceEligible')->toBool();
$newsletter = $user->content()->get('newsletter')->toBool();
$orderCount = $orders ? $orders->count() : 0;
$dateFmt    = $en ? 'M j, Y' : 'd.m.Y';
?>
<?php snippet('header') ?>

<section class="section account">
    <div class="container">
        <div class="account__head">
            <div>
                <span class="eyebrow"><?= t('account.title') ?></span>
                <h1><?= t('account.greeting') ?>, <?= esc($first) ?></h1>
                <p class="account__email"><?= esc($user->email()) ?></p>
            </div>
            <a class="btn btn--ghost" href="<?= url('logout') . ($en ? '?lang=en' : '') ?>"><?= t('account.logout') ?></a>
        </div>

<?php if ($saved): ?>
        <div class="form-alert form-alert--ok"><?= t('account.profile.saved') ?></div>
<?php elseif ($alert === 'save-failed'): ?>
        <div class="form-alert form-alert--err"><?= $en ? 'Saving failed. Please try again.' : 'Speichern fehlgeschlagen. Bitte erneut versuchen.' ?></div>
<?php endif ?>

        <div class="account__layout" data-account>
            <aside class="account__nav">
                <button class="account__navlink is-active" type="button" data-account-link="overview"><?= t('account.nav.overview') ?></button>
                <button class="account__navlink" type="button" data-account-link="orders"><?= t('account.nav.orders') ?><span class="account__count"><?= $orderCount ?></span></button>
                <button class="account__navlink" type="button" data-account-link="profile"><?= t('account.nav.profile') ?></button>
                <a class="account__navlink account__navlink--shop" href="<?= $site->url($code) ?>"><?= t('account.shop') ?></a>
            </aside>

            <div class="account__main">
                <!-- Overview -->
                <div class="account__panel is-active" data-account-panel="overview">
                    <div class="account-cards">
                        <div class="account-card account-card--invoice <?= $invoiceOk ? 'is-on' : 'is-off' ?>">
                            <div class="account-card__icon">
<?php if ($invoiceOk): ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
<?php else: ?>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
<?php endif ?>
                            </div>
                            <div>
                                <h3><?= $en ? 'Invoice purchase' : 'Kauf auf Rechnung' ?></h3>
                                <p><?= $invoiceOk ? t('account.invoice.on') : t('account.invoice.off') ?></p>
                            </div>
                        </div>

                        <div class="account-card">
                            <div class="account-card__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                            </div>
                            <div>
                                <h3><?= t('account.nav.orders') ?></h3>
                                <p><?= $orderCount === 1 ? ($en ? '1 order' : '1 Bestellung') : $orderCount . ($en ? ' orders' : ' Bestellungen') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="account-summary">
                        <h3><?= $en ? 'Your billing address' : 'Deine Rechnungsadresse' ?></h3>
<?php if ($user->content()->get('street')->isNotEmpty()): ?>
                        <address>
<?php if ($accountType === 'business' && $user->content()->get('company')->isNotEmpty()): ?>
                            <strong><?= esc($user->content()->get('company')) ?></strong><br>
<?php endif ?>
                            <?= esc(trim($user->content()->get('firstname') . ' ' . $user->content()->get('lastname'))) ?><br>
                            <?= esc($user->content()->get('street')) ?><br>
                            <?= esc(trim($user->content()->get('zip') . ' ' . $user->content()->get('city'))) ?><br>
                            <?= esc($user->content()->get('country')->or('Deutschland')) ?>
                        </address>
<?php else: ?>
                        <p class="account-summary__empty"><?= $en ? 'No address saved yet.' : 'Noch keine Adresse hinterlegt.' ?></p>
<?php endif ?>
                        <button class="link-muted" type="button" data-account-link="profile"><?= $en ? 'Edit profile & address' : 'Profil & Adresse bearbeiten' ?></button>
                    </div>
                </div>

                <!-- Orders -->
                <div class="account__panel" data-account-panel="orders">
                    <h2><?= t('account.orders.title') ?></h2>
<?php if ($orderCount === 0): ?>
                    <div class="account-empty">
                        <p><?= t('account.orders.empty') ?></p>
                        <a class="btn btn--cta" href="<?= $site->url($code) ?>"><?= t('account.shop') ?></a>
                    </div>
<?php else: ?>
                    <div class="table-wrap">
                        <table class="account-orders">
                            <thead>
                                <tr>
                                    <th><?= t('account.orders.number') ?></th>
                                    <th><?= t('account.orders.date') ?></th>
                                    <th><?= t('account.orders.status') ?></th>
                                    <th class="ta-right"><?= t('account.orders.total') ?></th>
                                </tr>
                            </thead>
                            <tbody>
<?php foreach ($orders as $o): $st = $o->content()->get('orderStatus')->or('new')->value(); ?>
                                <tr>
                                    <td data-label="<?= t('account.orders.number') ?>"><strong><?= esc($o->content()->get('orderNumber')->or($o->title())) ?></strong></td>
                                    <td data-label="<?= t('account.orders.date') ?>"><?= $o->date()->toDate($dateFmt) ?></td>
                                    <td data-label="<?= t('account.orders.status') ?>"><span class="order-status order-status--<?= $st ?>"><?= t('order.status.' . $st, $st) ?></span></td>
                                    <td class="ta-right" data-label="<?= t('account.orders.total') ?>"><?= mv_eur($o->content()->get('total')->toFloat(), $code) ?></td>
                                </tr>
<?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
<?php endif ?>
                </div>

                <!-- Profile -->
                <div class="account__panel" data-account-panel="profile">
                    <h2><?= t('account.profile.title') ?></h2>
                    <form class="form" method="post" novalidate data-register>
                        <input type="hidden" name="csrf" value="<?= csrf() ?>">
                        <input type="hidden" name="action" value="profile">

                        <div class="seg-toggle" role="radiogroup" aria-label="<?= t('auth.register.type') ?>">
                            <label class="seg-toggle__opt">
                                <input type="radio" name="accountType" value="private" data-account-type<?= $accountType !== 'business' ? ' checked' : '' ?>>
                                <span><?= t('auth.register.private') ?></span>
                            </label>
                            <label class="seg-toggle__opt">
                                <input type="radio" name="accountType" value="business" data-account-type<?= $accountType === 'business' ? ' checked' : '' ?>>
                                <span><?= t('auth.register.business') ?></span>
                            </label>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label for="p-first"><?= t('auth.firstname') ?></label>
                                <input id="p-first" name="firstname" value="<?= esc($user->content()->get('firstname')) ?>" autocomplete="given-name">
                            </div>
                            <div class="field">
                                <label for="p-last"><?= t('auth.lastname') ?></label>
                                <input id="p-last" name="lastname" value="<?= esc($user->content()->get('lastname')) ?>" autocomplete="family-name">
                            </div>
                        </div>

                        <div class="business-fields"<?= $accountType === 'business' ? '' : ' hidden' ?> data-business-fields>
                            <div class="form-grid">
                                <div class="field">
                                    <label for="p-company"><?= t('auth.company') ?></label>
                                    <input id="p-company" name="company" value="<?= esc($user->content()->get('company')) ?>" autocomplete="organization">
                                </div>
                                <div class="field">
                                    <label for="p-vat"><?= t('auth.vatid') ?></label>
                                    <input id="p-vat" name="vatId" value="<?= esc($user->content()->get('vatId')) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label for="p-street"><?= t('auth.street') ?></label>
                            <input id="p-street" name="street" value="<?= esc($user->content()->get('street')) ?>" autocomplete="street-address">
                        </div>

                        <div class="form-grid form-grid--zip">
                            <div class="field">
                                <label for="p-zip"><?= t('auth.zip') ?></label>
                                <input id="p-zip" name="zip" value="<?= esc($user->content()->get('zip')) ?>" autocomplete="postal-code" inputmode="numeric" data-zip data-zip-country="de">
                            </div>
                            <div class="field">
                                <label for="p-city"><?= t('auth.city') ?></label>
                                <input id="p-city" name="city" value="<?= esc($user->content()->get('city')) ?>" autocomplete="address-level2" data-city>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label for="p-country"><?= t('auth.country') ?></label>
                                <input id="p-country" name="country" value="<?= esc($user->content()->get('country')->or('Deutschland')) ?>" autocomplete="country-name">
                            </div>
                            <div class="field">
                                <label for="p-phone"><?= t('auth.phone') ?></label>
                                <input id="p-phone" type="tel" name="phone" value="<?= esc($user->content()->get('phone')) ?>" autocomplete="tel">
                            </div>
                        </div>

                        <label class="checkbox"><input type="checkbox" name="newsletter" value="1"<?= $newsletter ? ' checked' : '' ?>><span><?= t('auth.newsletter') ?></span></label>

                        <button class="btn btn--cta btn--lg" type="submit" name="submit" value="1"><?= t('account.profile.save') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
