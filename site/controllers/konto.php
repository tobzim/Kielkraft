<?php

return function ($kirby, $page) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';
    $user = $kirby->user();

    if (!$user) {
        go($code === 'en' ? 'en/login' : 'anmelden');
    }

    $alert = null; $saved = false;

    if ($kirby->request()->is('POST') && get('action') === 'profile') {
        if (csrf(get('csrf')) === true) {
            $upd = [];
            foreach (['firstname', 'lastname', 'company', 'vatId', 'phone', 'street', 'zip', 'city', 'country'] as $f) {
                $upd[$f] = trim((string) get($f));
            }
            $upd['accountType'] = get('accountType') === 'business' ? 'business' : 'private';
            $upd['newsletter']  = get('newsletter') !== null ? 'true' : 'false';
            $name = trim($upd['firstname'] . ' ' . $upd['lastname']);
            try {
                $kirby->impersonate('kirby');
                $user = $user->update($upd);
                if ($name !== '' && $name !== $user->name()->value()) {
                    $user = $user->changeName($name);
                }
                $kirby->impersonate(null);
                $user  = $kirby->user();
                $saved = true;
            } catch (Throwable $e) {
                $kirby->impersonate(null);
                $alert = 'save-failed';
            }
        } else {
            $alert = 'csrf';
        }
    }

    $ordersPage = page('orders');
    $orders = $ordersPage
        ? $ordersPage->drafts()->filterBy('customerEmail', $user->email())->sortBy('date', 'desc')
        : null;

    return compact('user', 'orders', 'alert', 'saved');
};
