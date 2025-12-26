<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityPrivacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Available activity types
     */
    const ACTIVITY_TYPES = [
        'portfolio' => 'Portfolio',
        'course' => 'Course',
        'discussion' => 'Discussion',
        'challenge' => 'Challenge',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk public activities
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope untuk private activities
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope untuk activity type tertentu
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Check if activity is public
     */
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    /**
     * Check if activity is private
     */
    public function isPrivate(): bool
    {
        return !$this->is_public;
    }

    /**
     * Get activity type label
     */
    public function getActivityTypeLabel(): string
    {
        return self::ACTIVITY_TYPES[$this->activity_type] ?? ucfirst($this->activity_type);
    }
}