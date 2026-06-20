<?php
/** Consent-gated analytics config via meta tags (CSP-safe, no inline script).
 *  IDs are maintained in the Panel (site → Marketing & Feeds). */
$ga4 = trim((string) $site->ga4Id());
$gtm = trim((string) $site->gtmId());
?>
<?php if ($ga4 !== ''): ?>
<meta name="kk-ga4" content="<?= htmlspecialchars($ga4, ENT_QUOTES) ?>">
<?php endif ?>
<?php if ($gtm !== ''): ?>
<meta name="kk-gtm" content="<?= htmlspecialchars($gtm, ENT_QUOTES) ?>">
<?php endif ?>
