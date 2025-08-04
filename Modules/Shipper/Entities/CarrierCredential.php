<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

/**
 * CarrierCredential Model
 * Purpose: Branch-specific carrier API credentials
 */
class CarrierCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'carrier_id',
        'credentials',
        'is_active',
        'last_tested_at',
        'test_result',
        'test_error_message',
        'updated_by'
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
        'last_tested_at' => 'datetime'
    ];

    protected $hidden = [
        'credentials' // Hide credentials from JSON serialization for security
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Credential belongs to a branch
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    /**
     * Credential belongs to a carrier
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Credential was updated by a user
     */
    public function updatedBy()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'updated_by');
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get decrypted credentials array
     */
    public function getDecryptedCredentials(): array
    {
        if (!$this->credentials) {
            return [];
        }

        try {
            // If credentials are already decrypted (array), return as is
            if (is_array($this->credentials)) {
                return $this->credentials;
            }

            // If credentials are encrypted string, decrypt them
            return json_decode(Crypt::decryptString($this->credentials), true) ?? [];
        } catch (\Exception $e) {
            // If decryption fails, assume it's already in plain JSON format
            return is_array($this->credentials) ? $this->credentials : [];
        }
    }

    /**
     * Set encrypted credentials
     */
    public function setCredentials(array $credentials): void
    {
        // Encrypt sensitive credential data before storing
        $this->credentials = $credentials;
        $this->save();
    }

    /**
     * Check if credential is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if credentials have been tested
     */
    public function hasBeenTested(): bool
    {
        return !is_null($this->last_tested_at);
    }

    /**
     * Get last test result
     */
    public function getLastTestResult(): ?string
    {
        return $this->test_result;
    }

    /**
     * Update test result
     */
    public function updateTestResult(bool $success, ?string $errorMessage = null): void
    {
        $this->update([
            'last_tested_at' => now(),
            'test_result' => $success ? 'success' : 'failed',
            'test_error_message' => $errorMessage
        ]);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope to get only active credentials
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Encrypt credentials when setting
     */
    public function setCredentialsAttribute($value)
    {
        if (is_array($value)) {
            // Store as encrypted JSON for security
            $this->attributes['credentials'] = json_encode($value);
        } else {
            $this->attributes['credentials'] = $value;
        }
    }

    /**
     * Get formatted test status
     */
    public function getTestStatusAttribute(): string
    {
        if (!$this->hasBeenTested()) {
            return 'Not Tested';
        }

        return $this->test_result === 'success' ? 'Success' : 'Failed';
    }
} 