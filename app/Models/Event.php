<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_at',
        'end_at',
        'type',
        'level',
        'mode',
        'location_text',
        'meeting_url',
        'capacity',
        'status',
        'finalized_at',
        'requirements',
        'cover_image',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'finalized_at' => 'datetime',
        'level' => 'integer',
        'capacity' => 'integer',
        'requirements' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function mentors(): HasMany
    {
        return $this->hasMany(EventMentor::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(EventFeedback::class);
    }

    public function requirementItems(): HasMany
    {
        return $this->hasMany(EventRequirement::class)->ordered();
    }

    public function requirementChecks(): HasMany
    {
        return $this->hasMany(EventRequirementCheck::class);
    }

    public function registeredUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_participants')
            ->withPivot(['registration_status', 'attendance_status', 'completion_status'])
            ->withTimestamps();
    }

    public function mentorUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_mentors')
            ->withPivot(['role', 'goal_title', 'goal_detail', 'goal_status'])
            ->withTimestamps();
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended' || now()->gt($this->end_at);
    }

    public function isStarted(): bool
    {
        return now()->gte($this->start_at);
    }

    public function isRequirementsLocked(): bool
    {
        return $this->isStarted();
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            1 => 'Beginner',
            2 => 'Fundamental',
            3 => 'Intermediate',
            4 => 'Advanced',
            default => 'Unknown',
        };
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Check if event is full (capacity reached).
     */
    public function isFull(): bool
    {
        if (!$this->capacity) {
            return false;
        }

        return $this->participants()->registered()->count() >= $this->capacity;
    }

    /**
     * Get remaining spots.
     */
    public function getRemainingSpots(): ?int
    {
        if (!$this->capacity) {
            return null;
        }

        return max(0, $this->capacity - $this->participants()->registered()->count());
    }

    /**
     * Check if event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'published' && now()->lt($this->start_at);
    }

    /**
     * Check if event is ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === 'published' && now()->gte($this->start_at) && now()->lte($this->end_at);
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        if ($this->isEnded()) {
            return 'Ended';
        }

        if ($this->isOngoing()) {
            return 'Ongoing';
        }

        if ($this->isUpcoming()) {
            return 'Upcoming';
        }

        return ucfirst($this->status);
    }

    /**
     * Scope for published events.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'published')->where('start_at', '>', now());
    }

    /**
     * Scope for ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'published')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    /**
     * Scope for ended events.
     */
    public function scopeEnded($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'ended')
                ->orWhere('end_at', '<', now());
        });
    }

    /**
     * Scope for filtering by mode.
     */
    public function scopeMode($query, string $mode)
    {
        return $query->where('mode', $mode);
    }
}
