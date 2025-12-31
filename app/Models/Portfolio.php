<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover_path',
        'readme_md',
        'is_published',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get the user that owns this portfolio.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the screenshots for this portfolio.
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(PortfolioScreenshot::class)->orderBy('sort_order');
    }


    /**
     * Get the source course if built from a course.
     */
    public function builtFromCourse(): BelongsTo
    {
        return $this->belongsTo(UserCourse::class, 'source_id');
    }

    /**
     * Get evidences for this portfolio.
     */
    public function evidences(): HasMany
    {
        return $this->hasMany(ItemEvidence::class, 'item_id')
            ->where('item_type', ItemEvidence::ITEM_PORTFOLIO);
    }

    /**
     * Check if portfolio was built from a course.
     */
    public function isBuiltFromCourse(): bool
    {
        return $this->source_type === 'course' && $this->source_id !== null;
    }

    /**
     * Scope for published portfolios.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for draft (unpublished) portfolios.
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Check if portfolio is published.
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * Check if portfolio is draft.
     */
    public function isDraft(): bool
    {
        return !$this->is_published;
    }

    /**
     * Publish this portfolio.
     */
    public function publish(): bool
    {
        $this->is_published = true;
        return $this->save();
    }

    /**
     * Unpublish this portfolio.
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        return $this->save();
    }

    /**
     * Get the cover URL.
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (!$this->cover_path) {
            return null;
        }

        return asset('storage/' . $this->cover_path);
    }
}
