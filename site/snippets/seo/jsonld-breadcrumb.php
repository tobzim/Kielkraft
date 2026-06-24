<?php
/** BreadcrumbList JSON-LD - drives breadcrumb rich results in search. */
$code = $kirby->language() ? $kirby->language()->code() : 'de';
$items = [];
$pos = 1;
$items[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $code === 'en' ? 'Home' : 'Start', 'item' => $site->url($code)];
foreach ($page->parents()->flip() as $par) {
    $items[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $par->title()->value(), 'item' => $par->url()];
}
$items[] = ['@type' => 'ListItem', 'position' => $pos++, 'name' => $page->title()->value(), 'item' => $page->url()];
$bc = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
?>
<script type="application/ld+json"><?= json_encode($bc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
