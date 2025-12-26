<?php

namespace App\Actions\Auth;

use App\Models\EmailVerification;

class CheckOtpCooldown
{
    /**
     * Check if email is still in cooldown period.
     *
     * @return array{in_cooldown: bool, remaining_seconds: int}
     */
    public function execute(string $email): array
    {
        $latest = $this->findLatestActiveVerification($email);

        if (!$latest) {
            return ['in_cooldown' => false, 'remaining_seconds' => 0];
        }

        $remainingSeconds = $latest->getCooldownRemaining();

        return [
            'in_cooldown' => $remainingSeconds > 0,
            'remaining_seconds' => $remainingSeconds,
        ];
    }

    private function findLatestActiveVerification(string $email): ?EmailVerification
    {
        return EmailVerification::where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
