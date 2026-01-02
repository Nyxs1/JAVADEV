<?php

namespace App\Services\Shared\Constants;

/**
 * OTP Configuration Constants
 */
final class OtpConfig
{
    public const EXPIRY_MINUTES = 30;
    public const COOLDOWN_SECONDS = 30;
    public const MAX_ATTEMPTS = 3;
    public const CODE_LENGTH = 6;
}
