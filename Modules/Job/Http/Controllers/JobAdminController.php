<?php

namespace Modules\Job\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Yajra\DataTables\Facades\DataTables;

// use Modules\Statement\Entities\TempStatements;
use Modules\Statement\Entities\Statements;
use App\Http\Controllers\Games\Sbo\MemberController ;
use Modules\Partner\Entities\PartnerBoards;
use Modules\Partner\Entities\PartnerBank;
use Modules\Customer\Entities\Customers;
use Modules\Customer\Entities\CustomerUsers;

use Modules\Core\Http\Controllers\AdminController;

use App\Models\TransferLogs;

use Modules\Job\Entities\Jobs;

class JobAdminController extends AdminController
{

    //////////////////////// datatable  ////////////////////////
    public function index(Request $request, $show_status = 'waiting'){
        $adminInit = $this->adminInit() ;
        switch($show_status){
            case "waiting":
                $show_status_text = 'ใบงานใหม่';
            break;
            case "doing":
                $show_status_text = 'ใบงานกำลังทำ';
            break;
            default:
                $show_status_text = 'ใบงานเสร็จ';
            break;
        }
        return view('job::job.table.index', ['show_status' => $show_status,'show_status_text'=>$show_status_text,'adminInit'=>$adminInit]);
    }

    public function datatable_ajax(Request $request){
        if ($request->ajax()) {
            //init datatable
            $dt_name_column = array('id', 'type', 'code', 'cust_id', 'cust_user_id', 'total_amount', 'bank', 'status', 'created_by', 'locked_by', 'created_at');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            $show_status = $request->get('show_status');

            // create job object 
            $o_job = new Jobs;

            switch ($show_status) {
                case 'waiting':
                    $o_job = $o_job->Where('status', '<', 8);
                    $o_job = $o_job->Where('locked', '!=', 1);
                    break;
                case 'doing':
                    $o_job = $o_job->Where('status', '<', 8);
                    $o_job = $o_job->Where('locked', '=', 1);
                    break;
                case 'complete':
                    $o_job = $o_job->Where('status', '>=', 8);
                    break;
            }


            

            // add search query if have search from datables
            if (!empty($dt_search)) {
                $o_job->Where(function ($query) use ($dt_search) {
                    $query->orWhere('cust_id', 'like', "%" . $dt_search . "%")
                        ->orWhere('cust_user_id', 'like', "%" . $dt_search . "%")
                        ->orWhere('code', 'like', "%" . $dt_search . "%");
                });
            }

            if(!empty($request->get('start_date'))){
                 $o_job = $o_job->Where('created_at', '>=', $request->get('start_date').' 00:00:00'); 
            }

            if(!empty($request->get('end_date'))){
                 $o_job = $o_job->Where('created_at', '<=', $request->get('end_date').' 23:59:59'); 
            }

            if(!empty($request->get('job_type'))){
                 $o_job = $o_job->Where('type', '=', $request->get('job_type')); 
            }

            if(!empty($request->get('job_status'))){
                 $o_job = $o_job->Where('status', '=', $request->get('job_status')); 
            }

            if(!empty($request->get('is_auto'))){
                if($request->get('is_auto')==1){
                    $o_job = $o_job->Where('is_auto', '=', 1); 
                }else{
                    $o_job = $o_job->Where('is_auto', '=', "0"); 
                } 
            }

            $dt_total = $o_job->count();

            // set query order & limit from datatable
            $o_job->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query job list
            $jobs = $o_job->with(['customer', 'customer_user', 'from_bank', 'to_bank', 'customer_bank', 'promotion_name', 'manual_credit', 'locked_user', 'created_user'])->get();

            // prepare datatable for resonse
            $tables = Datatables::of($jobs)
                ->addIndexColumn()
                ->setTotalRecords($dt_total)
                ->setFilteredRecords($dt_total)
                ->setOffset($dt_start)
                ->setRowId('id')
                ->setRowClass(function ($record) {
                    return 'job_row ' . $record->type['class'] . ' ' . $record->status['class'];
                })
                ->setTotalRecords($dt_total)
                ->editColumn('type', function ($record) {
                    // switch ($record->status['value']) {
                    //     case 9:
                    //         $show_type = '<span class="badge rounded-pill bg-success">' . $record->type['text'] . ' </span>';
                    //         break;
                    //     case 8:
                    //         $show_type = '<span class="badge rounded-pill bg-secondary">' . $record->type['text'] . ' </span>';
                    //         break;
                    //     case 1:
                    //         $show_type = '<span class="badge rounded-pill bg-danger">' . $record->type['text'] . ' </span>';
                    //         break;
                    //     case '':
                    //         $show_type = '<span class="badge rounded-pill bg-info">' . $record->type['text'] . ' </span>';
                    //         break;
                    // }
                    // if ($record->is_auto) {
                    //     $show_type .= ' <i class="lni lni-android"></i>';
                    // } else {
                    //     $show_type .= ' <i class="lni lni-user"></i>';
                    // }
                    // return $show_type;
                    return $this->table_show_type($record) ;
                })
                ->editColumn('code', function ($record) {
                    // $job_detail_link = '/admin/job/' . $record->type['type'] . '/' . $record->id;
                    // $icon = '<span class="badge rounded-pill bg-primary"><i class="lni lni-pencil"></i></span>';
                    // $show_code = '<a href="' . $job_detail_link . '" >' . $icon . ' ' . $record->code . '</a>';
                    // return $show_code;
                    return $this->table_show_code($record) ;
                })
                ->editColumn('cust_id', function ($record) {
                    // $cust_detail_link = '/admin/customer/edit/' . $record->cust_id;
                    // $icon = '<a href="' . $cust_detail_link . '" >' . '<span class="badge rounded-pill bg-primary"><i class="lni lni-pencil"></i></span>' . '</a> ';
                    // $show_cust = (!empty($record->customer)) ? $record->customer->name : '';
                    // return $icon . $show_cust;
                    return $this->table_show_customer($record) ;
                })
                ->editColumn('cust_user_id', function ($record) {
                    return (!empty($record->customer_user)) ? $record->customer_user->username : '';
                })
                ->addColumn('bank', function ($record) {
                    // $show_bank = '';
                    // if ($record->type['value'] == 2) {
                    //     if (!empty($record->to_bank)) {
                    //         $show_bank_acc_no = ' *' . substr($record->to_bank->acc_no, -4);
                    //         list($acc_fname, $acc_last_name) =  explode(' ', $record->to_bank->acc_name);

                    //         $show_bank_acc_name = mb_substr($acc_fname, 0, 5) . '* ' . mb_substr($acc_last_name, 0, 5) . '*';

                    //         $show_bank = '[' . $record->to_bank->bank_names->code . ']' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
                    //     } else {
                    //         if (!empty($record->customer_bank)) {
                    //             $show_bank_acc_no = ' *' . substr($record->customer_bank->acc_no, -4);
                    //             list($acc_fname, $acc_last_name) =  explode(' ', $record->customer_bank->acc_name);

                    //             $show_bank_acc_name = mb_substr($acc_fname, 0, 5) . '* ' . mb_substr($acc_last_name, 0, 5) . '*';

                    //             $show_bank = '[' . $record->customer_bank->bank_names->code . ']' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
                    //         }
                    //     }
                    // } else {
                    //     if (!empty($record->from_bank)) {
                    //         $show_bank_acc_no = ' *' . substr($record->from_bank->acc_no, -3);
                    //         list($acc_fname, $acc_last_name) =  explode(' ', $record->from_bank->acc_name);

                    //         $show_bank_acc_name = mb_substr($acc_fname, 0, 3) . '* ' . mb_substr($acc_last_name, 0, 3) . '*';

                    //         $show_bank = '[' . $record->from_bank->bank_names->code . '] ' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
                    //     }
                    // }

                    // if (!empty($record->promotion_id)) {
                    //     $show_bank =  (!empty($record->promotion_name->pro_name)) ? "[Pro] " . $record->promotion_name->pro_name : '';
                    // }

                    // if ($record->channel == 5) {
                    //     $reason = (!empty($record->manual_credit->reason)) ? $record->manual_credit->reason : '';
                    //     $ref_code = (!empty($record->manual_credit->ref_code)) ? $record->manual_credit->ref_code : '';
                    //     $show_bank = '[เติมมือ] ' . $reason . ' (' . $ref_code . ')';
                    // }

                    // return  $show_bank;
                    return $this->table_show_bank($record) ;
                })
                ->editColumn('status', function ($record) {
                    // $show_status = '';
                    // switch ($record->status['value']) {
                    //     case 9:
                    //         $show_status = '<span class="badge rounded-pill bg-success">' . $record->status['text'] . ' </span>';
                    //         break;
                    //     case 8:
                    //         $show_status = '<span class="badge rounded-pill bg-secondary">' . $record->status['text'] . ' </span>';
                    //         break;
                    //     case 1:
                    //         $show_status = '<span class="badge rounded-pill bg-danger">' . $record->status['text'] . ' </span>';
                    //         break;
                    //     case '':
                    //         $show_status = '<span class="badge rounded-pill bg-info">' . $record->status['text'] . ' </span>';
                    //         break;
                    // }
                    // return $show_status;
                    return $this->table_show_status($record) ;
                })
                ->editColumn('created_by', function ($record) {
                    return (!empty($record->created_user)) ? $record->created_user->name : '';
                })
                ->editColumn('locked_by', function ($record) {
                    return (!empty($record->locked_user)) ? $record->locked_user->name : '';
                })
                ->editColumn('created_at', function ($record) {
                    return $record->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($record) {
                    return $this->table_show_action($record) ;
                })
                ->escapeColumns([]);
            // response datatable json
            return $tables->make(true);
        }
    }
    
    public function table_show_type($record){
        switch ($record->status['value']) {
            case 9:
                $show_type = '<span class="badge rounded-pill bg-success">' . $record->type['text'] . ' </span>';
                break;
            case 8:
                $show_type = '<span class="badge rounded-pill bg-secondary">' . $record->type['text'] . ' </span>';
                break;
            case 1:
                $show_type = '<span class="badge rounded-pill bg-danger">' . $record->type['text'] . ' </span>';
                break;
            default:
                $show_type = '<span class="badge rounded-pill bg-info">' . $record->type['text'] . ' </span>';
                break;
        }

        if ($record->is_auto) {
            $show_type .= ' <i class="lni lni-android"></i>';
        } else {
            $show_type .= ' <i class="lni lni-user"></i>';
        }

        return $show_type;
    }

    public function table_show_code($record){
        $job_detail_link = '/admin/job/' . $record->type['type'] . '/' . $record->id;
        $icon = '<span class="badge rounded-pill bg-primary"><i class="lni lni-pencil"></i></span>';
        $show_code = '<a href="' . $job_detail_link . '" >' . $icon . ' ' . $record->code . '</a>';
        return $show_code;
    }
    
    public function table_show_customer($record){
        $cust_detail_link = '/admin/customer/edit/' . $record->cust_id;
        $icon = '<a href="' . $cust_detail_link . '" >' . '<span class="badge rounded-pill bg-primary"><i class="lni lni-pencil"></i></span>' . '</a> ';
        $show_cust = (!empty($record->customer)) ? $record->customer->name : '';
        return $icon . $show_cust;
    }

    public function table_show_bank($record){
        $show_bank = '';
        if ($record->type['value'] == 2) {
            if (!empty($record->to_bank)) {
                $show_bank_acc_no = ' *' . substr($record->to_bank->acc_no, -4);
                list($acc_fname, $acc_last_name) =  explode(' ', $record->to_bank->acc_name);

                $show_bank_acc_name = mb_substr($acc_fname, 0, 5) . '* ' . mb_substr($acc_last_name, 0, 5) . '*';

                $show_bank = '[' . $record->to_bank->bank_names->code . ']' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
            } else {
                if (!empty($record->customer_bank)) {
                    $show_bank_acc_no = ' *' . substr($record->customer_bank->acc_no, -4);
                    list($acc_fname, $acc_last_name) =  explode(' ', $record->customer_bank->acc_name);

                    $show_bank_acc_name = mb_substr($acc_fname, 0, 5) . '* ' . mb_substr($acc_last_name, 0, 5) . '*';

                    $show_bank = '[' . $record->customer_bank->bank_names->code . ']' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
                }
            }
        } else {
            if (!empty($record->from_bank)) {
                $show_bank_acc_no = ' *' . substr($record->from_bank->acc_no, -3);
                list($acc_fname, $acc_last_name) =  explode(' ', $record->from_bank->acc_name);

                $show_bank_acc_name = mb_substr($acc_fname, 0, 3) . '* ' . mb_substr($acc_last_name, 0, 3) . '*';

                $show_bank = '[' . $record->from_bank->bank_names->code . '] ' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
            }
        }

        if (!empty($record->promotion_id)) {
            $show_bank =  (!empty($record->promotion_name->pro_name)) ? "[Pro] " . $record->promotion_name->pro_name : '';
        }

        if ($record->channel == 5) {
            $reason = (!empty($record->manual_credit->reason)) ? $record->manual_credit->reason : '';
            $ref_code = (!empty($record->manual_credit->ref_code)) ? $record->manual_credit->ref_code : '';
            $show_bank = '[เติมมือ] ' . $reason . ' (' . $ref_code . ')';
        }

        return  $show_bank;
    }
    
    public function table_show_status($record){
        $show_status = '';
        switch ($record->status['value']) {
            case 9:
                $show_status = '<span class="badge rounded-pill bg-success">' . $record->status['text'] . ' </span>';
                break;
            case 8:
                $show_status = '<span class="badge rounded-pill bg-secondary">' . $record->status['text'] . ' </span>';
                break;
            case 1:
                $show_status = '<span class="badge rounded-pill bg-danger">' . $record->status['text'] . ' </span>';
                break;
            default:
                $show_status = '<span class="badge rounded-pill bg-info">' . $record->status['text'] . ' </span>';
                break;
        }
        return $show_status;
    }

    public function table_show_action($record){
        $allow_unlock = true;
        $allow_edit = true;
        $show_warning = true;
        $job_detail_link = '/admin/job/' . $record->type['type'] . '/' . $record->id;

        $action_btn = '<div class="square-buttons d-flex  gap-1 ">';

        // if ($record->status == 1) {
        //     $action_btn .= '<a onclick="setStatus(' . $record->id . ',0)" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-checkmark"></i></a></a>';
        // } else {
        //     $action_btn .=  '<a onclick="setStatus(' . $record->id . ',1)" href="javascript:void(0);"  class="btn btn-outline-warning"><i class="lni lni-close"></i></a></a>';
        // }
        if ($allow_edit) {
            $action_btn .= '<a href="' .  $job_detail_link  . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a></a>';
        }
        // $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a></a>';

        if ($record->status['value'] >= 8) {
            $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-info"><i class="lni lni-eye"></i></a></a>';
        } else {
            if ($record->locked['value']) {
                if ($allow_unlock) {
                    $action_btn .= '<a onclick="" href="'.route('admin.job.job.unlock',$record->id).'" class="btn btn-outline-warning"><i class="lni lni-unlock"></i></a></a>';
                }
            } else {
                // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-lock"></i></a></a>';
            }
        }

        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-lock"></i></a></a>';

        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-unlock"></i></a></a>';

        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-secondary"><i class="lni lni-eye"></i></a></a>';

        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-primary"><i class="lni lni-files"></i></a></a>';
        if ($show_warning) {
            $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-warning"></i></a></a>';
        }

        $action_btn .= '</div>';

        return $action_btn;
    }

    public function view(Request $request, $id = 0){
        $job = Jobs::find($id);
        if($job->type==1){
            return $this->deposit($request, $id) ;
        }else{
            return $this->withdraw($request, $id) ;
        }
    }

    //////////////////////// depsit detail  ////////////////////////
    public function deposit(Request $request, $id = 0){
        $adminInit = $this->adminInit() ;
        $user  = Auth::guard('admin')->user() ;
        $roles= ['admin'];
        $allow_edit = true;
        $job = [];
 
        $job = $job = Jobs::where('id',$id)->with(['customer','customer_user','from_bank','to_bank','created_user','updated_user','locked_user','approved_user','banker_user','cancel_user','refund_user','audit_user'])->first();

        if($job->locked['value']==0){
            if($job->status['value']<8){
                $job->status=1;
                $job->locked=1;
                $job->locked_by=$user->id;
                $job->locked_at=date('Y-m-d H:i:s');
                $job->save();

                $this->notJobUnlock($id);
            }
        }else{
            if($job->status<8 && $job->locked_by!=$user->id){
                return redirect()->route('admin.job.job.index');
            }
        }

        $data['status'] = [
            ['id' => 0, 'name' => 'รอทำ'],
            ['id' => 1, 'name' => 'กำลังทำ'],
            ['id' => 8, 'name' => 'ยกเลิก'],
            ['id' => 9, 'name' => 'เสร็จ']
        ];

        $o_statement = new Statements;
        if (empty($job->statement_id)) {
            $s_time = date('Y-m-d H:i:s', date(strtotime("-10 minutes", strtotime($job->transfer_datetime))));
            $e_time = date('Y-m-d H:i:s', date(strtotime("10 minutes", strtotime($job->transfer_datetime))));
            $statements = $o_statement->with('bank_web')->where('acc_id', $job->to_bank_id)->where('report_datetime', '>=', $s_time)->where('report_datetime', '<=', $e_time)->where('report_value', $job->amount)->get();
        } else {
            $statements = $o_statement->with('bank_web')->where('id', $job->statement_id)->get();
        }

        // get job history
        $o_job_history = new Jobs;
        $jobs = $o_job_history->where('cust_id',$job->cust_id)->where('id','<',$job->id)->with(['customer','customer_user','from_bank','to_bank','created_user'])->orderBy('id','desc')->offset(0)->limit(5)->get();

        // pre($job->toArray());

        return view('job::job.deposit.deposit', [
            'job' => $job,
            'jobs'=>$jobs, 
            'data' => $data, 
            'statements' => $statements, 
            'allow_edit' => $allow_edit,
            'adminInit'=>$adminInit
        ]);
    }

    public function deposit_confirm(Request $request){
        if(!empty($request->get('job_id'))){
            if(!empty($request->get('job_id'))){
                $resp = $this->transfer($request) ;
                // return response()->json($resp);

                if($resp['status']){
                    $resp = [
                        'code'=>200,
                        'status'=>1,
                        'msg'=>$resp['msg']
                     ]; 
                }else{
                     $resp = [
                        'code'=>800,
                        'status'=>0,
                        'msg'=>$resp['msg']
                     ]; 
                }

            }else{
                $resp = [
                    'code'=>700,
                    'status'=>0,
                    'msg'=>'ไม่มี Job ID'
                ];
            }
        }else{
            $resp = [
                'code'=>700,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function deposit_cancel(Request $request){
        if(!empty($request->get('job_id'))){
            $user = \Auth::user();

            $job = Jobs::find($request->get('job_id'));

            $job->status = 8 ;
            $job->cancel_note = $request->get('cancel_note') ;
            $job->cancel_by = $user->id ;
            $job->cancel_at = date('Y-m-d H:i:s');

            $save_status = $job->save() ;

            if(!empty($save_status)){
                $resp = [
                    'code'=>200,
                    'status'=>1,
                    'msg'=>'success'
                ];
            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'บันทึกใบงานไม่ได้'
                ];
            }

        }else{
            $resp = [
                'code'=>300,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function deposit_unlock(Request $request){
        if(!empty($request->get('job_id'))){
            $job = Jobs::find($request->get('job_id'));

            $job->locked = 0 ;
            $job->locked_by = 0 ;

            $save_status = $job->save() ;

            if(!empty($save_status)){
                $resp = [
                    'code'=>200,
                    'status'=>1,
                    'msg'=>'success'
                ];
            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'ปลดล็อกใบงานไม่ได้'
                ];
            }

        }else{
            $resp = [
                'code'=>300,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    /////////////////////// withdraw detail  ////////////////////////

    public function withdraw(Request $request, $id = 0){   
         $adminInit = $this->adminInit() ;
        $user  = Auth::guard('admin')->user() ;
        $roles= ['admin'];
        $allow_edit = true;

        if (!empty($id)) {
            // $job = Jobs::find($id);
            $o_job = new Jobs;
            $job = $o_job->where('id',$id)->with(['customer','customer_user','from_bank','to_bank','withdraw_from_bank','created_user','updated_user','locked_user','approved_user','banker_user','cancel_user','refund_user','audit_user'])->first();

        }


        // redirect if locked
        if($job->status['value']<8&&$job->locked['value']==1&&$job->locked_by!=$user->id){
            return redirect()->route('admin.job.job.index');
        }

        if($job->locked['value']==0){
            if($job->status['value']<8){
                $job->status=1;
                $job->locked=1;
                $job->locked_by=$user->id;
                $job->locked_at=date('Y-m-d H:i:s');
                $job->save();
                // noti job locked
                $this->notJobUnlock($id);
            }
        }else{
            if($job->status<8 && $job->locked_by!=$user->id){
                return redirect()->route('admin.job.job.index');
            }
        }

        $data['status'] = [
            ['id' => 0, 'name' => 'รอทำ'],
            ['id' => 1, 'name' => 'กำลังทำ'],
            ['id' => 8, 'name' => 'ยกเลิก'],
            ['id' => 9, 'name' => 'เสร็จ']
        ];

        // get job statemetn
        $o_statement = new Statements;
        if (empty($job->statement_id)) {
            $s_time = date('Y-m-d H:i:s', date(strtotime("-10 minutes", strtotime($job->transfer_datetime))));
            $e_time = date('Y-m-d H:i:s', date(strtotime("10 minutes", strtotime($job->transfer_datetime))));
            $statements = $o_statement->with('bank')->where('acc_id', $job->to_bank_id)->where('report_datetime', '>=', $s_time)->where('report_datetime', '<=', $e_time)->where('report_value', $job->amount)->get();
        } else {
            $statements = $o_statement->with('bank')->where('id', $job->statement_id)->get();
        }

        // get job history
        $o_job_history = new Jobs;
        $jobs = $o_job_history->where('cust_id',$job->cust_id)->where('id','<',$job->id)->with(['customer','customer_user','from_bank','to_bank','created_user'])->orderBy('id','desc')->offset(0)->limit(5)->get();

        if($user->id==$job->locked_by){
            $allow_edit = true;
        }else{
            if(in_array('admin',$roles)||in_array('owner',$roles)||in_array('company_manager',$roles)){
                $allow_edit = true;
            }else{
                $allow_edit = false;
            }
        }

        // bank tranfer
        $bank = PartnerBank::orWhere('bank_type',2)->orWhere('bank_type',3)->where('bank_status',1)->where('bank_transfer_auto',1)->get();

        $current_promotion = [] ;
        if(isset($job->customer->current_promotion_id)&&$job->customer->current_promotion_id!=0){
            $current_promotion = Promotions::find($job->customer->current_promotion_id) ;
        }

        return view('job::job.withdraw.withdraw', [
            'job' => $job, 
            'jobs'=>$jobs,
            'data' => $data, 
            'bank'=>$bank,
            'current_promotion'=>$current_promotion,
            'statements' => $statements, 
            'allow_edit' => $allow_edit,
            'adminInit'=>$adminInit
            ]
        );
    }

    public function withdraw_confirm(Request $request){
        if(!empty($request->get('job_id'))){
            $user = \Auth::user();
            $job_id = $request->get('job_id') ;
            $job = Jobs::find($request->get('job_id'));
            $job->status = 9 ;
            $job->approved = 1;
            
            $job->approved_by = $user->id ;
            $job->approved_at = date('Y-m-d H:i:s');

            $save_status = $job->save() ;
            if(!empty($save_status)){
                $resp = [
                    'code'=>200,
                    'status'=>1,
                    'msg'=>'success'
                ];
            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'บันทึกใบงานไม่ได้'
                ];
            } 
        }else{
            $resp = [
                'code'=>700,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function withdraw_tranfer(Request $request){
        if(!empty($request->get('job_id'))){
            $job_id = $request->get('job_id') ;
            $bank_id = $request->get('bank_id') ;
            $resp = $this->TransferMoney($job_id, $bank_id) ;
            return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }else{
            $resp = [
                'code'=>700,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
    
    public function withdraw_cancel(Request $request){
        if(!empty($request->get('job_id'))){
            $user = \Auth::user();
            $job_id = $request->get('job_id') ;
            $job = Jobs::find($request->get('job_id'));

            $job->status = 8 ;
            $job->cancel_note = $request->get('cancel_note') ;
            $job->cancel_by = $user->id ;
            $job->cancel_at = date('Y-m-d H:i:s');
            
            if($request->get('refund')){
                $job->refund_by = $user->id ;
                $job->refund_at = date('Y-m-d H:i:s');
            }

            $save_status = $job->save() ;
            
            if($request->get('refund')){
                $resp = $this->refund($job_id) ;
            }else{
               if(!empty($save_status)){
                    $resp = [
                        'code'=>200,
                        'status'=>1,
                        'msg'=>'success'
                    ];
                }else{
                    $resp = [
                        'code'=>400,
                        'status'=>0,
                        'msg'=>'บันทึกใบงานไม่ได้'
                    ];
                } 
            }

            

        }else{
            $resp = [
                'code'=>300,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function withdraw_unlock(Request $request){
        if(!empty($request->get('job_id'))){
            $job = Jobs::find($request->get('job_id'));

            $job->locked = 0 ;
            $job->locked_by = 0 ;

            $save_status = $job->save() ;

            if(!empty($save_status)){
                $resp = [
                    'code'=>200,
                    'status'=>1,
                    'msg'=>'success'
                ];
            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'บันทึกใบงานไม่ได้'
                ];
            }

        }else{
            $resp = [
                'code'=>300,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return response()->json($resp);
    }

    /////////////////////// job action   ////////////////////////

    public function unlock(Request $request,$job_id,$redirect=1){
        $user = \Auth::user();
        $o_job = new Jobs;
        $job = $o_job->where('id',$job_id)->first();
        $allow_user = [1,3,6,8,9,10] ;
        if(in_array($user->id,$allow_user)||$job->locked_by==$user->id){
            $job->status=0;
            $job->locked=0;
            $job->locked_by=0;
            $job->save();

            // noti unlock
            $this->notJobUnlock($job_id) ;
            if($redirect){
                if($job->type['value']==1){
                    return redirect()->route('admin.job.deposit.deposit', $job_id);
                }else{
                    return redirect()->route('admin.job.withdraw.withdraw', $job_id);
                }
            }else{
                return redirect('/admin/job');
            }
           
            
        }else{
            return redirect('/admin/job');
        }
    }

    public function transfer(Request $request,$is_auto=0){

        if(!empty($request->get('job_id'))){
            $job = Jobs::with(['customer','customer_user'])->where('id',$request->get('job_id'))->first();
            $board = PartnerBoards::where('is_active',1)->first();
            $prefix = $board->bd_prefix ;
            $api_key = $board->api_key ;
            // if job not cancel or complete
            if($job->status['value'] < 8){
                $data = [
                    'Agent' => $prefix,
                    'CompanyKey' => $api_key,
                    'Username' => $job->customer_user->username,
                    'CustID' => $job->cust_id,
                    'Amount' => $job->amount,
                    'TxnId' => $job->code,
                    'Type' => $job->type['value'],
                    'IsFullAmount'=>false
                ];

                $o_member = new MemberController ;
                $resp = $o_member->transfer($data);

                // webHook($resp, "2381dc38-d00f-4584-9db9-36b8cc59db86");

                if($resp['status']){
                    if($job->type['value']==1){
                        $job->status = 9 ;
                    }else{
                        if($is_auto){
                            $job->status = 9 ;
                        }else{
                            $job->status = 2 ;
                        }
                    }
                    $job->tranfer_refno = $resp['data']['refno'] ;
                    $job->turnover = $resp['data']['turnover'] ;
                    $job->balance_bf = $resp['data']['balance_bf']  ;
                    $job->balance_af = $resp['data']['balance']  ;
                    // $job->count_deposit = $job->count_deposit +1 ;
                    $job->log_transfer_request = json_encode($data,JSON_UNESCAPED_UNICODE) ;
                    $job->log_transfer_response = json_encode($resp,JSON_UNESCAPED_UNICODE) ;
                    $job->completed_at = date('Y-m-d H:i:s');
                    $job->save();

                    // customer update
                    $cust = Customers::find($job->cust_id);
                    if(empty($cust->first_job_amount)){
                        $cust->first_job_id = $job->id ;
                        $cust->first_job_amount = $job->amount ;
                        $cust->first_job_date = $job->created_at ;
                    }

                    if(empty($job->promotion_id)){
                        if($job->type['value']==1){
                            $cust->count_deposit = $cust->count_deposit+1 ;
                        }else{
                            $cust->count_withdraw =  $cust->count_withdraw+1 ;
                            $cust->current_promotion_id =  0 ;
                            $cust->current_promotion_amount = 0;
                            $cust->current_promotion_date = date('Y-m-d H:i:s');
                        }
                    }

                    if($job->type['value']==1){
                        $cust->last_deposit_at = date('Y-m-d H:i:s');
                    }else{
                        $cust->last_withdraw_at = date('Y-m-d H:i:s');
                    }

                    $amount = $job->amount;
                    if($job->type['value']==2){
                        $amount = $amount * -1;
                    }
                    $cust->balance = $cust->balance + $amount;

                    $cust->save();

                    $resp = [
                        'code'=>200,
                        'status'=>1,
                        'msg'=>'สำเร็จ',
                        'resp'=>$resp
                    ];

                }else{
                    
                    $job->log_transfer_request = json_encode($data,JSON_UNESCAPED_UNICODE) ;
                    $job->log_transfer_response = json_encode($resp,JSON_UNESCAPED_UNICODE) ;
                    $job->status = 8 ;
                    $job->note ="ระบบอัตโนมัติ ถอนเงินไม่สำเร็จ" ;
                    $job->locked_by = NULL ;
                    $job->locked_at = NULL ;
                    $job->save();

                    $resp = [
                        'code'=>300,
                        'status'=>0,
                        'msg'=>'โอนเงินไม่สำเร็จ',
                        'resp'=>$resp
                    ];
                }

            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'สถานะงานไม่สามารถดำเนินการได้',
                    'job'=>$job->status
                ];
            }
        }else{
            $resp = [
                'code'=>500,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return $resp ;
    }

    public function TransferMoney($id, $bank_id){
        $user = \Auth::user();
        $job = Jobs::where('id', $id)->with('customer')->first()->toArray();

        /**
         * Check Status
        */
        if($job['status']['value'] != 1){
            return ['status' => 0, 'code' => 1002, 'data' => [], 'msg' => 'สถานะนี้ไม่สามารถทำรายการได้ '.$job['status']['value'], 'errors' => []];
        }

        /**
         * Select Bank for transfer
        */
        $bank = PartnerBank::find($bank_id);

        if($bank['bank_status'] != 1){
            return ['status' => 0, 'code' => 1003, 'data' => [], 'msg' => 'บัญชีปิดอยู่ ไม่สามารถทำรายการได้', 'errors' => []];
        }
        if($bank['bank_transfer_auto'] != 1){
            return ['status' => 0, 'code' => 1004, 'data' => [], 'msg' => 'บัญชีไม่เปิดให้ถอนออโต้ ไม่สามารถทำรายการได้', 'errors' => []];
        }
        if($bank['bank_transfer_auto_locked'] != 0){
            return ['status' => 0, 'code' => 1005, 'data' => [], 'msg' => 'บัญชีถูกล็อก ไม่สามารถทำรายการได้', 'errors' => []];
        }

        /**
         * Lock Bank Account not transfer any order
        */
        PartnerBank::where('id', $bank['id'])->update(['bank_transfer_auto_locked_at' => date("Y-m-d H:i:s"), 'bank_transfer_auto_locked' => 1]);

        $arrLog = [
            'job_id' => $id,
            'acc_id' => $bank['id'],
            'step' => 1
        ];
        $log = TransferLogs::create($arrLog);

        /**
         * Start
        */
        $device_id = $bank['device_id'];
        $pin = $bank['refresh_token'];

        $arrPrepare = [
            'deviceId' => $device_id,
            'pin' => $pin,
            'accountNo' => $bank['bank_number']
        ];

        $scb = new ScbController();

        $sum = $scb->summary(new Request($arrPrepare));
        if($sum['data']['status']['code'] != 1000){
            $arrLog = [
                'status' => 3,
                'step' => 2,
                'response_body' => json_encode($sum, JSON_UNESCAPED_UNICODE),
                'response_at' => datetime()
            ];
            TransferLogs::where('id', $log->id)->update($arrLog);

            PartnerBank::where('id', $bank['id'])->update(['bank_transfer_auto_locked_at' => date("Y-m-d H:i:s"), 'bank_transfer_auto_locked' => 0, 'last_push' => date("Y-m-d H:i:s"), 'app_response' => json_encode($sum, JSON_UNESCAPED_UNICODE)]);

            return ['status' => 0, 'code' => 1006, 'data' => [], 'msg' => 'ไม่สามารถทำรายการได้', 'errors' => []];
        }

        $totalAvailableBalance = $sum['data']['totalAvailableBalance'];
        PartnerBank::where('id', $bank->id)->update(['bank_amount' => $totalAvailableBalance, 'bank_amount_at' => date("Y-m-d H:i:s"), 'last_push' => date("Y-m-d H:i:s")]);

        $acc_no = $job['customer']['bank_transfer']['acc_no'];
        $acc_ref = $job['customer']['bank_transfer']['bank_names']['ref'];
        $amount = $job['total_amount'];

        $tranRequest = [
            'deviceId' => $device_id,
            'pin' => $pin,
            'accountNo' => $bank['bank_number'],
            'accountTo' => $acc_no,
            'accountToBankCode' => $acc_ref,
            'amount' => $amount
        ];

        $reqLog = json_encode($tranRequest, JSON_UNESCAPED_UNICODE);

        $arrLog = [
            'request_body' => encrypter('encrypt', $reqLog),
            'request_at' => datetime(),
            'status' => 1,
            'step' => 3
        ];
        TransferLogs::where('id', $log->id)->update($arrLog);

        $arrJobUpdate = [
            'withdraw_bank_id' => $bank['id'],
            'withdraw_request' => encrypter('encrypt', $reqLog),
            'withdraw_request_at' => datetime(),
            'withdraw_balance_bf' => $totalAvailableBalance,
            'banker'=>1,
            'banker_by'=>$user->id,
            'banker_at'=>date('Y-m-d H:i:s'),
            'audit'=>1,
            'audit_by'=>$user->id,
            'audit_at'=>date('Y-m-d H:i:s')
        ];
        Jobs::where('id', $id)->update($arrJobUpdate);

        /**
         * Start Transfer
        */
        $tran = $scb->withdraw(new Request($tranRequest));

        if($tran['data']['status']['code'] != 1000){
            $arrLog = [
                'status' => 3,
                'step' => 4,
                'response_body' => json_encode($tran, JSON_UNESCAPED_UNICODE),
                'response_at' => datetime()
            ];
            TransferLogs::where('id', $log->id)->update($arrLog);

            PartnerBank::where('id', $bank['id'])->update(['bank_transfer_auto_locked_at' => date("Y-m-d H:i:s"), 'bank_transfer_auto_locked' => 0, 'last_push' => date("Y-m-d H:i:s"), 'app_response' => json_encode($tran, JSON_UNESCAPED_UNICODE)]);

            return ['status' => 0, 'code' => 1007, 'data' => [], 'msg' => 'ไม่สามารถทำรายการได้', 'errors' => []];
        }

        $arrLog = [
            'response_body' => json_encode($tran, JSON_UNESCAPED_UNICODE),
            'response_at' => datetime(),
            'step' => 5,
            'status' => 2,
            'msg' => $tran['data']['status']['description']
        ];
        TransferLogs::where('id', $log->id)->update($arrLog);

        $totalAvailableBalance = $tran['data']['data']['remainingBalance'];
        $arrJobUpdate = [
            'withdraw_response' => json_encode($tran, JSON_UNESCAPED_UNICODE),
            'withdraw_response_at' => datetime(),
            'withdraw_balance_af' => $totalAvailableBalance,
            'withdraw_transfer_ref' => $tran['data']['data']['transactionId'],
            'status' => 7
        ];
        Jobs::where('id', $id)->update($arrJobUpdate);

        PartnerBank::where('id', $bank['id'])->update(['bank_amount' => $totalAvailableBalance, 'bank_amount_at' => date("Y-m-d H:i:s"), 'bank_transfer_auto_locked_at' => date("Y-m-d H:i:s"), 'bank_transfer_auto_locked' => 0, 'last_push' => date("Y-m-d H:i:s"), 'app_response' => json_encode($tran, JSON_UNESCAPED_UNICODE)]);

        return ['status' => 1, 'code' => 0, 'data' => compact('tranRequest','tran'), 'msg' => 'ทำรายการสำเร็จ', 'errors' => [],'job_id' => $id];

    }

    public function refund($job_id){

        if(!empty($job_id)){
            $job = Jobs::with(['customer','customer_user'])->where('id',$job_id)->first();
            $board = PartnerBoards::where('is_active',1)->first();
            $prefix = $board->bd_prefix ;
            $api_key = $board->api_key ;
            // if job not cancel or complete

            $code =  'WR'.date('YmdHis').substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 5);
            

            if($job->status['value'] == 8){
                $data = [
                    'Agent' => $prefix,
                    'CompanyKey' => $api_key,
                    'Username' => $job->customer_user->username,
                    'CustID' => $job->cust_id,
                    'Amount' => $job->amount,
                    'TxnId' => $code,
                    'Type' => 1 ,
                    'IsFullAmount'=>false
                ];

                $o_member = new MemberController ;
                $resp = $o_member->transfer($data);

                if($resp['status']){
                    $cust = Customers::find($job->cust_id);
                    $amount = $job->amount;
                    $cust->balance = $cust->balance + $amount;
                    $cust->save();

                    // update code back
                    $job->code = $code;
                    $job->save();

                    $resp = [
                        'code'=>200,
                        'status'=>1,
                        'msg'=>'สำเร็จ',
                        'resp'=>$resp
                    ];

                }else{

                    $resp = [
                        'code'=>300,
                        'status'=>0,
                        'msg'=>'โอนเงินไม่สำเร็จ',
                        'resp'=>$resp
                    ];
                }

            }else{
                $resp = [
                    'code'=>400,
                    'status'=>0,
                    'msg'=>'สถานะงานไม่สามารถดำเนินการได้',
                    'job'=>$job->status
                ];
            }
        }else{
            $resp = [
                'code'=>500,
                'status'=>0,
                'msg'=>'ไม่มี Job ID'
            ];
        }

        return $resp ;
    }

    public function set_confirm_complete(Request $request){
        $user = \Auth::user();
        $job_id = $request->get('job_id') ;
        $data  = [
            "status"=>9,
            "updated_by"=>$user->id  
        ];
        $job = Jobs::find($job_id)->update($data);

        /**
         * Check Status
        */
        if($job){
            return ['status' => 1, 'code' => 200, 'data' => [], 'msg' => 'สำเร็จ'];
        }else{
            return ['status' => 0, 'code' => 300, 'data' => [], 'msg' => 'อัพเดทสถานะไม่ได้'];
        }
    }

    //////////////////////// jobs save  ////////////////////////

    public function saveJob($data, $auto = 1, $type=1, $transfer_withdraw=0){
        if($type==1){
            return $this->save_deposit($data,$auto);
        }else{
            return $this->save_withdraw($data,$auto,$transfer_withdraw);  
        }

    }

    public function save_deposit($data, $auto = 1){
        // Check job have order
        $ck = Jobs::where('cust_id', $data['cust_id'])
            ->whereIn('status', [0,1,2,3])
            ->get();
        if($ck->count() > 0){
            return ['status' => 0, 'code' => 1001, 'data' => [], 'msg' => 'มีรายการค้างในระบบ', 'errors' => []];
        }

        // Check First Job
        $ck_first = Jobs::where('cust_id', $data['cust_id'])->where('status', 9)->where('type', 1)->get();
        $num_first = $ck_first->count();
        $data['first_job'] = ($num_first == 0) ? 1 : '0';

        if(empty($data['job_ref'])&&empty($data['code'])) {
            $code =  'D'.date('YmdHis').substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 5);
            $data['code'] = $code ;
        }

        if(empty($data['cust_user_id'])){
            $cust_user = CustomerUsers::where('cust_id', $data['cust_id'])->where('status', 1)->first();
            $data['cust_user_id'] = $cust_user->id ;
        }        


        $job = Jobs::create($data);
        $transfer = '';
        if($auto == 1) {
            $arrUpdate = [];
            $arrUpdate['locked'] = 1;
            $arrUpdate['locked_by'] = 7;
            $arrUpdate['locked_at'] = date('Y-m-d H:i:s');
            $arrUpdate['status'] = 1;
            Jobs::where('id',$job->id)->update($arrUpdate);
            // Transfer
            $transfer = $this->transfer(new Request(['job_id' => $job->id]));
        }

        $this->notiJobCreate($job->id) ;

        return ['status' => 1, 'code' => 0, 'data' => compact('data','job', 'transfer'), 'msg' => 'ทำรายการสำเร็จ', 'errors' => [],'job_id' => $job->id];
    }

    public function save_withdraw($data, $auto = 1, $transfer_withdraw=0){
        // Check job have order
        $ck = Jobs::where('cust_id', $data['cust_id'])
            ->whereIn('status', [0,1,2,3])
            ->get();
        if($ck->count() > 0){
            return ['status' => 0, 'code' => 1001, 'data' => [], 'msg' => 'มีรายการค้างในระบบ', 'errors' => []];
        }

        // Check First Job
        $ck_first = Jobs::where('cust_id', $data['cust_id'])->where('status', 9)->where('type', 1)->get();
        $num_first = $ck_first->count();
        $data['first_job'] = ($num_first == 0) ? 1 : '0';

        

        if(empty($data['job_ref'])&&empty($data['code'])) {
            $code =  'W'.date('YmdHis').substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 5);
            $data['code'] = $code ;
        }

        $cust_user = CustomerUsers::where('cust_id', $data['cust_id'])->where('status', 1)->first();
        $data['cust_user_id'] = $cust_user->id ;
        $turnover = $this->get_turnover_from_last_job($data['cust_id'], $cust_user->username) ;
        $data['turnover'] = $turnover['turnover'] ; 


        $job = Jobs::create($data);
        $transfer = [];
        if($auto == 1) {
            $arrUpdate = [];
            $arrUpdate['locked'] = 1;
            $arrUpdate['locked_by'] = 7;
            $arrUpdate['locked_at'] = date('Y-m-d H:i:s');
            $arrUpdate['status'] = 1;
            Jobs::where('id',$job->id)->update($arrUpdate);
            
             // Transfer
            if($transfer_withdraw){
                $arrUpdate['locked'] = 0;
                $arrUpdate['locked_by'] = 0;
                $arrUpdate['locked_at'] = date('Y-m-d H:i:s');
                $transfer = $this->transfer(new Request(['job_id' => $job->id]),$auto);
                $arrUpdate['status'] = 9;
                Jobs::where('id',$job->id)->update($arrUpdate);
            }

        }

        $this->notiJobCreate($job->id) ;

        return ['status' => 1, 'code' => 0, 'data' => compact('data','job','transfer'), 'msg' => 'ทำรายการสำเร็จ', 'errors' => [],'job_id' => $job->id];
    }

   

    //////////////////////// balance trunover  ////////////////////////

    public function get_turnover_from_last_job($cust_id,$username){
        $last_deposit = Jobs::where('cust_id', $cust_id)
            ->where('status', 9)
            ->where('type', 1)
            ->orderBy('created_at','desc')
            ->first();
        // print_r($last_deposit->toArray());
        if( !empty($last_deposit) ){
            $last_job_date = date("Y-m-d",strtotime($last_deposit->created_at)).' 00:00:00' ;
            $start_date = date("Y-m-d").' 00:00:00' ;
            if($last_job_date>$start_date){
                $start_date = $last_job_date  ;
            }

            $end_date = date("Y-m-d").' 23:59:59' ;

            $board = PartnerBoards::where('is_active',1)->first();
            $prefix = $board->bd_prefix ;
            $api_key = $board->api_key ;
            
            $data = [
                'Agent'=>$prefix ,
                'CompanyKey'=>$api_key,
                'Username'=>$username,
                'StartDate'=>$start_date,
                'EndDate'=>$end_date,
                'Portfolio'=>'All',
                'All'=>0,
            ];

            // print_r($data);

            $request = new Request($data);
            $o_member = new MemberController ;
            $turnover = $o_member->getTurnover($request) ;

            if(!empty( $turnover)){
                return $turnover;
            }else{
                return 0 ;
            }
        }else{
            return 0 ;
        }
    }

    public function get_balance($username){
        $request = new Request;
        $request->replace(['Username' => $username]);
        $o_member = new MemberController ;
        $balance = $o_member->balance($request) ;
        if(is_array($balance)&&!empty($balance['error'])){
            return 0 ;
        }else{
            $balance_result = $balance->getData();

            if($balance_result->status){
                return $balance_result->data->balance ;
            }else{
                return 0 ;
            }
        }
    }

    //////////////////////// noti  ////////////////////////
    public function notiJobCreate($job_id){
        $event=(!empty(env('PUSHER_JOB_NEW')))?env('PUSHER_JOB_NEW'):'crm-job-new' ;
        return pusher(json_encode($this->create_not_message($job_id),JSON_UNESCAPED_UNICODE), $event);
    }

    public function notJobUnlock($job_id){
        $event=(!empty(env('PUSHER_JOB_LOCKED')))?env('PUSHER_JOB_LOCKED'):'crm-job-locked';
        return pusher(json_encode($this->create_locked_job($job_id),JSON_UNESCAPED_UNICODE), $event); 
    }

    public function create_not_message($job_id){
        $event="crm2-my-job";
        $record = Jobs::where('id',$job_id)->with(['customer', 'customer_user', 'from_bank', 'to_bank', 'customer_bank', 'promotion_name', 'manual_credit', 'locked_user', 'created_user'])->first() ;
        if(!empty( $record )){ 
            $message = [
                'class'=>'job_row ' . $record->type['class'] . ' ' . $record->status['class'],
                'id'=>$record->id,
                'type'=>$record->type['value'],
                'type_show'=>$this->table_show_type($record),
                'code'=>$this->table_show_code($record),
                'customer'=>$this->table_show_customer($record),
                'username'=>(!empty($record->customer_user)) ? $record->customer_user->username : '',
                'amount'=>$record->total_amount,
                'bank'=>$this->table_show_bank($record),
                'status'=>$this->table_show_status($record),
                'created_by'=>(!empty($record->created_user)) ? $record->created_user->name : '',
                'locked_by'=>(!empty($record->locked_user)) ? $record->locked_user->name : '',
                'created_at'=>$record->created_at->format('Y-m-d H:i:s'), 
                'action'=>$this->table_show_action($record),
            ];
        }else{
            $message = []; 
        }
        return $message ;
    }

    public function create_locked_job($job_id){
        $message = [
            'job_id'=>$job_id,
        ];
        return $message ;
    }


}
