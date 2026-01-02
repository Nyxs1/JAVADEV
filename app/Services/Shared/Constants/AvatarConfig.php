<?php

namespace App\Services\Shared\Constants;

/**
 * Avatar Configuration Constants
 */
final class AvatarConfig
{
    public const STORAGE_PATH = 'avatars';
    public const STORAGE_DISK = 'public';
    public const MAX_SIZE_BYTES = 5 * 1024 * 1024; // 5MB
    public const ALLOWED_TYPES = ['jpg', 'jpeg', 'png', 'gif'];
}
