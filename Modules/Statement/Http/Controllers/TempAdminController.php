<?php

namespace Modules\Statement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Statement\Entities\TempStatements;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Http\Controllers\AdminController;

class TempAdminController extends AdminController
{
    public function index()
    {
        $adminInit = $this->adminInit() ;
        return view('statement::temp.index',['adminInit'=>$adminInit]);
    }


    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            //init datatable
            $dt_name_column = array('id', 'acc_id', 'detail', 'source_from', 'status', 'created_at', 'updated_at', 'action');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];
            // count all parnter

            // create parnter object 
            $o_data = new TempStatements();
            // $o_data = $o_data->with('acc');
            // add search query if have search from datables
            if (!empty($dt_search)) {
                $o_data = $o_data->where(function ($query) use ($dt_search) {
                    $query->where('id', 'like', "%$dt_search%")
                        ->orWhere('acc_id', 'like', "%$dt_search%")
                        ->orWhere('detail', 'like', "%$dt_search%")
                        ->orWhere('source_from', 'like', "%$dt_search%")
                        ->orWhere('status', 'like', "%$dt_search%")
                        ->orWhere('created_at', 'like', "%$dt_search%")
                        ->orWhere('updated_at', 'like', "%$dt_search%");
                });
            }
            if (!empty($request->get('filter')['start'])) {
                $start = $request->get('filter')['start'];
                $o_data = $o_data->where(function ($query) use ($start) {
                    $query->where('created_at', '>=', "%$start%")
                        ->orWhere('updated_at', '>=', "%$start%");
                });
            }
            if (!empty($request->get('filter')['end'])) {
                $end = $request->get('filter')['end'];
                $o_data = $o_data->where(function ($query) use ($end) {
                    $query->where('created_at', '<=', "%$end%")
                        ->orWhere('updated_at', '<=', "%$end%");
                });
            }
            if (!empty($request->get('filter')['acc_id'])) {
                $o_data = $o_data->where('acc_id', $request->get('filter')['acc_id']);
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
                ->editColumn('created_at', function ($record) {
                    return date('Y-m-d H:i', strtotime($record->created_at));
                })
                ->editColumn('updated_at', function ($record) {
                    return date('Y-m-d H:i', strtotime($record->updated_at));
                })
                ->editColumn('detail', function ($record) {
                    $data = json_decode($record->detail, 1);
                    if (!empty($data)) {
                        # code...
                        $text = 'sender : ' . (!empty($data['sender']) ? $data['sender'] : '') . '<br>';
                        $text .= 'message : ' . (!empty($data['message']) ? limit($data['message'], 60) : '') . '<br>';
                        $text .= 'Time : ' . (!empty($data['date_time']) ? date('Y-m-d H:i', strtotime($data['date_time'])) : '');
                        return $text;
                    }
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex">';
                    if (roles('admin.statement.temp.index')) {
                        $action_btn .= '<a href="' . route('admin.statement.temp.view',  $record->id) . '" class="btn btn-sm me-1 btn-outline-primary"><i class="bx bx-show"></i></a>';
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
            // with('acc')->
            $data = TempStatements::find($id);
            $data->detail = json_decode($data->detail, 1);
        }

        return view('statement::temp.view', ['data' => $data,'adminInit'=>$adminInit]);
    }
}
