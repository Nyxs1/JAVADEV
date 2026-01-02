<?php

namespace App\Services\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class AttemptLogin
{
    /**
     * Attempt to authenticate user with login (email or username) and password.
     *
     * @param string $login Email or username
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function execute(string $login, string $password, bool $remember = false): bool
    {
        // Determine if login is email or username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $field => $login,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            return true;
        }

        // Fallback: try the other field (in case user enters email in username format or vice versa)
        $fallbackField = $field === 'email' ? 'username' : 'email';
        $fallbackCredentials = [
            $fallbackField => $login,
            'password' => $password,
        ];

        return Auth::attempt($fallbackCredentials, $remember);
    }
}
