<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Modules\User\Services\UserService;
use Modules\Branch\Entities\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class BranchUserAdminController extends Controller
{
    protected $userRepository;
    protected $userService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        
        // Apply middleware for branch isolation
        $this->middleware(['auth', 'role:branch_admin']);
    }

    /**
     * Display a listing of users in current branch only with DataTable
     *
     * @param Request $request
     * @return Renderable|JsonResponse
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->branch_id) {
            abort(403, 'No branch assigned to your account.');
        }

        if ($request->ajax()) {
            return $this->getDataTableData($request, $currentUser);
        }

        $userTypes = [
            User::USER_TYPE_BRANCH_ADMIN => 'Branch Administrator',
            User::USER_TYPE_BRANCH_STAFF => 'Branch Staff',
        ];
        
        $stats = [
            'total_users' => User::byBranch($currentUser->branch_id)->active()->count(),
            'branch_admins' => User::branchAdmins()->byBranch($currentUser->branch_id)->active()->count(),
            'branch_staff' => User::branchStaff()->byBranch($currentUser->branch_id)->active()->count(),
        ];

        $branchName = $currentUser->branch->name ?? 'Unknown Branch';

        return view('user::branch.users.index', compact('userTypes', 'stats', 'branchName'));
    }

    /**
     * Get DataTable data for AJAX requests (branch-scoped)
     */
    private function getDataTableData(Request $request, User $currentUser): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'branch_id' => $currentUser->branch_id, // Force branch scope
            'user_type' => $request->get('user_type'),
            'status' => $request->get('status'),
            'created_from' => $request->get('created_from'),
            'created_to' => $request->get('created_to'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $users->items(),
        ]);
    }

    /**
     * Show the form for creating a new branch staff
     *
     * @return Renderable
     */
    public function create()
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->branch_id) {
            abort(403, 'No branch assigned to your account.');
        }

        // Branch admin can only create branch staff
        $userTypes = [
            User::USER_TYPE_BRANCH_STAFF => 'Branch Staff',
        ];

        $currentBranch = $currentUser->branch;

        return view('user::branch.users.create', compact('userTypes', 'currentBranch'));
    }

    /**
     * Store a newly created branch staff
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->branch_id) {
            abort(403, 'No branch assigned to your account.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => ['required', 'string', 'in:' . User::USER_TYPE_BRANCH_STAFF],
            'is_active' => ['boolean'],
        ]);

        DB::beginTransaction();
        
        try {
            // Auto-assign to current branch
            $validated['branch_id'] = $currentUser->branch_id;
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->boolean('is_active', true);
            
            $user = $this->userService->createUserWithBranch($validated);

            DB::commit();

            return redirect()->route('admin.branch.users.index')
                ->with('success', 'Branch staff created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['general' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified user (branch-scoped)
     *
     * @param int $id
     * @return Renderable
     */
    public function show(int $id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Ensure user belongs to same branch
        if ($user->branch_id !== $currentUser->branch_id) {
            abort(403, 'Access denied. User not in your branch.');
        }

        $activitySummary = $this->userRepository->getUserActivitySummary($id);
        
        // Get recent activity logs if audit module exists
        $recentActivity = collect();
        if (class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            $recentActivity = $user->activityLogs()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('user::branch.users.show', compact(
            'user', 
            'activitySummary',
            'recentActivity'
        ));
    }

    /**
     * Show the form for editing the specified user (branch-scoped)
     *
     * @param int $id
     * @return Renderable
     */
    public function edit(int $id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Ensure user belongs to same branch
        if ($user->branch_id !== $currentUser->branch_id) {
            abort(403, 'Access denied. User not in your branch.');
        }

        // Prevent editing own record
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot edit your own user record.']);
        }

        // Branch admin can only edit branch staff
        $userTypes = [
            User::USER_TYPE_BRANCH_STAFF => 'Branch Staff',
        ];

        $currentBranch = $currentUser->branch;

        return view('user::branch.users.edit', compact('user', 'userTypes', 'currentBranch'));
    }

    /**
     * Update the specified user (branch-scoped)
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Ensure user belongs to same branch
        if ($user->branch_id !== $currentUser->branch_id) {
            abort(403, 'Access denied. User not in your branch.');
        }

        // Prevent editing own record
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot edit your own user record.']);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'user_type' => ['required', 'string', 'in:' . User::USER_TYPE_BRANCH_STAFF],
            'is_active' => ['boolean'],
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        
        try {
            // Hash password if provided
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->boolean('is_active', true);
            
            // Ensure branch cannot be changed
            $validated['branch_id'] = $currentUser->branch_id;
            
            $updatedUser = $this->userRepository->update($id, $validated);

            DB::commit();

            return redirect()->route('admin.branch.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['general' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    /**
     * Deactivate the specified user (soft delete, branch-scoped)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $currentUser = Auth::user();
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Ensure user belongs to same branch
        if ($user->branch_id !== $currentUser->branch_id) {
            abort(403, 'Access denied. User not in your branch.');
        }

        // Prevent deleting own record
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot delete your own user record.']);
        }

        // Branch admin cannot delete company admin or other branch admins
        if ($user->isCompanyAdmin() || $user->isBranchAdmin()) {
            return back()->withErrors(['general' => 'You do not have permission to delete this user.']);
        }

        DB::beginTransaction();
        
        try {
            $success = $this->userRepository->softDeleteUser($id, Auth::id());
            
            if ($success) {
                DB::commit();
                return redirect()->route('admin.branch.users.index')
                    ->with('success', 'User deactivated successfully.');
            } else {
                DB::rollBack();
                return back()->withErrors(['general' => 'Failed to deactivate user.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['general' => 'Failed to deactivate user: ' . $e->getMessage()]);
        }
    }

    /**
     * Get branch activity summary for current branch
     */
    public function getBranchSummary()
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->branch_id) {
            abort(403, 'No branch assigned to your account.');
        }

        $summary = $this->userRepository->getBranchActivitySummary($currentUser->branch_id);
        
        return response()->json($summary);
    }

    /**
     * Export branch users data
     */
    public function export(Request $request)
    {
        $currentUser = Auth::user();
        
        if (!$currentUser->branch_id) {
            abort(403, 'No branch assigned to your account.');
        }

        $filters = [
            'branch_id' => $currentUser->branch_id, // Force branch scope
            'user_type' => $request->get('user_type'),
            'status' => $request->get('status'),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Simple CSV export
        $filename = 'branch_users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'User Type', 'Status', 'Last Activity', 'Created At']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->user_type_display,
                    $user->status_display,
                    $user->last_branch_activity ? $user->last_branch_activity->format('Y-m-d H:i:s') : 'Never',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get sort column from DataTable request
     */
    private function getSortColumn($order): string
    {
        $columns = ['name', 'email', 'user_type', 'created_at', 'last_branch_activity'];
        
        if (empty($order) || !isset($order[0]['column'])) {
            return 'name';
        }

        $columnIndex = intval($order[0]['column']);
        return $columns[$columnIndex] ?? 'name';
    }

    /**
     * Get sort direction from DataTable request
     */
    private function getSortDirection($order): string
    {
        if (empty($order) || !isset($order[0]['dir'])) {
            return 'asc';
        }

        return in_array($order[0]['dir'], ['asc', 'desc']) ? $order[0]['dir'] : 'asc';
    }
} 