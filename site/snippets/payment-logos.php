<?php
/** Payment-method strip with official provider logos (white chips, work on any bg). */
$pays = [
    ['visa', 'Visa'],
    ['mastercard', 'Mastercard'],
    ['paypal', 'PayPal'],
    ['klarna', 'Klarna'],
    ['applepay', 'Apple Pay'],
    ['googlepay', 'Google Pay'],
    ['amex', 'American Express'],
    ['maestro', 'Maestro'],
];
?>
<div class="pay-strip">
<?php foreach ($pays as [$slug, $label]): ?>
    <span class="pay-chip"><img src="<?= url('assets/img/pay/' . $slug . '.svg') ?>" alt="<?= $label ?>" loading="lazy"></span>
<?php endforeach ?>
    <span class="pay-chip pay-chip--text">SEPA</span>
</div>
