<?php

namespace Modules\Audit\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceReport extends Model
{
    protected $fillable = [
        'report_type',
        'period',
        'description',
        'status',
        'generated_by',
        'branch_id',
        'file_path',
        'file_size',
        'format',
        'summary',
        'metadata'
    ];

    protected $casts = [
        'summary' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who generated the report
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by');
    }

    /**
     * Get the branch for the report
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }

    /**
     * Scope for completed reports
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for processing reports
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for failed reports
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for recent reports
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope by report type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }
} 