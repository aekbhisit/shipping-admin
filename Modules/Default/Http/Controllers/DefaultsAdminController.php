<?php

namespace Modules\Default\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

use Yajra\DataTables\Facades\DataTables;

use Modules\Default\Entities\Defaults;

class DefaultsAdminController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('default::datatable.index');
    }

    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {

            //init datatable
            $dt_name_column = array('sequence', 'image', 'name_th', 'updated_at');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // create default object
            $o_default = new Defaults();

            // add search query if have search from datable
            if (!empty($dt_search)) {
                $o_default = $o_default->where('name_th', 'like', "%" . $dt_search . "%");
            }

            $dt_total = $o_default->count();
            // set query order & limit from datatable
            $o_default = $o_default->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query default
            $default = $o_default->get();
            // prepare datatable for response
            $tables = Datatables::of($default)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('default_row')
                ->setTotalRecords($dt_total)
                ->setRowAttr([
                    'data-sequence' => function ($record) {
                        return $record->sequence;
                    },
                ])
                ->editColumn('updated_at', function ($record) {
                    return $record->updated_at->format('Y-m-d H:i:s');
                })
                ->editColumn('image', function ($record) {
                    if (CheckFileInServer($record->image) && !empty($record->image)) {
                        return '<img class="rounded" style="max-height: 60px" src="' . $record->image . '">';
                    }
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex flex-wrap gap-1">';
                   
                        # code...
                        if ($record->status == 1) {
                            $action_btn .= '<a onclick="setStatus(' . $record->id . ',0)" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-checkmark"></i></a></a>';
                        } else {
                            $action_btn .=  '<a onclick="setStatus(' . $record->id . ',1)" href="javascript:void(0);"  class="btn btn-outline-warning"><i class="lni lni-close"></i></a></a>';
                        }

                        $action_btn .= '<a href="' . route('admin.default.default.edit', $record->id) . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a></a>';
                    
                        
                        $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a></a>';

                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-lock"></i></a></a>';
                        
                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-unlock"></i></a></a>';

                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-secondary"><i class="lni lni-eye"></i></a></a>';

                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-primary"><i class="lni lni-files"></i></a></a>';

                        $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-warning"></i></a></a>';
                        
                    
                    $action_btn .= '</div>';

                    return $action_btn;
                })
                ->escapeColumns([]);

            // response datatable json
            return $tables->make(true);
        }
    }
    
    public function set_status(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $status = $request->get('status');

            $default = Defaults::find($id);
            $default->status = $status;

            if ($default->save()) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }
    
    public function delete(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $default = Defaults::find($id);
            if ($default->delete()) {
                $this->re_order() ;
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }

    public function form($id = 0)
    {   
        return view('default::default.form');
    }

    public function save(Request $request)
    {

        //validate post data
        $validator = Validator::make($request->all(), [
            'name_th' => 'required|max:100',
            'name_en' => 'required|max:100',
        ], [
            'name_th.*' => 'โปรดระบุชื่อหัวข้อภาษาไทย!',
            'name_en.*' => 'โปรดระบุชื่อหัวข้อภาษาอังกฤษ!',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $msg = $errors->first();
            $resp = ['success' => 0, 'code' => 301, 'msg' => $msg, 'error' => $errors];
            return response()->json($resp);
        }

        $attributes = [
            "name_th" => $request->get('name_th'),
            "name_en" => $request->get('name_en'),
            "desc_th" => $request->get('desc_th'),
            "desc_en" => $request->get('desc_en'),
            "detail_th" => $request->get('detail_th'),
            "detail_en" => $request->get('detail_en'),
            "status" => $request->get('status'),
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $new_filename = time() . "." . $image->extension();
            $path = $image->storeAs(
                'public/product-default',
                $new_filename
            );
            $attributes['image'] = Storage::url($path);
        } else {
            if (!empty($request->image_old)) {
                $attributes['image'] = $request->image_old;
                // delete old file
                if($request->get('image_del')){
                    $remove_file = 'public/product-default'.$request->image_old ;
                    if(Storage::disk('public')->exists($remove_file)){
                        Storage::disk('public')->delete($remove_file);
                    }
                    $attributes['image'] = '';
                }
            } else {
                $attributes['image'] = '';
            }
        }
       
        if (!empty($request->get('id'))) {
            $data_id = $request->get('id');
            $default = Defaults::where('id', $request->get('id'))->update($attributes);
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } else {
            // get max sequence
            $sequence = Defaults::max('sequence');
            (int)$sequence += 1;
            $attributes["sequence"] = $sequence;

            // insert new row
            $default = Defaults::create($attributes);
            $data_id = $default->id;

            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มรายการใหม่สำเร็จ'];
        }

        // create slug
        $o_slug = new SlugController;
        $o_slug->createMetadata($request, $data_id);

        return response()->json($resp);
    }


    public function re_order()
    {
        $lists = Defaults::orderBy('sequence','asc')->get();
        if(!empty($lists)){
            $cnt = 0;
            foreach ($lists as $row) {
                $cnt++;
                $row->sequence = $cnt ;
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
                    Defaults::find($id)->update(['sequence' => $sequence]);
                }
                $this->re_order() ;
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
                $result = Defaults::find($id);
                $new_sequence = $result->sequence + 1;

                $upnode = Defaults::where([['sequence', '>=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

                $upnode->sequence = $result->sequence;
                $upnode->save();

                $result->sequence = $new_sequence;
                $content = $result->save();
            }
            if ($move == 'down') {
                $result = Defaults::find($id);
                $new_sequence = $result->sequence - 1;

                $downnode = Defaults::where([['sequence', '<=', $result->sequence], ['id', '!=', $id], ['type', $result->type]])->orderBy('sequence', 'desc')->first();

                $downnode->sequence = $result->sequence;
                $downnode->save();

                $result->sequence = $new_sequence;
                $content = $result->save();
            }
            $this->re_order() ;
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


    public function get_default(Request $request)
    {
        $Defaults = Defaults::orderBy('sequence','asc')->get() ;
        $result = [];
        foreach($Defaults as $default){
            $result[] = [
                'id'=>$default->id,
                'text'=>$default->name_th,
                'image'=>$default->image
            ];
        }

        $resp = ['success' => 1, 'code' => 200, 'msg' => 'success','results'=>$result];

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);
    }

    // example
    public function datatable(Request $request){
        return view('default::datatable.index');
    }

    public function table(Request $request){
        return view('default::table.index');
    }

    public function tab(Request $request){
        return view('default::tab.tab');
    }

    public function form_form(Request $request){
        return view('default::form.form');
    }

    public function form_field(Request $request){
        return view('default::field.field');
    }

    public function form_tab(Request $request){
        return view('default::form_tab.tab');
    }
}
