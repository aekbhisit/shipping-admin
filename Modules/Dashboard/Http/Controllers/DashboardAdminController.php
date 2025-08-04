<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customer\Entities\Customers;
use Modules\Report\Entities\ReportCustomers;
use Modules\Core\Http\Controllers\AdminController;
use Carbon\Carbon;

class DashboardAdminController extends AdminController
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    public function index()
    {
        $adminInit = $this->adminInit();
        return view('dashboard::index', ['adminInit' => $adminInit]);
    }
    
    /**
     * Get shipments overview data
     */
    public function shipmentsOverview()
    {
        // Sample data structure - replace with actual database queries
        $stats = [
            'today' => [
                'count' => rand(15, 50),
                'change' => rand(5, 25),
                'trend' => 'up'
            ],
            'week' => [
                'count' => rand(150, 300),
                'change' => rand(10, 40),
                'trend' => 'up'
            ],
            'month' => [
                'count' => rand(800, 1500),
                'change' => rand(15, 35),
                'trend' => 'up'
            ]
        ];
        
        $chartData = [
            'labels' => $this->getLastDays(7),
            'values' => [
                rand(20, 40),
                rand(25, 45),
                rand(30, 50),
                rand(28, 48),
                rand(35, 55),
                rand(40, 60),
                rand(45, 65)
            ]
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chartData' => $chartData
        ]);
    }
    
    /**
     * Get revenue analytics data
     */
    public function revenueAnalytics()
    {
        $stats = [
            'today' => [
                'amount' => rand(50000, 150000),
                'change' => rand(5, 25),
                'trend' => 'up'
            ],
            'week' => [
                'amount' => rand(300000, 800000),
                'change' => rand(10, 30),
                'trend' => 'up'
            ],
            'month' => [
                'amount' => rand(1500000, 3000000),
                'change' => rand(15, 35),
                'trend' => 'up'
            ]
        ];
        
        $chartData = [
            'labels' => $this->getLastDays(7),
            'total' => [
                rand(80000, 120000),
                rand(90000, 130000),
                rand(100000, 140000),
                rand(95000, 135000),
                rand(110000, 150000),
                rand(120000, 160000),
                rand(130000, 170000)
            ],
            'shipping' => [
                rand(60000, 90000),
                rand(70000, 100000),
                rand(75000, 105000),
                rand(72000, 102000),
                rand(85000, 115000),
                rand(90000, 120000),
                rand(95000, 125000)
            ],
            'markup' => [
                rand(15000, 25000),
                rand(18000, 28000),
                rand(20000, 30000),
                rand(19000, 29000),
                rand(22000, 32000),
                rand(25000, 35000),
                rand(28000, 38000)
            ]
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chartData' => $chartData
        ]);
    }
    
    /**
     * Get branch performance data
     */
    public function branchPerformance()
    {
        $chartData = [
            'branches' => ['สาขากรุงเทพ', 'สาขาเชียงใหม่', 'สาขาภูเก็ต', 'สาขาขอนแก่น', 'สาขาหาดใหญ่'],
            'shipments' => [rand(150, 300), rand(100, 250), rand(80, 200), rand(90, 220), rand(70, 180)],
            'revenue' => [rand(800, 1500), rand(600, 1200), rand(500, 1000), rand(550, 1100), rand(450, 900)]
        ];
        
        $topBranches = [
            [
                'name' => 'สาขากรุงเทพ',
                'shipments' => rand(250, 350),
                'revenue' => rand(1200, 1800)
            ],
            [
                'name' => 'สาขาเชียงใหม่',
                'shipments' => rand(180, 280),
                'revenue' => rand(900, 1400)
            ],
            [
                'name' => 'สาขาภูเก็ต',
                'shipments' => rand(150, 250),
                'revenue' => rand(800, 1200)
            ]
        ];
        
        return response()->json([
            'success' => true,
            'chartData' => $chartData,
            'topBranches' => $topBranches
        ]);
    }
    
    /**
     * Get carrier usage data
     */
    public function carrierUsage()
    {
        $data = [
            'labels' => ['Thailand Post', 'J&T Express', 'Flash Express', 'Kerry Express'],
            'values' => [rand(100, 200), rand(150, 250), rand(120, 220), rand(80, 160)]
        ];
        
        return response()->json([
            'success' => true,
            'labels' => $data['labels'],
            'values' => $data['values']
        ]);
    }
    
    /**
     * Get recent activities
     */
    public function recentActivities()
    {
        $activities = [
            [
                'type' => 'shipment_created',
                'description' => 'สร้างใบจัดส่งใหม่ #SH001234',
                'user_name' => 'นายสมชาย ใจดี',
                'created_at' => Carbon::now()->subMinutes(15)->toISOString()
            ],
            [
                'type' => 'shipment_confirmed',
                'description' => 'ยืนยันการจัดส่ง #SH001233',
                'user_name' => 'นางสุดา รักษา',
                'created_at' => Carbon::now()->subMinutes(32)->toISOString()
            ],
            [
                'type' => 'shipment_picked_up',
                'description' => 'เก็บพัสดุ #SH001232 แล้ว',
                'user_name' => 'นายวิชัย ขับรถ',
                'created_at' => Carbon::now()->subHour()->toISOString()
            ],
            [
                'type' => 'shipment_delivered',
                'description' => 'จัดส่งสำเร็จ #SH001231',
                'user_name' => 'นายสมศักดิ์ ส่งของ',
                'created_at' => Carbon::now()->subHours(2)->toISOString()
            ],
            [
                'type' => 'user_login',
                'description' => 'เข้าสู่ระบบ',
                'user_name' => 'นางสาวมาลี ผู้จัดการ',
                'created_at' => Carbon::now()->subHours(3)->toISOString()
            ]
        ];
        
        return response()->json($activities);
    }
    
    /**
     * Get shipment status overview
     */
    public function shipmentStatus()
    {
        $statusData = [
            'draft' => rand(5, 15),
            'quoted' => rand(10, 25),
            'confirmed' => rand(20, 40),
            'picked_up' => rand(15, 35),
            'in_transit' => rand(25, 50),
            'delivered' => rand(100, 200)
        ];
        
        return response()->json($statusData);
    }
    
    /**
     * Helper method to get last N days
     */
    private function getLastDays($days = 7)
    {
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dates[] = Carbon::now()->subDays($i)->format('d/m');
        }
        return $dates;
    }
}
