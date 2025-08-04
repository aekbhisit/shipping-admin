<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarrierConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrier_id',
        'branch_id',
        'api_username',
        'api_password',
        'api_key',
        'api_secret',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'carrier_id' => 'integer',
        'branch_id' => 'integer',
        'created_by' => 'integer'
    ];

    protected $hidden = [
        'api_password',
        'api_secret'
    ];

    /**
     * Relationship with carrier
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Relationship with branch
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    /**
     * Relationship with user who created
     */
    public function creator()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'created_by');
    }

    /**
     * Scope for active configurations only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for global configurations (no branch)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('branch_id');
    }

    /**
     * Scope for branch-specific configurations
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Check if configuration is global
     */
    public function isGlobal()
    {
        return is_null($this->branch_id);
    }

    /**
     * Get configuration scope description
     */
    public function getScopeDescriptionAttribute()
    {
        return $this->isGlobal() ? 'Global Configuration' : 'Branch: ' . $this->branch->name;
    }

    /**
     * Get masked API credentials for display
     */
    public function getMaskedCredentials()
    {
        return [
            'api_username' => $this->api_username ? substr($this->api_username, 0, 4) . '****' : null,
            'api_password' => $this->api_password ? '****' : null,
            'api_key' => $this->api_key ? substr($this->api_key, 0, 8) . '****' : null,
            'api_secret' => $this->api_secret ? '****' : null,
        ];
    }

    /**
     * Test API connection with these credentials
     */
    public function testConnection()
    {
        // This will be implemented in the service layer
        return app(\Modules\Shipper\Services\ApiService::class)
            ->testConnection($this->carrier, $this);
    }

    protected static function newFactory()
    {
        return \Modules\Shipper\Database\factories\CarrierConfigurationFactory::new();
    }
} 