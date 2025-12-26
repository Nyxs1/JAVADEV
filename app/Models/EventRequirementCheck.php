<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRequirementCheck extends Model
{
    protected $fillable = [
        'event_id',
        'requirement_id',
        'user_id',
        'is_checked',
        'checked_at',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(EventRequirement::class, 'requirement_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
