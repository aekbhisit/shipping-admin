<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Report\Entities\ReportCustomers;
use Modules\Report\Entities\ReportGame;
use Modules\Report\Entities\ReportJobs;

class ChartController extends Controller
{
    public function chart_customers()
    {
        $date = new Carbon;
        $o_cust = new ReportCustomers;
        $chart = array('date' => null, 'cust' => null, 'deposit' => null);

        for ($i = 14; $i >= 0; $i--) {
            $o_date = date('Y-m-d', strtotime($date->now()->subDays($i)));
            $cust = $o_cust->where('date', 'like', $o_date);

            $chart['date'][] = date('d/m', strtotime($o_date));
            $chart['cust'][] =  $cust->sum('customer_cnt');
            $chart['deposit'][] =  $cust->sum('customer_deposit_cnt');
        }
        
        $list['day']['now'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['day']['old'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now()->subDay())))->sum('customer_cnt'));

        $list['week']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['week']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subWeeks())))->sum('customer_cnt'));

        $list['month']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['month']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subMonths())))->sum('customer_cnt'));
        $resp = ['success' => 1, 'code' => 200, 'data' => $chart, 'list' => $list];
        return response()->json($resp);
    }

    public function chart_play()
    {
        $date = new Carbon;
        $o_cust = new ReportGame;
        for ($i = 14; $i >= 0; $i--) {
            $o_date = date('Y-m-d', strtotime($date->now()->subDays($i)));
            $cust = $o_cust->where('date', 'like', $o_date);

            $chart['date'][] = date('d/m', strtotime($o_date));
            $chart['cust'][] =  $cust->sum('customer_cnt');
        }
        $list['day']['now'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['day']['old'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now()->subDay())))->sum('customer_cnt'));

        $list['week']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['week']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subWeeks())))->sum('customer_cnt'));

        $list['month']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('customer_cnt'));
        $list['month']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subMonths())))->sum('customer_cnt'));
        $resp = ['success' => 1, 'code' => 200, 'data' => $chart, 'list' => $list];
        return response()->json($resp);
    }

    public function chart_jobs()
    {
        $date = new Carbon;
        $o_cust = new ReportJobs;
        $chart = array('date' => null, 'deposit' => null, 'withdraw' => null, 'promotion' => null);
        for ($i = 14; $i >= 0; $i--) {
            $o_date = date('Y-m-d', strtotime($date->now()->subDays($i)));
            $cust = $o_cust->where('date', 'like', $o_date);

            $chart['date'][] = date('d/m', strtotime($o_date));
            $chart['deposit'][] =  $cust->sum('deposit_cnt');
            $chart['withdraw'][] =  $cust->sum('withdraw_cnt');
            $chart['promotion'][] =  $cust->sum('promotion_cnt');
        }
        $list['day']['now'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now())))->sum('job_cnt'));
        $list['day']['old'] = number_format($o_cust->where('date', 'like', date('Y-m-d', strtotime($date->now()->subDay())))->sum('job_cnt'));

        $list['week']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('job_cnt'));
        $list['week']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subWeeks(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subWeeks())))->sum('job_cnt'));

        $list['month']['now'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths())))->where('date', '<=', date('Y-m-d', strtotime($date->now())))->sum('job_cnt'));
        $list['month']['old'] = number_format($o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subMonths(2))))->where('date', '<', date('Y-m-d', strtotime($date->now()->subMonths())))->sum('job_cnt'));
        $resp = ['success' => 1, 'code' => 200, 'data' => array_reverse($chart), 'list' => $list];

        return response()->json($resp);
    }

    public function chart_winlose()
    {
        $date = new Carbon;
        $o_cust = new ReportGame;
        // $check = $o_cust->where('date', '>=', date('Y-m-d', strtotime($date->now()->subDays(15))))->count();
        $chart = array('date' => null, 'winlose' => null);
        for ($i = 14; $i >= 0; $i--) {
            $o_date = date('Y-m-d', strtotime($date->now()->subDays($i)));

            $chart['date'][] = date('d/m', strtotime($o_date));
            $chart['winlose'][] = $o_cust->where('date', 'like', $o_date)->sum('winlose');
        }
        $resp = ['success' => 1, 'code' => 200, 'data' => $chart];
        return response()->json($resp);
    }
}
