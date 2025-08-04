<?php

namespace Modules\Audit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Audit\Services\AuditService;
use Modules\Audit\Services\DataProtectionService;
use Modules\Audit\Entities\AuditLog;

/**
 * AuditAdminController
 * Purpose: Main audit log viewing and searching
 * Access Level: Company Admin and Branch Admin
 */
class AuditAdminController extends Controller
{
    protected AuditService $auditService;
    protected DataProtectionService $dataProtectionService;

    public function __construct(AuditService $auditService, DataProtectionService $dataProtectionService)
    {
        $this->middleware(['auth:admin', 'adminAccessControl']);
        $this->auditService = $auditService;
        $this->dataProtectionService = $dataProtectionService;
    }

    /**
     * Display audit logs with DataTable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        // Get filter options for dropdowns
        $filterOptions = $this->getFilterOptions();

        // Get summary statistics
        $statistics = $this->auditService->getAuditStatistics();

        return view('audit::admin.audit.index', compact(
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
            'event_type' => $request->get('event_type'),
            'model_type' => $request->get('model_type'),
            'branch_id' => $request->get('branch_id'),
            'per_page' => $request->get('length', 15),
            'sort' => $this->getSortColumn($request->get('order')),
            'direction' => $this->getSortDirection($request->get('order')),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $auditLogs = $this->auditService->getAuditLogs($filters, $filters['per_page'] ?? 15);

        // Format data to avoid serialization issues
        $formattedData = collect($auditLogs->items())->map(function($log, $index) {
            return [
                'DT_RowIndex' => $index + 1,
                'id' => $log->id,
                'user_name' => $log->user ? $log->user->name : 'System',
                'event_type' => ucfirst($log->event_type),
                'model_type' => class_basename($log->auditable_type),
                'description' => $this->generateDescription($log),
                'ip_address' => $log->ip_address ?? 'N/A',
                'created_at' => $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '',
                'actions' => $this->generateActionButtons($log),
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $auditLogs->total(),
            'recordsFiltered' => $auditLogs->total(),
            'data' => $formattedData,
        ]);
    }

    /**
     * DataTable AJAX method for audit logs
     */
    public function datatable_ajax(Request $request): JsonResponse
    {
        return $this->getDataTableData($request);
    }

    /**
     * Display specific audit log details
     * Display: Detailed audit record with before/after comparison
     */
    public function show(Request $request, $id)
    {
        $auditLog = AuditLog::with(['user', 'branch', 'auditable'])->findOrFail($id);

        // Check if user has access to this audit log (branch scope)
        if (auth()->user()->role === 'branch_admin' && 
            $auditLog->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to audit log');
        }

        // Clean sensitive data for display
        $auditLog->old_values = $this->dataProtectionService->cleanForDisplay($auditLog->old_values ?? []);
        $auditLog->new_values = $this->dataProtectionService->cleanForDisplay($auditLog->new_values ?? []);

        // Get formatted changes
        $formattedChanges = $auditLog->getFormattedChanges();

        // Get context information
        $contextInfo = $auditLog->getContextInfo();

        // Get related audit logs for the same record
        $relatedLogs = AuditLog::where('auditable_type', $auditLog->auditable_type)
            ->where('auditable_id', $auditLog->auditable_id)
            ->where('id', '!=', $auditLog->id)
            ->with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('audit::admin.audit.show', compact(
            'auditLog',
            'formattedChanges',
            'contextInfo',
            'relatedLogs'
        ));
    }

    /**
     * Advanced search functionality
     * Features: Full-text search, date ranges, user filtering
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $userId = $request->get('user_id');
        $eventType = $request->get('event_type');
        $modelType = $request->get('model_type');

        $searchResults = $this->auditService->searchAuditLogs([
            'query' => $query,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'user_id' => $userId,
            'event_type' => $eventType,
            'model_type' => $modelType
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $searchResults,
                'total' => $searchResults->total(),
                'filters' => [
                    'query' => $query,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'user_id' => $userId,
                    'event_type' => $eventType,
                    'model_type' => $modelType
                ]
            ]);
        }

        return view('audit::admin.audit.search', compact('searchResults'));
    }

    /**
     * Export audit logs
     * Formats: CSV, Excel, PDF
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $filters = $request->only(['date_from', 'date_to', 'user_id', 'event_type', 'model_type']);

        // Validate format
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            return response()->json(['error' => 'Invalid export format'], 400);
        }

        try {
            $exportData = $this->auditService->exportAuditLogs($filters, $format);
            
            $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.' . $format;
            
            return response()->download($exportData, $filename, [
                'Content-Type' => $this->getContentType($format)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get recent audit activities
     * Purpose: Dashboard widget data
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 10);
        $recentActivities = $this->auditService->getRecentActivities($limit);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $recentActivities
            ]);
        }

        return view('audit::admin.audit.recent', compact('recentActivities'));
    }

    /**
     * Get audit statistics
     * Purpose: Dashboard analytics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month');
        $statistics = $this->auditService->getAuditStatistics($period);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        }

        return view('audit::admin.audit.statistics', compact('statistics'));
    }

    /**
     * Cleanup old audit logs
     * Purpose: Data retention management
     */
    public function cleanup(Request $request)
    {
        // Only allow company admins to perform cleanup
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $olderThan = $request->get('older_than', '2_years');
        $dryRun = $request->get('dry_run', true);

        try {
            $result = $this->auditService->cleanupOldLogs($olderThan, $dryRun);

            return response()->json([
                'success' => true,
                'message' => $dryRun ? 'Dry run completed' : 'Cleanup completed',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cleanup failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get data retention status
     * Purpose: Compliance monitoring
     */
    public function retentionStatus(Request $request)
    {
        $status = $this->auditService->getRetentionStatus();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        }

        return view('audit::admin.audit.retention', compact('status'));
    }

    /**
     * Get filter options for dropdowns
     */
    private function getFilterOptions(): array
    {
        return [
            'users' => $this->auditService->getUsersList(),
            'event_types' => $this->auditService->getEventTypes(),
            'model_types' => $this->auditService->getModelTypes(),
            'branches' => $this->auditService->getBranchesList()
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
        $columns = ['id', 'user_name', 'event_type', 'model_type', 'description', 'ip_address', 'created_at'];
        
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
     * Generate description for audit log
     */
    private function generateDescription($log): string
    {
        $description = ucfirst($log->event_type) . ' ' . class_basename($log->auditable_type);
        
        if ($log->changed_fields) {
            $fields = is_array($log->changed_fields) ? $log->changed_fields : json_decode($log->changed_fields, true);
            if ($fields && count($fields) > 0) {
                $description .= ' (' . implode(', ', array_slice($fields, 0, 3)) . ')';
                if (count($fields) > 3) {
                    $description .= ' and ' . (count($fields) - 3) . ' more fields';
                }
            }
        }
        
        return $description;
    }

    /**
     * Generate action buttons HTML for DataTable
     */
    private function generateActionButtons($log): string
    {
        $viewRoute = route('admin.audit.show', ['audit' => $log->id]);
        
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
            ->performedOn(new AuditLog())
            ->withProperties($context)
            ->log($action);
    }
} 