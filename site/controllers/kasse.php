<?php

use Kirby\Data\Yaml;

return function ($kirby, $page) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';
    $cart = kk_cart_get();
    $alert = null; $success = false; $orderNumber = null; $invalid = [];
    $user = $kirby->user();
    $invoiceOk = $user ? $user->content()->get('invoiceEligible')->toBool() : false;
    $data = [
        'name' => '', 'email' => '', 'phone' => '',
        'street' => '', 'zip' => '', 'city' => '', 'country' => 'Deutschland',
        'payment' => 'vorkasse',
    ];

    // Prefill from the logged-in customer's account on first load.
    if ($user && !$kirby->request()->is('POST')) {
        $c = $user->content();
        $fullName = trim($c->get('firstname') . ' ' . $c->get('lastname'));
        $data['name']    = $fullName !== '' ? $fullName : (string) $user->name();
        $data['email']   = (string) $user->email();
        $data['phone']   = (string) $c->get('phone');
        $data['street']  = (string) $c->get('street');
        $data['zip']     = (string) $c->get('zip');
        $data['city']    = (string) $c->get('city');
        $data['country'] = (string) $c->get('country')->or('Deutschland');
    }

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        if (csrf(get('csrf')) !== true) {
            return ['alert' => 'csrf', 'success' => false, 'orderNumber' => null, 'invalid' => [], 'data' => $data, 'cart' => $cart, 'user' => $user, 'invoiceOk' => $invoiceOk];
        }
        if (empty($cart)) {
            return ['alert' => 'empty', 'success' => false, 'orderNumber' => null, 'invalid' => [], 'data' => $data, 'cart' => $cart, 'user' => $user, 'invoiceOk' => $invoiceOk];
        }

        foreach (array_keys($data) as $k) {
            $data[$k] = trim((string) get($k, $data[$k]));
        }
        $invalid = invalid($data, [
            'name'   => ['required', 'minLength' => 2],
            'email'  => ['required', 'email'],
            'street' => ['required'],
            'zip'    => ['required'],
            'city'   => ['required'],
        ]);
        if (get('terms') === null) {
            $invalid['terms'] = true;
        }
        // Invoice purchase is only valid for unlocked returning customers.
        if ($data['payment'] === 'rechnung' && !$invoiceOk) {
            $data['payment'] = 'vorkasse';
        }

        if (empty($invalid)) {
            try {
                $sub = kk_cart_subtotal();
                $ship = kk_cart_shipping();
                $total = kk_cart_total();
                $orderNumber = 'KK-' . date('Ymd-His');

                $items = [];
                $lines = '';
                foreach ($cart as $sku => $it) {
                    $items[] = ['sku' => $sku, 'title' => $it['title'], 'variant' => $it['variant'], 'price' => $it['price'], 'qty' => $it['qty']];
                    $lines .= $it['qty'] . 'x ' . $it['title'] . ($it['variant'] ? ' (' . $it['variant'] . ')' : '') . ' – ' . mv_eur($it['price'] * $it['qty'], $code) . "\n";
                }

                $kirby->impersonate('kirby');
                $slug = strtolower(str_replace(['-', ' '], ['', ''], $orderNumber)) . substr(md5($data['email'] . microtime()), 0, 5);
                page('orders')->createChild([
                    'slug'     => $slug,
                    'template' => 'order',
                    'content'  => [
                        'title'         => $orderNumber,
                        'orderNumber'   => $orderNumber,
                        'date'          => date('Y-m-d H:i:s'),
                        'orderStatus'   => 'new',
                        'paymentMethod' => $data['payment'],
                        'customerName'  => $data['name'],
                        'customerEmail' => $data['email'],
                        'customerPhone' => $data['phone'],
                        'street'        => $data['street'],
                        'zip'           => $data['zip'],
                        'city'          => $data['city'],
                        'country'       => $data['country'],
                        'items'         => Yaml::encode($items),
                        'subtotal'      => $sub,
                        'shipping'      => $ship,
                        'total'         => $total,
                    ],
                ]);

                $body = "Bestellnummer: $orderNumber\n\n$lines\nZwischensumme: " . mv_eur($sub, $code)
                    . "\nFracht: " . mv_eur($ship, $code) . "\nGesamt: " . mv_eur($total, $code)
                    . "\n\nZahlart: " . $data['payment']
                    . "\n\n" . $data['name'] . "\n" . $data['street'] . "\n" . $data['zip'] . ' ' . $data['city'] . "\n" . $data['country']
                    . ($data['phone'] ? "\nTel: " . $data['phone'] : '');

                $from  = option('kielkraft.mailFrom', 'info@boostboards.de');
                $inbox = option('kielkraft.contactTo', 'info@boostboards.de');
                try {
                    $kirby->email(['to' => $data['email'], 'from' => $from, 'replyTo' => $inbox, 'subject' => 'Kielkraft – Bestellbestätigung ' . $orderNumber, 'body' => "Vielen Dank für deine Bestellung bei Kielkraft.\n\n" . $body]);
                    $kirby->email(['to' => $inbox, 'from' => $from, 'replyTo' => $data['email'], 'subject' => 'Neue Bestellung ' . $orderNumber, 'body' => $body]);
                } catch (Throwable $mailEx) {
                    // order is placed even if mail fails; mail is retried/handled by the shop
                }

                kk_cart_save([]);
                $cart = [];
                $success = true;
            } catch (Throwable $e) {
                $alert = 'order-failed';
            }
        } else {
            $alert = 'invalid';
        }
    }

    return compact('alert', 'success', 'orderNumber', 'invalid', 'data', 'cart', 'user', 'invoiceOk');
};
