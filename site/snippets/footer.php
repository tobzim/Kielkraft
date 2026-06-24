<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$y    = date('Y');
?>
</main>

<footer class="site-footer">
    <div class="container footer-brands">
        <span><?= $en ? 'Official new goods:' : 'Offizielle Neuware:' ?></span>
        <span class="brand-chip"><?php snippet('brand-logo', ['brand' => 'Tohatsu']) ?></span>
        <span class="brand-chip"><?php snippet('brand-logo', ['brand' => 'ePropulsion']) ?></span>
    </div>
    <div class="container site-footer__grid">
        <div>
            <a class="brand brand--invert" href="<?= $site->url($code) ?>" style="margin-bottom:var(--sp-4)">
                <span class="brand__lockup"><?= $site->title()->or('Kielkraft') ?><span class="brand__tm">&trade;</span></span>
            </a>
            <p style="max-width:34ch"><?= t('kielkraft.tagline') ?></p>
            <p style="margin-top:var(--sp-3);font-size:var(--fs-200);line-height:1.6">
                Kielkraft &middot; <?= $en ? 'a brand of' : 'eine Marke der' ?> Boostboards GmbH &amp; Co. KG<br>
                Groten Hoff 21 &middot; 22359 Hamburg<br>
                <a href="tel:+4940609019969">+49 40 60 90 199 69</a> &middot; <a href="mailto:info@boostboards.de">info@boostboards.de</a>
            </p>
            <div style="margin-top:var(--sp-5)"><?php snippet('payment-logos') ?></div>
        </div>

        <div>
            <h4>Shop</h4>
            <ul>
                <li><a href="<?= url($en ? 'en/electric-outboards' : 'elektro-aussenborder') ?>"><?= t('nav.electric') ?></a></li>
                <li><a href="<?= url($en ? 'en/petrol-outboards' : 'benzin-aussenborder') ?>"><?= t('nav.petrol') ?></a></li>
                <li><a href="<?= url($en ? 'en/buying-advisor' : 'kaufberater') ?>"><?= t('nav.advisor') ?></a></li>
                <li><a href="<?= url($en ? 'en/guide' : 'ratgeber') ?>"><?= t('nav.guide') ?></a></li>
            </ul>
        </div>

        <div>
            <h4><?= t('nav.service') ?></h4>
            <ul>
                <li><a href="<?= url($en ? 'en/shipping' : 'versand') ?>"><?= t('footer.shipping') ?></a></li>
                <li><a href="<?= url($en ? 'en/payment' : 'zahlungsarten') ?>"><?= t('footer.payment') ?></a></li>
                <li><a href="<?= url($en ? 'en/warranty' : 'garantie') ?>"><?= t('product.warranty') ?></a></li>
                <li><a href="<?= url($en ? 'en/about' : 'ueber-uns') ?>"><?= $en ? 'About us' : 'Über uns' ?></a></li>
                <li><a href="<?= url($en ? 'en/contact' : 'kontakt') ?>"><?= t('nav.contact') ?></a></li>
                <li><a href="<?= $kirby->user() ? url($en ? 'en/account' : 'konto') : url($en ? 'en/login' : 'anmelden') ?>"><?= $kirby->user() ? t('account.title') : t('nav.login') ?></a></li>
            </ul>
        </div>

        <div>
            <h4><?= t('footer.legal') ?></h4>
            <ul>
                <li><a href="<?= url($en ? 'en/imprint' : 'impressum') ?>"><?= t('footer.imprint') ?></a></li>
                <li><a href="<?= url($en ? 'en/privacy' : 'datenschutz') ?>"><?= t('footer.privacy') ?></a></li>
                <li><a href="<?= url($en ? 'en/terms' : 'agb') ?>"><?= t('footer.terms') ?></a></li>
                <li><a href="<?= url($en ? 'en/withdrawal' : 'widerruf') ?>"><?= t('footer.withdrawal') ?></a></li>
            </ul>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <span>&copy; <?= $y ?> Kielkraft &middot; <?= $en ? 'All prices incl. VAT, plus shipping.' : 'Alle Preise inkl. MwSt., zzgl. Versand.' ?></span>
        <span><?= t('trust.new_goods') ?> &middot; <?= t('trust.secure') ?></span>
    </div>
</footer>

<div class="minicart" data-minicart data-empty="<?= $en ? 'Your cart is empty.' : 'Dein Warenkorb ist leer.' ?>" data-remove="<?= $en ? 'Remove' : 'Entfernen' ?>" hidden>
    <div class="minicart__overlay" data-minicart-close></div>
    <aside class="minicart__panel" role="dialog" aria-modal="true" aria-label="<?= t('cart.title') ?>">
        <header class="minicart__head">
            <strong><?= t('cart.title') ?></strong>
            <button class="minicart__close" type="button" data-minicart-close aria-label="<?= t('kielkraft.close') ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg></button>
        </header>
        <div class="minicart__body" data-minicart-body></div>
        <footer class="minicart__foot" data-minicart-foot hidden>
            <div class="minicart__row"><span><?= t('cart.subtotal') ?></span><span data-minicart-subtotal></span></div>
            <div class="minicart__row minicart__row--ship" data-minicart-shiprow hidden><span><?= $en ? 'Freight' : 'Fracht' ?></span><span data-minicart-shipping></span></div>
            <div class="minicart__row minicart__row--total"><span><?= $en ? 'Total' : 'Gesamt' ?></span><span data-minicart-total></span></div>
            <a class="btn btn--cta btn--lg btn--block" href="<?= url($en ? 'en/checkout' : 'kasse') ?>"><?= t('cart.checkout') ?></a>
            <a class="minicart__viewcart" href="<?= url($en ? 'en/cart' : 'warenkorb') ?>"><?= $en ? 'View full cart' : 'Warenkorb ansehen' ?></a>
        </footer>
        <input type="hidden" data-minicart-csrf value="<?= csrf() ?>">
    </aside>
</div>

<a class="wa-fab" href="https://wa.me/4940609019969?text=<?= rawurlencode($en ? 'Hi Kielkraft, I have a question.' : 'Hallo Kielkraft, ich habe eine Frage.') ?>" target="_blank" rel="noopener nofollow" aria-label="WhatsApp">
    <svg viewBox="0 0 32 32" aria-hidden="true" fill="currentColor"><path d="M16.04 4C9.94 4 5 8.94 5 15.04c0 1.94.51 3.84 1.48 5.52L5 28l7.62-1.45a11 11 0 0 0 3.42.55h.01c6.1 0 11.04-4.94 11.04-11.04C27.09 8.94 22.14 4 16.04 4zm0 20.2h-.01c-1.06 0-2.1-.28-3.01-.82l-.22-.13-4.52.86.86-4.41-.14-.23a9.13 9.13 0 0 1-1.4-4.86c0-5.06 4.12-9.18 9.19-9.18 2.45 0 4.76.96 6.49 2.69a9.12 9.12 0 0 1 2.69 6.5c0 5.06-4.12 9.18-9.18 9.18zm5.04-6.87c-.28-.14-1.63-.8-1.88-.9-.25-.09-.43-.14-.62.14-.18.28-.71.9-.87 1.08-.16.18-.32.21-.6.07-.28-.14-1.16-.43-2.21-1.36-.82-.73-1.37-1.63-1.53-1.91-.16-.28-.02-.43.12-.57.13-.13.28-.32.42-.49.14-.16.18-.28.28-.46.09-.18.05-.35-.02-.49-.07-.14-.62-1.5-.85-2.05-.22-.54-.45-.46-.62-.47l-.53-.01c-.18 0-.48.07-.73.35-.25.28-.96.94-.96 2.29 0 1.35.98 2.66 1.12 2.84.14.18 1.93 2.95 4.68 4.13.65.28 1.16.45 1.56.58.66.21 1.25.18 1.72.11.53-.08 1.63-.67 1.86-1.31.23-.64.23-1.19.16-1.31-.07-.12-.25-.19-.53-.33z"/></svg>
</a>

<?php snippet('consent') ?>
<script defer src="<?= mv_asset('assets/js/app.js') ?>"></script>
</body>
</html>
