<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$urlElectric = url($en ? 'en/electric-outboards' : 'elektro-aussenborder');
$urlPetrol   = url($en ? 'en/petrol-outboards' : 'benzin-aussenborder');
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container container--narrow">
        <div style="margin-bottom:var(--sp-6)">
            <span class="eyebrow"><?= t('nav.advisor') ?></span>
            <h1><?= $page->title() ?></h1>
            <p class="section__lead"><?= $page->intro() ?></p>
        </div>

        <div class="wizard" data-wizard>
            <div class="wizard__steps">
                <span><b>1</b> <?= $en ? 'Boat' : 'Boot' ?></span>
                <span><b>2</b> <?= $en ? 'Use' : 'Einsatz' ?></span>
                <span><b>3</b> <?= $en ? 'Drive' : 'Antrieb' ?></span>
            </div>

            <div data-step="0" data-next="1">
                <p class="wizard__q"><?= $en ? 'What kind of boat do you have?' : 'Was für ein Boot fährst du?' ?></p>
                <div class="choices">
                    <button class="choice" data-choice="schlauchboot"><b><?= $en ? 'Inflatable' : 'Schlauchboot' ?></b><span><?= $en ? 'Dinghy / RIB' : 'Schlauch- / RIB-Boot' ?></span></button>
                    <button class="choice" data-choice="tender"><b>Tender</b><span><?= $en ? 'Dinghy / yacht tender' : 'Beiboot / Yacht-Tender' ?></span></button>
                    <button class="choice" data-choice="segel"><b><?= $en ? 'Sailing yacht' : 'Segelyacht' ?></b><span><?= $en ? 'Auxiliary drive' : 'Hilfsantrieb' ?></span></button>
                    <button class="choice" data-choice="angeln"><b><?= $en ? 'Fishing boat' : 'Angelboot' ?></b><span><?= $en ? 'Quiet & manoeuvrable' : 'Leise & wendig' ?></span></button>
                </div>
            </div>

            <div data-step="1" data-next="2" hidden>
                <p class="wizard__q"><?= $en ? 'What matters most?' : 'Was ist dir am wichtigsten?' ?></p>
                <div class="choices">
                    <button class="choice" data-choice="leise"><b><?= $en ? 'Quiet & clean' : 'Leise & sauber' ?></b><span><?= $en ? 'No fumes, low maintenance' : 'Keine Abgase, wartungsarm' ?></span></button>
                    <button class="choice" data-choice="reichweite"><b><?= $en ? 'Range & power' : 'Reichweite & Leistung' ?></b><span><?= $en ? 'Long tours, planing' : 'Lange Touren, Gleitfahrt' ?></span></button>
                    <button class="choice" data-choice="leicht"><b><?= $en ? 'Light & portable' : 'Leicht & tragbar' ?></b><span><?= $en ? 'Easy to carry & stow' : 'Einfach tragen & verstauen' ?></span></button>
                    <button class="choice" data-choice="backup"><b><?= $en ? 'Reliable back-up' : 'Zuverlässiges Backup' ?></b><span><?= $en ? 'Occasional use' : 'Gelegentliche Nutzung' ?></span></button>
                </div>
            </div>

            <div data-step="2" hidden>
                <p class="wizard__q"><?= $en ? 'Drive preference?' : 'Antriebspräferenz?' ?></p>
                <div class="choices">
                    <button class="choice" data-choice="elektro"><b><?= $en ? 'Electric' : 'Elektro' ?></b><span>ePropulsion</span></button>
                    <button class="choice" data-choice="benzin"><b><?= $en ? 'Petrol' : 'Benzin' ?></b><span>Tohatsu 4-Takt</span></button>
                    <button class="choice" data-choice="egal"><b><?= $en ? 'Not sure' : 'Noch unklar' ?></b><span><?= $en ? 'Advise me' : 'Berate mich' ?></span></button>
                </div>
            </div>

            <div data-result hidden>
                <p class="wizard__q"><?= $en ? 'Our recommendation' : 'Unsere Empfehlung' ?></p>
                <div class="choices">
                    <a class="choice axis-cool" href="<?= $urlElectric ?>"><b><?= $en ? 'Electric: ePropulsion' : 'Elektro: ePropulsion' ?></b><span><?= $en ? 'Quiet, clean, low-maintenance – great for tenders, sailing and quiet lakes.' : 'Leise, sauber, wartungsarm – top für Tender, Segeln und ruhige Seen.' ?></span></a>
                    <a class="choice axis-warm" href="<?= $urlPetrol ?>"><b><?= $en ? 'Petrol: Tohatsu' : 'Benzin: Tohatsu' ?></b><span><?= $en ? 'Range and power for tours and planing – proven 4-stroke reliability.' : 'Reichweite und Leistung für Touren und Gleitfahrt – bewährte 4-Takt-Technik.' ?></span></a>
                </div>
                <p style="margin-top:var(--sp-5)"><?= $en ? 'Still unsure about shaft length or power?' : 'Noch unsicher bei Schaftlänge oder Leistung?' ?> <a class="link-quiet" href="https://wa.me/49000000000" rel="nofollow"><?= $en ? 'Ask us on WhatsApp' : 'Frag uns per WhatsApp' ?></a></p>
            </div>
        </div>
    </div>
</section>

<section class="section--tight bg-soft">
    <div class="container">
        <div style="margin-bottom:var(--sp-5)"><span class="eyebrow"><?= $en ? 'Electric vs petrol' : 'Elektro vs. Benzin' ?></span><h2 class="section__title"><?= $en ? 'Which drive fits you?' : 'Welcher Antrieb passt zu dir?' ?></h2></div>
        <table class="compare">
            <thead><tr><th>&nbsp;</th><th><?= $en ? 'Electric (ePropulsion)' : 'Elektro (ePropulsion)' ?></th><th>Benzin (Tohatsu)</th></tr></thead>
            <tbody>
                <tr><th><?= $en ? 'Noise' : 'Lautstärke' ?></th><td class="is-pro"><?= $en ? 'Near silent' : 'Nahezu lautlos' ?></td><td><?= $en ? 'Quiet 4-stroke' : 'Leiser 4-Takt' ?></td></tr>
                <tr><th><?= $en ? 'Maintenance' : 'Wartung' ?></th><td class="is-pro"><?= $en ? 'Very low' : 'Sehr gering' ?></td><td><?= $en ? 'Oil & service' : 'Öl & Service' ?></td></tr>
                <tr><th><?= $en ? 'Range' : 'Reichweite' ?></th><td><?= $en ? 'Battery-dependent' : 'Akku-abhängig' ?></td><td class="is-pro"><?= $en ? 'High (refuel fast)' : 'Hoch (schnell tanken)' ?></td></tr>
                <tr><th><?= $en ? 'Weight' : 'Gewicht' ?></th><td class="is-pro"><?= $en ? 'From 7.7 kg' : 'Ab 7,7 kg' ?></td><td><?= $en ? 'From 18 kg' : 'Ab 18 kg' ?></td></tr>
                <tr><th><?= $en ? 'Refuel / recharge' : 'Tanken / Laden' ?></th><td><?= $en ? 'Recharge' : 'Aufladen' ?></td><td class="is-pro"><?= $en ? 'Refuel anywhere' : 'Überall tanken' ?></td></tr>
                <tr><th><?= $en ? 'Best for' : 'Geeignet für' ?></th><td><?= $en ? 'Tenders, sailing, quiet waters' : 'Tender, Segeln, ruhige Gewässer' ?></td><td><?= $en ? 'Tours, planing, range' : 'Touren, Gleitfahrt, Reichweite' ?></td></tr>
                <tr><th><?= $en ? 'Entry price' : 'Einstiegspreis' ?></th><td>ab 1.099 €</td><td>ab 899 €</td></tr>
            </tbody>
        </table>
    </div>
</section>

<?php snippet('footer') ?>
