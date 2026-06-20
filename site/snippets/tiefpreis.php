<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
$urlContact = url($en ? 'en/contact' : 'kontakt');
?>
<section class="section--tight">
    <div class="container">
        <div class="tiefpreis">
            <div class="tiefpreis__seal" aria-hidden="true">
                <span class="tiefpreis__pct">%</span>
                <span><?= $en ? 'Best price' : 'Tiefpreis' ?></span>
                <strong><?= $en ? 'Guarantee' : 'Garantie' ?></strong>
            </div>
            <div class="tiefpreis__body">
                <span class="eyebrow" style="color:#ffd0cf"><?= $en ? 'Best-price guarantee' : 'Tiefpreis-Garantie' ?></span>
                <h2><?= $en ? 'Found it cheaper? We beat the price.' : 'Woanders günstiger? Wir unterbieten den Preis.' ?></h2>
                <p><?= $en
                    ? 'Find the same new, in-stock outboard cheaper at another German retailer? Send us the offer – we beat the price.'
                    : 'Findest du denselben Außenborder (Neuware, lieferbar) bei einem anderen deutschen Händler günstiger? Schick uns das Angebot – wir unterbieten den Preis.' ?></p>
                <div class="tiefpreis__steps">
                    <div class="tpstep"><b>1</b><span><?= $en ? 'Find a cheaper offer' : 'Günstigeres Angebot finden' ?></span></div>
                    <div class="tpstep"><b>2</b><span><?= $en ? 'Send us the link' : 'Link/Angebot senden' ?></span></div>
                    <div class="tpstep"><b>3</b><span><?= $en ? 'We beat the price' : 'Wir unterbieten den Preis' ?></span></div>
                </div>
                <a class="btn btn--sale btn--lg" href="<?= $urlContact ?>"><?= $en ? 'Get a price check' : 'Preis prüfen lassen' ?></a>
            </div>
        </div>
    </div>
</section>
