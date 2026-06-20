<?php
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
?>
<?php snippet('header') ?>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div>
                <span class="eyebrow"><?= t('nav.contact') ?></span>
                <h1><?= $page->title() ?></h1>
                <p class="section__lead"><?= $page->intro() ?></p>

<?php if ($success): ?>
                <div class="form-alert form-alert--ok"><?= $en ? 'Thank you! Your message has been sent.' : 'Danke! Deine Nachricht wurde gesendet. Wir melden uns zeitnah.' ?></div>
<?php elseif ($alert === 'send-failed'): ?>
                <div class="form-alert form-alert--err"><?= $en ? 'Sending failed. Please e-mail us directly at info@boostboards.de.' : 'Senden fehlgeschlagen. Bitte schreib uns direkt an info@boostboards.de.' ?></div>
<?php elseif ($alert === 'csrf'): ?>
                <div class="form-alert form-alert--err"><?= $en ? 'Your session expired. Please send again.' : 'Sitzung abgelaufen. Bitte erneut senden.' ?></div>
<?php elseif ($alert === 'invalid'): ?>
                <div class="form-alert form-alert--err"><?= $en ? 'Please check the highlighted fields.' : 'Bitte prüfe die markierten Felder.' ?></div>
<?php endif ?>

                <form class="form" method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= csrf() ?>">
                    <div class="hp"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                    <div class="field<?= isset($invalid['name']) ? ' has-error' : '' ?>">
                        <label for="c-name"><?= $en ? 'Name' : 'Name' ?> *</label>
                        <input id="c-name" name="name" value="<?= esc($data['name']) ?>" required>
<?php if (isset($invalid['name'])): ?><span class="field-err"><?= $en ? 'Please enter your name.' : 'Bitte gib deinen Namen an.' ?></span><?php endif ?>
                    </div>

                    <div class="field<?= isset($invalid['email']) ? ' has-error' : '' ?>">
                        <label for="c-email">E-Mail *</label>
                        <input id="c-email" type="email" name="email" value="<?= esc($data['email']) ?>" required>
<?php if (isset($invalid['email'])): ?><span class="field-err"><?= $en ? 'Please enter a valid e-mail address.' : 'Bitte gib eine gültige E-Mail-Adresse an.' ?></span><?php endif ?>
                    </div>

                    <div class="field">
                        <label for="c-subject"><?= $en ? 'Subject' : 'Betreff' ?></label>
                        <input id="c-subject" name="subject" value="<?= esc($data['subject']) ?>">
                    </div>

                    <div class="field<?= isset($invalid['message']) ? ' has-error' : '' ?>">
                        <label for="c-message"><?= $en ? 'Message' : 'Nachricht' ?> *</label>
                        <textarea id="c-message" name="message" rows="6" required><?= esc($data['message']) ?></textarea>
<?php if (isset($invalid['message'])): ?><span class="field-err"><?= $en ? 'Please enter a message (min. 10 characters).' : 'Bitte gib eine Nachricht ein (min. 10 Zeichen).' ?></span><?php endif ?>
                    </div>

                    <p class="field-note"><?= $en ? 'By sending you agree to our' : 'Mit dem Absenden stimmst du unserer' ?> <a href="<?= url($en ? 'en/privacy' : 'datenschutz') ?>"><?= t('footer.privacy') ?></a> <?= $en ? '.' : 'zu.' ?></p>
                    <button class="btn btn--cta btn--lg" type="submit" name="submit" value="1"><?= $en ? 'Send message' : 'Nachricht senden' ?></button>
                </form>
            </div>

            <aside class="contact-info">
                <h2><?= $en ? 'Reach us' : 'So erreichst du uns' ?></h2>
                <ul>
                    <li><strong><?= $en ? 'Phone' : 'Telefon' ?></strong><a href="tel:+4940609019969">+49 40 60 90 199 69</a></li>
                    <li><strong>WhatsApp</strong><a href="https://wa.me/4940609019969" rel="nofollow">+49 40 60 90 199 69</a></li>
                    <li><strong>E-Mail</strong><a href="mailto:info@boostboards.de">info@boostboards.de</a></li>
                    <li><strong><?= $en ? 'Address' : 'Anschrift' ?></strong>Boostboards GmbH &amp; Co. KG<br>Groten Hoff 21<br>22359 Hamburg</li>
                </ul>
                <p class="contact-note"><?= $en ? 'Kielkraft is a brand of Boostboards GmbH & Co. KG.' : 'Kielkraft ist eine Marke der Boostboards GmbH & Co. KG.' ?></p>
            </aside>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
