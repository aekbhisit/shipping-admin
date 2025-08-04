<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Address Model
 * Purpose: Separate addresses table with favorites (address book style)
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'address_type',
        'address_line_1',
        'address_line_2',
        'district',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
        'is_favorite'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_default' => 'boolean',
        'is_favorite' => 'boolean'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Address belongs to a sender
     */
    public function sender()
    {
        return $this->belongsTo(Sender::class);
    }

    /**
     * Address has many shipments (as pickup address)
     */
    public function shipments()
    {
        return $this->hasMany(\Modules\Shipment\Entities\Shipment::class, 'pickup_address_id');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Set this address as favorite
     */
    public function setAsFavorite(): bool
    {
        return $this->update(['is_favorite' => true]);
    }

    /**
     * Remove from favorites
     */
    public function removeFromFavorites(): bool
    {
        return $this->update(['is_favorite' => false]);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(): bool
    {
        return $this->update(['is_favorite' => !$this->is_favorite]);
    }

    /**
     * Set this address as default for its sender
     */
    public function setAsDefault(): bool
    {
        // Remove default status from other addresses of the same sender
        $this->sender->addresses()->update(['is_default' => false]);
        
        // Set this address as default
        $updated = $this->update(['is_default' => true]);
        
        // Update sender's default address reference
        if ($updated) {
            $this->sender->update(['default_address_id' => $this->id]);
        }
        
        return $updated;
    }

    /**
     * Get full address as single string
     */
    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->district,
            $this->province,
            $this->postal_code,
            $this->country !== 'Thailand' ? $this->country : null
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get short address for display in lists
     */
    public function getShortAddress(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->district,
            $this->province
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get address with postal code
     */
    public function getAddressWithPostalCode(): string
    {
        return $this->getShortAddress() . ' ' . $this->postal_code;
    }

    /**
     * Get formatted address for shipping labels
     */
    public function getFormattedForShipping(): array
    {
        return [
            'line_1' => $this->address_line_1,
            'line_2' => $this->address_line_2,
            'district' => $this->district,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'country' => $this->country
        ];
    }

    /**
     * Check if address has GPS coordinates
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get Google Maps URL
     */
    public function getGoogleMapsUrl(): string
    {
        if ($this->hasCoordinates()) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        
        $address = urlencode($this->getFullAddress());
        return "https://www.google.com/maps/search/?api=1&query={$address}";
    }

    /**
     * Validate address format
     */
    public function validateAddressFormat(): array
    {
        $errors = [];

        if (empty($this->address_line_1)) {
            $errors[] = 'Address line 1 is required';
        }

        if (empty($this->district)) {
            $errors[] = 'District is required';
        }

        if (empty($this->province)) {
            $errors[] = 'Province is required';
        }

        if (empty($this->postal_code) || !preg_match('/^\d{5}$/', $this->postal_code)) {
            $errors[] = 'Valid 5-digit postal code is required';
        }

        return $errors;
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get favorite addresses
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to get default addresses
     */
    public function scopeDefaults($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get pickup addresses
     */
    public function scopePickup($query)
    {
        return $query->where('address_type', 'pickup');
    }

    /**
     * Scope to get delivery addresses
     */
    public function scopeDelivery($query)
    {
        return $query->where('address_type', 'delivery');
    }

    /**
     * Scope to filter by province
     */
    public function scopeByProvince($query, string $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Scope to filter by postal code
     */
    public function scopeByPostalCode($query, string $postalCode)
    {
        return $query->where('postal_code', $postalCode);
    }

    /**
     * Scope to search addresses
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('address_line_1', 'like', "%{$search}%")
              ->orWhere('address_line_2', 'like', "%{$search}%")
              ->orWhere('district', 'like', "%{$search}%")
              ->orWhere('province', 'like', "%{$search}%")
              ->orWhere('postal_code', 'like', "%{$search}%");
        });
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get address type badge for UI
     */
    public function getTypeBadgeAttribute(): string
    {
        return $this->address_type === 'pickup' ? 'badge-primary' : 'badge-info';
    }

    /**
     * Get address type text for UI
     */
    public function getTypeTextAttribute(): string
    {
        return ucfirst($this->address_type);
    }

    /**
     * Get favorite badge for UI
     */
    public function getFavoriteBadgeAttribute(): string
    {
        return $this->is_favorite ? 'badge-warning' : '';
    }

    /**
     * Get default badge for UI
     */
    public function getDefaultBadgeAttribute(): string
    {
        return $this->is_default ? 'badge-success' : '';
    }

    /**
     * Get coordinate display for UI
     */
    public function getCoordinateDisplayAttribute(): string
    {
        if ($this->hasCoordinates()) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'No coordinates';
    }

    /**
     * Set postal code with validation
     */
    public function setPostalCodeAttribute($value)
    {
        $this->attributes['postal_code'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Set province with title case
     */
    public function setProvinceAttribute($value)
    {
        $this->attributes['province'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Set district with title case
     */
    public function setDistrictAttribute($value)
    {
        $this->attributes['district'] = ucwords(strtolower(trim($value)));
    }
} 