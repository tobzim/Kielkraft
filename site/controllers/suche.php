<?php

return function ($kirby, $page) {
    $q   = trim((string) get('q'));
    $cat = (string) get('cat');
    if (!in_array($cat, ['elektro', 'benzin'], true)) {
        $cat = '';
    }
    $results = kk_search_products($q, $cat);

    return compact('q', 'cat', 'results');
};
