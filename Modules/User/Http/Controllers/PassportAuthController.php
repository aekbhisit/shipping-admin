<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Modules\User\Entities\Users ;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
       $validator = Validator::make($request->all(), [
                'group_id' => 'required|integer',
                'name' => 'required|max:250',
                'username' => 'required|max:100',
                'email' => 'required|email|max:320',
                'password' => 'required|max:100',
                'avatar' => 'string',
                'status' => 'required|integer',
                'api_enable' => 'required|integer',
            ]);
 
        $attributes = [
            "group_id"=>$request->get('group_id'),
            "name"=>$request->get('name'),
            "username"=>$request->get('username'),
            "email"=>$request->get('email'),
            "password"=>bcrypt($request->get('password')),
            "avatar"=>'',
            "status" => 1,
            "api_enable" => 1

        ];
        
        $user = Users::create($attributes);
        $token = $user->createToken('MWZ_API_APP')->accessToken;
 
        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $data = [
            'username' => $request->username,
            'password' => $request->password,
            'status'=>1,
            'api_enable'=>1
        ];
        
        $check_attemp = Auth::guard('admin')->attempt($data) ;
        if ($check_attemp) {
            $user = Auth::guard('admin')->user();
            $token ="";
            // $token = $user->createToken('MWZ_API_APP')-> accessToken; 
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

    }   
 
 
}
