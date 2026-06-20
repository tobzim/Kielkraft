<?php

return function ($kirby, $page) {
    $code = $kirby->language() ? $kirby->language()->code() : 'de';

    $tokenValid = function ($user, string $token): bool {
        if (!$user || $token === '') {
            return false;
        }
        $hash = (string) $user->resetToken();
        $exp  = (int) $user->resetExpires()->value();
        if ($hash === '' || $exp < time()) {
            return false;
        }
        return password_verify($token, $hash);
    };

    $token = (string) get('token');
    $email = (string) get('email');
    $user  = $email !== '' ? $kirby->users()->findBy('email', $email) : null;
    $valid = $tokenValid($user, $token);

    $alert = null; $success = false; $invalid = [];

    if ($kirby->request()->is('POST') && get('submit') !== null) {
        if (csrf(get('csrf')) !== true) {
            return ['alert' => 'csrf', 'success' => false, 'valid' => $valid, 'invalid' => [], 'token' => $token, 'email' => $email];
        }
        $token = (string) get('token');
        $email = (string) get('email');
        $user  = $email !== '' ? $kirby->users()->findBy('email', $email) : null;
        $valid = $tokenValid($user, $token);

        if (!$valid) {
            $alert = 'invalid-token';
        } else {
            $pw  = (string) get('password');
            $pw2 = (string) get('password_confirm');
            if (strlen($pw) < 8) { $invalid['password'] = true; }
            if ($pw !== $pw2)    { $invalid['password_confirm'] = true; }

            if (empty($invalid)) {
                try {
                    $kirby->impersonate('kirby');
                    $user = $user->changePassword($pw);
                    $user = $user->update(['resetToken' => '', 'resetExpires' => '']);
                    $kirby->impersonate(null);
                    $user->loginPasswordless();
                    $success = true;
                } catch (Throwable $e) {
                    $kirby->impersonate(null);
                    $alert = 'failed';
                }
            } else {
                $alert = 'invalid';
            }
        }
    }

    return compact('alert', 'success', 'valid', 'invalid', 'token', 'email');
};
