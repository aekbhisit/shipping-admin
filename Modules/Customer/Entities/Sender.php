<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Sender Model
 * Purpose: Sender with unlimited addresses (address book style)
 */
class Sender extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sender_name',
        'sender_phone',
        'sender_email',
        'default_address_id',
        'preferences',
        'is_active'
    ];

    protected $casts = [
        'preferences' => 'array',
        'is_active' => 'boolean'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Sender belongs to a customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Sender has many addresses (unlimited)
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Sender has a default address
     */
    public function defaultAddress()
    {
        return $this->belongsTo(Address::class, 'default_address_id');
    }

    /**
     * Sender has many shipments
     */
    public function shipments()
    {
        return $this->hasMany(\Modules\Shipment\Entities\Shipment::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get default address or first available address
     */
    public function getDefaultAddress(): ?Address
    {
        if ($this->defaultAddress) {
            return $this->defaultAddress;
        }

        return $this->addresses()->first();
    }

    /**
     * Get favorite addresses (address book style)
     */
    public function getFavoriteAddresses()
    {
        return $this->addresses()->favorites()->get();
    }

    /**
     * Set default address for this sender
     */
    public function setDefaultAddress(int $addressId): bool
    {
        // Verify the address belongs to this sender
        $address = $this->addresses()->find($addressId);
        if (!$address) {
            return false;
        }

        // Update current default address
        $this->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        // Update sender's default address reference
        $this->update(['default_address_id' => $addressId]);

        return true;
    }

    /**
     * Add new address to sender
     */
    public function addAddress(array $addressData): Address
    {
        $addressData['sender_id'] = $this->id;
        
        // If this is the first address, make it default
        if ($this->addresses()->count() === 0) {
            $addressData['is_default'] = true;
        }

        $address = Address::create($addressData);

        // Update sender's default address if this is the first or default
        if ($address->is_default) {
            $this->update(['default_address_id' => $address->id]);
        }

        return $address;
    }

    /**
     * Get all addresses with favorites first
     */
    public function getAddressBook()
    {
        return $this->addresses()
            ->orderByDesc('is_favorite')
            ->orderByDesc('is_default')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get pickup addresses only
     */
    public function getPickupAddresses()
    {
        return $this->addresses()
            ->where('address_type', 'pickup')
            ->orderByDesc('is_favorite')
            ->orderByDesc('is_default')
            ->get();
    }

    /**
     * Get sender preferences
     */
    public function getPreference(string $key, $default = null)
    {
        return data_get($this->preferences, $key, $default);
    }

    /**
     * Set sender preference
     */
    public function setPreference(string $key, $value): bool
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        
        return $this->update(['preferences' => $preferences]);
    }

    /**
     * Get sender statistics
     */
    public function getStats(): array
    {
        return [
            'total_addresses' => $this->addresses()->count(),
            'favorite_addresses' => $this->addresses()->favorites()->count(),
            'total_shipments' => $this->shipments()->count(),
            'recent_shipments' => $this->shipments()->where('created_at', '>=', now()->subDays(30))->count()
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active senders
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get senders by customer
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope to search senders
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('sender_name', 'like', "%{$search}%")
              ->orWhere('sender_phone', 'like', "%{$search}%")
              ->orWhere('sender_email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get senders with default address
     */
    public function scopeWithDefaultAddress($query)
    {
        return $query->with('defaultAddress');
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

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
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->sender_phone;
        if (strlen($phone) === 10 && $phone[0] === '0') {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        return $phone;
    }

    /**
     * Get default address text for display
     */
    public function getDefaultAddressTextAttribute(): string
    {
        $address = $this->getDefaultAddress();
        return $address ? $address->getShortAddress() : 'No address';
    }

    /**
     * Set phone with formatting
     */
    public function setSenderPhoneAttribute($value)
    {
        $this->attributes['sender_phone'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Set email to lowercase
     */
    public function setSenderEmailAttribute($value)
    {
        $this->attributes['sender_email'] = $value ? strtolower(trim($value)) : null;
    }
} 