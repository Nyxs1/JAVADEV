<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code_hash',
        'expires_at',
        'attempts',
        'used_at',
        'last_sent_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];

    /**
     * Check if OTP is valid
     */
    public function isValid(): bool
    {
        return $this->used_at === null
            && $this->expires_at > now()
            && $this->attempts < 3;
    }

    /**
     * Check if OTP matches
     */
    public function verifyCode(string $code): bool
    {
        return Hash::check($code, $this->code_hash);
    }

    /**
     * Mark as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Get cooldown remaining seconds (max 30)
     */
    public function getCooldownRemaining(): int
    {
        if (!$this->last_sent_at) {
            return 0;
        }

        $cooldownSeconds = 30;
        $elapsed = (int) now()->diffInSeconds($this->last_sent_at, false);

        // If elapsed is negative (last_sent_at is in future somehow), return 0
        if ($elapsed < 0) {
            return 0;
        }

        $remaining = $cooldownSeconds - $elapsed;

        // Clamp between 0 and 30
        return max(0, min($cooldownSeconds, $remaining));
    }

    /**
     * Get latest verification for email
     */
    public static function getLatestForEmail(string $email): ?self
    {
        return self::where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}