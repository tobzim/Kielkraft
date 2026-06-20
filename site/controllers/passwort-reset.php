<?php

use Kirby\Toolkit\V;

return function ($kirby, $page) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';

    if ($kirby->user()) {
        go($code === 'en' ? 'en/account' : 'konto');
    }

    $sent = false; $alert = null; $email = '';

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        if (csrf(get('csrf')) !== true) {
            return ['sent' => false, 'alert' => 'csrf', 'email' => ''];
        }
        $email = trim((string) get('email'));

        if (V::email($email)) {
            $user = $kirby->users()->findBy('email', $email);
            if ($user) {
                try {
                    $token = bin2hex(random_bytes(20));
                    $kirby->impersonate('kirby');
                    $user->update([
                        'resetToken'   => password_hash($token, PASSWORD_DEFAULT),
                        'resetExpires' => (string) (time() + 3600),
                    ]);
                    $kirby->impersonate(null);

                    $base = url($code === 'en' ? 'en/new-password' : 'passwort-neu');
                    $link = $base . '?token=' . $token . '&email=' . rawurlencode($email);
                    $kirby->email([
                        'to'      => $email,
                        'from'    => option('kielkraft.mailFrom', 'info@boostboards.de'),
                        'subject' => $code === 'en' ? 'Reset your Kielkraft password' : 'Kielkraft – Passwort zurücksetzen',
                        'body'    => ($code === 'en'
                            ? "We received a request to reset your password.\n\nOpen this link to set a new password (valid for 1 hour):\n$link\n\nIf you did not request this, you can ignore this email."
                            : "Wir haben eine Anfrage zum Zurücksetzen deines Passworts erhalten.\n\nÖffne diesen Link, um ein neues Passwort zu vergeben (1 Stunde gültig):\n$link\n\nFalls du das nicht angefordert hast, kannst du diese E-Mail ignorieren."),
                    ]);
                } catch (Throwable $e) {
                    $kirby->impersonate(null);
                }
            }
        }
        // Always report success to avoid account enumeration.
        $sent = true;
    }

    return compact('sent', 'alert', 'email');
};
