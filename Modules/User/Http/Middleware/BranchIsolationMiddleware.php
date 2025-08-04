<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Services\BranchAccessService;

class BranchIsolationMiddleware
{
    protected $branchAccessService;

    public function __construct(BranchAccessService $branchAccessService)
    {
        $this->branchAccessService = $branchAccessService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user requires branch isolation
        if ($user->requiresBranchIsolation()) {
            $this->enforceBranchIsolation($request, $user);
        }

        // Refresh branch context if needed
        $this->branchAccessService->refreshBranchContext($user);

        // Set branch context for the request
        $this->setBranchContextForRequest($request, $user);

        return $next($request);
    }

    /**
     * Enforce branch isolation for branch-scoped users
     *
     * @param Request $request
     * @param $user
     * @return void
     */
    protected function enforceBranchIsolation(Request $request, $user): void
    {
        // Ensure user has branch assignment
        if (!$user->branch_id) {
            abort(403, 'No branch assigned to your account. Please contact system administrator.');
        }

        // Check if request includes branch parameter that doesn't match user's branch
        $requestedBranchId = $this->extractBranchIdFromRequest($request);
        
        if ($requestedBranchId && !$user->canAccessBranch($requestedBranchId)) {
            abort(403, 'Access denied. You can only access data from your assigned branch.');
        }

        // For branch admin and staff, automatically scope to their branch
        $this->applyCursorScopeToRequest($request, $user);
    }

    /**
     * Set branch context for the current request
     *
     * @param Request $request
     * @param $user
     * @return void
     */
    protected function setBranchContextForRequest(Request $request, $user): void
    {
        // Get current branch context or use user's primary branch
        $branchId = $this->branchAccessService->getCurrentBranchContext() ?? $user->getPrimaryBranchId();

        // Add branch context to request for use in controllers
        $request->attributes->set('branch_context', [
            'branch_id' => $branchId,
            'user_type' => $user->user_type,
            'requires_isolation' => $user->requiresBranchIsolation(),
            'accessible_branches' => $user->getAccessibleBranches()->pluck('id')->toArray(),
        ]);

        // Share branch context with views
        view()->share('branchContext', [
            'branch_id' => $branchId,
            'branch_name' => $user->branch?->name ?? 'All Branches',
            'user_type' => $user->user_type,
            'can_switch' => $user->isCompanyAdmin(),
            'accessible_branches' => $user->getAccessibleBranches(),
        ]);
    }

    /**
     * Extract branch ID from request parameters
     *
     * @param Request $request
     * @return int|null
     */
    protected function extractBranchIdFromRequest(Request $request): ?int
    {
        // Check various possible parameter names
        $branchId = $request->get('branch_id') 
                 ?? $request->get('branch') 
                 ?? $request->route('branch_id') 
                 ?? $request->route('branch');

        return $branchId ? (int) $branchId : null;
    }

    /**
     * Apply automatic scoping to request for branch-isolated users
     *
     * @param Request $request
     * @param $user
     * @return void
     */
    protected function applyCursorScopeToRequest(Request $request, $user): void
    {
        // For non-company admin users, automatically add branch scope to requests
        if (!$user->isCompanyAdmin() && $user->branch_id) {
            // Add branch_id to request parameters if not already present
            if (!$request->has('branch_id')) {
                $request->merge(['branch_id' => $user->branch_id]);
            }

            // Override any branch_id parameter to ensure isolation
            if ($request->get('branch_id') != $user->branch_id) {
                $request->merge(['branch_id' => $user->branch_id]);
            }
        }
    }

    /**
     * Log access attempt for audit trail
     *
     * @param Request $request
     * @param $user
     * @param bool $granted
     * @return void
     */
    protected function logAccessAttempt(Request $request, $user, bool $granted): void
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'branch_access_attempt',
                'description' => $granted ? 'Branch access granted' : 'Branch access denied',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'requested_branch' => $this->extractBranchIdFromRequest($request),
                    'user_branch' => $user->branch_id,
                    'access_granted' => $granted,
                    'timestamp' => now(),
                ],
                'risk_level' => $granted ? 'low' : 'medium',
                'branch_id' => $user->branch_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log branch access attempt: ' . $e->getMessage());
        }
    }

    /**
     * Check if route requires special branch handling
     *
     * @param Request $request
     * @return bool
     */
    protected function requiresBranchValidation(Request $request): bool
    {
        $route = $request->route();
        
        if (!$route) {
            return false;
        }

        // Check if route has branch-related parameters
        $branchRoutes = [
            'branch.users.*',
            'admin.branch.*',
            '*.branch.*',
        ];

        $routeName = $route->getName();
        
        foreach ($branchRoutes as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate branch access for specific route actions
     *
     * @param Request $request
     * @param $user
     * @return bool
     */
    protected function validateRouteAccess(Request $request, $user): bool
    {
        $route = $request->route();
        
        if (!$route) {
            return true;
        }

        // Get route parameters
        $routeParams = $route->parameters();
        
        // Check if route has a branch-related parameter
        if (isset($routeParams['branch_id'])) {
            $branchId = (int) $routeParams['branch_id'];
            return $user->canAccessBranch($branchId);
        }

        // Check if route affects branch-scoped resources
        if (isset($routeParams['id'])) {
            return $this->validateResourceAccess($request, $user, $routeParams['id']);
        }

        return true;
    }

    /**
     * Validate access to specific resource
     *
     * @param Request $request
     * @param $user
     * @param $resourceId
     * @return bool
     */
    protected function validateResourceAccess(Request $request, $user, $resourceId): bool
    {
        // This method can be extended to validate access to specific resources
        // based on the route and resource type
        
        $route = $request->route();
        $routeName = $route->getName();

        // Example: validate user resource access
        if (str_contains($routeName, 'users.')) {
            return $this->validateUserResourceAccess($user, $resourceId);
        }

        // Add more resource validation as needed
        return true;
    }

    /**
     * Validate access to user resource
     *
     * @param $currentUser
     * @param $targetUserId
     * @return bool
     */
    protected function validateUserResourceAccess($currentUser, $targetUserId): bool
    {
        if ($currentUser->isCompanyAdmin()) {
            return true; // Company admin can access all users
        }

        // For branch admin/staff, check if target user is in same branch
        $targetUser = \Modules\User\Entities\User::find($targetUserId);
        
        if (!$targetUser) {
            return false;
        }

        return $currentUser->branch_id === $targetUser->branch_id;
    }
} 