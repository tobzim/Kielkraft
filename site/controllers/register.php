<?php

return function ($kirby, $page, $site) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';
    $accountUrl = $code === 'en' ? 'en/account' : 'konto';

    if ($kirby->user()) {
        go($accountUrl);
    }

    $alert = null; $invalid = [];
    $data = [
        'type' => 'private', 'firstname' => '', 'lastname' => '', 'email' => '',
        'company' => '', 'vatId' => '', 'street' => '', 'zip' => '', 'city' => '',
        'country' => 'Deutschland', 'phone' => '', 'newsletter' => '',
    ];

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        if (csrf(get('csrf')) !== true) {
            return ['alert' => 'csrf', 'invalid' => [], 'data' => $data];
        }
        foreach (array_keys($data) as $k) {
            $data[$k] = trim((string) get($k, $data[$k]));
        }
        $pw  = (string) get('password');
        $pw2 = (string) get('password_confirm');

        $invalid = invalid($data, [
            'firstname' => ['required'],
            'lastname'  => ['required'],
            'email'     => ['required', 'email'],
        ]);
        if (strlen($pw) < 8) { $invalid['password'] = true; }
        if ($pw !== $pw2) { $invalid['password_confirm'] = true; }
        if (get('terms') === null) { $invalid['terms'] = true; }
        if ($data['type'] === 'business' && $data['company'] === '') { $invalid['company'] = true; }
        if (empty($invalid['email']) && $kirby->users()->findBy('email', $data['email'])) {
            $invalid['email'] = true;
            $alert = 'email-taken';
        }

        if (empty($invalid)) {
            try {
                $kirby->impersonate('kirby');
                $user = $kirby->users()->create([
                    'email'    => $data['email'],
                    'password' => $pw,
                    'role'     => 'customer',
                    'language' => $code,
                    'name'     => trim($data['firstname'] . ' ' . $data['lastname']),
                    'content'  => [
                        'firstname'       => $data['firstname'],
                        'lastname'        => $data['lastname'],
                        'accountType'     => $data['type'],
                        'company'         => $data['company'],
                        'vatId'           => $data['vatId'],
                        'phone'           => $data['phone'],
                        'street'          => $data['street'],
                        'zip'             => $data['zip'],
                        'city'            => $data['city'],
                        'country'         => $data['country'],
                        'newsletter'      => $data['newsletter'] !== '' ? 'true' : 'false',
                        'invoiceEligible' => 'false',
                    ],
                ]);
                $user->loginPasswordless();

                try {
                    $en = $code === 'en';
                    $benefits = $en
                        ? ['View your order history any time', 'Faster checkout with saved addresses', 'Invoice purchase for unlocked returning customers', 'Manage your profile and addresses']
                        : ['Bestellhistorie jederzeit einsehen', 'Schnellerer Checkout mit gespeicherten Adressen', 'Kauf auf Rechnung für freigeschaltete Bestandskunden', 'Profil und Adressen verwalten'];
                    $li = '';
                    foreach ($benefits as $b) {
                        $li .= '<tr><td style="padding:4px 0;font-size:14px;line-height:1.5;color:#3a4a5c;">&bull;&nbsp;&nbsp;' . esc($b) . '</td></tr>';
                    }
                    $html = '<p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#3a4a5c;">'
                        . ($en ? 'Hello ' : 'Hallo ') . esc($data['firstname'])
                        . ($en ? ', your Kielkraft account has been created. You can sign in any time with your e-mail address.' : ', dein Kielkraft-Konto wurde angelegt. Du kannst dich jederzeit mit deiner E-Mail-Adresse anmelden.')
                        . '</p><table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 4px;">' . $li . '</table>'
                        . kk_email_button($en ? 'Sign in' : 'Zum Login', url($en ? 'en/login' : 'anmelden'));
                    $kirby->email([
                        'to'       => $data['email'],
                        'from'     => option('kielkraft.mailFrom', 'info@boostboards.de'),
                        'fromName' => option('kielkraft.mailFromName', 'Kielkraft'),
                        'subject'  => $en ? 'Welcome to Kielkraft' : 'Willkommen bei Kielkraft',
                        'body'     => [
                            'html' => kk_email_shell($en ? 'Welcome to Kielkraft' : 'Willkommen bei Kielkraft', $html, $en ? 'Your account is ready' : 'Dein Konto ist bereit'),
                            'text' => ($en ? 'Hello ' : 'Hallo ') . $data['firstname'] . ($en ? ",\n\nyour Kielkraft account has been created. You can sign in any time with your e-mail address.\n\nKind regards\nKielkraft" : ",\n\ndein Kielkraft-Konto wurde angelegt. Du kannst dich jederzeit mit deiner E-Mail-Adresse anmelden.\n\nViele Grüße\nKielkraft"),
                        ],
                    ]);
                } catch (Throwable $e) { /* welcome mail is non-critical */ }

                go($accountUrl);
            } catch (Throwable $e) {
                $alert = 'failed';
            }
        } elseif ($alert === null) {
            $alert = 'invalid';
        }
    }

    return compact('alert', 'invalid', 'data');
};
