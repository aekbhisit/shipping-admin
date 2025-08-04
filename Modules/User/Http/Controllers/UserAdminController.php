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

class UserAdminController extends Controller
{
    protected $userRepository;
    protected $userService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        
        // Apply middleware
        $this->middleware(['auth', 'role:company_admin']);
    }

    /**
     * Display a listing of all users with DataTable and advanced filtering
     *
     * @param Request $request
     * @return Renderable|JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = User::getUserTypes();
        
        $stats = [
            'total_users' => User::active()->count(),
            'company_admins' => User::companyAdmins()->active()->count(),
            'branch_admins' => User::branchAdmins()->active()->count(),
            'branch_staff' => User::branchStaff()->active()->count(),
        ];

        return view('user::admin.users.index', compact('branches', 'userTypes', 'stats'));
    }

    /**
     * Get DataTable data for AJAX requests
     */
    private function getDataTableData(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'branch_id' => $request->get('branch_id'),
            'user_type' => $request->get('user_type'),
            'status' => $request->get('status'),
            'created_from' => $request->get('created_from'),
            'created_to' => $request->get('created_to'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Format data to avoid serialization issues
        $formattedData = collect($users->items())->map(function($user, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->getUserTypeDisplayAttribute(),
                'branch_name' => $user->branch ? $user->branch->name : 'ไม่ระบุ',
                'status_display' => $user->getStatusDisplayAttribute(),
                'last_branch_activity' => $user->last_branch_activity ? $user->last_branch_activity->format('d/m/Y H:i') : '',
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'actions' => $this->generateActionButtons($user, 'company'),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for all users
     */
    public function datatable_ajax(Request $request): JsonResponse
    {
        return $this->getDataTableData($request);
    }

    /**
     * Show the form for creating a new user
     *
     * @return Renderable
     */
    public function create()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = User::getUserTypes();

        return view('user::admin.users.create', compact('branches', 'userTypes'));
    }

    /**
     * Store a newly created user
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => ['required', 'string', 'in:' . implode(',', array_keys(User::getUserTypes()))],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'is_active' => ['boolean'],
        ]);

        // Business logic: Company Admin can be without branch, others require branch
        if ($validated['user_type'] !== User::USER_TYPE_COMPANY_ADMIN && empty($validated['branch_id'])) {
            return back()->withErrors(['branch_id' => 'Branch assignment is required for this user type.']);
        }

        // Company Admin cannot have branch assignment
        if ($validated['user_type'] === User::USER_TYPE_COMPANY_ADMIN) {
            $validated['branch_id'] = null;
        }

        DB::beginTransaction();
        
        try {
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->boolean('is_active', true);
            
            $user = $this->userService->createUserWithBranch($validated);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['general' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified user with branch activity summary
     *
     * @param int $id
     * @return Renderable
     */
    public function show(int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        $activitySummary = $this->userRepository->getUserActivitySummary($id);
        $accessibleBranches = $user->getAccessibleBranches();
        
        // Get recent activity logs if audit module exists
        $recentActivity = collect();
        if (class_exists(\Modules\Audit\Entities\UserActivityLog::class)) {
            $recentActivity = $user->activityLogs()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('user::admin.users.show', compact(
            'user', 
            'activitySummary', 
            'accessibleBranches',
            'recentActivity'
        ));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param int $id
     * @return Renderable
     */
    public function edit(int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent editing own role/branch
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot edit your own user record.']);
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = User::getUserTypes();

        return view('user::admin.users.edit', compact('user', 'branches', 'userTypes'));
    }

    /**
     * Update the specified user
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent editing own record
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot edit your own user record.']);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'user_type' => ['required', 'string', 'in:' . implode(',', array_keys(User::getUserTypes()))],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'is_active' => ['boolean'],
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        // Business logic validation
        if ($validated['user_type'] !== User::USER_TYPE_COMPANY_ADMIN && empty($validated['branch_id'])) {
            return back()->withErrors(['branch_id' => 'Branch assignment is required for this user type.']);
        }

        if ($validated['user_type'] === User::USER_TYPE_COMPANY_ADMIN) {
            $validated['branch_id'] = null;
        }

        DB::beginTransaction();
        
        try {
            // Hash password if provided
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->boolean('is_active', true);
            
            $updatedUser = $this->userRepository->update($id, $validated);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->withErrors(['general' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    /**
     * Soft delete the specified user (deactivate)
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Prevent deleting own record
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot delete your own user record.']);
        }

        // Prevent deleting last company admin
        if ($user->isCompanyAdmin()) {
            $companyAdminCount = User::companyAdmins()->active()->count();
            if ($companyAdminCount <= 1) {
                return back()->withErrors(['general' => 'Cannot delete the last company administrator.']);
            }
        }

        DB::beginTransaction();
        
        try {
            $success = $this->userRepository->softDeleteUser($id, Auth::id());
            
            if ($success) {
                DB::commit();
                return redirect()->route('admin.users.index')
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
     * Assign user to specific branch
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function assignBranch(Request $request, int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        // Business logic: Company Admin cannot be assigned to branch
        if ($user->isCompanyAdmin()) {
            return back()->withErrors(['general' => 'Company administrators cannot be assigned to specific branches.']);
        }

        DB::beginTransaction();
        
        try {
            $success = $this->userService->assignUserToBranch($id, $validated['branch_id']);
            
            if ($success) {
                DB::commit();
                return back()->with('success', 'User assigned to branch successfully.');
            } else {
                DB::rollBack();
                return back()->withErrors(['general' => 'Failed to assign user to branch.']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['general' => 'Failed to assign user to branch: ' . $e->getMessage()]);
        }
    }

    /**
     * Change user role/permissions
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function changeRole(Request $request, int $id)
    {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        $validated = $request->validate([
            'user_type' => ['required', 'string', 'in:' . implode(',', array_keys(User::getUserTypes()))],
        ]);

        // Prevent changing own role
        if ($user->id === Auth::id()) {
            return back()->withErrors(['general' => 'You cannot change your own role.']);
        }

        DB::beginTransaction();
        
        try {
            $updatedUser = $this->userRepository->update($id, $validated);
            
            DB::commit();
            
            return back()->with('success', 'User role changed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['general' => 'Failed to change user role: ' . $e->getMessage()]);
        }
    }

    /**
     * Get sort column from DataTable request
     */
    private function getSortColumn($order): string
    {
        $columns = ['name', 'email', 'user_type', 'branch_id', 'created_at', 'last_branch_activity'];
        
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

    /**
     * Export users data
     */
    public function export(Request $request)
    {
        $filters = [
            'branch_id' => $request->get('branch_id'),
            'user_type' => $request->get('user_type'),
            'status' => $request->get('status'),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Simple CSV export
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['ID', 'Name', 'Email', 'User Type', 'Branch', 'Status', 'Last Activity', 'Created At']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->user_type_display,
                    $user->branch_name ?? 'No Branch',
                    $user->status_display,
                    $user->last_branch_activity ? $user->last_branch_activity->format('Y-m-d H:i:s') : 'Never',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ============================== Company Admin Management Methods ============================== //
    
    /**
     * Display company admin users only
     */
    public function companyIndex(Request $request)
    {
        if ($request->ajax()) {
            return $this->getCompanyDataTableData($request);
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_COMPANY_ADMIN => 'Company Administrator'];
        
        $stats = [
            'total_company_admins' => User::companyAdmins()->active()->count(),
        ];

        return view('user::admin.users.company.index', compact('branches', 'userTypes', 'stats'));
    }

    /**
     * Get DataTable data for company admins only
     */
    private function getCompanyDataTableData(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'user_type' => User::USER_TYPE_COMPANY_ADMIN, // Force company admin filter
            'status' => $request->get('status'),
            'created_from' => $request->get('created_from'),
            'created_to' => $request->get('created_to'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Format data to avoid serialization issues
        $formattedData = collect($users->items())->map(function($user, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->getUserTypeDisplayAttribute(),
                'branch_name' => $user->branch ? $user->branch->name : 'ไม่ระบุ',
                'status_display' => $user->getStatusDisplayAttribute(),
                'last_branch_activity' => $user->last_branch_activity ? $user->last_branch_activity->format('d/m/Y H:i') : '',
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'actions' => $this->generateActionButtons($user, 'company'),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for company admins
     */
    public function companyDatatableAjax(Request $request): JsonResponse
    {
        return $this->getCompanyDataTableData($request);
    }

    public function companyForm($id = 0)
    {
        $mode = 'add';
        $user = [];

        if (!empty($id)) {
            $user = User::find($id);
            if (!$user || $user->user_type !== User::USER_TYPE_COMPANY_ADMIN) {
                abort(404, 'Company admin not found');
            }
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_COMPANY_ADMIN => 'Company Administrator'];

        return view('user::admin.users.company.form', compact('user', 'branches', 'userTypes', 'mode'));
    }

    public function companySave(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $request->get('id'),
            'password' => $request->get('id') ? 'nullable|min:6' : 'required|min:6',
            'status' => 'boolean',
        ]);

        $validated['user_type'] = User::USER_TYPE_COMPANY_ADMIN;
        $validated['branch_id'] = null; // Company admins don't have branches

        return $this->saveUser($validated, $request);
    }

    public function companySetStatus(Request $request)
    {
        return $this->setUserStatus($request, User::USER_TYPE_COMPANY_ADMIN);
    }

    public function companySetDelete(Request $request)
    {
        return $this->setUserDelete($request, User::USER_TYPE_COMPANY_ADMIN);
    }

    // ============================== Branch Admin Management Methods ============================== //
    
    /**
     * Display branch admin users only
     */
    public function branchIndex(Request $request)
    {
        if ($request->ajax()) {
            return $this->getBranchDataTableData($request);
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_BRANCH_ADMIN => 'Branch Administrator'];
        
        $stats = [
            'total_branch_admins' => User::branchAdmins()->active()->count(),
        ];

        return view('user::admin.users.branch.index', compact('branches', 'userTypes', 'stats'));
    }

    /**
     * Get DataTable data for branch admins only
     */
    private function getBranchDataTableData(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'user_type' => User::USER_TYPE_BRANCH_ADMIN, // Force branch admin filter
            'branch_id' => $request->get('branch_id'),
            'status' => $request->get('status'),
            'created_from' => $request->get('created_from'),
            'created_to' => $request->get('created_to'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Format data to avoid serialization issues
        $formattedData = collect($users->items())->map(function($user, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->getUserTypeDisplayAttribute(),
                'branch_name' => $user->branch ? $user->branch->name : 'ไม่ระบุ',
                'status_display' => $user->getStatusDisplayAttribute(),
                'last_branch_activity' => $user->last_branch_activity ? $user->last_branch_activity->format('d/m/Y H:i') : '',
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'actions' => $this->generateActionButtons($user, 'branch'),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for branch admins
     */
    public function branchDatatableAjax(Request $request): JsonResponse
    {
        return $this->getBranchDataTableData($request);
    }

    public function branchForm($id = 0)
    {
        $mode = 'add';
        $user = [];

        if (!empty($id)) {
            $user = User::find($id);
            if (!$user || $user->user_type !== User::USER_TYPE_BRANCH_ADMIN) {
                abort(404, 'Branch admin not found');
            }
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_BRANCH_ADMIN => 'Branch Administrator'];

        return view('user::admin.users.branch.form', compact('user', 'branches', 'userTypes', 'mode'));
    }

    public function branchSave(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $request->get('id'),
            'password' => $request->get('id') ? 'nullable|min:6' : 'required|min:6',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'boolean',
        ]);

        $validated['user_type'] = User::USER_TYPE_BRANCH_ADMIN;

        return $this->saveUser($validated, $request);
    }

    public function branchSetStatus(Request $request)
    {
        return $this->setUserStatus($request, User::USER_TYPE_BRANCH_ADMIN);
    }

    public function branchSetDelete(Request $request)
    {
        return $this->setUserDelete($request, User::USER_TYPE_BRANCH_ADMIN);
    }

    // ============================== Branch Staff Management Methods ============================== //
    
    /**
     * Display branch staff users only
     */
    public function staffIndex(Request $request)
    {
        if ($request->ajax()) {
            return $this->getStaffDataTableData($request);
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_BRANCH_STAFF => 'Branch Staff'];
        
        $stats = [
            'total_branch_staff' => User::branchStaff()->active()->count(),
        ];

        return view('user::admin.users.staff.index', compact('branches', 'userTypes', 'stats'));
    }

    /**
     * Get DataTable data for branch staff only
     */
    private function getStaffDataTableData(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'user_type' => User::USER_TYPE_BRANCH_STAFF, // Force branch staff filter
            'branch_id' => $request->get('branch_id'),
            'status' => $request->get('status'),
            'created_from' => $request->get('created_from'),
            'created_to' => $request->get('created_to'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        $users = $this->userRepository->getUsersWithPagination($filters);

        // Format data to avoid serialization issues
        $formattedData = collect($users->items())->map(function($user, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->getUserTypeDisplayAttribute(),
                'branch_name' => $user->branch ? $user->branch->name : 'ไม่ระบุ',
                'status_display' => $user->getStatusDisplayAttribute(),
                'last_branch_activity' => $user->last_branch_activity ? $user->last_branch_activity->format('d/m/Y H:i') : '',
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '',
                'actions' => $this->generateActionButtons($user, 'staff'),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for branch staff
     */
    public function staffDatatableAjax(Request $request): JsonResponse
    {
        return $this->getStaffDataTableData($request);
    }

    public function staffForm($id = 0)
    {
        $mode = 'add';
        $user = [];

        if (!empty($id)) {
            $user = User::find($id);
            if (!$user || $user->user_type !== User::USER_TYPE_BRANCH_STAFF) {
                abort(404, 'Branch staff not found');
            }
        }

        $branches = Branch::active()->orderBy('name')->get();
        $userTypes = [User::USER_TYPE_BRANCH_STAFF => 'Branch Staff'];

        return view('user::admin.users.staff.form', compact('user', 'branches', 'userTypes', 'mode'));
    }

    public function staffSave(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $request->get('id'),
            'password' => $request->get('id') ? 'nullable|min:6' : 'required|min:6',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'boolean',
        ]);

        $validated['user_type'] = User::USER_TYPE_BRANCH_STAFF;

        return $this->saveUser($validated, $request);
    }

    public function staffSetStatus(Request $request)
    {
        return $this->setUserStatus($request, User::USER_TYPE_BRANCH_STAFF);
    }

    public function staffSetDelete(Request $request)
    {
        return $this->setUserDelete($request, User::USER_TYPE_BRANCH_STAFF);
    }

    // ============================== Helper Methods ============================== //
    
    /**
     * Generic user save method
     */
    private function saveUser($validated, Request $request)
    {
        DB::beginTransaction();
        
        try {
            if (!empty($request->get('id'))) {
                $user = User::find($request->get('id'));
                if (!$user) {
                    abort(404, 'User not found');
                }
                
                // Hash password if provided
                if (!empty($validated['password'])) {
                    $validated['password'] = Hash::make($validated['password']);
                } else {
                    unset($validated['password']);
                }
                
                $user->update($validated);
                $message = 'User updated successfully';
            } else {
                $validated['password'] = Hash::make($validated['password']);
                User::create($validated);
                $message = 'User created successfully';
            }

            DB::commit();
            return response()->json(['success' => 1, 'msg' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => 0, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Generic user status update method
     */
    private function setUserStatus(Request $request, $userType)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            $user = User::find($id);
            if (!$user || $user->user_type !== $userType) {
                return response()->json(['success' => 0, 'msg' => 'User not found']);
            }

            $user->status = $status;
            if ($user->save()) {
                return response()->json(['success' => 1, 'msg' => 'Status updated successfully']);
            } else {
                return response()->json(['success' => 0, 'msg' => 'Failed to update status']);
            }
        }
    }

    /**
     * Generic user delete method
     */
    private function setUserDelete(Request $request, $userType)
    {
        if ($request->ajax()) {
            $id = $request->get('id');

            $user = User::find($id);
            if (!$user || $user->user_type !== $userType) {
                return response()->json(['success' => 0, 'msg' => 'User not found']);
            }

            if ($user->delete()) {
                return response()->json(['success' => 1, 'msg' => 'User deleted successfully']);
            } else {
                return response()->json(['success' => 0, 'msg' => 'Failed to delete user']);
            }
        }
    }

    /**
     * Generate action buttons HTML for DataTable
     */
    private function generateActionButtons($user, $type = 'company')
    {
        if ($type === 'branch') {
            $editRoute = route('admin.user.branch.edit', ['id' => $user->id]);
            $deleteRoute = route('admin.user.branch.set_delete');
            $statusRoute = route('admin.user.branch.set_status');
        } elseif ($type === 'staff') {
            $editRoute = route('admin.user.staff.edit', ['id' => $user->id]);
            $deleteRoute = route('admin.user.staff.set_delete');
            $statusRoute = route('admin.user.staff.set_status');
        } else {
            $editRoute = route('admin.user.company.edit', ['id' => $user->id]);
            $deleteRoute = route('admin.user.company.set_delete');
            $statusRoute = route('admin.user.company.set_status');
        }
        $csrf = csrf_token();
        $statusBtn = '';
        if ($user->status) {
            if ($type === 'staff') {
                $statusBtn = '<button type="button" class="btn btn-sm btn-success me-1" onclick="toggleStaffUserStatus(' . $user->id . ', 0)"><i class="lni lni-checkmark"></i> Active</button>';
            } else {
                $statusBtn = '<button type="button" class="btn btn-sm btn-success me-1" onclick="toggleBranchUserStatus(' . $user->id . ', 0)"><i class="lni lni-checkmark"></i> Active</button>';
            }
        } else {
            if ($type === 'staff') {
                $statusBtn = '<button type="button" class="btn btn-sm btn-danger me-1" onclick="toggleStaffUserStatus(' . $user->id . ', 1)"><i class="lni lni-close"></i> Inactive</button>';
            } else {
                $statusBtn = '<button type="button" class="btn btn-sm btn-danger me-1" onclick="toggleBranchUserStatus(' . $user->id . ', 1)"><i class="lni lni-close"></i> Inactive</button>';
            }
        }
        if ($type === 'branch') {
            return $statusBtn .
                '<a href="' . $editRoute . '" class="btn btn-sm btn-warning me-1"><i class="lni lni-pencil"></i> Edit</a>' .
                '<button type="button" class="btn btn-sm btn-danger" onclick="deleteBranchUser(' . $user->id . ')"><i class="lni lni-trash"></i> Delete</button>';
        } elseif ($type === 'staff') {
            return $statusBtn .
                '<a href="' . $editRoute . '" class="btn btn-sm btn-warning me-1"><i class="lni lni-pencil"></i> Edit</a>' .
                '<button type="button" class="btn btn-sm btn-danger" onclick="deleteStaffUser(' . $user->id . ')"><i class="lni lni-trash"></i> Delete</button>';
        } else {
            return $statusBtn .
                '<a href="' . $editRoute . '" class="btn btn-sm btn-warning me-1"><i class="lni lni-pencil"></i> Edit</a>' .
                '<button type="button" class="btn btn-sm btn-danger" onclick="deleteCompanyUser(' . $user->id . ')"><i class="lni lni-trash"></i> Delete</button>';
        }
    }
}
