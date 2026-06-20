<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
?>
<section class="section--tight">
    <div class="container">
        <a class="bb-banner" href="https://boostboards.de" target="_blank" rel="noopener">
            <div class="bb-banner__content">
                <span class="brand-chip bb-banner__logo"><img src="<?= url('assets/img/brands/boostboards.png') ?>" alt="Boostboards" loading="lazy"></span>
                <h2><?= $en ? 'More water sports? Discover Boostboards.' : 'Noch mehr Wassersport? Entdecke Boostboards.' ?></h2>
                <p><?= $en
                    ? 'E-surfboards, jetboards and e-foils by Boostboards – Kielkraft is a brand of Boostboards.'
                    : 'E-Surfboards, Jetboards und E-Foils von Boostboards – Kielkraft ist eine Marke von Boostboards.' ?></p>
                <span class="btn btn--on-navy"><?= $en ? 'Go to Boostboards' : 'Zu Boostboards' ?></span>
            </div>
            <div class="bb-banner__media">
                <img src="<?= url('assets/img/boostboards-board.webp') ?>" alt="Boostboards Board" loading="lazy">
            </div>
        </a>
    </div>
</section>
