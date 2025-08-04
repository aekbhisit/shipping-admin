<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\User\Entities\Users;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the login form for branch users
     * Route: GET / (root route redirects to /admin which comes here)
     */
    public function showLoginForm()
    {
        return view('user::user.login');
    }

    /**
     * Show the admin login form
     * Route: GET /admin
     */
    public function showAdminLoginForm()
    {
        return view('user::user.login');
    }

    /**
     * Handle login request
     * Route: POST /login or POST /admin/check_login
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return back()->withInput()->withErrors($validator);
        }

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'status' => 1, // Only active users
        ];

        // Attempt authentication using admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::guard('admin')->user();
            
            // Store user info in session for easy access
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->role_name ?? $user->user_type ?? 'user',
                'user_role_id' => $user->role_id,
                'user_type' => $user->user_type,
            ]);

            if ($request->expectsJson()) {
                // Use intended redirect if available, otherwise go to dashboard
                $redirectUrl = session()->pull('url.intended', '/admin/dashboard');
                
                // Ensure the redirect URL is absolute to prevent double prefix issues
                if (!str_starts_with($redirectUrl, 'http')) {
                    $redirectUrl = url($redirectUrl);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $redirectUrl
                ]);
            }

            // For non-AJAX requests, use Laravel's intended redirect
            return redirect()->intended('/admin/dashboard');
        }

        // Authentication failed
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials or account not active'
            ], 401);
        }

        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    /**
     * Handle AJAX login check (legacy support)
     * Route: POST /admin/check_login
     */
    public function checkLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username and password are required',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = [
                'username' => $request->username,
                'password' => $request->password,
                'status' => 1,
            ];

            if (Auth::guard('admin')->attempt($credentials)) {
                $user = Auth::guard('admin')->user();
                
                // Store session data
                session([
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role_name ?? $user->user_type ?? 'user',
                    'user_role_id' => $user->role_id,
                    'user_type' => $user->user_type,
                ]);

                // Use intended redirect if available, otherwise go to dashboard
                $redirectUrl = session()->pull('url.intended', '/admin/dashboard');
                
                // Ensure the redirect URL is absolute to prevent double prefix issues
                if (!str_starts_with($redirectUrl, 'http')) {
                    $redirectUrl = url($redirectUrl);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $redirectUrl,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role_name ?? $user->user_type ?? 'user'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log the user out
     * Route: POST /logout or GET /admin/logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }

        return redirect('/admin');
    }

    /**
     * Get the post-login redirect path
     */
    protected function redirectPath(): string
    {
        $user = Auth::guard('admin')->user();
        
        if (!$user) {
            return '/admin';
        }

        // Role-based redirection using user_type
        $userType = $user->user_type ?? 'user';
        
        switch ($userType) {
            case 'company_admin':
                return '/admin/dashboard';
            case 'branch_admin':
                return '/admin/dashboard';
            case 'branch_staff':
                return '/admin/dashboard';
            default:
                return '/admin/dashboard';
        }
    }

    /**
     * Show password reset form
     */
    public function showLinkRequestForm()
    {
        return view('user::auth.passwords.email');
    }
} 