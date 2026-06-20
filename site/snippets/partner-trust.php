<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
?>
<section class="section partner bg-soft">
    <div class="container">
        <div class="section__head"><h2 class="section__title"><?= $en ? 'Brands & partnership' : 'Marken & Partnerschaft' ?></h2></div>
        <div class="partner__grid">
            <div>
                <p class="partner-claim">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <?= $en
                        ? 'Official new goods of the Tohatsu and ePropulsion brands – with full manufacturer warranty.'
                        : 'Offizielle Neuware der Marken Tohatsu und ePropulsion – mit voller Herstellergarantie.' ?>
                </p>

                <div class="brandstrip">
                    <div class="brand-tile">
                        <?php snippet('brand-logo', ['brand' => 'Tohatsu', 'class' => 'brand-logo--tile']) ?>
                        <small><?= $en ? 'Petrol · 4-stroke' : 'Benzin · 4-Takt' ?></small>
                    </div>
                    <div class="brand-tile">
                        <?php snippet('brand-logo', ['brand' => 'ePropulsion', 'class' => 'brand-logo--tile']) ?>
                        <small><?= $en ? 'Electric' : 'Elektro' ?></small>
                    </div>
                </div>

                <p class="partner-note">
                    <?= $en
                        ? 'We state an authorised-dealer / partner status only once it is formally confirmed.'
                        : 'Einen Vertrags- oder Partner-Status nennen wir erst, wenn er offiziell bestätigt ist.' ?>
                </p>
            </div>

            <figure class="testimonial">
                <div class="testimonial__avatar" aria-hidden="true">
                    <svg viewBox="0 0 64 64"><circle cx="32" cy="32" r="32" fill="#e1e8ee"/><circle cx="32" cy="26" r="11" fill="#b7c4d2"/><path d="M13 55a19 19 0 0 1 38 0z" fill="#b7c4d2"/></svg>
                </div>
                <blockquote class="testimonial__quote">
                    <?= $en
                        ? '“[Insert a real, approved quote here – e.g. from a manufacturer representative or a satisfied customer, with their consent.]”'
                        : '„[Hier ein echtes, freigegebenes Zitat einsetzen – z. B. eines Herstellervertreters oder zufriedenen Kunden, mit dessen Einverständnis.]"' ?>
                </blockquote>
                <figcaption class="testimonial__by">
                    <strong>[Name], [<?= $en ? 'Position' : 'Position' ?>]</strong>
                    <span class="testimonial__placeholder"><?= $en ? 'Placeholder – insert a real, approved photo and quote. No fabricated endorsements.' : 'Platzhalter – echtes, freigegebenes Foto und Zitat einsetzen. Keine erfundenen Empfehlungen.' ?></span>
                </figcaption>
            </figure>
        </div>
    </div>
</section>
