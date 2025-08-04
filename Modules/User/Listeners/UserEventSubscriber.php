<?php

namespace Modules\User\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Notification;
use Modules\User\Notifications\NotiUserLogin ;
use Modules\User\Notifications\NotiUserLogout ;

class UserEventSubscriber
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
    * Function : listen user login
    * Dev : Tong
    * Update Date : 19 Jul 2021
    * @param no
    * @return insert user_login_history status
    */
    public function handleUserLogin($event) {
        $userinfo = $event->user;
        // $now = DB::raw('NOW()');
        // $saveHistory = DB::table('user_login_history')->insert(
        //     ['name' => $userinfo->name, 'email' => $userinfo->email,'action'=>'login','created_at' => $now, 'updated_at' => $now]
        // );
        // return $saveHistory;

        Notification::send($userinfo, new NotiUserLogin('UserLogin',$userinfo));

        

    }

    /**
    * Function : listen user logout
    * Dev : Tong
    * Update Date : 19 Jul 2021
    * @param no
    * @return insert user_login_history status
    */
    public function handleUserLogout($event) {
        $userinfo = $event->user;

        // $now = DB::raw('NOW()');
        // $saveHistory = DB::table('user_login_history')->insert(
        //     ['name' => $userinfo->name, 'email' => $userinfo->email,'action'=>'logout','created_at' => $now, 'updated_at' => $now]
        // );
        // return $saveHistory;
        Notification::send($userinfo, new NotiUserLogout('UserLogout',$userinfo));
    }


    /**
    * Function : subscribe user event and handler
    * Dev : Tong
    * Update Date : 19 Jul 2021
    * @param no
    * @return no
    */
    public function subscribe($events)
    {
        $events->listen(
            'Modules\User\Events\UserLogin',
            [UserEventSubscriber::class, 'handleUserLogin']
        );

        $events->listen(
            'Modules\User\Events\UserLogout',
            [UserEventSubscriber::class, 'handleUserLogout']
        );
    }

}
