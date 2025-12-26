<?php

namespace App\Enums;

/**
 * Skill proficiency levels for tech skills.
 * 
 * Centralizes all skill level logic:
 * - Labels (for display)
 * - Percentages (for progress bars)
 * - Gradients (for visual styling)
 */
enum SkillLevel: int
{
    case NOVICE = 1;
    case BEGINNER = 2;
    case SKILLED = 3;
    case EXPERT = 4;

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::NOVICE => 'Novice',
            self::BEGINNER => 'Beginner',
            self::SKILLED => 'Skilled',
            self::EXPERT => 'Expert',
        };
    }

    /**
     * Get percentage for progress bar display.
     */
    public function percent(): int
    {
        return match ($this) {
            self::NOVICE => 25,
            self::BEGINNER => 50,
            self::SKILLED => 75,
            self::EXPERT => 100,
        };
    }

    /**
     * Get Tailwind gradient class for progress bar styling.
     */
    public function gradient(): string
    {
        return match ($this) {
            self::NOVICE => 'bg-gradient-to-r from-slate-400 to-slate-500',
            self::BEGINNER => 'bg-gradient-to-r from-blue-400 to-blue-500',
            self::SKILLED => 'bg-gradient-to-r from-indigo-500 to-purple-500',
            self::EXPERT => 'bg-gradient-to-r from-amber-400 to-orange-500',
        };
    }

    /**
     * Get all levels as options for select dropdowns.
     * 
     * @return array<int, string>
     */
    public static function options(): array
    {
        return [
            self::NOVICE->value => self::NOVICE->label(),
            self::BEGINNER->value => self::BEGINNER->label(),
            self::SKILLED->value => self::SKILLED->label(),
            self::EXPERT->value => self::EXPERT->label(),
        ];
    }

    /**
     * Create from integer value with fallback.
     */
    public static function fromValue(int $value): self
    {
        return self::tryFrom($value) ?? self::NOVICE;
    }
}
