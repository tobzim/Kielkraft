<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$y    = date('Y');
?>
</main>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div>
            <a class="brand" href="<?= $site->url($code) ?>" style="margin-bottom:var(--sp-4)">
                <img class="brand__mark" src="<?= url('assets/img/logo.svg') ?>" alt="" width="34" height="34">
                <span class="brand__name" style="color:#fff"><?= $site->title()->or('Marvento') ?></span>
            </a>
            <p style="max-width:34ch"><?= t('marvento.tagline') ?></p>
            <div class="payment-icons" style="margin-top:var(--sp-5)">
                <span>VISA</span><span>Mastercard</span><span>PayPal</span>
                <span>Klarna</span><span>Apple Pay</span><span>SEPA</span>
            </div>
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
        <span>&copy; <?= $y ?> Marvento &middot; <?= $en ? 'All prices incl. VAT, plus shipping.' : 'Alle Preise inkl. MwSt., zzgl. Versand.' ?></span>
        <span><?= t('trust.new_goods') ?> &middot; <?= t('trust.secure') ?></span>
    </div>
</footer>

<?php snippet('consent') ?>
<?= js('assets/js/app.js', ['defer' => true]) ?>
</body>
</html>
