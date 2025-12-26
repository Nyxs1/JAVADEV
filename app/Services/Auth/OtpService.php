<?php

namespace App\Services\Auth;

use App\Actions\Auth\GenerateOtp;
use App\Actions\Auth\VerifyOtp;
use App\Actions\Auth\CheckOtpCooldown;
use App\Mail\VerificationCodeMail;
use App\Support\Constants\OtpConfig;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        private GenerateOtp $generateOtp,
        private VerifyOtp $verifyOtp,
        private CheckOtpCooldown $checkCooldown
    ) {
    }

    /**
     * Check if OTP dev mode is enabled.
     */
    public function isDevMode(): bool
    {
        return config('app.otp_dev_mode', config('app.env') !== 'production');
    }

    /**
     * Send OTP to email.
     *
     * @param string $email
     * @return array{success: bool, message: string, otp_code?: string, retry_after?: int}
     */
    public function sendOtp(string $email): array
    {
        // Check cooldown
        $cooldown = $this->checkCooldown->execute($email);
        if ($cooldown['in_cooldown']) {
            return [
                'success' => false,
                'message' => "Wait {$cooldown['remaining_seconds']} seconds before requesting again.",
                'retry_after' => $cooldown['remaining_seconds'],
            ];
        }

        // Generate OTP
        $code = $this->generateOtp->execute($email);
        Log::info("Generated OTP for {$email}: {$code}");

        // Send email (only in production)
        if (!$this->isDevMode()) {
            try {
                Mail::to($email)->send(new VerificationCodeMail($code, $email));
            } catch (\Exception $e) {
                Log::error('Failed to send verification email: ' . $e->getMessage());
            }
        }

        $response = [
            'success' => true,
            'message' => 'Verification code created.',
            'retry_after' => OtpConfig::COOLDOWN_SECONDS,
            'expiry_minutes' => OtpConfig::EXPIRY_MINUTES,
        ];

        if ($this->isDevMode()) {
            $response['otp_code'] = $code;
        }

        return $response;
    }

    /**
     * Verify OTP code.
     *
     * @param string $email
     * @param string $code
     * @return array{success: bool, message: string, attempts_left?: int}
     */
    public function verify(string $email, string $code): array
    {
        return $this->verifyOtp->execute($email, $code);
    }
}
