<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userType = session('user_type');
        if (!$userType) {
            // Not logged in or session expired
            return redirect()->route('admin.login');
        }

        // Support comma-separated roles (e.g. role:company_admin,branch_admin)
        $roles = count($roles) === 1 && strpos($roles[0], ',') !== false
            ? explode(',', $roles[0])
            : $roles;

        if (!in_array($userType, $roles)) {
            // Option 1: Redirect to a not permitted page
            if (\Route::has('admin.not.permitted')) {
                return redirect()->route('admin.not.permitted');
            }
            // Option 2: Abort with 403
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
} 