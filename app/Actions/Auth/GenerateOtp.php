<?php

namespace App\Actions\Auth;

use App\Models\EmailVerification;
use App\Support\Constants\OtpConfig;
use Illuminate\Support\Facades\Hash;

class GenerateOtp
{
    /**
     * Generate and store OTP for email verification.
     */
    public function execute(string $email): string
    {
        $code = $this->generateCode();

        EmailVerification::create([
            'email' => $email,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(OtpConfig::EXPIRY_MINUTES),
            'attempts' => 0,
            'used_at' => null,
            'last_sent_at' => now(),
        ]);

        return $code;
    }

    private function generateCode(): string
    {
        return str_pad(
            (string) random_int(0, 999999),
            OtpConfig::CODE_LENGTH,
            '0',
            STR_PAD_LEFT
        );
    }
}
