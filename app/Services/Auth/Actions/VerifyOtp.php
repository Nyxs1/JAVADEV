<?php

namespace App\Services\Auth\Actions;

use App\Models\EmailVerification;
use App\Services\Shared\Constants\OtpConfig;

class VerifyOtp
{
    /**
     * Verify OTP code for email.
     *
     * @return array{success: bool, message: string, attempts_left?: int}
     */
    public function execute(string $email, string $code): array
    {
        $verification = $this->findLatestVerification($email);

        if (!$verification) {
            return $this->errorResponse('Verification code not found.');
        }

        if ($this->isExpired($verification)) {
            return $this->errorResponse('Verification code has expired.');
        }

        if ($this->hasExceededAttempts($verification)) {
            return $this->errorResponse('Too many attempts. Please request a new code.');
        }

        if (!$verification->verifyCode($code)) {
            return $this->handleInvalidCode($verification);
        }

        $verification->markAsUsed();

        return ['success' => true, 'message' => 'Verification successful.'];
    }

    private function findLatestVerification(string $email): ?EmailVerification
    {
        return EmailVerification::where('email', $email)
            ->whereNull('used_at')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    private function isExpired(EmailVerification $verification): bool
    {
        return $verification->expires_at < now();
    }

    private function hasExceededAttempts(EmailVerification $verification): bool
    {
        return $verification->attempts >= OtpConfig::MAX_ATTEMPTS;
    }

    private function handleInvalidCode(EmailVerification $verification): array
    {
        $verification->incrementAttempts();
        $attemptsLeft = OtpConfig::MAX_ATTEMPTS - $verification->attempts;

        if ($attemptsLeft <= 0) {
            return $this->errorResponse('Too many attempts. Please request a new code.', 0);
        }

        return $this->errorResponse('Invalid verification code.', $attemptsLeft);
    }

    private function errorResponse(string $message, ?int $attemptsLeft = null): array
    {
        $response = ['success' => false, 'message' => $message];

        if ($attemptsLeft !== null) {
            $response['attempts_left'] = $attemptsLeft;
        }

        return $response;
    }
}
