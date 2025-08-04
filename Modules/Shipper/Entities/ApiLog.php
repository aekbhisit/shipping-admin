<?php

namespace Modules\Shipper\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'carrier_id',
        'endpoint',
        'method',
        'request_data',
        'response_data',
        'response_code',
        'response_time_ms',
        'error_message',
        'is_success',
        'logged_at'
    ];

    protected $casts = [
        'carrier_id' => 'integer',
        'response_code' => 'integer',
        'response_time_ms' => 'integer',
        'is_success' => 'boolean',
        'logged_at' => 'datetime'
    ];

    /**
     * Relationship with carrier
     */
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * Scope for successful requests only
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_success', true);
    }

    /**
     * Scope for failed requests only
     */
    public function scopeFailed($query)
    {
        return $query->where('is_success', false);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('logged_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for specific HTTP methods
     */
    public function scopeWithMethod($query, $method)
    {
        return $query->where('method', strtoupper($method));
    }

    /**
     * Scope for specific response codes
     */
    public function scopeWithResponseCode($query, $code)
    {
        return $query->where('response_code', $code);
    }

    /**
     * Scope ordered by log time
     */
    public function scopeByLogTime($query, $direction = 'desc')
    {
        return $query->orderBy('logged_at', $direction);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->is_success) {
            return 'badge-success';
        }
        
        if ($this->response_code >= 400 && $this->response_code < 500) {
            return 'badge-warning'; // Client error
        }
        
        if ($this->response_code >= 500) {
            return 'badge-danger'; // Server error
        }
        
        return 'badge-secondary'; // Unknown status
    }

    /**
     * Get response code description
     */
    public function getResponseCodeDescriptionAttribute()
    {
        if (!$this->response_code) {
            return 'No Response';
        }

        $descriptions = [
            200 => 'OK',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout'
        ];

        return $descriptions[$this->response_code] ?? 'Unknown Status';
    }

    /**
     * Get formatted response time
     */
    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time_ms) {
            return 'N/A';
        }

        if ($this->response_time_ms >= 1000) {
            return number_format($this->response_time_ms / 1000, 2) . 's';
        }

        return $this->response_time_ms . 'ms';
    }

    /**
     * Get response time performance class
     */
    public function getResponseTimeClassAttribute()
    {
        if (!$this->response_time_ms) {
            return 'text-muted';
        }

        if ($this->response_time_ms <= 1000) {
            return 'text-success'; // Fast
        } elseif ($this->response_time_ms <= 3000) {
            return 'text-warning'; // Moderate
        } else {
            return 'text-danger'; // Slow
        }
    }

    /**
     * Get formatted logged time
     */
    public function getFormattedLoggedAtAttribute()
    {
        return $this->logged_at->format('d/m/Y H:i:s');
    }

    /**
     * Get time ago
     */
    public function getTimeAgoAttribute()
    {
        return $this->logged_at->diffForHumans();
    }

    /**
     * Get truncated request data for display
     */
    public function getTruncatedRequestAttribute()
    {
        if (!$this->request_data) {
            return 'N/A';
        }

        return strlen($this->request_data) > 100 
            ? substr($this->request_data, 0, 100) . '...'
            : $this->request_data;
    }

    /**
     * Get truncated response data for display
     */
    public function getTruncatedResponseAttribute()
    {
        if (!$this->response_data) {
            return 'N/A';
        }

        return strlen($this->response_data) > 100 
            ? substr($this->response_data, 0, 100) . '...'
            : $this->response_data;
    }

    /**
     * Check if this is an error log
     */
    public function isError()
    {
        return !$this->is_success || $this->response_code >= 400;
    }

    /**
     * Get endpoint name for display
     */
    public function getEndpointNameAttribute()
    {
        // Extract last part of URL for display
        $parts = explode('/', $this->endpoint);
        return end($parts) ?: 'API Call';
    }

    protected static function newFactory()
    {
        return \Modules\Shipper\Database\factories\ApiLogFactory::new();
    }
} 