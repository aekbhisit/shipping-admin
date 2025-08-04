<?php

namespace Modules\User\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'user_type',
        'shipping_permissions',
        'last_branch_activity',
        'is_active',
        'deactivated_at',
        'deactivated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'shipping_permissions' => 'array',
        'last_branch_activity' => 'datetime',
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User types enum values
     */
    const USER_TYPE_COMPANY_ADMIN = 'company_admin';
    const USER_TYPE_BRANCH_ADMIN = 'branch_admin';
    const USER_TYPE_BRANCH_STAFF = 'branch_staff';

    /**
     * Get all available user types
     *
     * @return array
     */
    public static function getUserTypes(): array
    {
        return [
            self::USER_TYPE_COMPANY_ADMIN => 'Company Administrator',
            self::USER_TYPE_BRANCH_ADMIN => 'Branch Administrator',
            self::USER_TYPE_BRANCH_STAFF => 'Branch Staff',
        ];
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-assign default user type if not set
        static::creating(function ($user) {
            if (empty($user->user_type)) {
                $user->user_type = self::USER_TYPE_BRANCH_STAFF;
            }
        });
    }

    // ===================================================================
    // RELATIONSHIPS
    // ===================================================================

    /**
     * Get the branch that owns the user (nullable for company admin)
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id');
    }

    /**
     * Get the user who deactivated this user
     */
    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    /**
     * Get users deactivated by this user
     */
    public function deactivatedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'deactivated_by');
    }

    /**
     * Get audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(\Modules\Audit\Entities\AuditLog::class, 'user_id');
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(\Modules\Audit\Entities\UserActivityLog::class, 'user_id');
    }

    // ===================================================================
    // QUERY SCOPES
    // ===================================================================

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by branch
     */
    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to filter by user type
     */
    public function scopeByUserType($query, string $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope a query to only include company admins
     */
    public function scopeCompanyAdmins($query)
    {
        return $query->where('user_type', self::USER_TYPE_COMPANY_ADMIN);
    }

    /**
     * Scope a query to only include branch admins
     */
    public function scopeBranchAdmins($query)
    {
        return $query->where('user_type', self::USER_TYPE_BRANCH_ADMIN);
    }

    /**
     * Scope a query to only include branch staff
     */
    public function scopeBranchStaff($query)
    {
        return $query->where('user_type', self::USER_TYPE_BRANCH_STAFF);
    }

    /**
     * Scope a query to include users accessible by the given user
     */
    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->isCompanyAdmin()) {
            return $query; // Company admin can see all users
        }
        
        return $query->where('branch_id', $user->branch_id);
    }

    /**
     * Scope a query to include users with recent activity
     */
    public function scopeWithRecentActivity($query, int $days = 30)
    {
        $since = Carbon::now()->subDays($days);
        return $query->where('last_branch_activity', '>=', $since);
    }

    // ===================================================================
    // BUSINESS METHODS
    // ===================================================================

    /**
     * Check if user can access a specific branch
     */
    public function canAccessBranch(int $branchId): bool
    {
        // Company admin can access all branches
        if ($this->isCompanyAdmin()) {
            return true;
        }

        // Others can only access their assigned branch
        return $this->branch_id === $branchId;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasShippingPermission(string $permission): bool
    {
        // Check Laravel permission system first
        if ($this->can($permission)) {
            return true;
        }

        // Check shipping-specific permissions JSON field
        $permissions = $this->shipping_permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if user is company admin
     */
    public function isCompanyAdmin(): bool
    {
        return $this->user_type === self::USER_TYPE_COMPANY_ADMIN;
    }

    /**
     * Check if user is branch admin
     */
    public function isBranchAdmin(): bool
    {
        return $this->user_type === self::USER_TYPE_BRANCH_ADMIN;
    }

    /**
     * Check if user is branch staff
     */
    public function isBranchStaff(): bool
    {
        return $this->user_type === self::USER_TYPE_BRANCH_STAFF;
    }

    /**
     * Get user's display name with role
     */
    public function getDisplayNameWithRole(): string
    {
        $userTypes = self::getUserTypes();
        $roleDisplay = $userTypes[$this->user_type] ?? $this->user_type;
        
        return "{$this->name} ({$roleDisplay})";
    }

    /**
     * Get branch activity summary for this user
     */
    public function getBranchActivitySummary(): array
    {
        $cacheKey = "user_activity_summary_{$this->id}";
        
        return Cache::remember($cacheKey, 3600, function () {
            $summary = [
                'total_logins' => 0,
                'last_login' => null,
                'shipments_created' => 0,
                'customers_managed' => 0,
                'last_activity' => $this->last_branch_activity,
            ];

            // Get login activity from activity logs
            if (class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
                $loginStats = $this->activityLogs()
                    ->where('activity_type', 'login')
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->selectRaw('COUNT(*) as total, MAX(created_at) as last_login')
                    ->first();

                if ($loginStats) {
                    $summary['total_logins'] = $loginStats->total;
                    $summary['last_login'] = $loginStats->last_login;
                }
            }

            // Additional activity metrics would be added here based on other modules
            
            return $summary;
        });
    }

    /**
     * Update last branch activity timestamp
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_branch_activity' => now()]);
    }

    /**
     * Deactivate user (soft delete)
     */
    public function deactivate(User $deactivatedBy): bool
    {
        return $this->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivated_by' => $deactivatedBy->id,
        ]);
    }

    /**
     * Reactivate user
     */
    public function reactivate(): bool
    {
        return $this->update([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivated_by' => null,
        ]);
    }

    /**
     * Get accessible branches for this user
     */
    public function getAccessibleBranches()
    {
        if ($this->isCompanyAdmin()) {
            return \Modules\Branch\Entities\Branch::active()->get();
        }

        return collect([$this->branch])->filter();
    }

    /**
     * Check if user can manage another user
     */
    public function canManageUser(User $targetUser): bool
    {
        // Company admin can manage all users
        if ($this->isCompanyAdmin()) {
            return true;
        }

        // Branch admin can manage users in same branch (except company admins)
        if ($this->isBranchAdmin()) {
            return !$targetUser->isCompanyAdmin() && 
                   $this->branch_id === $targetUser->branch_id;
        }

        // Branch staff cannot manage users
        return false;
    }

    /**
     * Get user dashboard route based on role
     */
    public function getDashboardRoute(): string
    {
        return match ($this->user_type) {
            self::USER_TYPE_COMPANY_ADMIN => '/admin/dashboard',
            self::USER_TYPE_BRANCH_ADMIN => '/admin/branch/dashboard',
            self::USER_TYPE_BRANCH_STAFF => '/staff/dashboard',
            default => '/dashboard',
        };
    }

    /**
     * Check if user's session should be isolated to their branch
     */
    public function requiresBranchIsolation(): bool
    {
        return !$this->isCompanyAdmin();
    }

    /**
     * Get user's primary branch context
     */
    public function getPrimaryBranchId(): ?int
    {
        return $this->branch_id;
    }

    // ===================================================================
    // ATTRIBUTE ACCESSORS & MUTATORS
    // ===================================================================

    /**
     * Get the user's formatted user type
     */
    public function getUserTypeDisplayAttribute(): string
    {
        return self::getUserTypes()[$this->user_type] ?? $this->user_type;
    }

    /**
     * Get the user's status display
     */
    public function getStatusDisplayAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        return 'Active';
    }

    /**
     * Get the user's branch name (if assigned)
     */
    public function getBranchNameAttribute(): ?string
    {
        return $this->branch?->name;
    }
} 