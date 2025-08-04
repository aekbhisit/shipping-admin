<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShipmentQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'carrier_id',
        'original_price',
        'markup_percentage',
        'final_price',
        'service_type',
        'estimated_delivery_days',
        'quote_data',
        'is_selected',
        'quoted_at',
        'expires_at'
    ];

    protected $casts = [
        'shipment_id' => 'integer',
        'carrier_id' => 'integer',
        'original_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'final_price' => 'decimal:2',
        'estimated_delivery_days' => 'integer',
        'quote_data' => 'array',
        'is_selected' => 'boolean',
        'quoted_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Relationship with shipment
     */
    public function shipment()
    {
        return $this->belongsTo(\Modules\Shipment\Entities\Shipment::class);
    }

    /**
     * Relationship with carrier
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Scope for selected quotes only
     */
    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }

    /**
     * Scope for active (non-expired) quotes
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for quotes by carrier priority
     */
    public function scopeByCarrierPriority($query)
    {
        return $query->leftJoin('carriers', 'shipment_quotes.carrier_id', '=', 'carriers.id')
                     ->orderBy('carriers.priority_order', 'asc')
                     ->select('shipment_quotes.*');
    }

    /**
     * Check if quote is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Calculate final price with markup
     */
    public function calculateFinalPrice($markupPercentage = null)
    {
        $markup = $markupPercentage ?? $this->markup_percentage;
        return $this->original_price * (1 + ($markup / 100));
    }

    /**
     * Update final price based on markup
     */
    public function updateFinalPrice($markupPercentage = null)
    {
        $markup = $markupPercentage ?? $this->markup_percentage;
        $this->markup_percentage = $markup;
        $this->final_price = $this->calculateFinalPrice($markup);
        return $this;
    }

    /**
     * Get savings amount compared to other quotes
     */
    public function getSavingsAmount($comparedToQuoteId)
    {
        $comparedQuote = static::find($comparedToQuoteId);
        if (!$comparedQuote) {
            return 0;
        }
        
        return $comparedQuote->final_price - $this->final_price;
    }

    /**
     * Get delivery time description
     */
    public function getDeliveryTimeDescriptionAttribute()
    {
        if (!$this->estimated_delivery_days) {
            return 'Contact carrier for delivery time';
        }

        if ($this->estimated_delivery_days == 1) {
            return 'Next day delivery';
        }

        return $this->estimated_delivery_days . ' days delivery';
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return '฿' . number_format($this->final_price, 2);
    }

    /**
     * Get formatted original price with currency
     */
    public function getFormattedOriginalPriceAttribute()
    {
        return '฿' . number_format($this->original_price, 2);
    }

    /**
     * Get markup amount
     */
    public function getMarkupAmountAttribute()
    {
        return $this->final_price - $this->original_price;
    }

    /**
     * Mark this quote as selected and unselect others for same shipment
     */
    public function select()
    {
        // Unselect all other quotes for this shipment
        static::where('shipment_id', $this->shipment_id)
              ->where('id', '!=', $this->id)
              ->update(['is_selected' => false]);

        // Select this quote
        $this->update(['is_selected' => true]);

        return $this;
    }

    protected static function newFactory()
    {
        return \Modules\Shipper\Database\factories\ShipmentQuoteFactory::new();
    }
} 