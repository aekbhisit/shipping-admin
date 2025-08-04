<?php

namespace Modules\Statement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Statement\Entities\Statements;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Http\Controllers\AdminController;

class StatementAdminController extends AdminController
{
    public function index()
    {
        $adminInit = $this->adminInit() ;
        return view('statement::statement.index',['adminInit'=>$adminInit]);
    }


    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            //init datatable
            $dt_name_column = array('id', 'sms_id', 'temp_id', 'acc_id', 'report_datetime', 'report_value', 'bank_balance', 'report_detail', 'app_report_detail', 'report_acc', 'report_name', 'report_status');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];
            // count all parnter

            // create parnter object 
            $o_data = new Statements();
            // $o_data = $o_data->with(['sms', 'temp', 'acc', 'member', 'tran', 'job', 'user', 'statement_user', 'noted_user']);
            // add search query if have search from datables
            if (!empty($dt_search)) {
                $o_data = $o_data->where(function ($query) use ($dt_search) {
                    $query->where('id', 'like', "%$dt_search%")
                        ->orWhere('sms_id', 'like', "%$dt_search%")
                        ->orWhere('temp_id', 'like', "%$dt_search%")
                        ->orWhere('acc_id', 'like', "%$dt_search%")
                        ->orWhere('report_datetime', 'like', "%$dt_search%")
                        ->orWhere('report_value', 'like', "%$dt_search%")
                        ->orWhere('bank_balance', 'like', "%$dt_search%")
                        ->orWhere('report_detail', 'like', "%$dt_search%")
                        ->orWhere('app_report_detail', 'like', "%$dt_search%")
                        ->orWhere('report_acc', 'like', "%$dt_search%")
                        ->orWhere('report_name', 'like', "%$dt_search%")
                        ->orWhere('report_status', 'like', "%$dt_search%")
                        ->orWhere('updated_at', 'like', "%$dt_search%");
                });
            }
            if (!empty($request->get('filter')['start'])) {
                $o_data = $o_data->where('date_time', '>=', $request->get('filter')['start']);
            }
            if (!empty($request->get('filter')['end'])) {
                $o_data = $o_data->where('date_time', '<=', $request->get('filter')['end']);
            }
            if (!empty($request->get('filter')['acc_id'])) {
                $o_data = $o_data->where('acc_id', $request->get('filter')['acc_id']);
            }
            if (!empty($request->get('filter')['status'])) {
                $o_data = $o_data->where('report_status', $request->get('filter')['status']);
            }
            $dt_total = $o_data->count();
            // set query order & limit from datatable
            $o_data = $o_data->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query parnter list
            $custs = $o_data->get();

            // prepare datatable for resonse
            $tables = DataTables::of($custs)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('master_row')
                ->setOffset($dt_start)
                ->setTotalRecords($dt_total)
                ->setFilteredRecords($dt_total)
                ->editColumn('report_detail', function ($record) {
                    return limit($record->report_detail, 50);
                })
                ->editColumn('report_value', function ($record) {
                    return number_format($record->report_value, 2);
                })
                ->editColumn('bank_balance', function ($record) {
                    return number_format($record->bank_balance, 2);
                })
                ->editColumn('report_datetime', function ($record) {
                    return date('Y-m-d H:i', strtotime($record->report_datetime));
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex">';
                    if (roles('admin.statement.list.index')) {
                        $action_btn .= '<a href="' . route('admin.statement.list.view',  $record->id) . '" class="btn btn-sm me-1 btn-outline-primary"><i class="bx bx-show"></i></a>';
                    }
                    $action_btn .= '</div>';
                    return $action_btn;
                })
                ->escapeColumns([]);
            // response datatable json
            return $tables->make(true);
        }
    }


    public function form($id = 0)
    {
        $adminInit = $this->adminInit() ;
        $data = [];
        if (!empty($id)) {
            // with(['sms', 'temp', 'acc', 'member', 'tran', 'job', 'user', 'statement_user', 'noted_user'])->
            $data = Statements::find($id);
            $data->match_bank = json_decode($data->match_bank, 1);
        }

        return view('statement::statement.view', ['data' => $data,'adminInit'=>$adminInit]);
    }
}
