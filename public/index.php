<?php

/**
 * Kielkraft - Kirby bootstrap (public-folder layout).
 *
 * Only this "public/" directory is the web root. Everything else
 * (content/, site/, kirby/, storage/) lives one level up and is therefore
 * not reachable over HTTP.
 */

require __DIR__ . '/../kirby/bootstrap.php';

$base    = dirname(__DIR__);
$storage = $base . '/storage';

echo (new Kirby([
    'roots' => [
        'index'    => __DIR__,
        'base'     => $base,
        'site'     => $base . '/site',
        'content'  => $base . '/content',
        'accounts' => $storage . '/accounts',
        'cache'    => $storage . '/cache',
        'sessions' => $storage . '/sessions',
    ],
]))->render();
