<?php

return function ($kirby, $page) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';
    $accountUrl = $code === 'en' ? 'en/account' : 'konto';

    if ($kirby->user()) {
        go($accountUrl);
    }

    $alert = null;
    $email = '';

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        if (csrf(get('csrf')) !== true) {
            return ['alert' => 'csrf', 'email' => ''];
        }
        $email = trim((string) get('email'));
        $long  = get('remember') !== null;
        try {
            $kirby->auth()->login($email, (string) get('password'), $long);
            go($accountUrl);
        } catch (Throwable $e) {
            $alert = 'invalid';
        }
    }

    return compact('alert', 'email');
};
