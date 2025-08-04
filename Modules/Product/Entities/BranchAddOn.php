<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * BranchAddOn Model
 * Purpose: Branch-specific add-on service pricing
 */
class BranchAddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'add_on_service_id',
        'is_available',
        'branch_price',
        'percentage_rate',
        'min_amount',
        'max_amount',
        'is_active'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'branch_price' => 'decimal:2',
        'percentage_rate' => 'decimal:4',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * BranchAddOn belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    /**
     * BranchAddOn belongs to an add-on service
     */
    public function addOnService()
    {
        return $this->belongsTo(AddOnService::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Calculate branch-specific add-on price
     */
    public function calculatePrice($baseAmount = 0)
    {
        if (!$this->is_available || !$this->is_active) {
            return 0;
        }

        // Use branch-specific pricing if available
        if ($this->branch_price !== null) {
            return $this->branch_price;
        }

        // Use branch-specific percentage if available
        if ($this->percentage_rate !== null) {
            $percentageAmount = $baseAmount * ($this->percentage_rate / 100);
            return max($this->min_amount ?? 0, min($this->max_amount ?? PHP_FLOAT_MAX, $percentageAmount));
        }

        // Fall back to global add-on service pricing
        return $this->addOnService->calculatePrice($baseAmount);
    }

    /**
     * Get effective price display
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->branch_price !== null) {
            return '฿' . number_format($this->branch_price, 2);
        }

        if ($this->percentage_rate !== null) {
            $display = $this->percentage_rate . '%';
            if ($this->min_amount > 0) {
                $display .= ' (min: ฿' . number_format($this->min_amount, 2) . ')';
            }
            if ($this->max_amount > 0) {
                $display .= ' (max: ฿' . number_format($this->max_amount, 2) . ')';
            }
            return $display;
        }

        return 'Global Pricing';
    }

    /**
     * Get pricing type
     */
    public function getPricingTypeAttribute()
    {
        if ($this->branch_price !== null) {
            return 'Fixed';
        }

        if ($this->percentage_rate !== null) {
            return 'Percentage';
        }

        return 'Global';
    }

    /**
     * Get availability badge
     */
    public function getAvailabilityBadgeAttribute()
    {
        if (!$this->is_available) {
            return '<span class="badge bg-danger">Unavailable</span>';
        }

        if (!$this->is_active) {
            return '<span class="badge bg-warning">Inactive</span>';
        }

        return '<span class="badge bg-success">Available</span>';
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only available add-ons
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('is_active', true);
    }

    /**
     * Scope to get add-ons by branch
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to get add-ons with custom pricing
     */
    public function scopeWithCustomPricing($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('branch_price')
              ->orWhereNotNull('percentage_rate');
        });
    }

    /**
     * Scope to get add-ons by service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->whereHas('addOnService', function($q) use ($serviceType) {
            $q->where('service_type', $serviceType);
        });
    }
} 