<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en = $code === 'en';
?>
<div class="consent" data-consent hidden role="dialog" aria-label="<?= $en ? 'Cookie notice' : 'Cookie-Hinweis' ?>">
    <div class="container consent__row">
        <p class="consent__text">
            <?= $en
                ? 'We only use cookies necessary for the shop. Analytics and marketing run only after your consent.'
                : 'Wir verwenden nur für den Shop notwendige Cookies. Analyse und Marketing laufen erst nach deiner Einwilligung.' ?>
            <a href="<?= url($en ? 'en/privacy' : 'datenschutz') ?>"><?= t('footer.privacy') ?></a>
        </p>
        <div class="consent__actions">
            <button type="button" class="btn btn--ghost" data-consent-decline><?= $en ? 'Only necessary' : 'Nur notwendige' ?></button>
            <button type="button" class="btn btn--primary" data-consent-accept><?= $en ? 'Accept' : 'Einverstanden' ?></button>
        </div>
    </div>
</div>
