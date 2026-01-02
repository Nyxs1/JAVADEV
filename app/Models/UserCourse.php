<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'course_name',
        'progress_percent',
        'status',
        'is_published',
        'last_activity_at',
        'completed_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'progress_percent' => 'integer',
        'last_activity_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Possible statuses.
     */
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * Get the user that owns this course enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get evidences for this course.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(ItemEvidence::class, 'item_id')
            ->where('item_type', ItemEvidence::ITEM_USER_COURSE);
    }

    /**
     * Scope for published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for completed courses.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for in-progress courses.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Check if course is published.
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * Check if course is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if course is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Publish this course.
     */
    public function publish(): bool
    {
        $this->is_published = true;
        return $this->save();
    }

    /**
     * Unpublish this course.
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        return $this->save();
    }

    /**
     * Get human-readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_IN_PROGRESS => 'Sedang Belajar',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
