<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Carrier Model
 * Purpose: Carrier/shipper configuration
 */
class Carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'api_base_url',
        'api_version',
        'logo_path',
        'is_active',
        'supported_services',
        'api_documentation_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supported_services' => 'array'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Carrier has many credentials across branches
     */
    public function carrierCredentials()
    {
        return $this->hasMany(CarrierCredential::class);
    }

    /**
     * Carrier has many quote requests
     */
    public function quoteRequests()
    {
        return $this->hasMany(QuoteRequest::class);
    }

    /**
     * Carrier has many branch markups
     */
    public function branchMarkups()
    {
        return $this->hasMany(\Modules\Branch\Entities\BranchMarkup::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Check if carrier is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get supported services array
     */
    public function getSupportedServices(): array
    {
        return $this->supported_services ?? [];
    }

    /**
     * Check if carrier has credentials for specific branch
     */
    public function hasCredentialsForBranch(int $branchId): bool
    {
        return $this->carrierCredentials()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get credentials for specific branch
     */
    public function getCredentialsForBranch(int $branchId): ?CarrierCredential
    {
        return $this->carrierCredentials()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->first();
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active carriers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
} 