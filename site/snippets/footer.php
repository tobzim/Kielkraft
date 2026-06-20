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
            <a class="brand" href="<?= $site->url($code) ?>" style="margin-bottom:var(--sp-4)">
                <img class="brand__mark" src="<?= url('assets/img/logo.svg') ?>" alt="" width="34" height="34">
                <span class="brand__name" style="color:#fff"><?= $site->title()->or('Kielkraft') ?></span>
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

<?php snippet('consent') ?>
<script defer src="<?= mv_asset('assets/js/app.js') ?>"></script>
</body>
</html>
