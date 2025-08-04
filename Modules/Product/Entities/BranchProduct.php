<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * BranchProduct Model
 * Purpose: Branch-specific product availability
 */
class BranchProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'is_available',
        'branch_price'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'branch_price' => 'decimal:2'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Branch product belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    /**
     * Branch product belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get effective price (branch price or global price)
     */
    public function getEffectivePrice(): float
    {
        if ($this->branch_price !== null) {
            return (float) $this->branch_price;
        }

        return (float) $this->product->price;
    }

    /**
     * Get formatted effective price
     */
    public function getFormattedEffectivePrice(): string
    {
        return '฿' . number_format($this->getEffectivePrice(), 2);
    }

    /**
     * Check if product is available in this branch
     */
    public function isAvailable(): bool
    {
        return $this->is_available;
    }

    /**
     * Check if branch has custom pricing
     */
    public function hasCustomPrice(): bool
    {
        return $this->branch_price !== null;
    }

    /**
     * Get price difference from global price
     */
    public function getPriceDifference(): float
    {
        if (!$this->hasCustomPrice()) {
            return 0.0;
        }

        return (float) $this->branch_price - (float) $this->product->price;
    }

    /**
     * Get formatted price difference
     */
    public function getFormattedPriceDifference(): string
    {
        $diff = $this->getPriceDifference();
        if ($diff == 0) {
            return 'No difference';
        }

        $prefix = $diff > 0 ? '+' : '';
        return $prefix . '฿' . number_format($diff, 2);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get products with custom pricing
     */
    public function scopeWithCustomPrice($query)
    {
        return $query->whereNotNull('branch_price');
    }

    /**
     * Scope to filter by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by product
     */
    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get availability status badge for UI
     */
    public function getAvailabilityBadgeAttribute(): string
    {
        return $this->is_available ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get availability status text for UI
     */
    public function getAvailabilityTextAttribute(): string
    {
        return $this->is_available ? 'Available' : 'Not Available';
    }

    /**
     * Get pricing type for UI
     */
    public function getPricingTypeAttribute(): string
    {
        return $this->hasCustomPrice() ? 'Custom' : 'Global';
    }

    /**
     * Get pricing type badge for UI
     */
    public function getPricingTypeBadgeAttribute(): string
    {
        return $this->hasCustomPrice() ? 'badge-warning' : 'badge-info';
    }
} 