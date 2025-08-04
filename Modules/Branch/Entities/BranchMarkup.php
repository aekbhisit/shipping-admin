<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * BranchMarkup Model
 * Purpose: Simple markup rules per branch per carrier (fixed percentage)
 */
class BranchMarkup extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'carrier_id',
        'markup_percentage',
        'min_markup_amount',
        'max_markup_percentage',
        'is_active',
        'updated_by'
    ];

    protected $casts = [
        'markup_percentage' => 'decimal:2',
        'min_markup_amount' => 'decimal:2',
        'max_markup_percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Markup belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Markup belongs to a carrier
     */
    public function carrier()
    {
        return $this->belongsTo(\Modules\Shipper\Entities\Carrier::class);
    }

    /**
     * Markup was updated by a user
     */
    public function updatedBy()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'updated_by');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Calculate markup for given base price
     */
    public function calculateMarkup(float $basePrice): float
    {
        if (!$this->is_active) {
            return $basePrice;
        }

        // Calculate percentage markup
        $markupAmount = ($basePrice * $this->markup_percentage) / 100;
        
        // Apply minimum markup if set
        if ($this->min_markup_amount > 0 && $markupAmount < $this->min_markup_amount) {
            $markupAmount = $this->min_markup_amount;
        }
        
        // Apply maximum markup percentage if set
        $maxMarkupAmount = ($basePrice * $this->max_markup_percentage) / 100;
        if ($markupAmount > $maxMarkupAmount) {
            $markupAmount = $maxMarkupAmount;
        }

        return $basePrice + $markupAmount;
    }

    /**
     * Get just the markup amount (not total price)
     */
    public function getMarkupAmount(float $basePrice): float
    {
        return $this->calculateMarkup($basePrice) - $basePrice;
    }

    /**
     * Validate markup percentage is within limits
     */
    public function isValid(): bool
    {
        return $this->markup_percentage >= 0 
            && $this->markup_percentage <= $this->max_markup_percentage
            && $this->min_markup_amount >= 0;
    }

    /**
     * Check if percentage is within allowed limits
     */
    public function isWithinLimits(float $percentage): bool
    {
        return $percentage >= 0 && $percentage <= $this->max_markup_percentage;
    }

    /**
     * Update markup with validation
     */
    public function updateMarkup(float $percentage, int $updatedBy): bool
    {
        if (!$this->isWithinLimits($percentage)) {
            return false;
        }

        return $this->update([
            'markup_percentage' => $percentage,
            'updated_by' => $updatedBy,
            'updated_at' => now()
        ]);
    }

    /**
     * Get markup history (if tracking is needed)
     */
    public function getMarkupHistory(): array
    {
        return [
            'current_percentage' => $this->markup_percentage,
            'min_amount' => $this->min_markup_amount,
            'max_percentage' => $this->max_markup_percentage,
            'last_updated' => $this->updated_at,
            'updated_by' => $this->updatedBy?->name
        ];
    }

    /**
     * Calculate potential revenue for price
     */
    public function calculatePotentialRevenue(float $basePrice, int $volume = 1): array
    {
        $markup = $this->getMarkupAmount($basePrice);
        
        return [
            'base_price' => $basePrice,
            'markup_amount' => $markup,
            'final_price' => $basePrice + $markup,
            'volume' => $volume,
            'total_revenue' => ($basePrice + $markup) * $volume,
            'total_markup' => $markup * $volume
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get active markups
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific branch
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope for specific carrier
     */
    public function scopeForCarrier($query, int $carrierId)
    {
        return $query->where('carrier_id', $carrierId);
    }

    /**
     * Scope for markups above certain percentage
     */
    public function scopeAbovePercentage($query, float $percentage)
    {
        return $query->where('markup_percentage', '>', $percentage);
    }

    /**
     * Scope for recently updated markups
     */
    public function scopeRecentlyUpdated($query, int $days = 7)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get formatted markup percentage for display
     */
    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->markup_percentage, 2) . '%';
    }

    /**
     * Get formatted minimum markup amount
     */
    public function getFormattedMinAmountAttribute(): string
    {
        return '฿' . number_format($this->min_markup_amount, 2);
    }

    /**
     * Get formatted maximum markup percentage
     */
    public function getFormattedMaxPercentageAttribute(): string
    {
        return number_format($this->max_markup_percentage, 2) . '%';
    }

    /**
     * Get status badge for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get status text for UI
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get carrier name for display
     */
    public function getCarrierNameAttribute(): string
    {
        return $this->carrier?->name ?? 'Unknown Carrier';
    }

    /**
     * Get branch name for display
     */
    public function getBranchNameAttribute(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    // ========================================
    // VALIDATION RULES
    // ========================================

    /**
     * Get validation rules for markup creation/update
     */
    public static function getValidationRules(int $branchId = null, int $carrierId = null): array
    {
        $uniqueRule = 'unique:branch_markups,branch_id,NULL,id,carrier_id,' . $carrierId;
        
        return [
            'branch_id' => 'required|exists:branches,id',
            'carrier_id' => 'required|exists:carriers,id',
            'markup_percentage' => 'required|numeric|min:0|max:100',
            'min_markup_amount' => 'numeric|min:0',
            'max_markup_percentage' => 'required|numeric|min:0|max:100|gte:markup_percentage',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Get validation messages
     */
    public static function getValidationMessages(): array
    {
        return [
            'markup_percentage.required' => 'Markup percentage is required',
            'markup_percentage.numeric' => 'Markup percentage must be a number',
            'markup_percentage.min' => 'Markup percentage cannot be negative',
            'markup_percentage.max' => 'Markup percentage cannot exceed 100%',
            'max_markup_percentage.gte' => 'Maximum markup percentage must be greater than or equal to markup percentage',
            'branch_id.exists' => 'Selected branch does not exist',
            'carrier_id.exists' => 'Selected carrier does not exist'
        ];
    }

    // ========================================
    // EVENTS
    // ========================================

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Validate markup limits on save
        static::saving(function ($markup) {
            if (!$markup->isValid()) {
                throw new \InvalidArgumentException('Markup values are not within valid limits');
            }
        });
        
        // Set updated_by on update
        static::updating(function ($markup) {
            if (auth()->check()) {
                $markup->updated_by = auth()->id();
            }
        });
    }
} 