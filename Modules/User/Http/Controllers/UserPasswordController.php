<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use Modules\User\Entities\Users ;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\UserEmail;


class UserPasswordController extends Controller
{
    /**
    * Function : __construct check admin pass
    * Dev : Tong
    * Update Date : 16 Jun 2021
    * @param Get
    * @return if not pass redirect to /admin
    */

    public function __construct()
    {
        // $this->middleware('guest:admin')->except(['forget_password','reset_password']);
         $this->middleware('guest');
    }

    protected function broker()
    {
      return Password::broker('admin');
    }

    protected function guard()
    {
      return Auth::guard('admin');
    }

    /**
    * Function : admin forget password form 
    * Dev : Tong
    * Update Date : 30 Jun 2021
    * @param Get
    * @return forget password form
    */
    public function forget_password(Request $request){

        if($request->get('email')){

            $request->validate(['email' => 'required|email']);

            $status = $this->broker()->sendResetLink(
                $request->only('email')
            );
            
            return $status === $this->broker()::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
        }

        return view('user::forget_password');
    }

    public function reset_password($token){
        return view('user::reset_password',['token'=>$token]);
    }


    public function set_reset_password( Request $request){

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === $this->broker()::PASSWORD_RESET
                    ? redirect()->route('admin.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
        
    }


    /**
    * Function : admin logout from /admin
    * Dev : Tong
    * Update Date : 16 Jun 2021
    * @param Get
    * @return redirect to /admin
    */
    public function notify(Request $request){

        $user = Users::find(3);
  
        // $details = [
        //     'greeting' => 'Hi Artisan',
        //     'body' => 'This is my first notification from Nicesnippests.com',
        //     'thanks' => 'Thank you for using Nicesnippests.com tuto!',
        //     'actionText' => 'View My Site',
        //     'actionURL' => url('/'),
        //     'order_id' => 101
        // ];

        // Notification::send($user, new NotifyUser());

        // return redirect()->route('user.login');
        Mail::to($user->email)->send(new UserEmail($user));
    }
}
