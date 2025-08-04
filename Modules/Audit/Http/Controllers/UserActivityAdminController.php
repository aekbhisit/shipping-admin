<?php

namespace Modules\Audit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Audit\Services\UserActivityService;
use Modules\Audit\Entities\UserActivityLog;
use Modules\User\Entities\User;

/**
 * UserActivityAdminController
 * Purpose: User activity tracking and monitoring
 * Access Level: Company Admin and Branch Admin
 */
class UserActivityAdminController extends Controller
{
    protected UserActivityService $userActivityService;

    public function __construct(UserActivityService $userActivityService)
    {
        $this->middleware(['auth:admin', 'adminAccessControl']);
        $this->userActivityService = $userActivityService;
    }

    /**
     * Display user activity logs with DataTable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        // Get filter options for dropdowns
        $filterOptions = $this->getFilterOptions();

        // Get summary statistics
        $statistics = $this->userActivityService->getActivityStatistics();

        return view('audit::admin.user-activity.index', compact(
            'filterOptions',
            'statistics'
        ));
    }

    /**
     * Get DataTable data for AJAX requests
     */
    private function getDataTableData(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search')['value'] ?? '',
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'user_id' => $request->get('user_id'),
            'action' => $request->get('action'),
            'module' => $request->get('module'),
            'branch_id' => $request->get('branch_id'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $userActivities = $this->userActivityService->getUserActivities($filters, $filters['per_page'] ?? 15);

        // Format data to avoid serialization issues
        $formattedData = collect($userActivities->items())->map(function($activity, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $activity->id,
                'user_name' => $activity->user_name ?? 'System',
                'action' => ucfirst($activity->action),
                'module' => $activity->module ?? 'N/A',
                'description' => $activity->description,
                'ip_address' => $activity->ip_address ?? 'N/A',
                'created_at' => $activity->created_at ? $activity->created_at->format('d/m/Y H:i:s') : '',
                'actions' => $this->generateActionButtons($activity),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $userActivities->total(),
            'recordsFiltered' => $userActivities->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for user activities
     */
    public function datatable_ajax(Request $request): JsonResponse
    {
        return $this->getDataTableData($request);
    }

    /**
     * Display specific user activity details
     */
    public function show(Request $request, $id)
    {
        $userActivity = UserActivityLog::with(['user', 'branch'])->findOrFail($id);

        // Check if user has access to this activity log (branch scope)
        if (auth()->user()->role === 'branch_admin' && 
            $userActivity->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to activity log');
        }

        // Get related activities for the same user
        $relatedActivities = UserActivityLog::where('user_id', $userActivity->user_id)
            ->where('id', '!=', $userActivity->id)
            ->with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('audit::admin.user-activity.show', compact(
            'userActivity',
            'relatedActivities'
        ));
    }

    /**
     * Display user activity timeline
     * Purpose: Show chronological user activities
     */
    public function timeline(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        
        // Check if user has access to this user's activities (branch scope)
        if (auth()->user()->role === 'branch_admin' && 
            $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to user activities');
        }

        $activities = UserActivityLog::where('user_id', $userId)
            ->with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('audit::admin.user-activity.timeline', compact(
            'user',
            'activities',
            'userId'
        ));
    }

    /**
     * Display failed login attempts
     * Purpose: Security monitoring
     */
    public function failedAttempts(Request $request)
    {
        $failedAttempts = $this->userActivityService->getFailedLoginAttempts([
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'username' => $request->get('username'),
            'ip_address' => $request->get('ip_address')
        ], 20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $failedAttempts,
                'total' => $failedAttempts->total()
            ]);
        }

        return view('audit::admin.user-activity.failed-attempts', compact('failedAttempts'));
    }

    /**
     * Clear failed login attempts
     * Purpose: Security maintenance
     */
    public function clearFailedAttempts(Request $request)
    {
        // Only allow company admins to clear failed attempts
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $result = $this->userActivityService->clearFailedAttempts();

            return response()->json([
                'success' => true,
                'message' => 'Failed attempts cleared successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to clear attempts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export user activities
     * Formats: CSV, Excel, PDF
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $filters = $request->only(['date_from', 'date_to', 'user_id', 'action', 'module']);

        // Validate format
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            return response()->json(['error' => 'Invalid export format'], 400);
        }

        try {
            $exportData = $this->userActivityService->exportUserActivities($filters, $format);
            
            $filename = 'user_activities_' . date('Y-m-d_H-i-s') . '.' . $format;
            
            return response()->download($exportData, $filename, [
                'Content-Type' => $this->getContentType($format)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user activity statistics
     * Purpose: Dashboard analytics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month');
        $statistics = $this->userActivityService->getActivityStatistics($period);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        }

        return view('audit::admin.user-activity.statistics', compact('statistics'));
    }

    /**
     * Get user session data
     * Purpose: Active session monitoring
     */
    public function sessions(Request $request)
    {
        $sessions = $this->userActivityService->getActiveSessions();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);
        }

        return view('audit::admin.user-activity.sessions', compact('sessions'));
    }

    /**
     * Terminate user session
     * Purpose: Security control
     */
    public function terminateSession(Request $request, string $sessionId)
    {
        // Only allow company admins to terminate sessions
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $result = $this->userActivityService->terminateSession($sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Session terminated successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to terminate session: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get filter options for dropdowns
     */
    private function getFilterOptions(): array
    {
        return [
            'users' => $this->userActivityService->getUsersList(),
            'actions' => $this->userActivityService->getActionsList(),
            'modules' => $this->userActivityService->getModulesList(),
            'branches' => $this->userActivityService->getBranchesList()
        ];
    }

    /**
     * Get content type for export
     */
    private function getContentType(string $format): string
    {
        return match($format) {
            'csv' => 'text/csv',
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf' => 'application/pdf',
            default => 'text/plain'
        };
    }

    /**
     * Get sort column from DataTable request
     */
    private function getSortColumn($order): string
    {
        $columns = ['id', 'user_name', 'action', 'module', 'description', 'ip_address', 'created_at'];
        
        if (empty($order) || !isset($order[0]['column'])) {
            return 'created_at';
        }

        $columnIndex = intval($order[0]['column']);
        return $columns[$columnIndex] ?? 'created_at';
    }

    /**
     * Get sort direction from DataTable request
     */
    private function getSortDirection($order): string
    {
        if (empty($order) || !isset($order[0]['dir'])) {
            return 'desc';
        }

        return in_array($order[0]['dir'], ['asc', 'desc']) ? $order[0]['dir'] : 'desc';
    }

    /**
     * Generate action buttons HTML for DataTable
     */
    private function generateActionButtons($activity): string
    {
        $viewRoute = route('admin.user-activity.show', ['user-activity' => $activity->id]);
        
        return '<a href="' . $viewRoute . '" class="btn btn-sm btn-info me-1"><i class="bx bx-show"></i> View</a>';
    }

    /**
     * Log admin action for audit trail
     */
    private function logAction(string $action, array $context = []): void
    {
        // Log the admin action for audit trail
        activity()
            ->causedBy(auth()->user())
            ->performedOn(new UserActivityLog())
            ->withProperties($context)
            ->log($action);
    }
} 