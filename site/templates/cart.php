<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$cart = kk_cart_get();
$sub  = kk_cart_subtotal();
$ship = kk_cart_shipping();
$total = kk_cart_total();
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <div style="margin-bottom:var(--sp-5)">
            <span class="eyebrow"><?= t('cart.title') ?></span>
            <h1><?= t('cart.title') ?></h1>
        </div>

<?php if (empty($cart)): ?>
        <div class="container--narrow" style="text-align:center;margin-inline:0">
            <p class="section__lead"><?= t('cart.empty') ?></p>
            <div style="display:flex;gap:var(--sp-3);flex-wrap:wrap;margin-top:var(--sp-5)">
                <a class="btn btn--cta btn--lg" href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>">Tohatsu</a>
                <a class="btn btn--ghost btn--lg" href="<?= url($en ? 'en/electric-outboards' : 'elektro-aussenborder') ?>">ePropulsion</a>
            </div>
        </div>
<?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
<?php foreach ($cart as $sku => $it): ?>
                <div class="cart-item">
                    <a class="cart-item__img" href="<?= $it['url'] ?>">
<?php if (!empty($it['img'])): ?><img src="<?= $it['img'] ?>" alt=""><?php endif ?>
                    </a>
                    <div class="cart-item__info">
                        <a href="<?= $it['url'] ?>"><strong><?= esc($it['title']) ?></strong></a>
<?php if (!empty($it['variant'])): ?><span class="cart-item__variant"><?= esc($it['variant']) ?></span><?php endif ?>
                        <span class="cart-item__unit price"><?= mv_eur($it['price'], $code) ?> <?= $en ? 'each' : 'pro Stück' ?></span>
                    </div>
                    <form method="post" action="<?= url('cart/update') ?>" class="cart-item__qty">
                        <input type="hidden" name="csrf" value="<?= csrf() ?>">
                        <input type="hidden" name="sku" value="<?= esc($sku) ?>">
                        <input type="hidden" name="lang" value="<?= $code ?>">
                        <input type="number" name="qty" value="<?= (int) $it['qty'] ?>" min="0" max="99" aria-label="<?= $en ? 'Quantity' : 'Menge' ?>">
                        <button type="submit" class="btn btn--ghost btn--sm"><?= $en ? 'Update' : 'Ändern' ?></button>
                    </form>
                    <div class="cart-item__line price"><?= mv_eur($it['price'] * $it['qty'], $code) ?></div>
                    <form method="post" action="<?= url('cart/remove') ?>" class="cart-item__rm">
                        <input type="hidden" name="csrf" value="<?= csrf() ?>">
                        <input type="hidden" name="sku" value="<?= esc($sku) ?>">
                        <input type="hidden" name="lang" value="<?= $code ?>">
                        <button type="submit" aria-label="<?= $en ? 'Remove' : 'Entfernen' ?>">&times;</button>
                    </form>
                </div>
<?php endforeach ?>
            </div>

            <aside class="cart-summary">
                <h2><?= $en ? 'Summary' : 'Zusammenfassung' ?></h2>
                <div class="cart-row"><span><?= $en ? 'Subtotal' : 'Zwischensumme' ?></span><span class="price"><?= mv_eur($sub, $code) ?></span></div>
                <div class="cart-row"><span><?= $en ? 'Freight (forwarder)' : 'Fracht (Spedition)' ?></span><span class="price"><?= $ship > 0 ? mv_eur($ship, $code) : ($en ? 'free' : 'frachtfrei') ?></span></div>
                <div class="cart-row cart-row--total"><span><?= $en ? 'Total' : 'Gesamt' ?></span><span class="price"><?= mv_eur($total, $code) ?></span></div>
                <p class="cart-vat"><?= t('product.incl_vat') ?> · <?= $en ? 'one consolidated shipment' : 'eine Sammellieferung' ?></p>
                <a class="btn btn--cta btn--lg btn--block" href="<?= url($en ? 'en/checkout' : 'kasse') ?>"><?= $en ? 'Proceed to checkout' : 'Zur Kasse' ?></a>
                <div style="margin-top:var(--sp-4)"><?php snippet('payment-logos') ?></div>
                <ul class="microtrust" style="margin-top:var(--sp-4)">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><?= $en ? 'Buyer protection' : 'Käuferschutz' ?></li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/></svg><?= $en ? '14-day returns' : '14 Tage Widerruf' ?></li>
                </ul>
            </aside>
        </div>
<?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
