<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class ResetUserPassword
{
    /**
     * Reset user password using token.
     *
     * @param array{token: string, email: string, password: string, password_confirmation: string} $data
     * @return string Password broker status constant
     */
    public function execute(array $data): string
    {
        return Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
    }
}
