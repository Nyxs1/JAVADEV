<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemEvidence extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'item_evidences';

    // Evidence types
    const TYPE_GITHUB = 'github';
    const TYPE_LINK = 'link';
    const TYPE_DEMO = 'demo';
    const TYPE_PDF = 'pdf';

    // Item types
    const ITEM_PORTFOLIO = 'portfolio';
    const ITEM_USER_COURSE = 'user_course';

    protected $fillable = [
        'user_id',
        'item_type',
        'item_id',
        'type',
        'label',
        'value',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the user that owns this evidence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific item.
     */
    public function scopeForItem($query, string $type, int $id)
    {
        return $query->where('item_type', $type)->where('item_id', $id);
    }

    /**
     * Scope for public evidence only.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get available evidence types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_GITHUB => 'GitHub',
            self::TYPE_LINK => 'Link',
            self::TYPE_DEMO => 'Demo',
            self::TYPE_PDF => 'PDF',
        ];
    }

    /**
     * Get icon class for display.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_GITHUB => 'github',
            self::TYPE_DEMO => 'play',
            self::TYPE_PDF => 'file-text',
            default => 'link',
        };
    }

    /**
     * Get display label (use custom label or type name).
     */
    public function getDisplayLabelAttribute(): string
    {
        return $this->label ?: self::getTypes()[$this->type] ?? 'Link';
    }
}
