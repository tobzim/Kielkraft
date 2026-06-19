<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container container--narrow" style="text-align:center">
        <div class="section__label" style="justify-content:center"><?= t('cart.title') ?></div>
        <h1><?= t('cart.empty') ?></h1>
        <p class="section__lead"><?= $en ? 'Browse the models and add your outboard. Secure checkout with all common payment methods.' : 'Stöbere durch die Modelle und lege deinen Außenborder in den Warenkorb. Sicherer Checkout mit allen gängigen Zahlarten.' ?></p>
        <div style="display:flex;gap:var(--sp-3);justify-content:center;flex-wrap:wrap;margin-top:var(--sp-5)">
            <a class="btn btn--primary btn--lg" href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>">Tohatsu</a>
            <a class="btn btn--warm btn--lg" href="<?= url($en ? 'en/electric-outboards' : 'elektro-aussenborder') ?>" style="--bg:var(--strom)">ePropulsion</a>
        </div>
        <div class="callout" style="margin-top:var(--sp-7);text-align:left">
            <?= $en ? 'Note: the secure Stripe checkout (card, PayPal, Klarna, Apple/Google Pay, SEPA) and the cart are being finalised in the next build phase.' : 'Hinweis: Der sichere Stripe-Checkout (Karte, PayPal, Klarna, Apple/Google Pay, SEPA) und der Warenkorb werden in der nächsten Ausbaustufe fertiggestellt.' ?>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
