<?php

namespace Modules\User\Services;

use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class BranchAccessService
{
    /**
     * Check if user can access a specific branch
     *
     * @param User $user
     * @param int $branchId
     * @return bool
     */
    public function canUserAccessBranch(User $user, int $branchId): bool
    {
        // Use the User model's built-in method
        return $user->canAccessBranch($branchId);
    }

    /**
     * Get branches accessible by the user
     *
     * @param User $user
     * @return Collection
     */
    public function getUserAccessibleBranches(User $user): Collection
    {
        return $user->getAccessibleBranches();
    }

    /**
     * Set branch context in session for the user
     *
     * @param User $user
     * @param int|null $branchId
     * @return void
     */
    public function setBranchContext(User $user, ?int $branchId): void
    {
        // Validate branch access
        if ($branchId && !$this->canUserAccessBranch($user, $branchId)) {
            throw new \Exception('User does not have access to the specified branch');
        }

        // Set branch context in session
        Session::put('branch_context', [
            'branch_id' => $branchId,
            'branch_name' => $branchId ? $this->getBranchName($branchId) : null,
            'user_id' => $user->id,
            'set_at' => now(),
        ]);

        // Log branch access for audit trail
        $this->logBranchAccess($user, $branchId);
    }

    /**
     * Get current branch context from session
     *
     * @return int|null
     */
    public function getCurrentBranchContext(): ?int
    {
        $context = Session::get('branch_context');
        
        if (!$context || !isset($context['branch_id'])) {
            return null;
        }

        // Verify context is still valid (not expired)
        if (isset($context['set_at'])) {
            $setAt = \Carbon\Carbon::parse($context['set_at']);
            if ($setAt->diffInHours(now()) > 24) {
                // Context expired, clear it
                $this->clearBranchContext();
                return null;
            }
        }

        return $context['branch_id'];
    }

    /**
     * Clear branch context from session
     *
     * @return void
     */
    public function clearBranchContext(): void
    {
        Session::forget('branch_context');
    }

    /**
     * Get branch context information
     *
     * @return array|null
     */
    public function getBranchContextInfo(): ?array
    {
        $context = Session::get('branch_context');
        
        if (!$context) {
            return null;
        }

        return [
            'branch_id' => $context['branch_id'] ?? null,
            'branch_name' => $context['branch_name'] ?? null,
            'set_at' => $context['set_at'] ?? null,
            'is_valid' => $this->isContextValid($context),
        ];
    }

    /**
     * Switch branch context for company admin
     *
     * @param User $user
     * @param int $branchId
     * @return bool
     */
    public function switchBranchContext(User $user, int $branchId): bool
    {
        // Only company admin can switch branch context
        if (!$user->isCompanyAdmin()) {
            throw new \Exception('Only company administrators can switch branch context');
        }

        // Validate branch exists and is active
        $branch = \Modules\Branch\Entities\Branch::active()->find($branchId);
        if (!$branch) {
            throw new \Exception('Branch not found or inactive');
        }

        // Set new branch context
        $this->setBranchContext($user, $branchId);

        return true;
    }

    /**
     * Get branch isolation rules for current user
     *
     * @param User|null $user
     * @return array
     */
    public function getBranchIsolationRules(?User $user = null): array
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return [
                'isolated' => true,
                'accessible_branches' => [],
                'current_branch' => null,
                'can_switch' => false,
            ];
        }

        $accessibleBranches = $this->getUserAccessibleBranches($user);

        return [
            'isolated' => $user->requiresBranchIsolation(),
            'accessible_branches' => $accessibleBranches->pluck('id')->toArray(),
            'current_branch' => $this->getCurrentBranchContext() ?? $user->getPrimaryBranchId(),
            'can_switch' => $user->isCompanyAdmin(),
            'user_type' => $user->user_type,
            'primary_branch' => $user->getPrimaryBranchId(),
        ];
    }

    /**
     * Apply branch scope to query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User|null $user
     * @param string $branchColumn
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyBranchScope($query, ?User $user = null, string $branchColumn = 'branch_id')
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            // No user context, return empty result
            return $query->whereRaw('1 = 0');
        }

        // Company admin can see all
        if ($user->isCompanyAdmin()) {
            $currentBranch = $this->getCurrentBranchContext();
            if ($currentBranch) {
                // Company admin has selected a specific branch
                return $query->where($branchColumn, $currentBranch);
            }
            // No branch filter for company admin
            return $query;
        }

        // Branch-scoped users can only see their branch
        if ($user->branch_id) {
            return $query->where($branchColumn, $user->branch_id);
        }

        // User without branch cannot see anything
        return $query->whereRaw('1 = 0');
    }

    /**
     * Validate branch access for current request
     *
     * @param int $branchId
     * @param User|null $user
     * @return bool
     * @throws \Exception
     */
    public function validateBranchAccess(int $branchId, ?User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            throw new \Exception('Authentication required');
        }

        if (!$this->canUserAccessBranch($user, $branchId)) {
            throw new \Exception('Access denied to the specified branch');
        }

        return true;
    }

    /**
     * Get branch statistics for accessible branches
     *
     * @param User|null $user
     * @return array
     */
    public function getBranchStatistics(?User $user = null): array
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return [];
        }

        $accessibleBranches = $this->getUserAccessibleBranches($user);
        $statistics = [];

        foreach ($accessibleBranches as $branch) {
            $cacheKey = "branch_stats_{$branch->id}";
            
            $stats = Cache::remember($cacheKey, 1800, function () use ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'user_count' => User::byBranch($branch->id)->active()->count(),
                    'admin_count' => User::branchAdmins()->byBranch($branch->id)->active()->count(),
                    'staff_count' => User::branchStaff()->byBranch($branch->id)->active()->count(),
                    'last_activity' => User::byBranch($branch->id)
                        ->whereNotNull('last_branch_activity')
                        ->max('last_branch_activity'),
                ];
            });

            $statistics[$branch->id] = $stats;
        }

        return $statistics;
    }

    /**
     * Check if user session needs branch context refresh
     *
     * @param User $user
     * @return bool
     */
    public function needsContextRefresh(User $user): bool
    {
        $context = Session::get('branch_context');
        
        if (!$context) {
            return true;
        }

        // Check if context belongs to current user
        if (isset($context['user_id']) && $context['user_id'] !== $user->id) {
            return true;
        }

        // Check if context is expired
        if (!$this->isContextValid($context)) {
            return true;
        }

        // Check if user's branch assignment has changed
        if (!$user->isCompanyAdmin() && 
            isset($context['branch_id']) && 
            $context['branch_id'] !== $user->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * Refresh branch context for user
     *
     * @param User $user
     * @return void
     */
    public function refreshBranchContext(User $user): void
    {
        if ($this->needsContextRefresh($user)) {
            $this->setBranchContext($user, $user->getPrimaryBranchId());
        }
    }

    /**
     * Get branch name by ID
     *
     * @param int $branchId
     * @return string|null
     */
    protected function getBranchName(int $branchId): ?string
    {
        $cacheKey = "branch_name_{$branchId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($branchId) {
            $branch = \Modules\Branch\Entities\Branch::find($branchId);
            return $branch?->name;
        });
    }

    /**
     * Check if context is valid (not expired)
     *
     * @param array $context
     * @return bool
     */
    protected function isContextValid(array $context): bool
    {
        if (!isset($context['set_at'])) {
            return false;
        }

        $setAt = \Carbon\Carbon::parse($context['set_at']);
        return $setAt->diffInHours(now()) <= 24;
    }

    /**
     * Log branch access for audit trail
     *
     * @param User $user
     * @param int|null $branchId
     * @return void
     */
    protected function logBranchAccess(User $user, ?int $branchId): void
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'branch_access',
                'description' => 'Branch context set',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => Session::getId(),
                'metadata' => [
                    'branch_id' => $branchId,
                    'branch_name' => $branchId ? $this->getBranchName($branchId) : null,
                    'timestamp' => now(),
                ],
                'risk_level' => 'low',
                'branch_id' => $branchId,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log branch access: ' . $e->getMessage());
        }
    }

    /**
     * Get middleware data for branch isolation
     *
     * @param User $user
     * @return array
     */
    public function getMiddlewareData(User $user): array
    {
        return [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'requires_isolation' => $user->requiresBranchIsolation(),
            'primary_branch_id' => $user->getPrimaryBranchId(),
            'accessible_branches' => $this->getUserAccessibleBranches($user)->pluck('id')->toArray(),
            'current_context' => $this->getCurrentBranchContext(),
        ];
    }
} 