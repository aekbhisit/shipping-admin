<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackingUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'carrier_id',
        'tracking_number',
        'status',
        'location',
        'description',
        'tracking_data',
        'updated_at_carrier',
        'updated_by'
    ];

    protected $casts = [
        'shipment_id' => 'integer',
        'carrier_id' => 'integer',
        'tracking_data' => 'array',
        'updated_at_carrier' => 'datetime',
        'updated_by' => 'integer'
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
     * Relationship with user who updated
     */
    public function updater()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class, 'updated_by');
    }

    /**
     * Scope for recent updates
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('updated_at_carrier', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope ordered by carrier timestamp
     */
    public function scopeByCarrierTime($query, $direction = 'desc')
    {
        return $query->orderBy('updated_at_carrier', $direction);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        $status = strtolower($this->status);
        
        switch ($status) {
            case 'picked up':
            case 'collected':
                return 'badge-info';
            case 'in transit':
            case 'on route':
                return 'badge-warning';
            case 'delivered':
            case 'completed':
                return 'badge-success';
            case 'failed':
            case 'returned':
            case 'cancelled':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute()
    {
        $status = strtolower($this->status);
        
        switch ($status) {
            case 'picked up':
            case 'collected':
                return 'fas fa-box';
            case 'in transit':
            case 'on route':
                return 'fas fa-truck';
            case 'delivered':
            case 'completed':
                return 'fas fa-check-circle';
            case 'failed':
            case 'returned':
                return 'fas fa-exclamation-triangle';
            case 'cancelled':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-info-circle';
        }
    }

    /**
     * Get formatted carrier timestamp
     */
    public function getFormattedCarrierTimeAttribute()
    {
        return $this->updated_at_carrier->format('d/m/Y H:i:s');
    }

    /**
     * Get time difference from now
     */
    public function getTimeAgoAttribute()
    {
        return $this->updated_at_carrier->diffForHumans();
    }

    /**
     * Check if this is the latest update for the shipment
     */
    public function isLatest()
    {
        $latest = static::where('shipment_id', $this->shipment_id)
                       ->orderBy('updated_at_carrier', 'desc')
                       ->first();
        
        return $latest && $latest->id === $this->id;
    }

    /**
     * Get previous update in the timeline
     */
    public function getPreviousUpdate()
    {
        return static::where('shipment_id', $this->shipment_id)
                    ->where('updated_at_carrier', '<', $this->updated_at_carrier)
                    ->orderBy('updated_at_carrier', 'desc')
                    ->first();
    }

    /**
     * Get next update in the timeline
     */
    public function getNextUpdate()
    {
        return static::where('shipment_id', $this->shipment_id)
                    ->where('updated_at_carrier', '>', $this->updated_at_carrier)
                    ->orderBy('updated_at_carrier', 'asc')
                    ->first();
    }

    /**
     * Check if update was manually created
     */
    public function isManual()
    {
        return !is_null($this->updated_by);
    }

    protected static function newFactory()
    {
        return \Modules\Shipper\Database\factories\TrackingUpdateFactory::new();
    }
} 