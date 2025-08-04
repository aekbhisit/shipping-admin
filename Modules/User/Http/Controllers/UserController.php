<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Modules\User\Entities\Users ;

class UserController extends Controller
{

    /**
    * Function : __construct check admin login
    * Dev : Tong
    * Update Date : 16 Jun 2021
    * @param Get
    * @return if not login redirect to /admin
    */
    public function register(Request $request){    
        
        $main = $request->validate([
            'line_id' => ['required'],
            'username' => ['required']
        ]);

        $attributes = $request->validate([
            'shop_id' => ['required'],
            'name' => ['required'],
            'email' => ['required']        
        ]);

        $attributes["password"] = Hash::make('giftandgive'.$request->get('username'));
        $user = Users::updateOrCreate($main,$attributes);
        if (!empty($user->id)) {
            $auth_status = Auth::guard('admin')->login($user, true);
        }
        $resp = ['success' => 1, 'code' => 200, 'msg' =>'สมัครสมาชิกสำเร็จ', 'user'=>$user];
        return response()->json($resp);
    }

    public function update_profile(Request $request){    
        
        $main = $request->validate([
            'line_id' => ['required'],
        ]);

        $attributes = $request->validate([
            'shop_id' => ['required'],
            'name' => ['required'],
            'email' => ['required']        
        ]);

        $user = Users::updateOrCreate($main,$attributes);
        if (!empty($user->id)) {
            $auth_status = Auth::guard('admin')->login($user, true);
        }
        $resp = ['success' => 1, 'code' => 200, 'msg' =>'อัพเดทโปรไฟล์สำเร็จ', 'user'=>$user];
        return response()->json($resp);
    }
}
