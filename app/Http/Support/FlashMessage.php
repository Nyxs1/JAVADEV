<?php

namespace App\Http\Support;

/**
 * Flash Message Keys - Standardized flash message constants
 */
final class FlashMessage
{
    // Standard keys
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const INFO = 'info';
    public const WARNING = 'warning';

    // Special keys
    public const STATUS = 'status';
    public const LOGOUT_SUCCESS = 'logout_success';
}
