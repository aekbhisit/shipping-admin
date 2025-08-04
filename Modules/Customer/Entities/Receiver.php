<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Receiver Model
 * Purpose: Simple receiver model for delivery information
 */
class Receiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_district',
        'delivery_province',
        'delivery_postal_code',
        'delivery_country',
        'delivery_instructions',
        'is_frequent'
    ];

    protected $casts = [
        'is_frequent' => 'boolean'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Receiver has many shipments
     */
    public function shipments()
    {
        return $this->hasMany(\Modules\Shipment\Entities\Shipment::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get full delivery address as single string
     */
    public function getFullDeliveryAddress(): string
    {
        $parts = array_filter([
            $this->delivery_address_line_1,
            $this->delivery_address_line_2,
            $this->delivery_district,
            $this->delivery_province,
            $this->delivery_postal_code,
            $this->delivery_country !== 'Thailand' ? $this->delivery_country : null
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get short delivery address for display
     */
    public function getShortDeliveryAddress(): string
    {
        $parts = array_filter([
            $this->delivery_address_line_1,
            $this->delivery_district,
            $this->delivery_province
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get delivery address with postal code
     */
    public function getDeliveryAddressWithPostalCode(): string
    {
        return $this->getShortDeliveryAddress() . ' ' . $this->delivery_postal_code;
    }

    /**
     * Get formatted delivery address for shipping labels
     */
    public function getFormattedDeliveryAddress(): array
    {
        return [
            'name' => $this->receiver_name,
            'phone' => $this->receiver_phone,
            'email' => $this->receiver_email,
            'line_1' => $this->delivery_address_line_1,
            'line_2' => $this->delivery_address_line_2,
            'district' => $this->delivery_district,
            'province' => $this->delivery_province,
            'postal_code' => $this->delivery_postal_code,
            'country' => $this->delivery_country,
            'instructions' => $this->delivery_instructions
        ];
    }

    /**
     * Mark as frequent receiver
     */
    public function markAsFrequent(): bool
    {
        return $this->update(['is_frequent' => true]);
    }

    /**
     * Remove from frequent receivers
     */
    public function removeFromFrequent(): bool
    {
        return $this->update(['is_frequent' => false]);
    }

    /**
     * Toggle frequent status
     */
    public function toggleFrequent(): bool
    {
        return $this->update(['is_frequent' => !$this->is_frequent]);
    }

    /**
     * Get Google Maps URL for delivery address
     */
    public function getDeliveryGoogleMapsUrl(): string
    {
        $address = urlencode($this->getFullDeliveryAddress());
        return "https://www.google.com/maps/search/?api=1&query={$address}";
    }

    /**
     * Validate delivery address format
     */
    public function validateDeliveryAddress(): array
    {
        $errors = [];

        if (empty($this->receiver_name)) {
            $errors[] = 'Receiver name is required';
        }

        if (empty($this->receiver_phone)) {
            $errors[] = 'Receiver phone is required';
        }

        if (empty($this->delivery_address_line_1)) {
            $errors[] = 'Delivery address line 1 is required';
        }

        if (empty($this->delivery_district)) {
            $errors[] = 'Delivery district is required';
        }

        if (empty($this->delivery_province)) {
            $errors[] = 'Delivery province is required';
        }

        if (empty($this->delivery_postal_code) || !preg_match('/^\d{5}$/', $this->delivery_postal_code)) {
            $errors[] = 'Valid 5-digit postal code is required';
        }

        if ($this->receiver_phone && !preg_match('/^[0-9+\-\s\(\)]+$/', $this->receiver_phone)) {
            $errors[] = 'Invalid phone number format';
        }

        if ($this->receiver_email && !filter_var($this->receiver_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        return $errors;
    }

    /**
     * Get receiver statistics
     */
    public function getStats(): array
    {
        return [
            'total_shipments' => $this->shipments()->count(),
            'recent_shipments' => $this->shipments()->where('created_at', '>=', now()->subDays(30))->count(),
            'completed_shipments' => $this->shipments()->where('status', 'delivered')->count()
        ];
    }

    /**
     * Check if receiver has recent shipments
     */
    public function hasRecentShipments(int $days = 30): bool
    {
        return $this->shipments()
            ->where('created_at', '>=', now()->subDays($days))
            ->exists();
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get frequent receivers
     */
    public function scopeFrequent($query)
    {
        return $query->where('is_frequent', true);
    }

    /**
     * Scope to search receivers by phone
     */
    public function scopeByPhone($query, string $phone)
    {
        return $query->where('receiver_phone', 'like', "%{$phone}%");
    }

    /**
     * Scope to filter by delivery province
     */
    public function scopeByDeliveryProvince($query, string $province)
    {
        return $query->where('delivery_province', $province);
    }

    /**
     * Scope to filter by delivery postal code
     */
    public function scopeByDeliveryPostalCode($query, string $postalCode)
    {
        return $query->where('delivery_postal_code', $postalCode);
    }

    /**
     * Scope to search receivers
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('receiver_name', 'like', "%{$search}%")
              ->orWhere('receiver_phone', 'like', "%{$search}%")
              ->orWhere('receiver_email', 'like', "%{$search}%")
              ->orWhere('delivery_address_line_1', 'like', "%{$search}%")
              ->orWhere('delivery_district', 'like', "%{$search}%")
              ->orWhere('delivery_province', 'like', "%{$search}%")
              ->orWhere('delivery_postal_code', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get recent receivers
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get frequent badge for UI
     */
    public function getFrequentBadgeAttribute(): string
    {
        return $this->is_frequent ? 'badge-warning' : '';
    }

    /**
     * Get frequent text for UI
     */
    public function getFrequentTextAttribute(): string
    {
        return $this->is_frequent ? 'Frequent' : 'Regular';
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->receiver_phone;
        if (strlen($phone) === 10 && $phone[0] === '0') {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        return $phone;
    }

    /**
     * Get province and postal code for display
     */
    public function getLocationDisplayAttribute(): string
    {
        return $this->delivery_province . ' ' . $this->delivery_postal_code;
    }

    /**
     * Set receiver phone with formatting
     */
    public function setReceiverPhoneAttribute($value)
    {
        $this->attributes['receiver_phone'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Set receiver email to lowercase
     */
    public function setReceiverEmailAttribute($value)
    {
        $this->attributes['receiver_email'] = $value ? strtolower(trim($value)) : null;
    }

    /**
     * Set delivery postal code with validation
     */
    public function setDeliveryPostalCodeAttribute($value)
    {
        $this->attributes['delivery_postal_code'] = preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Set delivery province with title case
     */
    public function setDeliveryProvinceAttribute($value)
    {
        $this->attributes['delivery_province'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Set delivery district with title case
     */
    public function setDeliveryDistrictAttribute($value)
    {
        $this->attributes['delivery_district'] = ucwords(strtolower(trim($value)));
    }
} 