<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * AddOnService Model
 * Purpose: Additional services and pricing for shipments
 */
class AddOnService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'service_type',
        'pricing_type',
        'base_price',
        'percentage_rate',
        'min_amount',
        'max_amount',
        'is_active',
        'sort_order',
        'requirements',
        'restrictions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'percentage_rate' => 'decimal:4',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'requirements' => 'array',
        'restrictions' => 'array'
    ];

    // ========================================
    // BOOT METHOD FOR SLUG GENERATION
    // ========================================

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($addOn) {
            if (empty($addOn->slug)) {
                $addOn->slug = Str::slug($addOn->name);
            }
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * AddOnService has many branch-specific pricing
     */
    public function branchAddOns()
    {
        return $this->hasMany(BranchAddOn::class);
    }

    /**
     * AddOnService has many shipment add-ons
     */
    public function shipmentAddOns()
    {
        return $this->hasMany(\Modules\Shipment\Entities\ShipmentAddOn::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Calculate add-on price based on type and parameters
     */
    public function calculatePrice($baseAmount = 0, $branchId = null)
    {
        // Get branch-specific pricing if available
        if ($branchId) {
            $branchAddOn = $this->branchAddOns()
                ->where('branch_id', $branchId)
                ->first();
            
            if ($branchAddOn) {
                return $branchAddOn->calculatePrice($baseAmount);
            }
        }

        // Use global pricing
        switch ($this->pricing_type) {
            case 'fixed':
                return $this->base_price;
                
            case 'percentage':
                $percentageAmount = $baseAmount * ($this->percentage_rate / 100);
                return max($this->min_amount, min($this->max_amount, $percentageAmount));
                
            case 'tiered':
                return $this->calculateTieredPrice($baseAmount);
                
            default:
                return $this->base_price;
        }
    }

    /**
     * Calculate tiered pricing
     */
    private function calculateTieredPrice($baseAmount)
    {
        // Implementation for tiered pricing
        // This can be customized based on business requirements
        return $this->base_price;
    }

    /**
     * Check if add-on is available for given parameters
     */
    public function isAvailable($shipmentData = [])
    {
        if (!$this->is_active) {
            return false;
        }

        // Check restrictions
        if ($this->restrictions) {
            foreach ($this->restrictions as $restriction) {
                if (!$this->checkRestriction($restriction, $shipmentData)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check specific restriction
     */
    private function checkRestriction($restriction, $shipmentData)
    {
        switch ($restriction['type']) {
            case 'weight_limit':
                return $shipmentData['weight'] <= $restriction['max_weight'];
                
            case 'distance_limit':
                return $shipmentData['distance'] <= $restriction['max_distance'];
                
            case 'package_type':
                return in_array($shipmentData['package_type'], $restriction['allowed_types']);
                
            default:
                return true;
        }
    }

    /**
     * Get service type badge
     */
    public function getServiceTypeBadgeAttribute()
    {
        $badgeClasses = [
            'insurance' => 'bg-primary',
            'handling' => 'bg-warning',
            'delivery' => 'bg-success',
            'cod' => 'bg-info'
        ];

        $badgeClass = $badgeClasses[$this->service_type] ?? 'bg-secondary';
        
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->service_type) . '</span>';
    }

    /**
     * Get pricing type badge
     */
    public function getPricingTypeBadgeAttribute()
    {
        $badgeClasses = [
            'fixed' => 'bg-success',
            'percentage' => 'bg-warning',
            'tiered' => 'bg-info'
        ];

        $badgeClass = $badgeClasses[$this->pricing_type] ?? 'bg-secondary';
        
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->pricing_type) . '</span>';
    }

    /**
     * Get formatted price display
     */
    public function getFormattedPriceAttribute()
    {
        switch ($this->pricing_type) {
            case 'fixed':
                return '฿' . number_format($this->base_price, 2);
                
            case 'percentage':
                $display = $this->percentage_rate . '%';
                if ($this->min_amount > 0) {
                    $display .= ' (min: ฿' . number_format($this->min_amount, 2) . ')';
                }
                if ($this->max_amount > 0) {
                    $display .= ' (max: ฿' . number_format($this->max_amount, 2) . ')';
                }
                return $display;
                
            default:
                return '฿' . number_format($this->base_price, 2);
        }
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active add-ons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get add-ons by service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope to get add-ons by pricing type
     */
    public function scopeByPricingType($query, $pricingType)
    {
        return $query->where('pricing_type', $pricingType);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope to search add-ons
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
} 