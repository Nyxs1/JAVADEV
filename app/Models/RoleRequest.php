<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_role_id',
        'to_role_id',
        'status',
        'reason',
        'admin_notes',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke user yang request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke role asal
     */
    public function fromRole()
    {
        return $this->belongsTo(Role::class, 'from_role_id');
    }

    /**
     * Relasi ke role tujuan
     */
    public function toRole()
    {
        return $this->belongsTo(Role::class, 'to_role_id');
    }

    /**
     * Relasi ke admin yang memproses
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope untuk pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope untuk rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}