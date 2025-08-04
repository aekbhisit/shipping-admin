<?php

namespace Modules\User\Http\Controllers;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\User\Entities\Roles;

class RoleController extends Controller
{
    public  $exclude_route = ['admin.login', 'admin.logout', 'admin.forget_password', 'admin.reset_password', 'admin.set_reset_password', 'admin.notify', 'admin.default', 'admin.role.failback', 'admin.user.permission.generate_permission'];
    public  $exclude_user_group = [];

    public function getExcludeRoute()
    {
        return $this->exclude_route;
    }

    public  function roles_route()
    {
        return Roles::with('permissions_route_name')->where('id', Auth::guard('admin')->user()->role_id)->first(['id', 'name']);
    }

    public  function roles_group()
    {
        return Roles::with('permissions_group')->where('id', Auth::guard('admin')->user()->role_id)->first(['id', 'name']);
    }

    public  function allow_route($route = '', $get_current_route = false)
    {
        if ($get_current_route && empty($route)) {
            $route = Route::currentRouteName();
        }
        if (Auth::guard('admin')->user()->id == 1) {
            return true;
        } else {
            if (in_array($route, $this->exclude_route)) {
                return true;
            } else {
                $roles = $this->roles_route();
                if (!empty($roles)) {
                    foreach ($roles->permissions_route_name as $permission) {
                        if ($permission->route_name == $route) {
                            return true;
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    public  function allow($module = '', $group = 'view')
    {
        if (Auth::guard('admin')->user()->id == 1) {
            return true;
        } else {

            $roles = $this->roles_route();
            if (!empty($roles)) {
                foreach ($roles->permissions_group as $permission) {
                    if ($permission->module == $module && $permission->group == $group) {
                        return true;
                        break;
                    }
                }
            }
        }
        return false;
    }
}
