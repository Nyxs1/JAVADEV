<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'registration_status',
        'attendance_status',
        'completion_status',
        'joined_at',
        'checked_in_at',
        'certificate_url',
        'reflection',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRegistered($query)
    {
        return $query->where('registration_status', 'registered');
    }

    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'present');
    }

    public function scopeCompleted($query)
    {
        return $query->where('completion_status', 'completed');
    }

    public function isCheckedIn(): bool
    {
        return $this->attendance_status === 'present';
    }

    public function isCompleted(): bool
    {
        return $this->completion_status === 'completed';
    }

    public function canReceiveCertificate(): bool
    {
        return $this->isCompleted() && !empty($this->certificate_url);
    }

    public function checkIn(): void
    {
        $this->update([
            'attendance_status' => 'present',
            'checked_in_at' => now(),
        ]);
    }

    public function markAbsent(): void
    {
        if ($this->attendance_status === null) {
            $this->update(['attendance_status' => 'absent']);
        }
    }

    public function markCompleted(?string $certificateUrl = null): void
    {
        if ($this->attendance_status !== 'present') {
            return;
        }

        $this->update([
            'completion_status' => 'completed',
            'certificate_url' => $certificateUrl,
        ]);
    }
}
