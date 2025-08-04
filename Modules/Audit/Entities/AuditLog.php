<?php

namespace Modules\Audit\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

/**
 * AuditLog Model
 * Purpose: All data modifications tracking with polymorphic relationships
 */
class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event_type',
        'old_values',
        'new_values',
        'changed_fields',
        'user_id',
        'branch_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'notes'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'created_at' => 'datetime'
    ];

    // Disable updated_at since audit logs are immutable
    public $timestamps = false;
    
    protected $dates = ['created_at'];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Polymorphic relationship to any auditable model
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * User who made the change
     */
    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\User::class);
    }

    /**
     * Branch context where change occurred
     */
    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class);
    }

    // ========================================
    // BUSINESS METHODS
    // ========================================

    /**
     * Get the model name from auditable_type
     */
    public function getModelName(): string
    {
        return class_basename($this->auditable_type);
    }

    /**
     * Get count of changed fields
     */
    public function getChangedFieldsCount(): int
    {
        return count($this->changed_fields ?? []);
    }

    /**
     * Check if audit log has old values
     */
    public function hasOldValues(): bool
    {
        return !empty($this->old_values);
    }

    /**
     * Check if audit log has new values
     */
    public function hasNewValues(): bool
    {
        return !empty($this->new_values);
    }

    /**
     * Get formatted changes for display
     */
    public function getFormattedChanges(): array
    {
        $changes = [];
        
        if (empty($this->changed_fields)) {
            return $changes;
        }

        foreach ($this->changed_fields as $field) {
            $oldValue = data_get($this->old_values, $field);
            $newValue = data_get($this->new_values, $field);
            
            $changes[] = [
                'field' => $field,
                'old_value' => $this->formatValue($oldValue),
                'new_value' => $this->formatValue($newValue),
                'is_sensitive' => $this->isSensitiveField($field)
            ];
        }

        return $changes;
    }

    /**
     * Check if this audit log contains sensitive data
     */
    public function isSensitiveData(): bool
    {
        $sensitiveFields = ['password', 'api_key', 'credit_card', 'ssn', 'token'];
        
        if (empty($this->changed_fields)) {
            return false;
        }

        foreach ($this->changed_fields as $field) {
            if ($this->isSensitiveField($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get activity description
     */
    public function getActivityDescription(): string
    {
        $modelName = $this->getModelName();
        $userName = $this->user?->name ?? 'System';
        
        switch ($this->event_type) {
            case 'created':
                return "{$userName} created a new {$modelName} record";
            case 'updated':
                return "{$userName} updated {$modelName} record (changed " . $this->getChangedFieldsCount() . " fields)";
            case 'deleted':
                return "{$userName} deleted {$modelName} record";
            default:
                return "{$userName} performed {$this->event_type} action on {$modelName}";
        }
    }

    /**
     * Get summary of changes
     */
    public function getChangesSummary(): string
    {
        if (empty($this->changed_fields)) {
            return 'No specific fields tracked';
        }

        $fieldCount = count($this->changed_fields);
        $fieldNames = implode(', ', array_slice($this->changed_fields, 0, 3));
        
        if ($fieldCount > 3) {
            $fieldNames .= " and " . ($fieldCount - 3) . " more";
        }

        return "Changed: {$fieldNames}";
    }

    /**
     * Get context information
     */
    public function getContextInfo(): array
    {
        return [
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'url' => $this->url,
            'method' => $this->method,
            'branch' => $this->branch?->name,
            'user' => $this->user?->name,
            'timestamp' => $this->created_at->format('Y-m-d H:i:s')
        ];
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope by model type
     */
    public function scopeByModel($query, string $modelType)
    {
        return $query->where('auditable_type', $modelType);
    }

    /**
     * Scope by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope by date range
     */
    public function scopeByDateRange($query, Carbon $from, Carbon $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope by event type
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for today's logs
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for sensitive data
     */
    public function scopeSensitiveData($query)
    {
        $sensitiveFields = ['password', 'api_key', 'credit_card', 'ssn', 'token'];
        
        return $query->where(function($q) use ($sensitiveFields) {
            foreach ($sensitiveFields as $field) {
                $q->orWhereJsonContains('changed_fields', $field);
            }
        });
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i:s');
    }

    /**
     * Get event type badge class
     */
    public function getEventBadgeAttribute(): string
    {
        switch ($this->event_type) {
            case 'created':
                return 'badge-success';
            case 'updated':
                return 'badge-warning';
            case 'deleted':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Get model icon for display
     */
    public function getModelIconAttribute(): string
    {
        $modelName = $this->getModelName();
        
        $icons = [
            'User' => 'bi-person',
            'Branch' => 'bi-building',
            'Customer' => 'bi-people',
            'Product' => 'bi-box',
            'Shipment' => 'bi-truck',
            'Carrier' => 'bi-airplane'
        ];

        return $icons[$modelName] ?? 'bi-file-text';
    }

    /**
     * Get user name with fallback
     */
    public function getUserNameAttribute(): string
    {
        return $this->user?->name ?? 'System User';
    }

    /**
     * Get branch name with fallback
     */
    public function getBranchNameAttribute(): string
    {
        return $this->branch?->name ?? 'No Branch';
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Format value for display
     */
    private function formatValue($value): string
    {
        if (is_null($value)) {
            return '(null)';
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        if (is_string($value) && strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }
        
        return (string) $value;
    }

    /**
     * Check if field is sensitive
     */
    private function isSensitiveField(string $field): bool
    {
        $sensitiveFields = [
            'password', 'password_hash', 'remember_token',
            'api_key', 'api_token', 'secret_key',
            'credit_card', 'card_number', 'cvv',
            'ssn', 'social_security', 'tax_id'
        ];

        return in_array(strtolower($field), $sensitiveFields);
    }

    /**
     * Static method to log data changes
     */
    public static function logChange(Model $model, string $eventType, array $oldValues = [], array $newValues = []): void
    {
        $changedFields = [];
        
        if ($eventType === 'updated') {
            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
        } elseif ($eventType === 'created') {
            $changedFields = array_keys($newValues);
        } elseif ($eventType === 'deleted') {
            $changedFields = array_keys($oldValues);
        }

        static::create([
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'event_type' => $eventType,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'changed_fields' => $changedFields,
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()?->branch_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'created_at' => now()
        ]);
    }
} 