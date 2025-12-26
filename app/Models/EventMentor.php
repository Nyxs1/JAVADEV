<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventMentor extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'role',
        'goal_title',
        'goal_detail',
        'goal_status',
        'target_participants',
        'achieved_participants',
        'materials_url',
        'notes',
    ];

    protected $casts = [
        'target_participants' => 'integer',
        'achieved_participants' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'mentor' => 'Mentor',
            'co-mentor' => 'Co-Mentor',
            'speaker' => 'Speaker',
            'moderator' => 'Moderator',
            default => ucfirst($this->role),
        };
    }

    public function scopeMentors($query)
    {
        return $query->whereIn('role', ['mentor', 'co-mentor']);
    }

    public function scopeSpeakers($query)
    {
        return $query->where('role', 'speaker');
    }

    public function scopeModerators($query)
    {
        return $query->where('role', 'moderator');
    }

    /**
     * Get computed goal status based on event status.
     */
    public function getComputedGoalStatusAttribute(): string
    {
        $event = $this->event;

        if ($event->isEnded()) {
            return 'done';
        }

        if ($event->isOngoing()) {
            return 'in_progress';
        }

        return 'not_started';
    }

    /**
     * Get achieved participants count (present attendees).
     */
    public function getAchievedParticipantsCountAttribute(): int
    {
        return $this->event->participants()
            ->registered()
            ->present()
            ->count();
    }

    /**
     * Sync goal status based on event status.
     */
    public function syncGoalStatus(): void
    {
        $newStatus = match (true) {
            $this->event->isEnded() => 'achieved',
            $this->event->isOngoing() => 'in_progress',
            default => 'planned',
        };

        if ($this->goal_status !== $newStatus) {
            $this->update(['goal_status' => $newStatus]);
        }
    }

    /**
     * Sync achieved participants count.
     */
    public function syncAchievedParticipants(): void
    {
        $count = $this->achieved_participants_count;
        if ($this->achieved_participants !== $count) {
            $this->update(['achieved_participants' => $count]);
        }
    }
}
