<?php

namespace Modules\User\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;

use Modules\User\Entities\Roles;
use Modules\User\Entities\Permissions;
use Modules\User\Entities\RolesAndPermissions;

use Modules\Core\Http\Controllers\AdminController;

// Repository Pattern - Clean Architecture
use Modules\User\Repositories\Contracts\RoleRepositoryInterface;

class RoleAdminController extends AdminController
{
    protected $roleRepository;

    /**
     * Constructor - Inject RoleRepository
     * Clean Architecture: Controller → Repository → Database
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->middleware('auth:admin');
        $this->roleRepository = $roleRepository;
    }

    /**
     * Function : role index
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param Get
     * @return index.blade view
     */
    public function index()
    {
        $adminInit = $this->adminInit();
        return view('user::role.index', ['adminInit' => $adminInit]);
    }

    /**
     * Function : role datatable ajax response
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param Get
     * @return json of role
     */
    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            // Use Repository Pattern - Clean Architecture
            return $this->roleRepository->getDatatableRoles($request->all());
        }
    }


    /**
     * Function : update role status
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param POST
     * @return json of update status
     */
    public function set_status(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            $role = roles::find($id);
            $role->status = $status;

            if ($role->save()) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }


    /**
     * Function : delete role
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param POST
     * @return json of delete status
     */
    public function set_delete(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $role = roles::find($id);



            if ($role->delete()) {
                if (RolesAndPermissions::where('role_id', $id)->count()) {
                    RolesAndPermissions::where('role_id', $id)->delete();
                }
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }


    /**
     * Function : add role form
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param GET
     * @return role form view
     */
    public function form($id = 0)
    {
        $adminInit = $this->adminInit();
        $role = [];
        $checked = [];
        if (!empty($id)) {
            $role = roles::find($id);
            $checked['all'] = $role->all;
            $checked['module'] = json_decode($role->module, 1);
            $checked['group'] = json_decode($role->group, 1);
            $checked['permissions'] = json_decode($role->permissions, 1);
        }

        $permissions = Permissions::orderBy('module', 'asc')->orderBy('action', 'asc')->orderBy('action', 'asc')->get();
        // print_r($checked['group']) ;
        $permission_list = [];
        foreach ($permissions as $p) {
            $permission_list[$p->module][$p->group][] = $p->toArray();
        }


        return view('user::role.form', ['data' => $role, 'permissions' => $permission_list, 'checked' => $checked, 'adminInit' => $adminInit]);
    }

    /**
     * Function : role save
     * Dev : Jang
     * Update Date : 11 Oct 2022
     * @param POST
     * @return json response status
     */
    public function save(Request $request)
    {

        //validate post data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'permissions' => 'required',

        ], [
            'name.*' => 'โปรดระบุชื่อ!',
            'permissions.*' => 'โปรดเลือกสิทธิ์!',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msg = $errors->first();
            $resp = ['success' => 0, 'code' => 301, 'msg' => $msg, 'error' => $errors];
            return response()->json($resp);
        }

        $attributes = [
            "name" => $request->get('name'),
            "all" => (!empty($request->get('all'))) ? $request->get('all') : '',
            "module" => (!empty($request->get('module'))) ? json_encode($request->get('module')) : '',
            "group" => (!empty($request->get('group'))) ? json_encode($request->get('group')) : '',
            "permissions" => (!empty($request->get('permissions'))) ? json_encode($request->get('permissions')) : '',
            "status" => 1,
        ];


        if (!empty($request->get('id'))) {
            $data_id = $request->get('id');
            $role = roles::where('id', $request->get('id'))->update($attributes);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } else {
            // insert new row

            // get max sequence
            $sequence = roles::max('sequence');
            (int)$sequence += 1;
            $attributes["sequence"] = $sequence;

            $role = roles::create($attributes);
            $data_id = $role->id;
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มรายการใหม่สำเร็จ'];
        }

        // create role permission
        if (RolesAndPermissions::where('role_id', $data_id)->count()) {
            RolesAndPermissions::where('role_id', $data_id)->delete();
        }
        if (!empty($request->get('permissions'))) {
            $permissions = $request->get('permissions');
            foreach ($permissions as $key => $permission) {
                $attributes = [
                    'role_id' => $data_id,
                    'permission_id' => $permission
                ];
                RolesAndPermissions::create($attributes);
            }
        }


        return response()->json($resp);
    }

    /**
     * Function :  set_re_order
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param POST
     * @return json response update sequence status
     */
    public function re_order()
    {
        $lists = roles::orderBy('sequence', 'asc')->get();
        if (!empty($lists)) {
            $cnt = 0;
            foreach ($lists as $row) {
                $cnt++;
                $row->sequence = $cnt;
                $row->save();
            }
        }
    }


    /**
     * Function :  set_re_order
     * Dev : Tong
     * Update Date : 11 Oct 2022
     * @param POST
     * @return json response update sequence status
     */
    public function set_re_order(Request $request)
    {
        if ($request->ajax()) {
            $sort_json = @json_decode($request->get('sort_json'), 1);
            if (!empty($sort_json)) {
                foreach ($sort_json as $id => $sequence) {
                    roles::find($id)->update(['sequence' => $sequence]);
                }
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'เรียงข้อมูลใหม่สำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 300, 'msg' => 'ไม่มีข้อมูลที่ต้องเรียง'];
            }

            return response()->json($resp);
        }
    }

    /**
     * Function :  update up content 
     * Dev : Tong
     * Update Date : 19 Sep 2022
     * @param POST
     * @return json response update content up
     */
    public function sort(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->get('id');
            $move = $request->get('move');
            if ($move == 'up') {
                $result = roles::find($id);
                $new_sequence = $result->sequence + 1;

                $upnode = roles::where([['sequence', '>=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

                $upnode->sequence = $result->sequence;
                $upnode->save();

                $result->sequence = $new_sequence;
                $content = $result->save();
            }
            if ($move == 'down') {
                $result = roles::find($id);
                $new_sequence = $result->sequence - 1;

                $downnode = roles::where([['sequence', '<=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

                $downnode->sequence = $result->sequence;
                $downnode->save();

                $result->sequence = $new_sequence;
                $content = $result->save();
            }
            $this->re_order();
            if ($content) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'เรียงข้อมูลใหม่สำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 300, 'msg' => 'ไม่มีข้อมูลที่ต้องเรียง'];
            }
        } else {
            $resp = ['success' => 0, 'code' => 300, 'msg' => 'ไม่มีข้อมูลที่ต้องเรียง'];
        }

        return response()->json($resp);
    }

    /**
     * Function :  get role
     * Dev : Tong
     * Update Date : 19 Sep 2022
     * @param POST
     * @return json response update content up
     */
    public function get_role(Request $request)
    {
        $roles = roles::orderBy('sequence', 'asc')->get();
        $result = [];
        foreach ($roles as $role) {
            $result[] = [
                'id' => $role->id,
                'text' => $role->name,
                'image' => ''
            ];
        }

        $resp = ['success' => 1, 'code' => 200, 'msg' => 'success', 'results' => $result];

        return response()->json(
            $resp,
            200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function generate_role()
    {
        $group_role = [
            'view' => ['index', 'datatable_ajax', 'get_'],
            'add' => ['add', 'save'],
            'edit' => ['edit', 'set_status', 'sort', 'set_move_node', 'set_re_order'],
            'delete' => ['set_delete']
        ];

        $routeCollection = Route::getRoutes();
        foreach ($routeCollection as $value) {
            $route_name = $value->getName();
            if (str_contains($route_name, 'admin.')) {
                $route_arr =  explode('.', $route_name);
                $module = (!empty($route_arr[1])) ? $route_arr[1] : '';
                $page = (!empty($route_arr[2])) ? $route_arr[2] : '';
                $action = (!empty($route_arr[3])) ? $route_arr[3] : '';

                if (in_array($action, $group_role['view'])) {
                    $group = 'view';
                } elseif (in_array($action, $group_role['add'])) {
                    $group = 'add';
                } elseif (in_array($action, $group_role['edit'])) {
                    $group = 'edit';
                } elseif (in_array($action, $group_role['delete'])) {
                    $group = 'delete';
                } else {
                    if (str_contains($route_name, 'get_')) {
                        $group = 'view';
                    } elseif (str_contains($route_name, 'save_')) {
                        $group = 'add';
                    } else {
                        $group = 'other';
                    }
                }

                $attributes_main = [
                    'route_name' => $route_name
                ];

                $attributes = [
                    'name' => __($route_name),
                    'group' => $group,
                    'module' => $module,
                    'page' => $page,
                    'action' => $action,
                ];

                roles::updateOrCreate($attributes_main, $attributes);
            }
        }
    }
}
