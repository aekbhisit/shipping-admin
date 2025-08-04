<?php

namespace Modules\Statement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Statement\Entities\Sms;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Http\Controllers\AdminController;

class SmsAdminController extends AdminController
{
    public function index()
    {
        $adminInit = $this->adminInit() ;
        return view('statement::sms.index',['adminInit'=>$adminInit]);
    }


    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            //init datatable

            $dt_name_column = array('id', 'statement_id', 'job_id', 'phone', 'port', 'message', 'bank_no', 'amount', 'balance', 'date_time', 'bank_id', 'acc_id', 'bank_account', 'bank_number', 'status', 'created_at');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // count all parnter

            // create parnter object 
            $o_data = new Sms();
            // $o_data = $o_data->with(['statement', 'job', 'bank', 'accout', 'createdby', 'updatedby']);
            // add search query if have search from datables
            if (!empty($dt_search)) {
                $o_data = $o_data->where(function ($query) use ($dt_search) {
                    $query->where('id', 'like', "%$dt_search%")
                        ->orWhere('statement_id', 'like', "%$dt_search%")
                        ->orWhere('job_id', 'like', "%$dt_search%")
                        ->orWhere('phone', 'like', "%$dt_search%")
                        ->orWhere('port', 'like', "%$dt_search%")
                        ->orWhere('message', 'like', "%$dt_search%")
                        ->orWhere('bank_no', 'like', "%$dt_search%")
                        ->orWhere('amount', 'like', "%$dt_search%")
                        ->orWhere('balance', 'like', "%$dt_search%")
                        ->orWhere('date_time', 'like', "%$dt_search%")
                        ->orWhere('bank_id', 'like', "%$dt_search%")
                        ->orWhere('acc_id', 'like', "%$dt_search%")
                        ->orWhere('bank_account', 'like', "%$dt_search%")
                        ->orWhere('bank_number', 'like', "%$dt_search%")
                        ->orWhere('status', 'like', "%$dt_search%")
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
            if (!empty($request->get('filter')['bank_id'])) {
                $o_data = $o_data->where('bank_id', $request->get('filter')['bank_id']);
            }
            if (!empty($request->get('filter')['status'])) {
                $o_data = $o_data->where('status', $request->get('filter')['status']);
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
                ->editColumn('message', function ($record) {
                    return limit($record->message, 50);
                })
                ->editColumn('amount', function ($record) {
                    return number_format($record->amount, 2);
                })
                ->editColumn('balance', function ($record) {
                    return number_format($record->balance, 2);
                })
                ->editColumn('created_at', function ($record) {
                    return date('Y-m-d H:i', strtotime($record->created_at));
                })
                ->editColumn('date_time', function ($record) {
                    return date('Y-m-d H:i', strtotime($record->date_time));
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex">';
                    if (roles('admin.statement.sms.index')) {
                        $action_btn .= '<a href="' . route('admin.statement.sms.view',  $record->id) . '" class="btn btn-sm me-1 btn-outline-primary"><i class="bx bx-show"></i></a>';
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
            // with(['statement', 'job', 'bank', 'accout', 'createdby', 'updatedby'])->
            $data = Sms::find($id);
        }

        return view('statement::sms.view', ['data' => $data,'adminInit'=>$adminInit]);
    }
}
