<?php

namespace App\Services\Auth;

use App\Services\Auth\Actions\GenerateOtp;
use App\Services\Auth\Actions\VerifyOtp;
use App\Services\Auth\Actions\CheckOtpCooldown;
use App\Services\Shared\Constants\OtpConfig;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(
        private GenerateOtp $generateOtp,
        private VerifyOtp $verifyOtp,
        private CheckOtpCooldown $checkCooldown,
        private VerificationMailService $mailService
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
            $this->mailService->send($email, $code, OtpConfig::EXPIRY_MINUTES);
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
