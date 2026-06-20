<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$cartNow = kk_cart_get();
$sub = kk_cart_subtotal();
$ship = kk_cart_shipping();
$total = kk_cart_total();
$pays = [
    'vorkasse'   => $en ? 'Bank transfer (advance)' : 'Vorkasse / Überweisung',
    'paypal'     => 'PayPal',
    'klarna'     => 'Klarna',
    'kreditkarte' => $en ? 'Credit card' : 'Kreditkarte',
    'sepa'       => 'SEPA-Lastschrift',
];
if (!empty($invoiceOk)) {
    $pays['rechnung'] = $en ? 'Invoice purchase (returning customer)' : 'Kauf auf Rechnung (Bestandskunde)';
}
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <div style="margin-bottom:var(--sp-5)">
            <span class="eyebrow"><?= $en ? 'Checkout' : 'Kasse' ?></span>
            <h1><?= $en ? 'Checkout' : 'Zur Kasse' ?></h1>
        </div>

<?php if ($success): ?>
        <div class="container--narrow" style="margin-inline:0">
            <div class="form-alert form-alert--ok"><?= $en ? 'Thank you! Your order has been placed.' : 'Vielen Dank! Deine Bestellung ist eingegangen.' ?></div>
            <h2><?= $en ? 'Order' : 'Bestellung' ?> <span class="mono"><?= esc($orderNumber) ?></span></h2>
            <p class="section__lead"><?= $en ? 'You will receive a confirmation by e-mail shortly.' : 'Du erhältst in Kürze eine Bestätigung per E-Mail.' ?></p>
            <div class="callout" style="margin-top:var(--sp-4)">
                <?= $en
                    ? 'Next step: the secure online payment (Stripe: card, PayPal, Klarna, Apple/Google Pay, SEPA) is activated once the payment connection is set up. For bank transfer you will receive the bank details by e-mail. We will be in touch.'
                    : 'Nächster Schritt: Die sichere Online-Zahlung (Stripe: Karte, PayPal, Klarna, Apple/Google Pay, SEPA) wird aktiviert, sobald die Zahlungsanbindung eingerichtet ist. Bei Vorkasse erhältst du die Bankverbindung per E-Mail. Wir melden uns.' ?>
            </div>
            <a class="btn btn--cta btn--lg" style="margin-top:var(--sp-5)" href="<?= $site->url($code) ?>"><?= $en ? 'Back to the shop' : 'Zurück zum Shop' ?></a>
        </div>
<?php elseif (empty($cartNow)): ?>
        <p class="section__lead"><?= t('cart.empty') ?></p>
        <a class="btn btn--cta btn--lg" style="margin-top:var(--sp-4)" href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>"><?= $en ? 'Browse models' : 'Modelle ansehen' ?></a>
<?php else: ?>
<?php if ($alert === 'invalid'): ?><div class="form-alert form-alert--err"><?= $en ? 'Please check the highlighted fields and accept the terms.' : 'Bitte prüfe die markierten Felder und akzeptiere die Bedingungen.' ?></div>
<?php elseif ($alert === 'order-failed'): ?><div class="form-alert form-alert--err"><?= $en ? 'Sorry, the order could not be saved. Please try again or contact us.' : 'Die Bestellung konnte nicht gespeichert werden. Bitte erneut versuchen oder uns kontaktieren.' ?></div>
<?php elseif ($alert === 'csrf'): ?><div class="form-alert form-alert--err"><?= $en ? 'Session expired. Please submit again.' : 'Sitzung abgelaufen. Bitte erneut absenden.' ?></div>
<?php endif ?>

        <form method="post" class="checkout">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <div class="checkout-grid">
                <div class="checkout-form">
<?php if ($user): ?>
                    <div class="checkout-account checkout-account--in"><?= $en ? 'Signed in as' : 'Angemeldet als' ?> <strong><?= esc($user->email()) ?></strong> · <a href="<?= url($en ? 'en/account' : 'konto') ?>"><?= t('account.title') ?></a></div>
<?php else: ?>
                    <div class="checkout-account"><?= $en ? 'Already a customer?' : 'Bereits Kunde?' ?> <a href="<?= url($en ? 'en/login' : 'anmelden') ?>"><?= $en ? 'Sign in for a faster checkout' : 'Anmelden für schnelleren Checkout' ?></a> · <a href="<?= url($en ? 'en/register' : 'registrieren') ?>"><?= t('auth.register.title') ?></a></div>
<?php endif ?>
                    <h2><?= $en ? 'Delivery address' : 'Lieferadresse' ?></h2>
                    <div class="field<?= isset($invalid['name']) ? ' has-error' : '' ?>"><label for="k-name"><?= $en ? 'Full name' : 'Name' ?> *</label><input id="k-name" name="name" value="<?= esc($data['name']) ?>" required></div>
                    <div class="field<?= isset($invalid['email']) ? ' has-error' : '' ?>"><label for="k-email">E-Mail *</label><input id="k-email" type="email" name="email" value="<?= esc($data['email']) ?>" required></div>
                    <div class="field"><label for="k-phone"><?= $en ? 'Phone' : 'Telefon' ?></label><input id="k-phone" name="phone" value="<?= esc($data['phone']) ?>"></div>
                    <div class="field<?= isset($invalid['street']) ? ' has-error' : '' ?>"><label for="k-street"><?= $en ? 'Street & number' : 'Straße & Nr.' ?> *</label><input id="k-street" name="street" value="<?= esc($data['street']) ?>" required></div>
                    <div class="field-row">
                        <div class="field<?= isset($invalid['zip']) ? ' has-error' : '' ?>"><label for="k-zip"><?= $en ? 'ZIP' : 'PLZ' ?> *</label><input id="k-zip" name="zip" value="<?= esc($data['zip']) ?>" inputmode="numeric" data-zip data-zip-country="de" required></div>
                        <div class="field<?= isset($invalid['city']) ? ' has-error' : '' ?>"><label for="k-city"><?= $en ? 'City' : 'Ort' ?> *</label><input id="k-city" name="city" value="<?= esc($data['city']) ?>" data-city required></div>
                    </div>
                    <div class="field"><label for="k-country"><?= $en ? 'Country' : 'Land' ?></label><input id="k-country" name="country" value="<?= esc($data['country']) ?>"></div>

                    <h2 style="margin-top:var(--sp-5)"><?= $en ? 'Payment method' : 'Zahlart' ?></h2>
                    <div class="pay-radios">
<?php foreach ($pays as $val => $label): ?>
                        <label class="pay-radio"><input type="radio" name="payment" value="<?= $val ?>"<?= $data['payment'] === $val ? ' checked' : '' ?>> <span><?= $label ?></span></label>
<?php endforeach ?>
                    </div>
                    <p class="field-note"><?= $en ? 'Invoice purchase is available for unlocked returning customers.' : 'Kauf auf Rechnung ist für freigeschaltete Bestandskunden verfügbar.' ?></p>

                    <label class="checkout-terms<?= isset($invalid['terms']) ? ' has-error' : '' ?>">
                        <input type="checkbox" name="terms" value="1">
                        <span><?= $en ? 'I accept the' : 'Ich akzeptiere die' ?> <a href="<?= url($en ? 'en/terms' : 'agb') ?>"><?= t('footer.terms') ?></a> <?= $en ? 'and have read the' : 'und habe die' ?> <a href="<?= url($en ? 'en/withdrawal' : 'widerruf') ?>"><?= t('footer.withdrawal') ?></a> <?= $en ? '.' : 'zur Kenntnis genommen.' ?></span>
                    </label>

                    <button type="submit" name="submit" value="1" class="btn btn--cta btn--lg btn--block" style="margin-top:var(--sp-4)"><?= $en ? 'Buy now (payment obligatory)' : 'Zahlungspflichtig bestellen' ?></button>
                </div>

                <aside class="checkout-summary">
                    <h2><?= $en ? 'Your order' : 'Deine Bestellung' ?></h2>
<?php foreach ($cartNow as $sku => $it): ?>
                    <div class="checkout-item">
                        <span><?= (int) $it['qty'] ?>× <?= esc($it['title']) ?><?= $it['variant'] ? ' <small>(' . esc($it['variant']) . ')</small>' : '' ?></span>
                        <span class="price"><?= mv_eur($it['price'] * $it['qty'], $code) ?></span>
                    </div>
<?php endforeach ?>
                    <div class="cart-row"><span><?= $en ? 'Subtotal' : 'Zwischensumme' ?></span><span class="price"><?= mv_eur($sub, $code) ?></span></div>
                    <div class="cart-row"><span><?= $en ? 'Freight' : 'Fracht' ?></span><span class="price"><?= $ship > 0 ? mv_eur($ship, $code) : ($en ? 'free' : 'frachtfrei') ?></span></div>
                    <div class="cart-row cart-row--total"><span><?= $en ? 'Total' : 'Gesamt' ?></span><span class="price"><?= mv_eur($total, $code) ?></span></div>
                    <p class="cart-vat"><?= t('product.incl_vat') ?></p>
                    <div style="margin-top:var(--sp-3)"><?php snippet('payment-logos') ?></div>
                </aside>
            </div>
        </form>
<?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
