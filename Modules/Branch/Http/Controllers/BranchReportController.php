<?php

namespace Modules\Branch\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchReport;
use Carbon\Carbon;

/**
 * BranchReportController
 * Purpose: Branch performance and analytics reporting
 * Access: Company Admin (all branches) / Branch Admin (own branch)
 */
class BranchReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin', 'role:company_admin|branch_admin']);
    }

    /**
     * Branch performance dashboard
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 30); // days
        
        if ($user->hasRole('company_admin')) {
            // Company admin sees all branches
            $branches = Branch::active()->get();
            $data = $this->getCompanyDashboardData($period);
        } else {
            // Branch admin sees only their branch
            $branch = $user->branch;
            if (!$branch) {
                return redirect()->route('admin.dashboard.index')
                    ->with('error', 'No branch assigned to your account.');
            }
            $branches = collect([$branch]);
            $data = $this->getBranchDashboardData($branch, $period);
        }

        return view('branch::reports.dashboard', compact('branches', 'data', 'period'));
    }

    /**
     * Shipment statistics
     */
    public function shipmentStats(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 30);
        $branchId = $request->get('branch_id');

        if ($user->hasRole('company_admin')) {
            // Company admin can filter by branch
            $branch = $branchId ? Branch::find($branchId) : null;
            $data = $this->getShipmentStats($branch, $period);
        } else {
            // Branch admin sees only their branch
            $branch = $user->branch;
            if (!$branch) {
                return redirect()->route('admin.dashboard.index')
                    ->with('error', 'No branch assigned to your account.');
            }
            $data = $this->getShipmentStats($branch, $period);
        }

        return view('branch::reports.shipment-stats', compact('data', 'period', 'branch'));
    }

    /**
     * Revenue and markup analysis
     */
    public function revenueReport(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 30);
        $branchId = $request->get('branch_id');

        if ($user->hasRole('company_admin')) {
            $branch = $branchId ? Branch::find($branchId) : null;
            $data = $this->getRevenueData($branch, $period);
        } else {
            $branch = $user->branch;
            if (!$branch) {
                return redirect()->route('admin.dashboard.index')
                    ->with('error', 'No branch assigned to your account.');
            }
            $data = $this->getRevenueData($branch, $period);
        }

        return view('branch::reports.revenue', compact('data', 'period', 'branch'));
    }

    /**
     * Carrier usage by branch
     */
    public function carrierPerformance(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 30);
        $branchId = $request->get('branch_id');

        if ($user->hasRole('company_admin')) {
            $branch = $branchId ? Branch::find($branchId) : null;
            $data = $this->getCarrierPerformance($branch, $period);
        } else {
            $branch = $user->branch;
            if (!$branch) {
                return redirect()->route('admin.dashboard.index')
                    ->with('error', 'No branch assigned to your account.');
            }
            $data = $this->getCarrierPerformance($branch, $period);
        }

        return view('branch::reports.carrier-performance', compact('data', 'period', 'branch'));
    }

    /**
     * Export branch reports
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'shipment_stats');
        $period = $request->get('period', 30);
        $branchId = $request->get('branch_id');

        if ($user->hasRole('company_admin')) {
            $branch = $branchId ? Branch::find($branchId) : null;
        } else {
            $branch = $user->branch;
            if (!$branch) {
                return back()->with('error', 'No branch assigned to your account.');
            }
        }

        switch ($type) {
            case 'shipment_stats':
                $data = $this->getShipmentStats($branch, $period);
                $filename = 'shipment_stats_' . now()->format('Y-m-d_H-i-s') . '.' . $format;
                break;
            case 'revenue':
                $data = $this->getRevenueData($branch, $period);
                $filename = 'revenue_report_' . now()->format('Y-m-d_H-i-s') . '.' . $format;
                break;
            case 'carrier_performance':
                $data = $this->getCarrierPerformance($branch, $period);
                $filename = 'carrier_performance_' . now()->format('Y-m-d_H-i-s') . '.' . $format;
                break;
            default:
                return back()->with('error', 'Invalid report type.');
        }

        if ($format === 'csv') {
            return $this->exportToCsv($data, $filename);
        }

        return back()->with('error', 'Unsupported export format.');
    }

    /**
     * Get company dashboard data
     */
    private function getCompanyDashboardData($period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        // TODO: Update when Shipment module is implemented
        return [
            'total_branches' => Branch::active()->count(),
            'total_shipments' => 0, // \DB::table('shipments')->where('created_at', '>=', $startDate)->count(),
            'total_revenue' => 0, // \DB::table('shipments')->where('created_at', '>=', $startDate)->sum('total_amount'),
            'total_markup' => 0, // \DB::table('shipments')->where('created_at', '>=', $startDate)->sum('markup_amount'),
            'top_branches' => Branch::active()->take(5)->get() // TODO: Add shipment count when Shipment module is implemented
        ];
    }

    /**
     * Get branch dashboard data
     */
    private function getBranchDashboardData($branch, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        
        // TODO: Update when Shipment module is implemented
        return [
            'total_shipments' => 0, // $branch->shipments()->where('created_at', '>=', $startDate)->count(),
            'total_revenue' => 0, // $branch->shipments()->where('created_at', '>=', $startDate)->sum('total_amount'),
            'total_markup' => 0, // $branch->shipments()->where('created_at', '>=', $startDate)->sum('markup_amount'),
            'carrier_usage' => collect() // $branch->shipments()->where('created_at', '>=', $startDate)->with('carrier')->get()->groupBy('carrier.name')->map->count()
        ];
    }

    /**
     * Get shipment statistics
     */
    private function getShipmentStats($branch = null, $period = 30)
    {
        // TODO: Update when Shipment module is implemented
        return [
            'total_shipments' => 0,
            'daily_stats' => collect(),
            'status_breakdown' => collect()
        ];
    }

    /**
     * Get revenue data
     */
    private function getRevenueData($branch = null, $period = 30)
    {
        // TODO: Update when Shipment module is implemented
        return [
            'total_revenue' => 0,
            'total_markup' => 0,
            'daily_revenue' => collect()
        ];
    }

    /**
     * Get carrier performance data
     */
    private function getCarrierPerformance($branch = null, $period = 30)
    {
        // TODO: Update when Shipment module is implemented
        return collect();
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Convert data to CSV format (simplified)
            if (isset($data['daily_stats'])) {
                fputcsv($file, ['Date', 'Count']);
                foreach ($data['daily_stats'] as $row) {
                    fputcsv($file, [$row->date, $row->count]);
                }
            } elseif (isset($data['daily_revenue'])) {
                fputcsv($file, ['Date', 'Revenue', 'Markup']);
                foreach ($data['daily_revenue'] as $row) {
                    fputcsv($file, [$row->date, $row->revenue, $row->markup]);
                }
            } else {
                // Handle other data types
                fputcsv($file, ['Data']);
                fputcsv($file, [json_encode($data)]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 