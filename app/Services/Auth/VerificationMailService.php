<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Service for sending verification emails.
 * 
 * Replaces the Mailable class to eliminate app/Mail dependency.
 */
class VerificationMailService
{
    /**
     * Send verification code email.
     *
     * @param string $email Recipient email
     * @param string $code Verification code
     * @param int $expiryMinutes Code expiry in minutes
     * @return bool Success status
     */
    public function send(string $email, string $code, int $expiryMinutes = 30): bool
    {
        try {
            Mail::send('emails.verification-code', [
                'code' => $code,
                'username' => $email,
                'expiryMinutes' => $expiryMinutes,
            ], function ($message) use ($email, $code) {
                $message->to($email)
                    ->subject('Kode Verifikasi JavaDev - ' . $code);
            });

            Log::info("[Mail] Verification code sent to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("[Mail] Failed to send verification email to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
