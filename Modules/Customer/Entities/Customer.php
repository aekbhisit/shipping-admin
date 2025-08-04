<?php

namespace Modules\Customer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Customer Model
 * Purpose: Top-level customer entity with UUID uniqueness and cross-branch sharing
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_code',
        'customer_type',
        'company_name',
        'individual_name',
        'tax_id',
        'phone',
        'email',
        'notes',
        'is_active',
        'created_by_branch',
        'created_by_user'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'customer_type' => 'string'
    ];

    // ========================================
    // BOOT METHOD FOR UUID GENERATION
    // ========================================

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            if (empty($customer->uuid)) {
                $customer->uuid = (string) Str::uuid();
            }
            
            if (empty($customer->customer_code)) {
                $customer->customer_code = self::generateCustomerCode($customer->uuid);
            }
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Customer has many senders
     */
    public function senders()
    {
        return $this->hasMany(Sender::class);
    }



    /**
     * Customer has many shipments
     */
    public function shipments()
    {
        return $this->hasMany(\Modules\Shipment\Entities\Shipment::class);
    }

    /**
     * Customer belongs to the branch that created it
     */
    public function createdByBranch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'created_by_branch');
    }

    /**
     * Customer belongs to the user that created it
     */
    public function createdByUser()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by_user');
    }

    /**
     * Alias for createdByBranch for DataTable compatibility
     */
    public function branch()
    {
        return $this->createdByBranch();
    }

    /**
     * Name accessor for DataTable compatibility
     */
    public function getNameAttribute()
    {
        return $this->getDisplayName();
    }

    /**
     * Status accessor for DataTable compatibility
     */
    public function getStatusAttribute()
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Generate customer code from UUID
     */
    public static function generateCustomerCode(?string $uuid = null): string
    {
        if (!$uuid) {
            $uuid = (string) Str::uuid();
        }
        
        // Take first 8 characters of UUID and make it uppercase
        $shortUuid = strtoupper(substr(str_replace('-', '', $uuid), 0, 8));
        return 'CUS' . $shortUuid;
    }

    /**
     * Get customer display name based on type
     */
    public function getDisplayName(): string
    {
        if ($this->customer_type === 'business' || $this->customer_type === 'corporate') {
            return $this->company_name ?? 'Unknown Business';
        }
        
        return $this->individual_name ?? 'Unknown Individual';
    }

    /**
     * Get active senders for this customer
     */
    public function getSenders()
    {
        return $this->senders()->active()->with('defaultAddress')->get();
    }

    /**
     * Get shipment history for this customer
     */
    public function getShipmentHistory()
    {
        return $this->shipments()
            ->with(['sender', 'receiver'])
            ->latest()
            ->limit(50)
            ->get();
    }

    /**
     * Find potential duplicates using fuzzy matching
     */
    public function findPotentialDuplicates()
    {
        $query = self::where('id', '!=', $this->id);
        
        $duplicates = collect();
        
        // Match by phone
        if ($this->phone) {
            $phoneMatches = $query->where('phone', $this->phone)->get();
            $duplicates = $duplicates->merge($phoneMatches);
        }
        
        // Match by email
        if ($this->email) {
            $emailMatches = $query->where('email', $this->email)->get();
            $duplicates = $duplicates->merge($emailMatches);
        }
        
        // Fuzzy match by name
        $name = $this->getDisplayName();
        if ($name && $name !== 'Unknown Business' && $name !== 'Unknown Individual') {
            $nameMatches = $query->where(function($q) use ($name) {
                $q->where('company_name', 'like', "%{$name}%")
                  ->orWhere('individual_name', 'like', "%{$name}%");
            })->get();
            $duplicates = $duplicates->merge($nameMatches);
        }
        
        return $duplicates->unique('id');
    }

    /**
     * Check if customer is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get customer stats
     */
    public function getStats(): array
    {
        return [
            'total_senders' => $this->senders()->count(),
            'active_senders' => $this->senders()->active()->count(),
            'total_shipments' => $this->shipments()->count(),
            'recent_shipments' => $this->shipments()->where('created_at', '>=', now()->subDays(30))->count()
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by customer type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('customer_type', $type);
    }

    /**
     * Scope to search customers
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('customer_code', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('individual_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to find by phone for fuzzy matching
     */
    public function scopeFindByPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }

    /**
     * Scope to find by email for fuzzy matching
     */
    public function scopeFindByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope to get recent customers
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get customer type badge for UI
     */
    public function getTypeBadgeAttribute(): string
    {
        return $this->customer_type === 'business' ? 'badge-primary' : 'badge-info';
    }

    /**
     * Get customer type text for UI
     */
    public function getTypeTextAttribute(): string
    {
        return ucfirst($this->customer_type);
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
     * Get formatted creation date
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i');
    }

    /**
     * Set phone with formatting
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/[^0-9+]/', '', $value);
    }

    /**
     * Set email to lowercase
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }
} 