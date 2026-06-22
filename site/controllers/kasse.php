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

                $en = $code === 'en';
                $payLabels = [
                    'vorkasse'    => $en ? 'Bank transfer (advance)' : 'Vorkasse / Überweisung',
                    'paypal'      => 'PayPal',
                    'klarna'      => 'Klarna',
                    'kreditkarte' => $en ? 'Credit card' : 'Kreditkarte',
                    'sepa'        => 'SEPA-Lastschrift',
                    'rechnung'    => $en ? 'Invoice purchase' : 'Kauf auf Rechnung',
                ];
                $payLabel = $payLabels[$data['payment']] ?? $data['payment'];
                $fn = strtok(trim($data['name']), ' ') ?: $data['name'];

                // Plain-text fallback part (multipart e-mail)
                $body = "Bestellnummer: $orderNumber\n\n$lines\nZwischensumme: " . mv_eur($sub, $code)
                    . "\nFracht: " . mv_eur($ship, $code) . "\nGesamt: " . mv_eur($total, $code)
                    . "\n\nZahlart: " . $payLabel
                    . "\n\n" . $data['name'] . "\n" . $data['street'] . "\n" . $data['zip'] . ' ' . $data['city'] . "\n" . $data['country']
                    . ($data['phone'] ? "\nTel: " . $data['phone'] : '');

                // Branded HTML parts
                $rows = '';
                foreach ($items as $it) {
                    $nm = esc($it['title']) . ($it['variant'] ? ' <span style="color:#6b7c8c;">(' . esc($it['variant']) . ')</span>' : '');
                    $rows .= '<tr><td style="border-top:1px solid #edf1f5;padding:9px 0;font-size:14px;color:#16263a;">' . (int) $it['qty'] . '&times; ' . $nm
                        . '</td><td align="right" style="border-top:1px solid #edf1f5;padding:9px 0;font-size:14px;color:#16263a;white-space:nowrap;">' . mv_eur($it['price'] * $it['qty'], $code) . '</td></tr>';
                }
                $itemsTable = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-family:Arial,Helvetica,sans-serif;">'
                    . $rows
                    . '<tr><td style="border-top:2px solid #e1e8ee;padding:10px 0 2px;font-size:13px;color:#6b7c8c;">' . ($en ? 'Subtotal' : 'Zwischensumme') . '</td><td align="right" style="border-top:2px solid #e1e8ee;padding:10px 0 2px;font-size:13px;color:#6b7c8c;">' . mv_eur($sub, $code) . '</td></tr>'
                    . '<tr><td style="padding:2px 0;font-size:13px;color:#6b7c8c;">' . ($en ? 'Freight' : 'Fracht') . '</td><td align="right" style="padding:2px 0;font-size:13px;color:#6b7c8c;">' . ($ship > 0 ? mv_eur($ship, $code) : ($en ? 'free' : 'frachtfrei')) . '</td></tr>'
                    . '<tr><td style="padding:5px 0 0;font-size:16px;font-weight:800;color:#16263a;">' . ($en ? 'Total' : 'Gesamt') . '</td><td align="right" style="padding:5px 0 0;font-size:16px;font-weight:800;color:#16263a;">' . mv_eur($total, $code) . '</td></tr>'
                    . '</table>';
                $addr = esc($data['name']) . '<br>' . esc($data['street']) . '<br>' . esc($data['zip']) . ' ' . esc($data['city']) . '<br>' . esc($data['country']) . ($data['phone'] ? '<br>Tel: ' . esc($data['phone']) : '');
                $badge = '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 18px;"><tr><td style="background:#eef4fb;border:1px solid #d7e6f5;border-radius:8px;padding:8px 14px;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#0f4e97;">' . ($en ? 'Order number' : 'Bestellnummer') . ': <strong>' . esc($orderNumber) . '</strong></td></tr></table>';
                $nextStep = $data['payment'] === 'vorkasse'
                    ? ($en ? 'For advance payment you will receive the bank details in a separate e-mail. We prepare dispatch once payment is received.' : 'Bei Vorkasse erhältst du die Bankverbindung in einer separaten E-Mail. Nach Zahlungseingang bereiten wir den Versand vor.')
                    : ($en ? 'We will confirm payment and the expected delivery date shortly.' : 'Wir bestätigen dir Zahlung und voraussichtlichen Liefertermin in Kürze.');
                $btn = $user
                    ? kk_email_button($en ? 'View order in your account' : 'Bestellung im Konto ansehen', url($en ? 'en/account' : 'konto'))
                    : kk_email_button($en ? 'Continue to the shop' : 'Weiter zum Shop', url($en ? 'en' : ''));

                $custHtml = '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#3a4a5c;">'
                    . ($en ? 'Hello ' : 'Hallo ') . esc($fn)
                    . ($en ? ', thank you for your order at Kielkraft. We have received it and will be in touch with the payment or dispatch confirmation.' : ', vielen Dank für deine Bestellung bei Kielkraft. Wir haben sie erhalten und melden uns mit der Zahlungs- bzw. Versandbestätigung.')
                    . '</p>' . $badge
                    . '<p style="margin:16px 0 4px;font-size:13px;font-weight:700;color:#16263a;">' . ($en ? 'Your order' : 'Deine Bestellung') . '</p>'
                    . $itemsTable
                    . kk_email_panel('<strong>' . ($en ? 'Payment' : 'Zahlart') . ':</strong> ' . esc($payLabel) . '<br><strong>' . ($en ? 'Delivery address' : 'Lieferadresse') . ':</strong><br>' . $addr)
                    . '<p style="margin:4px 0 0;font-size:14px;line-height:1.6;color:#3a4a5c;">' . $nextStep . '</p>'
                    . $btn;

                $shopHtml = '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#3a4a5c;">Es ist eine neue Bestellung eingegangen.</p>' . $badge
                    . kk_email_panel('<strong>Kunde:</strong> ' . esc($data['name']) . '<br><strong>E-Mail:</strong> ' . esc($data['email']) . ($data['phone'] ? '<br><strong>Tel:</strong> ' . esc($data['phone']) : '') . '<br><strong>Lieferadresse:</strong><br>' . esc($data['street']) . ', ' . esc($data['zip']) . ' ' . esc($data['city']) . ', ' . esc($data['country']) . '<br><strong>Zahlart:</strong> ' . esc($payLabel))
                    . '<p style="margin:16px 0 4px;font-size:13px;font-weight:700;color:#16263a;">Positionen</p>'
                    . $itemsTable;

                $from     = option('kielkraft.mailFrom', 'info@boostboards.de');
                $fromName = option('kielkraft.mailFromName', 'Kielkraft');
                $inbox    = option('kielkraft.contactTo', 'info@boostboards.de');
                try {
                    $kirby->email([
                        'to' => $data['email'], 'from' => $from, 'fromName' => $fromName, 'replyTo' => $inbox,
                        'subject' => ($en ? 'Kielkraft – order confirmation ' : 'Kielkraft – Bestellbestätigung ') . $orderNumber,
                        'body' => [
                            'html' => kk_email_shell($en ? 'Thank you for your order!' : 'Vielen Dank für deine Bestellung!', $custHtml, ($en ? 'Order ' : 'Bestellung ') . $orderNumber),
                            'text' => ($en ? "Thank you for your order at Kielkraft.\n\n" : "Vielen Dank für deine Bestellung bei Kielkraft.\n\n") . $body,
                        ],
                    ]);
                    $kirby->email([
                        'to' => $inbox, 'from' => $from, 'fromName' => $fromName, 'replyTo' => $data['email'],
                        'subject' => 'Neue Bestellung ' . $orderNumber,
                        'body' => ['html' => kk_email_shell('Neue Bestellung ' . $orderNumber, $shopHtml), 'text' => $body],
                    ]);
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
