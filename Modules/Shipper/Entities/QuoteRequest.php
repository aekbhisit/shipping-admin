<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * QuoteRequest Model
 * Purpose: Track API requests for debugging
 */
class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'carrier_id',
        'request_data',
        'response_data',
        'quote_price',
        'service_type',
        'is_successful',
        'error_message',
        'processing_time_ms',
        'requested_at',
        'requested_by'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'quote_price' => 'decimal:2',
        'is_successful' => 'boolean',
        'processing_time_ms' => 'integer',
        'requested_at' => 'datetime'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Quote request belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    /**
     * Quote request belongs to a carrier
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Quote request was made by a user
     */
    public function requestedBy()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'requested_by');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Check if request was successful
     */
    public function isSuccessful(): bool
    {
        return $this->is_successful;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice(): ?string
    {
        return $this->quote_price ? '฿' . number_format($this->quote_price, 2) : null;
    }

    /**
     * Get processing time in seconds
     */
    public function getProcessingTimeSeconds(): ?float
    {
        return $this->processing_time_ms ? $this->processing_time_ms / 1000 : null;
    }

    /**
     * Get package details from request data
     */
    public function getPackageDetails(): array
    {
        return $this->request_data['package'] ?? [];
    }

    /**
     * Get pickup details from request data
     */
    public function getPickupDetails(): array
    {
        return $this->request_data['pickup'] ?? [];
    }

    /**
     * Get delivery details from request data
     */
    public function getDeliveryDetails(): array
    {
        return $this->request_data['delivery'] ?? [];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    /**
     * Scope to get only failed requests
     */
    public function scopeFailed($query)
    {
        return $query->where('is_successful', false);
    }

    /**
     * Scope to get today's requests
     */
    public function scopeToday($query)
    {
        return $query->whereDate('requested_at', today());
    }

    /**
     * Scope to filter by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by carrier
     */
    public function scopeByCarrier($query, int $carrierId)
    {
        return $query->where('carrier_id', $carrierId);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_successful ? 'badge-success' : 'badge-danger';
    }

    /**
     * Get status text for UI
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_successful ? 'Success' : 'Failed';
    }

    /**
     * Get formatted processing time
     */
    public function getFormattedProcessingTimeAttribute(): string
    {
        if (!$this->processing_time_ms) {
            return 'N/A';
        }

        return $this->processing_time_ms . 'ms';
    }
} 