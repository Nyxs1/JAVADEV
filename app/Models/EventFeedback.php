<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFeedback extends Model
{
    protected $table = 'event_feedback';

    protected $fillable = [
        'event_id',
        'from_user_id',
        'to_user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('to_user_id');
    }

    public function scopeForMentor($query, int $mentorId)
    {
        return $query->where('to_user_id', $mentorId);
    }
}
