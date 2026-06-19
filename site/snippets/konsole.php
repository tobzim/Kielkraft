<?php
/**
 * "Die Konsole" - the PDP buy unit (signature element).
 * @var \Kirby\Cms\Page $product
 */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$isElectric = $product->antrieb()->value() === 'elektro';
$axis = $isElectric ? 'axis-cool' : 'axis-warm';

$variants = $product->variants()->toStructure();
$first    = $variants->first();
$basePrice = $first && $first->price()->isNotEmpty() ? (float) $first->price()->value() : (float) $product->priceFrom()->value();
$uvp   = $product->uvp()->isNotEmpty() ? (float) $product->uvp()->value() : null;
$ship  = (float) $product->shippingCost()->or(0)->value();
$warr  = $product->warrantyYears()->or(2)->value();

$vjson = [];
foreach ($variants as $i => $v) {
    $vjson[] = [
        'label' => $v->label()->value(),
        'sku'   => $v->sku()->value(),
        'stock' => (int) $v->stock()->value(),
        'priceFormatted' => mv_eur($v->price()->value(), $code),
    ];
}

$availLabels = [
    'instock'  => $en ? 'Lieferbar'           : 'Lieferbar',
    'short'    => $en ? 'Only a few in stock' : 'Nur wenige verfügbar',
    'preorder' => $en ? 'Pre-order'           : 'Vorbestellung',
];
if ($en) { $availLabels['instock'] = 'In stock'; }
$avail = $product->availability()->or('instock')->value();

$wa = 'https://wa.me/49000000000'; // TODO: echte WhatsApp-Nummer
?>
<aside class="konsole <?= $axis ?>" data-konsole data-variants='<?= htmlspecialchars(json_encode($vjson, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS), ENT_QUOTES) ?>'>
    <div class="konsole__head">
        <span class="konsole__model"><?= $product->title() ?></span>
        <span class="konsole__axis"><?= $isElectric ? ($en ? 'Electric' : 'Elektro') : ($en ? 'Petrol' : 'Benzin') ?></span>
    </div>

    <div class="konsole__price" data-price><?= mv_eur($basePrice, $code) ?></div>
    <div class="konsole__ship">
        <span><?= t('product.incl_vat') ?></span> ·
<?php if ($ship > 0): ?>
        <?= $en ? 'plus freight' : 'zzgl. Fracht' ?> <b><?= mv_eur($ship, $code) ?></b>
<?php else: ?>
        <b><?= $en ? 'free freight' : 'fracht­frei' ?></b>
<?php endif ?>
<?php if ($uvp && $uvp > $basePrice): ?>
        · <s><?= $en ? 'RRP' : 'UVP' ?> <?= mv_eur($uvp, $code) ?></s>
<?php endif ?>
    </div>

    <!-- compact gauge readout -->
    <div class="kgauges">
        <div class="kgauge"><span><?= $product->powerPs() ?></span><em>PS</em></div>
        <div class="kgauge"><span><?= $product->powerKw() ?></span><em>kW</em></div>
        <div class="kgauge"><span><?= $product->weightKg() ?></span><em>kg</em></div>
    </div>

<?php if ($variants->count() > 0): ?>
    <div class="opt-group">
        <div class="opt-label"><span><?= t('product.shaft') ?> / <?= $en ? 'variant' : 'Variante' ?></span> <span class="opt-current mono" data-sku><?= $first->sku() ?></span></div>
        <div class="seg" role="group" aria-label="<?= t('product.choose_variant') ?>">
<?php foreach ($variants as $i => $v): ?>
            <button type="button" class="seg__btn" data-variant="<?= $i ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><?= $v->label() ?></button>
<?php endforeach ?>
        </div>
    </div>
<?php endif ?>

    <div class="konsole__avail">
        <span class="dot" aria-hidden="true"></span>
        <span><b><?= $availLabels[$avail] ?? $availLabels['instock'] ?></b> · <?= $product->deliveryTime() ?></span>
    </div>

    <div class="konsole__cta">
        <button type="button" class="btn btn--primary btn--lg btn--block" data-add-to-cart><?= t('product.add_to_cart') ?></button>
        <a class="btn btn--ghost btn--block" href="<?= $wa ?>" rel="nofollow"><?= $en ? 'Get advice on WhatsApp' : 'Per WhatsApp beraten lassen' ?></a>
    </div>

    <div class="wallets" aria-label="<?= t('footer.payment') ?>">
        <span>PayPal</span><span>Klarna</span><span>Apple&nbsp;Pay</span><span>Google&nbsp;Pay</span><span><?= $en ? 'Card' : 'Karte' ?></span>
    </div>

    <ul class="microtrust">
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><?= $en ? 'Buyer protection' : 'Käuferschutz' ?></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg><?= $en ? '14-day returns' : '14 Tage Widerruf' ?></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.5 13.5 7 22l5-3 5 3-1.5-8.5"/></svg><?= $warr ?> <?= $en ? 'yrs warranty' : 'Jahre Garantie' ?></li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><?= $en ? 'Secure payment' : 'Sichere Zahlung' ?></li>
    </ul>

    <div class="invoice-note">
        <b><?= $en ? 'Invoice purchase' : 'Kauf auf Rechnung' ?></b> – <?= $en ? 'unlocked for returning customers.' : 'für Bestandskunden freigeschaltet.' ?>
    </div>
</aside>
