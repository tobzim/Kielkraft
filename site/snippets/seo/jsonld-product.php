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
    'mpn'         => $product->mpn()->or($product->modelcode())->value(),
    'offers'      => [
        '@type'           => 'Offer',
        'price'           => number_format($price, 2, '.', ''),
        'priceCurrency'   => 'EUR',
        'availability'    => $availMap[$product->availability()->or('instock')->value()] ?? 'https://schema.org/InStock',
        'url'             => $product->url(),
        'priceValidUntil' => date('Y') . '-12-31',
        'itemCondition'   => 'https://schema.org/NewCondition',
        'seller'          => ['@type' => 'Organization', 'name' => 'Kielkraft'],
        'shippingDetails' => [
            '@type'               => 'OfferShippingDetails',
            'shippingRate'        => ['@type' => 'MonetaryAmount', 'value' => number_format((float) $product->shippingCost()->or(0)->value(), 2, '.', ''), 'currency' => 'EUR'],
            'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'DE'],
            'deliveryTime'        => [
                '@type'        => 'ShippingDeliveryTime',
                'handlingTime' => ['@type' => 'QuantitativeValue', 'minValue' => 1, 'maxValue' => 2, 'unitCode' => 'DAY'],
                'transitTime'  => ['@type' => 'QuantitativeValue', 'minValue' => 2, 'maxValue' => 5, 'unitCode' => 'DAY'],
            ],
        ],
        'hasMerchantReturnPolicy' => [
            '@type'                => 'MerchantReturnPolicy',
            'applicableCountry'    => 'DE',
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
            'merchantReturnDays'   => 14,
            'returnMethod'         => 'https://schema.org/ReturnByMail',
            'returnFees'           => 'https://schema.org/ReturnShippingFees',
        ],
    ],
];
if ($img) {
    $product_ld['image'] = $img->url();
}
if ($product->gtin()->isNotEmpty()) {
    $product_ld['gtin'] = $product->gtin()->value();
}
// AggregateRating + Reviews nur mit echten, freigegebenen Bewertungen (keine Fakes).
if (function_exists('kk_review_stats')) {
    $rvStats = kk_review_stats($product);
    if ($rvStats['count'] > 0) {
        $product_ld['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => number_format($rvStats['avg'], 1, '.', ''),
            'reviewCount' => $rvStats['count'],
            'bestRating'  => 5,
            'worstRating' => 1,
        ];
        $product_ld['review'] = [];
        foreach (kk_reviews_for($product)->limit(20) as $rv) {
            $product_ld['review'][] = [
                '@type'         => 'Review',
                'reviewRating'  => ['@type' => 'Rating', 'ratingValue' => (int) $rv->rating()->value(), 'bestRating' => 5, 'worstRating' => 1],
                'author'        => ['@type' => 'Person', 'name' => $rv->author()->value()],
                'datePublished' => date('Y-m-d', strtotime((string) $rv->date())),
                'name'          => $rv->title()->value(),
                'reviewBody'    => $rv->body()->value(),
            ];
        }
    }
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
