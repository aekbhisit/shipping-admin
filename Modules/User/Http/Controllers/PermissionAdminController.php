<?php

namespace Modules\User\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

use Modules\User\Entities\Permissions;

use Modules\Core\Http\Controllers\AdminController;

// Repository Pattern - Clean Architecture
use Modules\User\Repositories\Contracts\PermissionRepositoryInterface;

class PermissionAdminController extends AdminController
{
    protected $permissionRepository;

    /**
     * Constructor - Inject PermissionRepository
     * Clean Architecture: Controller → Repository → Database
     */
    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->middleware('auth:admin');
        $this->permissionRepository = $permissionRepository;
    }


    public function index()
    {
        // $this->generate_permission() ;
        $adminInit = $this->adminInit();
        return view('user::permission.index', ['adminInit' => $adminInit]);
    }


    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {
            // Use Repository Pattern - Clean Architecture
            return $this->permissionRepository->getDatatablePermissions($request->all());
        }
    }


    public function set_status(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            $permission = Permissions::find($id);
            $permission->status = $status;

            if ($permission->save()) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }


    public function set_delete(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $permission = Permissions::find($id);
            if ($permission->delete()) {
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }


    public function form($id = 0)
    {
        $adminInit = $this->adminInit();
        $permission = [];
        if (!empty($id)) {
            $permission = Permissions::find($id);
        }

        return view('user::permission.form', ['data' => $permission, 'adminInit' => $adminInit]);
    }


    public function save(Request $request)
    {

        //validate post data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',

        ], [
            'name.*' => 'โปรดระบุชื่อ!',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msg = $errors->first();
            $resp = ['success' => 0, 'code' => 301, 'msg' => $msg, 'error' => $errors];
            return response()->json($resp);
        }

        $attributes = [
            "name" => $request->get('name')
        ];


        if (!empty($request->get('id'))) {
            $data_id = $request->get('id');
            $permission = Permissions::where('id', $request->get('id'))->update($attributes);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } else {
            // insert new row
            $permission = Permissions::create($attributes);
            $data_id = $permission->id;

            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มรายการใหม่สำเร็จ'];
        }

        return response()->json($resp);
    }


    public function re_order()
    {
        $lists = Permissions::orderBy('sequence', 'asc')->get();
        if (!empty($lists)) {
            $cnt = 0;
            foreach ($lists as $row) {
                $cnt++;
                $row->sequence = $cnt;
                $row->save();
            }
        }
    }

    public function set_re_order(Request $request)
    {
        if ($request->ajax()) {
            $sort_json = @json_decode($request->get('sort_json'), 1);
            if (!empty($sort_json)) {
                foreach ($sort_json as $id => $sequence) {
                    Permissions::find($id)->update(['sequence' => $sequence]);
                }
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'เรียงข้อมูลใหม่สำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 300, 'msg' => 'ไม่มีข้อมูลที่ต้องเรียง'];
            }

            return response()->json($resp);
        }
    }


    public function sort(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->get('id');
            $move = $request->get('move');
            if ($move == 'up') {
                $result = Permissions::find($id);
                $new_sequence = $result->sequence + 1;

                $upnode = Permissions::where([['sequence', '>=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

                $upnode->sequence = $result->sequence;
                $upnode->save();

                $result->sequence = $new_sequence;
                $content = $result->save();
            }
            if ($move == 'down') {
                $result = Permissions::find($id);
                $new_sequence = $result->sequence - 1;

                $downnode = Permissions::where([['sequence', '<=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

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


    public function get_permission(Request $request)
    {
        $permissions = Permissions::orderBy('sequence', 'asc')->get();
        $result = [];
        foreach ($permissions as $permission) {
            $result[] = [
                'id' => $permission->id,
                'text' => $permission->name,
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

    public function get_route_name(Request $request)
    {
        // dd($request->all());

        $permissions = new Permissions();
        if (!empty($request->get('search'))) {
            $permissions = $permissions->where('name', 'like', '%' . $request->get('search') . '%');
        }
        $permissions = $permissions->orderBy('name', 'asc')->get();
        $result = [];
        foreach ($permissions as $permission) {
            $result[] = [
                'id' => $permission->route_name,
                'text' => $permission->name,
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

    public function generate_permission()
    {
        $group_permission = [
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

                if (in_array($action, $group_permission['view'])) {
                    $group = 'view';
                } elseif (in_array($action, $group_permission['add'])) {
                    $group = 'add';
                } elseif (in_array($action, $group_permission['edit'])) {
                    $group = 'edit';
                } elseif (in_array($action, $group_permission['delete'])) {
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

                Permissions::updateOrCreate($attributes_main, $attributes);
            }
        }

        $resp = ['success' => 1, 'code' => 200, 'msg' => 'success'];

        return response()->json(
            $resp,
            200,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }
}
 