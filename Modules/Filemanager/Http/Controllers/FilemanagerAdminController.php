<?php

namespace Modules\Filemanager\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\AdminController;

class FilemanagerAdminController extends AdminController
{
    /**
    * Function : __construct check admin login
    * Dev : Tong
    * Update Date : 16 July 2021
    * @param Get
    * @return if not login redirect to /admin
    */
    public function __construct()
    {

        $this->middleware('auth:admin');

        // $this->middleware(function ($request, $next) {
        //     $role = new RoleController();
        //     if($role->allow()){
        //         return $next($request);
        //     }else{ 
        //         redirect()->route('admin.dashboard.dashboard.index')->send();
        //     }
        // });
    }



     /**
    * Function : filemanager
    * Dev : Tong
    * Update Date : 16 July 2021
    * @param Get
    * @return filemanager view
    */
    public function index()
    {
        $adminInit = $this->adminInit() ;
        $user = Auth::guard('admin')->user()->name;
        print_r($user);

        $locale=false;
        $dir = '/packages/barryvdh/elfinder' ;
        return view('filemanager::index',['dir'=>$dir,'locale'=>$locale,'adminInit'=>$adminInit]);
    }

}
