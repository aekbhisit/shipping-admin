<?php

namespace Modules\Log\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use Modules\Core\Http\Controllers\AdminController;

class LogsAdminController extends AdminController
{
    public function index()
    {   
        $adminInit = $this->adminInit() ;
        return view('log::logs.index',['adminInit'=>$adminInit]);
    }

    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {

            //init datatable
            $dt_name_column = array('id', 'log_name', 'description', 'subject_type','event','subject_id','updated_at');
            $dt_order_column =(!empty($request->get('order')[0]['column']))?$request->get('order')[0]['column']:0;
            $dt_order_dir = (!empty($request->get('order')[0]['dir']))?$request->get('order')[0]['dir']:'desc';
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // create brand object
            $o_data = new Activity();

            // add search query if have search from datable
            if (!empty($dt_search)) {
                $o_data = $o_data->where(function ($query) use ($dt_search) {
                    $query->orWhere('log_name', 'like', "%" . $dt_search . "%")
                            ->orWhere('description', 'like', "%" . $dt_search . "%")
                            ->orWhere('subject_type', 'like', "%" . $dt_search . "%")
                            ->orWhere('event', 'like', "%" . $dt_search . "%")
                            ->orWhere('subject_id', 'like', "%" . $dt_search . "%");
                });
            }

            $dt_total = $o_data->count();
            // set query order & limit from datatable
            $o_data = $o_data->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query brand
            $slug = $o_data->get();
            // prepare datatable for response
            $tables = DataTables::of($slug)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('brand_row')
                ->setTotalRecords($dt_total)
                ->editColumn('updated_at', function ($record) {
                    return $record->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="btn-list">';
                    $action_btn .= '<a href="' . route('admin.log.log.view', $record->id) . '" class="btn btn-outline-primary"><i class="fa fa-eye"></i></a>';
                    $action_btn .= '</div>';
                    return $action_btn;
                })
                ->escapeColumns([]);

            // response datatable json
            return $tables->make(true);
        }
    }

    public function view(Request $request, $log_id){
        $adminInit = $this->adminInit() ;
        $data = [];
        if (!empty($log_id)) {
            $data = Activity::find($log_id);
        }

        return view('log::logs.form',['data'=>$data,'adminInit'=>$adminInit]);
    }
   

   
}
