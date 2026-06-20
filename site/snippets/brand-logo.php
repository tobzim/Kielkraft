<?php
/**
 * Official manufacturer logo by brand name (falls back to text).
 * @var string      $brand  e.g. "Tohatsu" | "ePropulsion"
 * @var string|null $class  extra CSS classes
 */
$map = [
    'tohatsu'     => 'assets/img/brands/tohatsu.svg',
    'epropulsion' => 'assets/img/brands/epropulsion.svg',
];
$b   = (string) ($brand ?? '');
$cls = $class ?? '';
$src = $map[strtolower(trim($b))] ?? null;
?>
<?php if ($src): ?>
<img class="brand-logo <?= $cls ?>" src="<?= url($src) ?>" alt="<?= htmlspecialchars($b) ?>" loading="lazy" decoding="async">
<?php else: ?>
<span class="brand-logo-text <?= $cls ?>"><?= htmlspecialchars($b) ?></span>
<?php endif ?>
