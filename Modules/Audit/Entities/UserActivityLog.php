<?php

namespace Modules\Audit\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'branch_id',
        'details'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the branch where the activity occurred
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    /**
     * Scope for failed login attempts
     */
    public function scopeFailedLogins($query)
    {
        return $query->where('action', 'login_failed');
    }

    /**
     * Scope for successful logins
     */
    public function scopeSuccessfulLogins($query)
    {
        return $query->where('action', 'login');
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
} 