<?php

namespace Modules\Default\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Yajra\DataTables\Facades\DataTables;
use Kalnoy\Nestedset\NestedSet;
use Carbon\Carbon;

use Modules\Default\Entities\DefaultCategories;
use Modules\Core\Http\Controllers\SlugController;

class DefaultCategoriesAdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

   
    public function index(Request $request)
    {
        return view('default::category.index');
    }

    
    public function datatable_ajax(Request $request,)
    {
        if ($request->ajax()) {

            //init datatable
            $dt_name_column = array('sequence', 'image', 'name_th', 'updated_at', 'action');

            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];


            // create product cat object
            $o_product_cat = new DefaultCategories();

            // dt_search 
            if (!empty($dt_search)) {
                $o_product_cat = $o_product_cat->Where(function ($query) use ($dt_search) {
                    $dt_search->orWhere('name_th', 'like', "" . $dt_search . "%")
                        ->orWhere('name_en', 'like', "" . $dt_search . "%");
                });
            }

            // count all product cat
            $dt_total = $o_product_cat->count();

            // set query order & limit from datatable
            $o_product_cat->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query product cat
            $categories = $o_product_cat->withDepth()->defaultOrder()->get();

            // prepare datatable for response
            $tables = Datatables::of($categories)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('product_row')
                ->setTotalRecords($dt_total)
                ->setFilteredRecords($dt_total)
                //->setOffset($dt_start)
                ->editColumn('updated_at', function ($record) {
                    return $record->updated_at->format('d/m/Y H:i:s');
                })
                ->editColumn('name_th', function ($record) {
                    $result = array();

                    $result = str_repeat(' - ', $record->depth) . $record->name_th;

                    return $result;
                })
                ->editColumn('name_en', function ($record) {
                    $result = array();

                    $result = str_repeat(' - ', $record->depth) . $record->name_en;

                    return $result;
                })
                ->editColumn('image', function ($record) {
                    if (!empty($record->image) && CheckFileInServer($record->image)) {
                        $img = '<img class="rounded" src="' . $record->image . '" />';
                    } else {
                        $img = '<img alt="' . CheckFileInServer($record->image) . '" class="rounded" src="/storage/_blank.jpg" />';
                    }
                    return $img;
                })
                ->addColumn('sort', function ($record) {
                    $sort_btn = '<div class="btn-list">';
                    $sort_btn .= '<a onclick="setUpdateSort(' . $record->id . ',\'up\');"  href="javascript:void(0);" class="btn btn-sm btn-outline-default"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
                    $sort_btn .= '<a onclick="setUpdateSort(' . $record->id . ',\'down\');" href="javascript:void(0);" class="btn btn-sm btn-outline-default"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
                    $sort_btn .= '</div>';

                    return $sort_btn;
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex flex-wrap gap-1">';
                   
                     
                        if ($record->status == 1) {
                            $action_btn .= '<a onclick="setStatus(' . $record->id . ',0)" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-checkmark"></i></a></a>';
                        } else {
                            $action_btn .=  '<a onclick="setStatus(' . $record->id . ',1)" href="javascript:void(0);"  class="btn btn-outline-warning"><i class="lni lni-close"></i></a></a>';
                        }

                        $action_btn .= '<a href="' . route('admin.default.category.edit', $record->id) . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a></a>';
                    
                        
                        $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a></a>';

                        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-lock"></i></a></a>';
                        
                        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-unlock"></i></a></a>';

                        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-secondary"><i class="lni lni-eye"></i></a></a>';

                        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-primary"><i class="lni lni-files"></i></a></a>';

                        // $action_btn .= '<a onclick="" href="javascript:void(0);" class="btn btn-outline-warning"><i class="lni lni-warning"></i></a></a>';
                        
                    
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

            $product = DefaultCategories::find($id);
            $product->status = $status;

            if ($product->save()) {
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
            $product_cat = DefaultCategories::find($id);
            if ($product_cat->delete()) {
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }

            return response()->json($resp);
        }
    }

    
    public function form(Request $request, $id = 0)
    {   
        $metadata = [
            'th' => [
                'module' => 'product',
                'method' => 'category',
                'level' => 1
            ],
            'en' => [
                'module' => 'product',
                'method' => 'category',
                'level' => 1
            ],
        ];

        $category = [];

        if (!empty($id)) {
            $category = DefaultCategories::find($id);
            $category->desc_th = getTextString($category->desc_th);
            $category->desc_en = getTextString($category->desc_en);
            $category->detail_th = getTextString($category->detail_th);
            $category->detail_en = getTextString($category->detail_en);
            
            $o_slug = new SlugController;
            $meta = $o_slug->getMetadata($metadata['th']['module'], $metadata['th']['method'], $id);
            if (!empty($meta)) {
                $metadata  = $meta;
            }

        }

        $parents = DefaultCategories::all()->totree();
        $parents = setFlatCategory($parents);
        return view('default::category.form', ['category' => $category, 'parents' => $parents, 'metadata' => $metadata]);
    }

    public function save(Request $request)
    {   
        //validate post data
        $validator = Validator::make($request->all(), [
            'id' => 'integer',
            'name_th' => 'required|max:255',
            'name_en' => 'required|max:255',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resp = ['success' => 0, 'code' => 301, 'msg' => $errors->first(), 'error' => $errors];
            return response()->json($resp);
        }

        $attributes = [
            "name_th" => $request->get('name_th'),
            "name_en" => $request->get('name_en'),
            "desc_th" => setTextString($request->get('desc_th')),
            "desc_en" => setTextString($request->get('desc_en')),
            "detail_th" => setTextString($request->get('detail_th')),
            "detail_en" => setTextString($request->get('detail_en')),
            "status" => $request->get('status'),
            "parent_id" => $request->get('parent_id')
        ];

        $image = set_image_upload($request,'image',$path="public/default","df_cat_") ;
        if($image){ $attributes['image'] = $image ; }

        if (!empty($request->get('id'))) {
            $data_id = $request->get('id');
            $node = DefaultCategories::where('id',  $data_id)->update($attributes);
            DefaultCategories::fixTree();
            $this->re_order();
            $resp = ['success' => 1, 'code' => 201, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } else {
            $node = DefaultCategories::create($attributes);
            $data_id = $node->id ;
            $this->re_order();
            $resp = ['success' => 1, 'code' => 200, 'msg' => 'เพิ่มรายการใหม่สำเร็จ'];
        }

        // create slug
        $o_slug = new SlugController;
        $o_slug->createMetadata($request, $data_id);

        return response()->json($resp);
    }

    public function re_order()
    {
        $all_cat = DefaultCategories::orderBy('_lft','asc')->get();
        $cnt = 0;
        foreach($all_cat as $cat){
            $cnt++ ;
            $cat->sequence = $cnt ;
            $cat->save();
        }    
    }

    public function sort(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $move = $request->get('move');
            $node = DefaultCategories::find($id);
            $is_move = false;
            if ($move == 'up') {
                $is_move = $node->up();
            }

            if ($move == 'down') {
                $is_move = $node->down();
            }

            $this->re_order();

            if ($is_move) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
            }


            return response()->json($resp);
        }
    }


    public function set_move_node(Request $request)
    {
        if ($request->ajax()) {
           
            if (!empty($request->get('node_id'))&&!empty($request->get('next_by'))) {
                $node = DefaultCategories::find($request->get('node_id')) ;
                $neighbor = DefaultCategories::find($request->get('next_by')) ;
                // $move_status = $node->prependToNode($parent)->save(); 
                $move_status = $node->afterNode($neighbor)->save();
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => 'เรียงข้อมูลใหม่สำเร็จ','move_status'=>$move_status ];
            } else {
                $resp = ['success' => 0, 'code' => 300, 'msg' => 'ไม่มีข้อมูลที่ต้องเรียง'];
            }

            return response()->json($resp);
        }
    }


    public function get_category(Request $request)
    {
    
        $categories = DefaultCategories::withDepth()->defaultOrder()->get();

        $result = [];
        foreach($categories as $category){
            $showname = str_repeat(' - ', $category->depth) . $category->name_th;
            $result[] = [
                'id'=>$category->id,
                'text'=>$showname,
                'image'=>$category->image
            ];
        }

        $resp = ['success' => 1, 'code' => 200, 'msg' => 'success','results'=>$result];

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);
    }
}
