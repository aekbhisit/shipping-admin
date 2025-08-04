<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'carrier_id',
        'tracking_number',
        'label_format',
        'label_path',
        'label_data',
        'generated_at'
    ];

    protected $casts = [
        'shipment_id' => 'integer',
        'carrier_id' => 'integer',
        'generated_at' => 'datetime'
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
     * Get label download URL
     */
    public function getDownloadUrlAttribute()
    {
        return route('shipper.labels.download', $this->id);
    }

    /**
     * Get label file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->label_path) {
            return asset('storage/' . $this->label_path);
        }
        return null;
    }

    /**
     * Check if label file exists
     */
    public function fileExists()
    {
        if (!$this->label_path) {
            return false;
        }
        
        return \Storage::disk('public')->exists($this->label_path);
    }

    /**
     * Get label file size in human readable format
     */
    public function getFileSizeAttribute()
    {
        if (!$this->fileExists()) {
            return null;
        }

        $bytes = \Storage::disk('public')->size($this->label_path);
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get format icon for display
     */
    public function getFormatIconAttribute()
    {
        switch (strtolower($this->label_format)) {
            case 'pdf':
                return 'fas fa-file-pdf text-danger';
            case 'png':
            case 'jpg':
            case 'jpeg':
                return 'fas fa-file-image text-info';
            default:
                return 'fas fa-file text-secondary';
        }
    }

    /**
     * Get formatted generation time
     */
    public function getFormattedGeneratedAtAttribute()
    {
        return $this->generated_at->format('d/m/Y H:i:s');
    }

    /**
     * Delete label file from storage
     */
    public function deleteFile()
    {
        if ($this->label_path && \Storage::disk('public')->exists($this->label_path)) {
            return \Storage::disk('public')->delete($this->label_path);
        }
        return true;
    }

    /**
     * Override delete to also remove file
     */
    public function delete()
    {
        $this->deleteFile();
        return parent::delete();
    }

    protected static function newFactory()
    {
        return \Modules\Shipper\Database\factories\ShippingLabelFactory::new();
    }
} 