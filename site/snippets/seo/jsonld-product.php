<?php
/** Product + Offer + FAQPage JSON-LD (SEO + GEO). @var \Kirby\Cms\Page $product */
$variants = $product->variants()->toStructure();
$first = $variants->first();
$price = $first && $first->price()->isNotEmpty() ? (float) $first->price()->value() : (float) $product->priceFrom()->value();
$img = $product->image();

$availMap = [
    'instock'  => 'https://schema.org/InStock',
    'short'    => 'https://schema.org/LimitedAvailability',
    'preorder' => 'https://schema.org/PreOrder',
];

$product_ld = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    'name'        => $product->title()->value(),
    'brand'       => ['@type' => 'Brand', 'name' => $product->brand()->value()],
    'description' => $product->intro()->or($product->metaDescription())->value(),
    'sku'         => $first ? $first->sku()->value() : $product->modelcode()->value(),
    'offers'      => [
        '@type'           => 'Offer',
        'price'           => number_format($price, 2, '.', ''),
        'priceCurrency'   => 'EUR',
        'availability'    => $availMap[$product->availability()->or('instock')->value()] ?? 'https://schema.org/InStock',
        'url'             => $product->url(),
        'priceValidUntil' => date('Y') . '-12-31',
        'itemCondition'   => 'https://schema.org/NewCondition',
    ],
];
if ($img) {
    $product_ld['image'] = $img->url();
}
?>
<script type="application/ld+json"><?= json_encode($product_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if ($product->faq()->toStructure()->count() > 0): ?>
<?php
$faq_ld = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
foreach ($product->faq()->toStructure() as $f) {
    $faq_ld['mainEntity'][] = [
        '@type' => 'Question',
        'name'  => $f->q()->value(),
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->a()->value()],
    ];
}
?>
<script type="application/ld+json"><?= json_encode($faq_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif ?>
