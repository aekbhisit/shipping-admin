<?php

namespace Modules\Audit\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Audit\Services\ComplianceService;
use Modules\Audit\Entities\ComplianceReport;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * ComplianceAdminController
 * Purpose: Compliance reporting and regulatory tracking
 * Access Level: Company Admin only
 */
class ComplianceAdminController extends Controller
{
    protected ComplianceService $complianceService;

    public function __construct(ComplianceService $complianceService)
    {
        $this->middleware(['auth:admin', 'adminAccessControl']);
        $this->complianceService = $complianceService;
    }

    /**
     * Display compliance reports
     * UI Implementation: DataTable with filtering
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'report_type' => $request->get('report_type'),
            'status' => $request->get('status'),
            'generated_by' => $request->get('generated_by')
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        // Get paginated compliance reports
        $complianceReports = $this->complianceService->getComplianceReports($filters, 20);

        // Get filter options for dropdowns
        $filterOptions = $this->getFilterOptions();

        // Get summary statistics
        $statistics = $this->complianceService->getComplianceStatistics();

        return view('audit::admin.compliance.index', compact(
            'complianceReports',
            'filters',
            'filterOptions',
            'statistics'
        ));
    }

    /**
     * Display specific compliance report details
     */
    public function show(Request $request, $id)
    {
        $complianceReport = ComplianceReport::with(['user', 'branch'])->findOrFail($id);

        // Check if user has access to this report (branch scope)
        if (auth()->user()->role === 'branch_admin' && 
            $complianceReport->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to compliance report');
        }

        // Get related reports for the same type
        $relatedReports = ComplianceReport::where('report_type', $complianceReport->report_type)
            ->where('id', '!=', $complianceReport->id)
            ->with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('audit::admin.compliance.show', compact(
            'complianceReport',
            'relatedReports'
        ));
    }

    /**
     * Generate new compliance report
     * Purpose: Create regulatory compliance reports
     */
    public function generate(Request $request)
    {
        // Only allow company admins to generate reports
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'report_type' => 'required|string|in:monthly,quarterly,annual,audit,user_activity',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'format' => 'nullable|string|in:pdf,excel,csv'
        ]);

        try {
            $report = $this->complianceService->generateReport([
                'report_type' => $request->report_type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'format' => $request->format ?? 'pdf',
                'generated_by' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_id
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report generated successfully',
                    'data' => $report
                ]);
            }

            return redirect()->route('admin.compliance.show', $report->id)
                ->with('success', 'Report generated successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Report generation failed: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Report generation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Download compliance report
     */
    public function download(Request $request, $reportId)
    {
        $report = ComplianceReport::findOrFail($reportId);

        // Check if user has access to this report
        if (auth()->user()->role === 'branch_admin' && 
            $report->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to compliance report');
        }

        // Check if file exists
        if (!$report->file_path || !Storage::exists($report->file_path)) {
            abort(404, 'Report file not found');
        }

        return Storage::download($report->file_path, $report->filename ?? 'compliance_report.pdf');
    }

    /**
     * Display compliance dashboard
     * Purpose: Overview of compliance status
     */
    public function dashboard(Request $request)
    {
        // Get dashboard statistics
        $stats = $this->complianceService->getDashboardStats();

        // Get recent reports
        $recentReports = ComplianceReport::with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get report type distribution
        $reportTypeStats = $this->complianceService->getReportTypeStats();

        // Get monthly report generation stats
        $monthlyStats = $this->complianceService->getMonthlyStats();

        return view('audit::admin.compliance.dashboard', compact(
            'stats',
            'recentReports',
            'reportTypeStats',
            'monthlyStats'
        ));
    }

    /**
     * Store new compliance report
     */
    public function store(Request $request)
    {
        // Only allow company admins to create reports
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'report_type' => 'required|string',
            'period' => 'required|string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);

        try {
            $report = $this->complianceService->createReport([
                'report_type' => $request->report_type,
                'period' => $request->period,
                'description' => $request->description,
                'generated_by' => auth()->user()->id,
                'branch_id' => auth()->user()->branch_id,
                'file' => $request->file('file')
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report created successfully',
                    'data' => $report
                ]);
            }

            return redirect()->route('admin.compliance.show', $report->id)
                ->with('success', 'Report created successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Report creation failed: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Report creation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete compliance report
     */
    public function destroy(Request $request, $id)
    {
        // Only allow company admins to delete reports
        if (auth()->user()->role !== 'company_admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $report = ComplianceReport::findOrFail($id);

        try {
            $this->complianceService->deleteReport($report);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Report deleted successfully'
                ]);
            }

            return redirect()->route('admin.compliance.index')
                ->with('success', 'Report deleted successfully');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Report deletion failed: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Report deletion failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Export compliance reports list
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $filters = $request->only(['date_from', 'date_to', 'report_type', 'status']);

        // Validate format
        if (!in_array($format, ['csv', 'excel', 'pdf'])) {
            return response()->json(['error' => 'Invalid export format'], 400);
        }

        try {
            $exportData = $this->complianceService->exportReportsList($filters, $format);
            
            $filename = 'compliance_reports_' . date('Y-m-d_H-i-s') . '.' . $format;
            
            return response()->download($exportData, $filename, [
                'Content-Type' => $this->getContentType($format)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get compliance statistics
     */
    public function statistics(Request $request)
    {
        $period = $request->get('period', 'month');
        $statistics = $this->complianceService->getComplianceStatistics($period);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        }

        return view('audit::admin.compliance.statistics', compact('statistics'));
    }

    /**
     * Get filter options for dropdowns
     */
    private function getFilterOptions(): array
    {
        return [
            'report_types' => $this->complianceService->getReportTypes(),
            'statuses' => $this->complianceService->getStatuses(),
            'users' => $this->complianceService->getUsersList(),
            'branches' => $this->complianceService->getBranchesList()
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
     * Log admin action for audit trail
     */
    private function logAction(string $action, array $context = []): void
    {
        // Log the admin action for audit trail
        activity()
            ->causedBy(auth()->user())
            ->performedOn(new ComplianceReport())
            ->withProperties($context)
            ->log($action);
    }
} 