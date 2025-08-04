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

use Modules\Customer\Entities\Customers;
use Modules\Customer\Entities\CustomerUsers;

use Modules\Job\Entities\ManualCredit;
use Modules\Job\Entities\Jobs;
use Modules\Partner\Entities\PartnerPromotions;

use Modules\Job\Http\Controllers\JobAdminController;
use Modules\Core\Http\Controllers\AdminController;


class ManualCreditAdminController extends AdminController
{
    public function index(Request $request)
    {   
        $adminInit = $this->adminInit() ;
        return view('job::manual_credit.table.index',['adminInit'=>$adminInit]);
    }

    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            //init datatable
            $dt_name_column = array('id','job_id' ,'type', 'cust_id', 'cust_user_id', 'total_amount', 'reson','ref_code', 'status', 'created_by');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // count all job

            

            // create job object 
            $o_mc = new ManualCredit;

            

            // add search query if have search from datables
            if (!empty($dt_search)) {
                $o_mc->Where(function ($query) use ($dt_search) {
                    $query->orWhere('cust_id', 'like', "%" . $dt_search . "%")
                        ->orWhere('cust_user_id', 'like', "%" . $dt_search . "%")
                        ->orWhere('code', 'like', "%" . $dt_search . "%");
                });
            }

            if(!empty($request->get('start_date'))){
                $o_mc = $o_mc->Where('created_at', '>=', $request->get('start_date').' 00:00:00'); 
            }

            if(!empty($request->get('end_date'))){
                $o_mc = $o_mc->Where('created_at', '<=', $request->get('end_date').' 23:59:59'); 
            }

            if(!empty($request->get('job_type'))){
                $o_mc = $o_mc->Where('job_type', $request->get('job_type')); 
            }

            if(!empty($request->get('status'))){
                if($request->get('status')==1){
                    $o_mc = $o_mc->Where('status', 1); 
                }else{
                    $o_mc = $o_mc->Where('status', "0"); 
                } 
            }
           
            $dt_total = $o_mc->count();

            // set query order & limit from datatable
            $o_mc->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query job list
            $mcs = $o_mc->with(['customer', 'customer_user', 'created_user', 'updated_user'])->get();

            // prepare datatable for resonse
            $tables = Datatables::of($mcs)
                ->addIndexColumn()
                ->setTotalRecords($dt_total)
                ->setFilteredRecords($dt_total)
                ->setOffset($dt_start)
                ->setRowId('id')
                ->setRowClass(function ($record) {
                    return 'cm_row '  ; //. $record->type['class'] ;
                })
                ->setTotalRecords($dt_total)
                ->editColumn('job_type', function ($record) {
                    //$show_type = '' ;
                    switch ($record->job_type) {
                        case 1:
                            $show_type = '<span class="badge rounded-pill bg-success">เพิ่มเครดิต</span>';
                            break;
                        default:
                            $show_type = '<span class="badge rounded-pill bg-danger">ลดเครดิต</span>';
                            break;
                    }

                    return $show_type;
                })
                ->editColumn('cust_id', function ($record) {
                    $cust_detail_link = '/admin/customer/edit/' . $record->cust_id;
                    $icon = '<a href="' . $cust_detail_link . '" >' . '<span class="badge rounded-pill bg-primary"><i class="lni lni-pencil"></i></span>' . '</a> ';
                    $show_cust = (!empty($record->customer)) ? $record->customer->name : '';
                    return $icon . $show_cust;
                })
                ->editColumn('cust_user_id', function ($record) {
                    return (!empty($record->customer_user)) ? $record->customer_user->username : '';
                })
                ->editColumn('status', function ($record) {
                    $show_status = '';
                    switch ($record->status) {
                        case 1:
                            $show_status = '<span class="badge rounded-pill bg-success">สำเร็จ</span>';
                            break;
                        default:
                            $show_status = '<span class="badge rounded-pill bg-danger">ล้มเหลว</span>';
                            break;
                    }
                    return $show_status;
                })
                ->editColumn('created_by', function ($record) {
                    return (!empty($record->created_user)) ? $record->created_user->name : '';
                })
                ->editColumn('updated_at', function ($record) {
                    return $record->updated_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($record) {
                    $type = ($record->type==1)?"deposit":"withdraw";
                    if(!empty($record->job_id)){
                        $job_detail_link = '/admin/job/' . $type . '/' . $record->job_id;
                        $action_btn = '<div class="square-buttons d-flex  gap-1 ">';
                        $action_btn .= '<a href="'. $job_detail_link.'" class="btn btn-outline-info"><i class="lni lni-eye"></i></a></a>';
                        $action_btn .= '</div>';
                    }else{
                        $action_btn = '<div class="square-buttons d-flex  gap-1 ">';
                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-warning"></i></a></a>';
                        $action_btn .= '</div>';
                    }
                   
                    return $action_btn;
                })
                ->escapeColumns([]);
            // response datatable json
            return $tables->make(true);
        }
    }

    public function deposit(Request $request, $id = 0)
    {   
        $adminInit = $this->adminInit() ;
        return view('job::manual_credit.form.deposit',['adminInit'=>$adminInit]);
    }

    public function withdraw(Request $request, $id = 0)
    {   
        $adminInit = $this->adminInit() ;
        return view('job::manual_credit.form.withdraw',['adminInit'=>$adminInit]);
    }
    
    public function save(Request $request)
    {
        $now = date("Y-m-d H:i:s");
        $user = \Auth::user();

        $username = $request->get('username');
        $amount = abs($request->get('amount'));
        $ref_code = $request->get('ref_code');
        $reason = $request->get('reason');
        $job_type = $request->get('job_type');
        
        $cust_user = CustomerUsers::where('username',$username)->first();

        $attributes = [
            'job_type'=>$job_type,
            'cust_id'=>$cust_user->cust_id,
            'cust_user_id'=>$cust_user->id,
            'cust_user_name'=>$cust_user->username,
            'amount'=>$amount,
            'ref_code'=>$ref_code,
            'reason'=>$reason,
            'status'=>0,
            'updated_at'=>$now,
            'created_at'=>$now,
            'created_by'=>$user->id,
            'updated_by'=>$user->id

        ];

        $mcredit = ManualCredit::create($attributes);

        // 3. create job
        $arrJob = array(
            'cust_id' => $cust_user->cust_id,
            'cust_user_id' => $cust_user->id,
            'type' => $job_type,
            'statement_id' => 0,
            'note' => "From Manual Credit ID ".$mcredit->id,
            'channel' => 5,
            'transfer_datetime' => $now,
            'from_bank_id' => 0,
            'from_bank_acc_no' => '',
            'to_bank_id' => 0,
            'total_amount' =>$amount,
            'amount' =>$amount,
            'status' => 0,
            'is_auto' => 0
        );

        $o_job_admin = new JobAdminController ; 
        $job = $o_job_admin->saveJob($arrJob,1,$job_type,1);

        // 4. update job in tempro
        if($job['status']){
            $mcredit->job_id = $job['job_id'] ;
            $mcredit->status = 9 ;
            $mcredit->save();

            $resp = [
                'code'=>200,
                'status'=>1,
                'msg'=>'api key not correct'
            ];
        }else{
   
            $resp = [
                'code'=>300,
                'status'=>0,
                'msg'=>'บันทึกเครดิตไม่ได้ '.$job['msg'],
                'job'=>$job
            ];
        }

        return response()->json($resp);
    }

    public function promotion(Request $request)
    {      
        $adminInit = $this->adminInit() ;
        $promotions = PartnerPromotions::where('pro_status',1)->get();
        return view('job::manual_credit.form.promotion',['promotions'=>$promotions,'adminInit'=>$adminInit]);
    }

    public function promotion_save(Request $request)
    {
        $now = date("Y-m-d H:i:s");
        $expire = date('Y-m-d H:i:s',strtotime('+7 days')) ;
        $user = \Auth::user();
        $usernames = explode(',',$request->get('username'));

        $resp = [
            'code'=>500,
            'status'=>0,
            'msg'=>'บันทึกเครดิตไม่ได้'
        ];

        if(!empty($usernames)){
            foreach($usernames as $username){
                $username=trim($username) ;
                $pro_id = $request->get('ref_code') ;
                $cust_user = CustomerUsers::where('username',$username)->first();
                $promotion = Promotions::find($pro_id) ; 

               
                $amount = $request->get('amount');
                $ref_code = $promotion->pro_name.' (ID:'.$pro_id.')';
                $reason = $request->get('reason');
                $job_type = $request->get('job_type');
                
                

                if(!empty($cust_user)&& $cust_user->cust_id){

                    // 1. create manual credit
                    $attributes = [
                        'job_type'=>$job_type,
                        'cust_id'=>$cust_user->cust_id,
                        'cust_user_id'=>$cust_user->id,
                        'cust_user_name'=>$cust_user->username,
                        'amount'=>$amount,
                        'ref_code'=>$ref_code,
                        'reason'=>$reason,
                        'status'=>0,
                        'updated_at'=>$now,
                        'created_at'=>$now,
                        'created_by'=>$user->id,
                        'updated_by'=>$user->id

                    ];

                    $mcredit = ManualCredit::create($attributes);

                    // 2 create temppro
                    $attributes = [
                        'from_job_id'=>0,
                        'cust_id'=>$cust_user->cust_id,
                        'pro_type'=>$promotion->pro_type,
                        'pro_id'=>$promotion->id,
                        'amount'=>$amount,
                        'status'=>0,
                        'expired_at'=>$expire,
                        'request_at'=>$now,
                    ] ;

                    $tempro = TempPromotions::create($attributes) ;

                    // 3. create job
                    $arrJob = array(
                        'cust_id' => $cust_user->cust_id,
                        'cust_user_id' => $cust_user->id,
                        'type' => $job_type,
                        'temp_pro_id'=>$tempro->id,
                        'promotion_id'=>$pro_id,
                        'promotion_amount'=>$amount,
                        'statement_id' => 0,
                        'note' => "From Manual Credit By Promotion ID ".$mcredit->id,
                        'channel' => 5,
                        'transfer_datetime' => $now,
                        'from_bank_id' => 0,
                        'from_bank_acc_no' => '',
                        'to_bank_id' => 0,
                        'total_amount' =>$amount,
                        'amount' =>$amount,
                        'status' => 0,
                        'is_auto' => 0
                    );

                    $job = JobsController::saveJob($arrJob,1,$job_type,1);

                    // 4. update job in tempro
                    if($job['status']){
                        $mcredit->job_id = $job['job_id'] ;
                        $mcredit->status = 9 ;
                        $mcredit->save();

                        $resp = [
                            'code'=>200,
                            'status'=>1,
                            'msg'=>'api key not correct'
                        ];
                    }else{
               
                        $resp = [
                            'code'=>300,
                            'status'=>0,
                            'msg'=>'บันทึกเครดิตไม่ได้ '.$job['msg'],
                            'job'=>$job
                        ];
                    }
                }
            }
        }
          

        return response()->json($resp);
    }


    public function set_quick_search_username(Request $request)
    {
        $search_term = $request->get('search')  ;

        $customers = Customer::whereHas('customer_user', function ($query) use($search_term) {
            return $query->where('username', 'link', $search_term.'%');
        })->with(['customer_user'])->orWhere('username', 'like', ''.$search_term.'%')->offset(0)->limit(10)->get();

        if($customers->count()==0){
            $resp[0] =['id'=>0,"text"=>'ไม่พบใบงาน'] ;
        }
        foreach($customers as $cust){
            $show = $cust->username.' '.$cust->name.' (user:'.$cust->customer_user->username.')' ;
            $resp[] = ['id'=>$cust->username,'text'=>$show];
        }
        return response()->json($resp);
    }

}
