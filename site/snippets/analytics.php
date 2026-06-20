<?php
/** Consent-gated analytics config. IDs are maintained in the Panel (site → Marketing). */
$ga4 = trim((string) $site->ga4Id());
$gtm = trim((string) $site->gtmId());
if ($ga4 === '' && $gtm === '') {
    return;
}
?>
<script>window.__kkAnalytics={ga4:<?= json_encode($ga4) ?>,gtm:<?= json_encode($gtm) ?>};</script>
