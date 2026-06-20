<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$urlElectric = url($en ? 'en/electric-outboards' : 'elektro-aussenborder');
$urlPetrol   = url($en ? 'en/petrol-outboards' : 'benzin-aussenborder');
$wa = 'https://wa.me/4940609019969';

$products = $site->index()->filterBy('intendedTemplate', 'product')->listed();
$ds = [];
foreach ($products as $p) {
    $v = $p->variants()->toStructure()->first();
    $price = $v && $v->price()->isNotEmpty() ? (float) $v->price()->value() : (float) $p->priceFrom()->value();
    $im = $p->image();
    $ds[] = [
        'title'   => $p->title()->value(),
        'url'     => $p->url(),
        'img'     => $im ? $im->resize(420)->url() : '',
        'brand'   => $p->brand()->value(),
        'antrieb' => $p->antrieb()->value(),
        'ps'      => (float) $p->powerPs()->value(),
        'kw'      => (float) $p->powerKw()->value(),
        'kg'      => (float) $p->weightKg()->value(),
        'price'   => mv_eur($price, $code),
    ];
}
$L = $en ? [
    'rec' => 'Our recommendation', 'why' => 'Why this model', 'alt' => 'Also a good fit',
    'view' => 'View model', 'restart' => 'Start over', 'advice' => 'Ask on WhatsApp',
    'reason' => 'For a {boot} and "{use}" we recommend a {drive} outboard with around {ps} hp.',
    'electric' => 'electric', 'petrol' => 'petrol',
] : [
    'rec' => 'Unsere Empfehlung', 'why' => 'Warum dieses Modell', 'alt' => 'Passt ebenfalls gut',
    'view' => 'Modell ansehen', 'restart' => 'Neu starten', 'advice' => 'Per WhatsApp fragen',
    'reason' => 'Für ein {boot} und „{use}" empfehlen wir einen {drive}-Außenborder mit rund {ps} PS.',
    'electric' => 'Elektro', 'petrol' => 'Benzin',
];
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container container--narrow">
        <div style="margin-bottom:var(--sp-5)">
            <span class="eyebrow"><?= t('nav.advisor') ?></span>
            <h1><?= $page->title() ?></h1>
            <p class="section__lead"><?= $page->intro() ?></p>
        </div>
        <div class="advisor-brands">
            <?php snippet('brand-logo', ['brand' => 'Tohatsu', 'class' => 'brand-logo--block']) ?>
            <?php snippet('brand-logo', ['brand' => 'ePropulsion', 'class' => 'brand-logo--block']) ?>
        </div>

        <div class="advisor" data-advisor>
            <script type="application/json" data-advisor-products><?= json_encode($ds, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
            <script type="application/json" data-advisor-labels><?= json_encode($L, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

            <div class="advisor__progress"><span data-advisor-progress></span></div>

            <div data-step="boot">
                <p class="advisor__q"><?= $en ? '1. What kind of boat do you have?' : '1. Was für ein Boot fährst du?' ?></p>
                <div class="advisor__choices">
                    <button class="acard" data-choice="schlauchboot"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 14h18l-2 5H5l-2-5zM5 14V9a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v5"/></svg><b><?= $en ? 'Inflatable / RIB' : 'Schlauchboot / RIB' ?></b></button>
                    <button class="acard" data-choice="tender"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 15h20l-3 5H5l-3-5zM6 15l2-7h8l2 7"/></svg><b>Tender / <?= $en ? 'dinghy' : 'Beiboot' ?></b></button>
                    <button class="acard" data-choice="segel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3v14M12 5l7 12H5l7-12zM3 19h18l-2 2H5z"/></svg><b><?= $en ? 'Sailing yacht' : 'Segelyacht' ?></b></button>
                    <button class="acard" data-choice="angel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 15h18l-2 5H5zM7 15V8h10v7M12 3v3"/></svg><b><?= $en ? 'Fishing boat' : 'Angelboot' ?></b></button>
                    <button class="acard" data-choice="kajak"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 12c4-2 16-2 20 0-4 2-16 2-20 0zM12 8v8"/></svg><b><?= $en ? 'Kayak / micro boat' : 'Kajak / Kleinstboot' ?></b></button>
                </div>
            </div>

            <div data-step="size" hidden>
                <p class="advisor__q"><?= $en ? '2. How big / heavy is the boat?' : '2. Wie groß / schwer ist das Boot?' ?></p>
                <div class="advisor__choices advisor__choices--3">
                    <button class="acard" data-choice="klein"><b><?= $en ? 'Small' : 'Klein' ?></b><span><?= $en ? 'up to ~3 m / light' : 'bis ~3 m / leicht' ?></span></button>
                    <button class="acard" data-choice="mittel"><b><?= $en ? 'Medium' : 'Mittel' ?></b><span>~3–4 m</span></button>
                    <button class="acard" data-choice="gross"><b><?= $en ? 'Large' : 'Groß' ?></b><span><?= $en ? 'over 4 m' : 'über 4 m' ?></span></button>
                </div>
            </div>

            <div data-step="use" hidden>
                <p class="advisor__q"><?= $en ? '3. What matters most?' : '3. Was ist dir am wichtigsten?' ?></p>
                <div class="advisor__choices">
                    <button class="acard" data-choice="leise"><b><?= $en ? 'Quiet & clean' : 'Leise & sauber' ?></b><span><?= $en ? 'no fumes, low maintenance' : 'keine Abgase, wartungsarm' ?></span></button>
                    <button class="acard" data-choice="reichweite"><b><?= $en ? 'Range & tours' : 'Reichweite & Touren' ?></b><span><?= $en ? 'long distances' : 'lange Strecken' ?></span></button>
                    <button class="acard" data-choice="leistung"><b><?= $en ? 'Planing & power' : 'Gleitfahrt & Leistung' ?></b><span><?= $en ? 'get on the plane' : 'ins Gleiten kommen' ?></span></button>
                    <button class="acard" data-choice="backup"><b><?= $en ? 'Occasional / back-up' : 'Gelegentlich / Backup' ?></b><span><?= $en ? 'light & portable' : 'leicht & tragbar' ?></span></button>
                </div>
            </div>

            <div data-step="drive" hidden>
                <p class="advisor__q"><?= $en ? '4. Drive preference?' : '4. Antriebspräferenz?' ?></p>
                <div class="advisor__choices advisor__choices--3">
                    <button class="acard" data-choice="elektro"><b><?= $en ? 'Electric' : 'Elektro' ?></b><span>ePropulsion</span></button>
                    <button class="acard" data-choice="benzin"><b><?= $en ? 'Petrol' : 'Benzin' ?></b><span>Tohatsu</span></button>
                    <button class="acard" data-choice="egal"><b><?= $en ? 'Not sure' : 'Egal' ?></b><span><?= $en ? 'advise me' : 'beraten' ?></span></button>
                </div>
            </div>

            <div class="advisor__result" data-advisor-result hidden></div>
        </div>
    </div>
</section>

<section class="section--tight bg-soft">
    <div class="container container--narrow">
        <div style="margin-bottom:var(--sp-5)"><span class="eyebrow"><?= $en ? 'Good to know' : 'Gut zu wissen' ?></span><h2 class="section__title"><?= $en ? 'Choosing the right shaft length' : 'Schaftlänge richtig wählen' ?></h2></div>
        <div class="shaft-explainer">
            <svg viewBox="0 0 260 200" class="shaft-svg" role="img" aria-label="<?= $en ? 'Shaft length diagram' : 'Schaftlängen-Diagramm' ?>">
                <rect x="20" y="20" width="120" height="120" rx="4" fill="#eef4fb" stroke="#c9d6e2"/>
                <text x="30" y="40" font-size="11" fill="#6B7C8C"><?= $en ? 'Transom' : 'Spiegel' ?></text>
                <line x1="140" y1="20" x2="140" y2="170" stroke="#0B2F55" stroke-width="2"/>
                <line x1="140" y1="60" x2="175" y2="60" stroke="#1F9D55" stroke-width="3"/><text x="180" y="64" font-size="11" fill="#16263A">S · ~381 mm</text>
                <line x1="140" y1="100" x2="175" y2="100" stroke="#1366C4" stroke-width="3"/><text x="180" y="104" font-size="11" fill="#16263A">L · ~508 mm</text>
                <line x1="140" y1="140" x2="175" y2="140" stroke="#C2772E" stroke-width="3"/><text x="180" y="144" font-size="11" fill="#16263A">UL · ~635 mm</text>
                <path d="M20 150 q30 -8 60 0 t60 0 t60 0 t40 0" fill="none" stroke="#46b3ff" stroke-width="2"/>
                <text x="20" y="185" font-size="10" fill="#6B7C8C"><?= $en ? 'Waterline' : 'Wasserlinie' ?></text>
            </svg>
            <div class="shaft-text">
                <p><?= $en
                    ? 'Measure from the top of the transom to its lower edge. Up to approx. 43 cm choose a short shaft (S), above that a long shaft (L); ultra-long (UL) is for high sailboat transoms.'
                    : 'Miss den Abstand von der Oberkante des Spiegels bis zur Unterkante. Bis ca. 43 cm Kurzschaft (S), darüber Langschaft (L); Ultralangschaft (UL) für hohe Segelboot-Spiegel.' ?></p>
                <p><?= $en ? 'Wrong shaft length means cavitation or a propeller too deep – so this really matters.' : 'Falsche Schaftlänge bedeutet Kavitation oder einen zu tief sitzenden Propeller – das ist also wirklich wichtig.' ?> <a href="<?= $wa ?>" rel="nofollow"><?= $en ? 'Unsure? Ask us.' : 'Unsicher? Frag uns.' ?></a></p>
            </div>
        </div>
    </div>
</section>

<section class="section--tight">
    <div class="container">
        <div style="margin-bottom:var(--sp-5)"><span class="eyebrow"><?= $en ? 'Electric vs petrol' : 'Elektro vs. Benzin' ?></span><h2 class="section__title"><?= $en ? 'Which drive fits you?' : 'Welcher Antrieb passt zu dir?' ?></h2></div>
        <table class="compare">
            <thead><tr><th>&nbsp;</th><th><?= $en ? 'Electric (ePropulsion)' : 'Elektro (ePropulsion)' ?></th><th>Benzin (Tohatsu)</th></tr></thead>
            <tbody>
                <tr><th><?= $en ? 'Noise' : 'Lautstärke' ?></th><td class="is-pro"><?= $en ? 'Near silent' : 'Nahezu lautlos' ?></td><td><?= $en ? 'Quiet 4-stroke' : 'Leiser 4-Takt' ?></td></tr>
                <tr><th><?= $en ? 'Maintenance' : 'Wartung' ?></th><td class="is-pro"><?= $en ? 'Very low' : 'Sehr gering' ?></td><td><?= $en ? 'Oil & service' : 'Öl & Service' ?></td></tr>
                <tr><th><?= $en ? 'Range' : 'Reichweite' ?></th><td><?= $en ? 'Battery-dependent' : 'Akku-abhängig' ?></td><td class="is-pro"><?= $en ? 'High (refuel fast)' : 'Hoch (schnell tanken)' ?></td></tr>
                <tr><th><?= $en ? 'Weight' : 'Gewicht' ?></th><td class="is-pro"><?= $en ? 'From 7.7 kg' : 'Ab 7,7 kg' ?></td><td><?= $en ? 'From 18 kg' : 'Ab 18 kg' ?></td></tr>
                <tr><th><?= $en ? 'Best for' : 'Geeignet für' ?></th><td><?= $en ? 'Tenders, sailing, quiet waters' : 'Tender, Segeln, ruhige Gewässer' ?></td><td><?= $en ? 'Tours, planing, range' : 'Touren, Gleitfahrt, Reichweite' ?></td></tr>
                <tr><th><?= $en ? 'Entry price' : 'Einstiegspreis' ?></th><td>ab 1.099 €</td><td>ab 899 €</td></tr>
            </tbody>
        </table>
    </div>
</section>

<?php snippet('footer') ?>
