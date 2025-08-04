<?php

namespace Modules\Branch\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Branch Model
 * Purpose: Branch details and configuration with markup management
 */
class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'contact_person',
        'is_active',
        'operating_hours',
        'settings',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'operating_hours' => 'array',
        'settings' => 'array'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Branch has many users
     */
    public function users()
    {
        return $this->hasMany(\Modules\User\Entities\User::class, 'branch_id');
    }

    /**
     * Branch has many markups
     */
    public function markups()
    {
        return $this->hasMany(BranchMarkup::class);
    }

    /**
     * Branch has many reports
     */
    public function reports()
    {
        return $this->hasMany(BranchReport::class);
    }

    /**
     * Branch has many shipments
     * Note: Shipment module not yet implemented
     */
    public function shipments()
    {
        // TODO: Uncomment when Shipment module is implemented
        // return $this->hasMany(\Modules\Shipment\Entities\Shipment::class);
        
        // Temporary: Return empty collection to prevent errors
        return $this->newQuery()->whereRaw('1 = 0');
    }

    /**
     * Branch belongs to the user who created it
     */
    public function creator()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by');
    }

    /**
     * Branch has many customers created by this branch
     */
    public function customersCreated()
    {
        return $this->hasMany(\Modules\Customer\Entities\Customer::class, 'created_by_branch');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get markup for specific carrier
     */
    public function getMarkupForCarrier(int $carrierId): ?BranchMarkup
    {
        return $this->markups()
            ->where('carrier_id', $carrierId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Calculate markup for base price and carrier
     */
    public function calculateMarkup(float $basePrice, int $carrierId): float
    {
        $markup = $this->getMarkupForCarrier($carrierId);
        
        if (!$markup) {
            return $basePrice; // No markup, return base price
        }

        return $markup->calculateMarkup($basePrice);
    }

    /**
     * Get performance metrics for date range
     */
    public function getPerformanceMetrics(array $dateRange = []): array
    {
        $startDate = $dateRange['start'] ?? now()->subDays(30);
        $endDate = $dateRange['end'] ?? now();

        $reports = $this->reports()
            ->whereBetween('report_date', [$startDate, $endDate])
            ->get();

        return [
            'total_shipments' => $reports->sum('shipment_count'),
            'total_revenue' => $reports->sum('total_revenue'),
            'total_markup' => $reports->sum('total_markup'),
            'average_daily_shipments' => $reports->avg('shipment_count'),
            'average_daily_revenue' => $reports->avg('total_revenue'),
            'days_reported' => $reports->count()
        ];
    }

    /**
     * Check if branch is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get branch statistics
     */
    public function getStats(): array
    {
        return [
            'total_users' => $this->users()->count(),
            'active_users' => $this->users()->where('is_active', true)->count(),
            'total_markups' => $this->markups()->count(),
            'active_markups' => $this->markups()->where('is_active', true)->count(),
            'total_shipments' => 0, // TODO: Update when Shipment module is implemented
            'recent_shipments' => 0, // TODO: Update when Shipment module is implemented
            'customers_created' => $this->customersCreated()->count()
        ];
    }

    /**
     * Generate branch code if not provided
     */
    public static function generateBranchCode(string $name): string
    {
        // Take first 3 characters of each word, uppercase
        $words = explode(' ', strtoupper($name));
        $code = '';
        
        foreach ($words as $word) {
            $code .= substr($word, 0, 3);
        }
        
        // Add random suffix if needed to ensure uniqueness
        $baseCode = substr($code, 0, 6);
        $counter = 1;
        $finalCode = $baseCode;
        
        while (self::where('code', $finalCode)->exists()) {
            $finalCode = $baseCode . sprintf('%02d', $counter);
            $counter++;
        }
        
        return $finalCode;
    }

    /**
     * Get operating hours for specific day
     */
    public function getOperatingHours(string $day = null): ?array
    {
        if (!$day) {
            $day = strtolower(now()->format('l'));
        }
        
        return data_get($this->operating_hours, $day);
    }

    /**
     * Check if branch is open at specific time
     */
    public function isOpenAt(\DateTime $dateTime = null): bool
    {
        if (!$dateTime) {
            $dateTime = now();
        }
        
        $day = strtolower($dateTime->format('l'));
        $hours = $this->getOperatingHours($day);
        
        if (!$hours || !isset($hours['open'], $hours['close'])) {
            return false;
        }
        
        $currentTime = $dateTime->format('H:i');
        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    /**
     * Get branch setting
     */
    public function getSetting(string $key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set branch setting
     */
    public function setSetting(string $key, $value): bool
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        
        return $this->update(['settings' => $settings]);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active branches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to find by branch code
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope to search branches
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for branches with markup for carrier
     */
    public function scopeWithMarkupForCarrier($query, int $carrierId)
    {
        return $query->whereHas('markups', function($q) use ($carrierId) {
            $q->where('carrier_id', $carrierId)
              ->where('is_active', true);
        });
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get status badge for UI
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'badge bg-success' : 'badge bg-secondary';
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
        $phone = $this->phone;
        if (strlen($phone) === 10 && $phone[0] === '0') {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        return $phone;
    }

    /**
     * Get short address for display
     */
    public function getShortAddressAttribute(): string
    {
        return \Str::limit($this->address, 50);
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
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * Set branch code to uppercase
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    /**
     * Auto-generate code if not provided
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($branch) {
            if (empty($branch->code)) {
                $branch->code = self::generateBranchCode($branch->name);
            }
        });
    }
} 