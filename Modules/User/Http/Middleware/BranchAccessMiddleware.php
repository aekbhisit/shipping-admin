<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Services\BranchAccessService;

class BranchAccessMiddleware
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
     * @param  string|null  $branchParam
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $branchParam = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Get branch ID from request
        $branchId = $this->extractBranchId($request, $branchParam);

        if ($branchId) {
            // Verify user can access the requested branch
            if (!$this->branchAccessService->canUserAccessBranch($user, $branchId)) {
                return $this->handleUnauthorizedAccess($request, $user, $branchId);
            }

            // Set branch context for the request
            $this->branchAccessService->setBranchContext($user, $branchId);
        }

        // Log branch access
        $this->logBranchAccess($request, $user, $branchId);

        return $next($request);
    }

    /**
     * Extract branch ID from request
     *
     * @param Request $request
     * @param string|null $branchParam
     * @return int|null
     */
    protected function extractBranchId(Request $request, ?string $branchParam = null): ?int
    {
        // Use custom parameter name if specified
        if ($branchParam && $request->has($branchParam)) {
            return (int) $request->get($branchParam);
        }

        // Try common parameter names
        $branchId = $request->get('branch_id') 
                 ?? $request->get('branch') 
                 ?? $request->route('branch_id') 
                 ?? $request->route('branch')
                 ?? $request->header('X-Branch-ID');

        return $branchId ? (int) $branchId : null;
    }

    /**
     * Handle unauthorized branch access attempt
     *
     * @param Request $request
     * @param $user
     * @param int $branchId
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthorizedAccess(Request $request, $user, int $branchId)
    {
        // Log unauthorized access attempt
        $this->logUnauthorizedAccess($request, $user, $branchId);

        // Return appropriate response based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Unauthorized branch access',
                'message' => 'You do not have permission to access this branch.',
                'branch_id' => $branchId,
                'user_branches' => $user->getAccessibleBranches()->pluck('id')->toArray(),
            ], 403);
        }

        // For web requests, redirect with error
        return redirect()->back()
            ->withErrors(['branch_access' => 'You do not have permission to access this branch.'])
            ->withInput();
    }

    /**
     * Log branch access for audit trail
     *
     * @param Request $request
     * @param $user
     * @param int|null $branchId
     * @return void
     */
    protected function logBranchAccess(Request $request, $user, ?int $branchId): void
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'branch_access',
                'description' => 'Branch access requested',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'requested_branch' => $branchId,
                    'user_branch' => $user->branch_id,
                    'user_type' => $user->user_type,
                    'access_granted' => true,
                    'timestamp' => now(),
                ],
                'risk_level' => 'low',
                'branch_id' => $branchId ?? $user->branch_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log branch access: ' . $e->getMessage());
        }
    }

    /**
     * Log unauthorized access attempt
     *
     * @param Request $request
     * @param $user
     * @param int $branchId
     * @return void
     */
    protected function logUnauthorizedAccess(Request $request, $user, int $branchId): void
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return;
        }

        try {
            \Modules\Audit\Entities\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'unauthorized_branch_access',
                'description' => 'Unauthorized branch access attempt',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'attempted_branch' => $branchId,
                    'user_branch' => $user->branch_id,
                    'user_type' => $user->user_type,
                    'access_granted' => false,
                    'timestamp' => now(),
                ],
                'risk_level' => 'high',
                'branch_id' => $user->branch_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log unauthorized branch access: ' . $e->getMessage());
        }
    }

    /**
     * Validate branch exists and is active
     *
     * @param int $branchId
     * @return bool
     */
    protected function validateBranchExists(int $branchId): bool
    {
        if (!class_exists(\Modules\Branch\Entities\Branch::class)) {
            return false;
        }

        return \Modules\Branch\Entities\Branch::active()->where('id', $branchId)->exists();
    }

    /**
     * Get user's current branch context
     *
     * @param $user
     * @return array
     */
    protected function getUserBranchContext($user): array
    {
        return [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'primary_branch' => $user->branch_id,
            'accessible_branches' => $user->getAccessibleBranches()->pluck('id')->toArray(),
            'current_context' => $this->branchAccessService->getCurrentBranchContext(),
            'requires_isolation' => $user->requiresBranchIsolation(),
        ];
    }

    /**
     * Set branch information in request attributes
     *
     * @param Request $request
     * @param $user
     * @param int|null $branchId
     * @return void
     */
    protected function setBranchAttributes(Request $request, $user, ?int $branchId): void
    {
        $branchContext = $this->getUserBranchContext($user);
        $branchContext['current_branch'] = $branchId;

        $request->attributes->set('branch_context', $branchContext);
        $request->attributes->set('current_branch_id', $branchId);
    }

    /**
     * Check if request requires branch context
     *
     * @param Request $request
     * @return bool
     */
    protected function requiresBranchContext(Request $request): bool
    {
        $route = $request->route();
        
        if (!$route) {
            return false;
        }

        // Routes that always require branch context
        $branchRequiredRoutes = [
            'admin.branch.*',
            '*.branch.*',
            'branch.*',
        ];

        $routeName = $route->getName();
        
        foreach ($branchRequiredRoutes as $pattern) {
            if ($routeName && fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        // Check if route has branch parameters
        $branchParams = ['branch_id', 'branch'];
        foreach ($branchParams as $param) {
            if ($request->has($param) || $route->hasParameter($param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle branch switching for company admin
     *
     * @param Request $request
     * @param $user
     * @param int $newBranchId
     * @return bool
     */
    protected function handleBranchSwitching(Request $request, $user, int $newBranchId): bool
    {
        // Only company admin can switch branches
        if (!$user->isCompanyAdmin()) {
            return false;
        }

        // Validate target branch
        if (!$this->validateBranchExists($newBranchId)) {
            return false;
        }

        try {
            $this->branchAccessService->switchBranchContext($user, $newBranchId);
            
            // Log branch switch
            $this->logBranchSwitch($request, $user, $newBranchId);
            
            return true;
        } catch (\Exception $e) {
            \Log::warning('Failed to switch branch context: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log branch switch activity
     *
     * @param Request $request
     * @param $user
     * @param int $newBranchId
     * @return void
     */
    protected function logBranchSwitch(Request $request, $user, int $newBranchId): void
    {
        if (!class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            return;
        }

        try {
            $oldBranchId = $this->branchAccessService->getCurrentBranchContext();
            
            \Modules\Audit\Entities\UserActivityLog::create([
                'user_id' => $user->id,
                'activity_type' => 'branch_switch',
                'description' => 'Branch context switched',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'url' => $request->fullUrl(),
                    'old_branch_id' => $oldBranchId,
                    'new_branch_id' => $newBranchId,
                    'user_type' => $user->user_type,
                    'timestamp' => now(),
                ],
                'risk_level' => 'low',
                'branch_id' => $newBranchId,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log branch switch: ' . $e->getMessage());
        }
    }
} 