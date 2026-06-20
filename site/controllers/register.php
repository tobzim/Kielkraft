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
                    $kirby->email([
                        'to'      => $data['email'],
                        'from'    => option('kielkraft.mailFrom', 'info@boostboards.de'),
                        'subject' => 'Willkommen bei Kielkraft',
                        'body'    => "Hallo {$data['firstname']},\n\ndein Kielkraft-Konto wurde angelegt. Du kannst dich jederzeit mit deiner E-Mail-Adresse anmelden.\n\nViele Grüße\nKielkraft",
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
