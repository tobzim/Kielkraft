<?php

return function ($kirby, $page) {
    $alert   = null;
    $success = false;
    $invalid = [];
    $data    = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        // CSRF
        if (csrf(get('csrf')) !== true) {
            return ['alert' => 'csrf', 'success' => false, 'invalid' => [], 'data' => $data];
        }

        // Honeypot: bots fill the hidden "website" field
        if (empty(get('website')) === false) {
            return ['alert' => null, 'success' => true, 'invalid' => [], 'data' => $data];
        }

        $data = [
            'name'    => trim(get('name')),
            'email'   => trim(get('email')),
            'subject' => trim(get('subject')),
            'message' => trim(get('message')),
        ];

        $invalid = invalid($data, [
            'name'    => ['required', 'minLength' => 2],
            'email'   => ['required', 'email'],
            'message' => ['required', 'minLength' => 10],
        ]);

        if (empty($invalid) === true) {
            try {
                $inbox = option('kielkraft.contactTo', 'info@boostboards.de');
                $from  = option('kielkraft.mailFrom', 'info@boostboards.de');
                $html = kk_email_panel('<strong>Name:</strong> ' . esc($data['name'])
                        . '<br><strong>E-Mail:</strong> ' . esc($data['email'])
                        . ($data['subject'] !== '' ? '<br><strong>Betreff:</strong> ' . esc($data['subject']) : ''))
                    . '<p style="margin:8px 0 0;font-size:14px;line-height:1.7;color:#16263a;white-space:pre-line;">' . esc($data['message']) . '</p>';
                $kirby->email([
                    'to'       => $inbox,
                    'from'     => $from,
                    'fromName' => option('kielkraft.mailFromName', 'Kielkraft'),
                    'replyTo'  => $data['email'],
                    'subject'  => 'Kontaktanfrage: ' . ($data['subject'] !== '' ? $data['subject'] : 'Kielkraft'),
                    'body'     => [
                        'html' => kk_email_shell('Neue Kontaktanfrage', $html),
                        'text' => "Name: {$data['name']}\nE-Mail: {$data['email']}\nBetreff: {$data['subject']}\n\n{$data['message']}\n",
                    ],
                ]);
                $success = true;
                $data = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
            } catch (Throwable $e) {
                $alert = 'send-failed';
            }
        } else {
            $alert = 'invalid';
        }
    }

    return compact('alert', 'success', 'invalid', 'data');
};
