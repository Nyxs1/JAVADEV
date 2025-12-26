<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSkill extends Model
{
    protected $fillable = [
        'user_id',
        'tech_slug',
        'tech_name',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public const LEVEL_NOVICE = 1;
    public const LEVEL_BEGINNER = 2;
    public const LEVEL_SKILLED = 3;
    public const LEVEL_EXPERT = 4;

    public const LEVEL_LABELS = [
        self::LEVEL_NOVICE => 'Novice',
        self::LEVEL_BEGINNER => 'Beginner',
        self::LEVEL_SKILLED => 'Skilled',
        self::LEVEL_EXPERT => 'Expert',
    ];

    public const LEVEL_PERCENTAGES = [
        self::LEVEL_NOVICE => 25,
        self::LEVEL_BEGINNER => 50,
        self::LEVEL_SKILLED => 75,
        self::LEVEL_EXPERT => 100,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLevelLabelAttribute(): string
    {
        return self::LEVEL_LABELS[$this->level] ?? 'Unknown';
    }

    public function getLevelPercentageAttribute(): int
    {
        return self::LEVEL_PERCENTAGES[$this->level] ?? 0;
    }
}
