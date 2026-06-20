<?php
/** Article + FAQPage JSON-LD. @var \Kirby\Cms\Page $article */
$cover = $article->image();
$art = [
    '@context'         => 'https://schema.org',
    '@type'            => 'Article',
    'headline'         => $article->title()->value(),
    'description'      => $article->excerpt()->value(),
    'author'           => ['@type' => 'Organization', 'name' => 'Kielkraft'],
    'publisher'        => [
        '@type' => 'Organization',
        'name'  => 'Kielkraft',
        'logo'  => ['@type' => 'ImageObject', 'url' => url('assets/img/logo.svg')],
    ],
    'mainEntityOfPage' => $article->url(),
];
if ($article->date()->isNotEmpty()) {
    $art['datePublished'] = $article->date()->toDate('c');
}
if ($cover) {
    $art['image'] = $cover->url();
}
?>
<script type="application/ld+json"><?= json_encode($art, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php if ($article->faq()->toStructure()->count() > 0): ?>
<?php
$faq = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
foreach ($article->faq()->toStructure() as $f) {
    $faq['mainEntity'][] = [
        '@type'          => 'Question',
        'name'           => $f->q()->value(),
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->a()->value()],
    ];
}
?>
<script type="application/ld+json"><?= json_encode($faq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif ?>
