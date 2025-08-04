<?php

namespace Modules\Shipper\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\AdminController;
use Modules\Shipper\Repositories\Contracts\ApiLogRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\CarrierRepositoryInterface;

class ApiMonitoringController extends AdminController
{
    protected $apiLogRepository;
    protected $carrierRepository;

    /**
     * Constructor - Inject Repositories
     */
    public function __construct(
        ApiLogRepositoryInterface $apiLogRepository,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->apiLogRepository = $apiLogRepository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * Simple status indicators for all carriers
     */
    public function dashboard()
    {
        $adminInit = $this->adminInit();
        
        // Get all carriers with their statistics
        $carriers = $this->carrierRepository->all();
        $carriersWithStats = $carriers->map(function ($carrier) {
            $stats = $this->carrierRepository->getStatistics($carrier->id);
            $recentLogs = $this->apiLogRepository->getLogsByCarrier($carrier->id, ['recent' => 24]);
            
            return [
                'carrier' => $carrier,
                'statistics' => $stats,
                'recent_errors' => $recentLogs->where('is_success', false)->count(),
                'recent_calls' => $recentLogs->count(),
                'status' => $this->getCarrierApiStatus($carrier->id),
                'last_call' => $recentLogs->first()?->logged_at
            ];
        });

        // Overall system statistics
        $systemStats = [
            'total_carriers' => $carriers->count(),
            'active_carriers' => $carriers->where('is_active', true)->count(),
            'total_api_calls_today' => $this->apiLogRepository->getRecentLogs(100)->where('logged_at', '>=', today())->count(),
            'error_rate_today' => $this->getSystemErrorRate(),
            'average_response_time' => $this->getAverageResponseTime()
        ];

        return view('shipper::admin.monitoring.dashboard', [
            'adminInit' => $adminInit,
            'carriersWithStats' => $carriersWithStats,
            'systemStats' => $systemStats
        ]);
    }

    /**
     * View API logs with basic filtering
     */
    public function logs(Request $request)
    {
        $adminInit = $this->adminInit();
        $carriers = $this->carrierRepository->all();

        return view('shipper::admin.monitoring.logs', [
            'adminInit' => $adminInit,
            'carriers' => $carriers
        ]);
    }

    /**
     * DataTable AJAX endpoint for API logs
     */
    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            return $this->apiLogRepository->getForDataTable($request->all());
        }
    }

    /**
     * Export API logs
     */
    public function export(Request $request)
    {
        $filters = $request->all();
        
        // Get filtered logs
        $logs = $this->apiLogRepository->getLogsByCarrier(null, $filters);
        
        // Prepare CSV data
        $filename = 'api_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'Date/Time',
                'Carrier',
                'Endpoint',
                'Method',
                'Response Code',
                'Response Time (ms)',
                'Status',
                'Error Message'
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->logged_at->format('Y-m-d H:i:s'),
                    $log->carrier->name,
                    $log->endpoint,
                    $log->method,
                    $log->response_code,
                    $log->response_time_ms,
                    $log->is_success ? 'Success' : 'Failed',
                    $log->error_message
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get carrier statistics for AJAX
     */
    public function getCarrierStats(Request $request, $carrierId)
    {
        if ($request->ajax()) {
            $stats = $this->carrierRepository->getStatistics($carrierId);
            $recentLogs = $this->apiLogRepository->getLogsByCarrier($carrierId, ['recent' => 24]);
            
            $carrierStats = [
                'statistics' => $stats,
                'recent_calls' => $recentLogs->count(),
                'recent_errors' => $recentLogs->where('is_success', false)->count(),
                'success_rate' => $stats['success_rate'],
                'status' => $this->getCarrierApiStatus($carrierId),
                'last_call' => $recentLogs->first()?->formatted_logged_at,
                'error_logs' => $recentLogs->where('is_success', false)->take(5)->map(function($log) {
                    return [
                        'time' => $log->time_ago,
                        'endpoint' => $log->endpoint_name,
                        'error' => $log->error_message
                    ];
                })
            ];

            return response()->json([
                'success' => 1,
                'data' => $carrierStats
            ]);
        }
    }

    /**
     * Get real-time dashboard data for AJAX refresh
     */
    public function getDashboardData(Request $request)
    {
        if ($request->ajax()) {
            $carriers = $this->carrierRepository->all();
            $dashboardData = [];

            foreach ($carriers as $carrier) {
                $recentLogs = $this->apiLogRepository->getLogsByCarrier($carrier->id, ['recent' => 1]);
                $stats = $this->carrierRepository->getStatistics($carrier->id);
                
                $dashboardData[] = [
                    'carrier_id' => $carrier->id,
                    'carrier_name' => $carrier->name,
                    'status' => $this->getCarrierApiStatus($carrier->id),
                    'success_rate' => $stats['success_rate'],
                    'recent_calls' => $recentLogs->count(),
                    'recent_errors' => $recentLogs->where('is_success', false)->count(),
                    'last_call' => $recentLogs->first()?->time_ago ?? 'No calls'
                ];
            }

            return response()->json([
                'success' => 1,
                'data' => $dashboardData,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get carrier API status
     */
    private function getCarrierApiStatus($carrierId)
    {
        $successRate = $this->carrierRepository->getSuccessRate($carrierId, 1); // Last 1 hour
        
        if ($successRate === null) {
            return 'no_data';
        }
        
        if ($successRate >= 95) {
            return 'excellent';
        } elseif ($successRate >= 80) {
            return 'good';
        } elseif ($successRate >= 60) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    /**
     * Get system-wide error rate
     */
    private function getSystemErrorRate()
    {
        $recentLogs = $this->apiLogRepository->getRecentLogs(1000);
        $totalCalls = $recentLogs->count();
        
        if ($totalCalls === 0) {
            return 0;
        }
        
        $errorCalls = $recentLogs->where('is_success', false)->count();
        return round(($errorCalls / $totalCalls) * 100, 2);
    }

    /**
     * Get average response time
     */
    private function getAverageResponseTime()
    {
        $recentLogs = $this->apiLogRepository->getRecentLogs(100);
        $totalTime = $recentLogs->sum('response_time_ms');
        $totalCalls = $recentLogs->count();
        
        if ($totalCalls === 0) {
            return 0;
        }
        
        return round($totalTime / $totalCalls, 0);
    }
} 