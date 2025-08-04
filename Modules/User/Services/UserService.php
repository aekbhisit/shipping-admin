<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create user with branch assignment and proper permissions
     *
     * @param array $userData
     * @return User
     * @throws \Exception
     */
    public function createUserWithBranch(array $userData): User
    {
        DB::beginTransaction();
        
        try {
            // Validate business rules
            $this->validateUserCreation($userData);
            
            // Create the user
            $user = $this->userRepository->create($userData);
            
            // Sync default permissions based on user type
            $this->syncUserPermissions($user);
            
            // Log creation activity
            $this->logUserCreation($user);
            
            DB::commit();
            
            return $user;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign user to specific branch with validation
     *
     * @param int $userId
     * @param int $branchId
     * @return bool
     * @throws \Exception
     */
    public function assignUserToBranch(int $userId, int $branchId): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user) {
            throw new \Exception('User not found');
        }

        // Validate branch assignment business rules
        $this->validateBranchAssignment($user, $branchId);
        
        DB::beginTransaction();
        
        try {
            $updated = $this->userRepository->update($userId, ['branch_id' => $branchId]);
            
            // Clear user activity cache
            $this->clearUserCache($userId);
            
            // Log branch assignment
            $this->logBranchAssignment($user, $branchId);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Deactivate user with proper audit trail
     *
     * @param int $userId
     * @param int $deactivatedBy
     * @return bool
     * @throws \Exception
     */
    public function deactivateUser(int $userId, int $deactivatedBy): bool
    {
        $user = $this->userRepository->findById($userId);
        $deactivator = $this->userRepository->findById($deactivatedBy);
        
        if (!$user || !$deactivator) {
            throw new \Exception('User or deactivator not found');
        }

        // Validate deactivation permissions
        if (!$deactivator->canManageUser($user)) {
            throw new \Exception('Insufficient permissions to deactivate this user');
        }

        // Prevent deactivating last company admin
        if ($user->isCompanyAdmin()) {
            $activeAdminCount = User::companyAdmins()->active()->count();
            if ($activeAdminCount <= 1) {
                throw new \Exception('Cannot deactivate the last company administrator');
            }
        }

        DB::beginTransaction();
        
        try {
            $success = $this->userRepository->softDeleteUser($userId, $deactivatedBy);
            
            // Clear user cache
            $this->clearUserCache($userId);
            
            // Log deactivation
            $this->logUserDeactivation($user, $deactivator);
            
            DB::commit();
            
            return $success;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get comprehensive user activity summary
     *
     * @param int $userId
     * @return array
     */
    public function getUserActivitySummary(int $userId): array
    {
        $cacheKey = "user_activity_summary_{$userId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            $user = $this->userRepository->findById($userId);
            
            if (!$user) {
                return [];
            }

            $summary = [
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_type' => $user->user_type,
                'branch_id' => $user->branch_id,
                'branch_name' => $user->branch?->name,
                'is_active' => $user->is_active,
                'last_activity' => $user->last_branch_activity,
                'created_at' => $user->created_at,
            ];

            // Get activity statistics
            $activityStats = $this->calculateActivityStatistics($user);
            $summary = array_merge($summary, $activityStats);

            // Get permissions summary
            $summary['permissions'] = $this->getUserPermissionsSummary($user);

            // Get recent activities
            $summary['recent_activities'] = $this->getRecentActivities($user);

            return $summary;
        });
    }

    /**
     * Validate branch assignment business rules
     *
     * @param User $user
     * @param int $branchId
     * @return bool
     * @throws \Exception
     */
    public function validateBranchAssignment(User $user, int $branchId): bool
    {
        // Company admin cannot be assigned to branches
        if ($user->isCompanyAdmin()) {
            throw new \Exception('Company administrators cannot be assigned to specific branches');
        }

        // Check if branch exists and is active
        $branch = \Modules\Branch\Entities\Branch::active()->find($branchId);
        if (!$branch) {
            throw new \Exception('Branch not found or inactive');
        }

        // Users are permanently assigned to branches (no transfers)
        if ($user->branch_id && $user->branch_id !== $branchId) {
            throw new \Exception('Users cannot be transferred between branches');
        }

        return true;
    }

    /**
     * Sync user permissions based on user type and branch
     *
     * @param User $user
     * @return void
     */
    public function syncUserPermissions(User $user): void
    {
        // This is handled in the User model's syncDefaultPermissions method
        $user->syncDefaultPermissions();
        
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Clear user cache
        $this->clearUserCache($user->id);
    }

    /**
     * Validate user creation business rules
     *
     * @param array $userData
     * @throws \Exception
     */
    protected function validateUserCreation(array $userData): void
    {
        $userType = $userData['user_type'] ?? User::USER_TYPE_BRANCH_STAFF;
        $branchId = $userData['branch_id'] ?? null;

        // Company admin cannot have branch assignment
        if ($userType === User::USER_TYPE_COMPANY_ADMIN && $branchId) {
            throw new \Exception('Company administrators cannot be assigned to specific branches');
        }

        // Non-company admin must have branch assignment
        if ($userType !== User::USER_TYPE_COMPANY_ADMIN && !$branchId) {
            throw new \Exception('Branch assignment is required for this user type');
        }

        // Validate branch exists if provided
        if ($branchId) {
            $branch = \Modules\Branch\Entities\Branch::active()->find($branchId);
            if (!$branch) {
                throw new \Exception('Selected branch not found or inactive');
            }
        }
    }

    /**
     * Calculate activity statistics for user
     *
     * @param User $user
     * @return array
     */
    protected function calculateActivityStatistics(User $user): array
    {
        $stats = [
            'total_logins' => 0,
            'last_login' => null,
            'failed_attempts' => 0,
            'days_since_last_activity' => null,
            'activity_score' => 0,
        ];

        // Calculate days since last activity
        if ($user->last_branch_activity) {
            $stats['days_since_last_activity'] = Carbon::now()->diffInDays($user->last_branch_activity);
        }

        // Get activity logs if audit module exists
        if (class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            $thirtyDaysAgo = Carbon::now()->subDays(30);

            // Login statistics
            $loginStats = $user->activityLogs()
                ->where('activity_type', 'login')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('COUNT(*) as total, MAX(created_at) as last_login')
                ->first();

            if ($loginStats) {
                $stats['total_logins'] = $loginStats->total;
                $stats['last_login'] = $loginStats->last_login;
            }

            // Failed login attempts
            $stats['failed_attempts'] = $user->activityLogs()
                ->where('activity_type', 'failed_login')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count();

            // Calculate activity score (0-100)
            $stats['activity_score'] = $this->calculateActivityScore($user, $stats);
        }

        return $stats;
    }

    /**
     * Calculate user activity score (0-100)
     *
     * @param User $user
     * @param array $stats
     * @return int
     */
    protected function calculateActivityScore(User $user, array $stats): int
    {
        $score = 0;

        // Base score for active user
        if ($user->is_active) {
            $score += 20;
        }

        // Score for recent logins
        if ($stats['total_logins'] > 0) {
            $score += min(30, $stats['total_logins'] * 2);
        }

        // Score for recent activity
        if ($stats['days_since_last_activity'] !== null) {
            if ($stats['days_since_last_activity'] <= 1) {
                $score += 30;
            } elseif ($stats['days_since_last_activity'] <= 7) {
                $score += 20;
            } elseif ($stats['days_since_last_activity'] <= 30) {
                $score += 10;
            }
        }

        // Penalty for failed attempts
        if ($stats['failed_attempts'] > 0) {
            $score -= min(20, $stats['failed_attempts'] * 5);
        }

        return max(0, min(100, $score));
    }

    /**
     * Get user permissions summary
     *
     * @param User $user
     * @return array
     */
    protected function getUserPermissionsSummary(User $user): array
    {
        // Since we removed Spatie, return basic user type info
        return [
            'total_permissions' => 0,
            'roles' => [$user->user_type],
            'key_permissions' => [],
        ];
    }

    /**
     * Get recent activities for user
     *
     * @param User $user
     * @return array
     */
    protected function getRecentActivities(User $user): array
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return [];
        }

        return $user->activityLogs()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'type' => $activity->activity_type,
                    'description' => $activity->description,
                    'timestamp' => $activity->created_at,
                    'ip_address' => $activity->ip_address,
                ];
            })
            ->toArray();
    }

    /**
     * Clear user-related cache
     *
     * @param int $userId
     */
    protected function clearUserCache(int $userId): void
    {
        Cache::forget("user_activity_summary_{$userId}");
        Cache::forget("user_permissions_{$userId}");
    }

    /**
     * Log user creation activity
     *
     * @param User $user
     */
    protected function logUserCreation(User $user): void
    {
        if (!class_exists(\Modules\Audit\Entities\AuditLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\AuditLog::create([
                'user_id' => auth()->id(),
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'event_type' => 'created',
                'old_values' => null,
                'new_values' => $user->toArray(),
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'branch_id' => $user->branch_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log user creation: ' . $e->getMessage());
        }
    }

    /**
     * Log branch assignment activity
     *
     * @param User $user
     * @param int $branchId
     */
    protected function logBranchAssignment(User $user, int $branchId): void
    {
        if (!class_exists(\Modules\Audit\Entities\AuditLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\AuditLog::create([
                'user_id' => auth()->id(),
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'event_type' => 'branch_assigned',
                'old_values' => ['branch_id' => $user->branch_id],
                'new_values' => ['branch_id' => $branchId],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'branch_id' => $branchId,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log branch assignment: ' . $e->getMessage());
        }
    }

    /**
     * Log user deactivation activity
     *
     * @param User $user
     * @param User $deactivator
     */
    protected function logUserDeactivation(User $user, User $deactivator): void
    {
        if (!class_exists(\Modules\Audit\Entities\AuditLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\AuditLog::create([
                'user_id' => $deactivator->id,
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'event_type' => 'deactivated',
                'old_values' => ['is_active' => true],
                'new_values' => [
                    'is_active' => false,
                    'deactivated_by' => $deactivator->id,
                    'deactivated_at' => now()
                ],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'branch_id' => $user->branch_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log user deactivation: ' . $e->getMessage());
        }
    }

    /**
     * Bulk operations for user management
     */
    public function bulkUpdateUsers(array $userIds, array $updates): int
    {
        DB::beginTransaction();
        
        try {
            $count = 0;
            
            foreach ($userIds as $userId) {
                $user = $this->userRepository->findById($userId);
                if ($user) {
                    $this->userRepository->update($userId, $updates);
                    $this->clearUserCache($userId);
                    $count++;
                }
            }
            
            DB::commit();
            
            return $count;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 