<?php
/** Produkt-Bewertungen: Zusammenfassung, Liste, Einreichformular. @var \Kirby\Cms\Page $product */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$en   = $code === 'en';
$reviews = kk_reviews_for($product);
$stats   = kk_review_stats($product);
$count   = $stats['count'];
$avg     = $stats['avg'];
$pct     = $count ? round($avg / 5 * 100) : 0;
$flash   = get('review');
?>
<section class="reviews" id="bewertungen">
    <div class="container">
        <div class="reviews__head">
            <h2><?= $en ? 'Customer reviews' : 'Kundenbewertungen' ?></h2>
<?php if ($count > 0): ?>
            <div class="reviews__summary">
                <span class="reviews__avg"><?= number_format($avg, 1, $en ? '.' : ',', '') ?></span>
                <span class="stars stars--lg" style="--p: <?= $pct ?>%" aria-label="<?= $avg ?> / 5">★★★★★</span>
                <span class="reviews__count"><?= $count ?> <?= $count === 1 ? ($en ? 'review' : 'Bewertung') : ($en ? 'reviews' : 'Bewertungen') ?></span>
            </div>
<?php endif ?>
        </div>

<?php if ($flash === 'thanks'): ?>
        <p class="reviews__flash reviews__flash--ok"><?= $en ? 'Thank you! Your review has been submitted and will appear after a short check.' : 'Danke! Deine Bewertung ist eingegangen und erscheint nach einer kurzen Prüfung.' ?></p>
<?php elseif ($flash === 'error'): ?>
        <p class="reviews__flash reviews__flash--err"><?= $en ? 'Please choose a star rating, a name and at least 10 characters of text.' : 'Bitte wähle eine Sterne-Bewertung, einen Namen und mindestens 10 Zeichen Text.' ?></p>
<?php endif ?>

        <div class="reviews__grid">
            <div class="reviews__list">
<?php if ($count === 0): ?>
                <p class="reviews__empty"><?= $en ? 'No reviews yet – be the first to share your experience with this motor.' : 'Noch keine Bewertungen – sei der Erste und teile deine Erfahrung mit diesem Motor.' ?></p>
<?php else: ?>
<?php foreach ($reviews as $r): ?>
                <article class="review">
                    <div class="review__top">
                        <span class="stars" style="--p: <?= round(((int) $r->rating()->value()) / 5 * 100) ?>%" aria-label="<?= (int) $r->rating()->value() ?> / 5">★★★★★</span>
                        <span class="review__author"><?= $r->author()->esc() ?></span>
<?php if ($r->verified()->toBool()): ?>
                        <span class="review__verified" title="<?= $en ? 'Verified purchase' : 'Verifizierter Kauf' ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><?= $en ? 'Verified purchase' : 'Verifizierter Kauf' ?></span>
<?php endif ?>
                    </div>
<?php if ($r->title()->isNotEmpty()): ?>
                    <h3 class="review__title"><?= $r->title()->esc() ?></h3>
<?php endif ?>
                    <p class="review__body"><?= nl2br($r->body()->esc()) ?></p>
                    <time class="review__date"><?= date($en ? 'M j, Y' : 'd.m.Y', strtotime((string) $r->date())) ?></time>
                </article>
<?php endforeach ?>
<?php endif ?>
            </div>

            <aside class="reviews__form">
                <h3><?= $en ? 'Write a review' : 'Bewertung schreiben' ?></h3>
                <p class="reviews__hint"><?= $en ? 'Bought this motor from us? Enter the e-mail you ordered with to get a "verified purchase" badge.' : 'Diesen Motor bei uns gekauft? Gib die Bestell-E-Mail an, um das Abzeichen „Verifizierter Kauf" zu erhalten.' ?></p>
                <form method="post" action="<?= url('reviews/submit') ?>">
                    <input type="hidden" name="csrf" value="<?= csrf() ?>">
                    <input type="hidden" name="product" value="<?= $product->id() ?>">
                    <input type="hidden" name="lang" value="<?= $code ?>">
                    <div class="hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                    <fieldset class="rate">
                        <legend><?= $en ? 'Your rating' : 'Deine Bewertung' ?></legend>
                        <div class="rate__stars">
<?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" id="rate<?= $i ?>" value="<?= $i ?>"><label for="rate<?= $i ?>" title="<?= $i ?> / 5" aria-label="<?= $i ?> / 5">★</label>
<?php endfor ?>
                        </div>
                    </fieldset>

                    <label class="field"><span><?= $en ? 'Name (public)' : 'Name (öffentlich)' ?></span>
                        <input type="text" name="author" maxlength="60" required></label>
                    <label class="field"><span><?= $en ? 'E-mail (not published)' : 'E-Mail (wird nicht veröffentlicht)' ?></span>
                        <input type="email" name="email" maxlength="120"></label>
                    <label class="field"><span><?= $en ? 'Headline' : 'Überschrift' ?></span>
                        <input type="text" name="title" maxlength="120"></label>
                    <label class="field"><span><?= $en ? 'Your review' : 'Dein Text' ?></span>
                        <textarea name="body" rows="4" maxlength="2000" required></textarea></label>

                    <button type="submit" class="btn btn--cta btn--block"><?= $en ? 'Submit review' : 'Bewertung absenden' ?></button>
                    <p class="reviews__legal"><?= $en ? 'Reviews are checked before publication. No purchase obligation.' : 'Bewertungen werden vor Veröffentlichung geprüft.' ?></p>
                </form>
            </aside>
        </div>
    </div>
</section>
