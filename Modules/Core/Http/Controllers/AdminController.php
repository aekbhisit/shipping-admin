<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Modules\Core\Entities\AdminErrorLog;
use Modules\Job\Entities\Jobs;

class AdminController extends Controller
{

    function adminInit(){
        $init = [];

        try {
            $job_noti_waiting = Jobs::where('status',0)->with('customer_user')->get();
            $init['job_waiting'] = [
                'job_waiting_cnt'=>count($job_noti_waiting),
                'job_waiting_list'=>$job_noti_waiting
            ];

            $init['job_doing'] = [
                'job_doing_cnt'=>Jobs::where('status','>','0')->where('status','<','8')->where('locked',1)->count()
            ];
        } catch (\Exception $e) {
            // Handle missing tables gracefully
            $init['job_waiting'] = [
                'job_waiting_cnt'=>0,
                'job_waiting_list'=>[]
            ];

            $init['job_doing'] = [
                'job_doing_cnt'=>0
            ];
        }

        return $init ;
    }

    function resp(Request $request, $resp,$type='json'){
        
        if($resp['success']){
            $url = $request->fullUrl();
            $method = $request->getMethod();
            $ip = $request->getClientIp();

            $request_url = "{$ip}: {$method}@{$url}"; 
            $request_header = json_encode($request->headers->all(),JSON_UNESCAPED_UNICODE); 
            $request_body =json_encode( $request->all(),JSON_UNESCAPED_UNICODE); 
            $response_code = $resp['code'] ;
            $response = json_encode($resp,JSON_UNESCAPED_UNICODE); 
            AdminErrorLog::create([
                'request'=>$request_url,
                'request_header'=>$request_header,
                'request_body'=>$request_body,
                'response_code'=>$response_code,
                'response'=>$response
            ]);
        }
        if($type=='json'){
            return response()->json(
                $resp,
                200,
                ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
                JSON_UNESCAPED_UNICODE
            );
        }else{
            return $resp ;
        }
    }

    function not_permit(Request $request){
        return view('core::not_permit');
    }
    
    
}
