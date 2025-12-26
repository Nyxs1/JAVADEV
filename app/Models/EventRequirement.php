<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventRequirement extends Model
{
    protected $fillable = [
        'event_id',
        'title',
        'type',
        'category',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(EventRequirementCheck::class, 'requirement_id');
    }

    public function isCheckedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->checks()
            ->where('user_id', $user->id)
            ->where('is_checked', true)
            ->exists();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function scopeInfo($query)
    {
        return $query->where('type', 'info');
    }

    public function scopeChecklist($query)
    {
        return $query->where('type', 'checklist');
    }

    public function scopeTech($query)
    {
        return $query->where('type', 'tech');
    }
}
