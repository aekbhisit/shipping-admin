<?php

namespace Modules\Setting\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\Slugs;
use Yajra\DataTables\DataTables;
use Modules\Core\Http\Controllers\AdminController;

class SlugAdminController extends AdminController
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $adminInit = $this->adminInit() ;
        return view('setting::slug.index',['adminInit'=>$adminInit]);
    }
    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {

            //init datatable
            $dt_name_column = array('id', 'level', 'module', 'method', 'data_id',  'slug', 'slug_uid');
            $dt_order_column = $request->get('order')[0]['column'];
            $dt_order_dir = $request->get('order')[0]['dir'];
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // create brand object
            $o_data = new Slugs();

            // $o_data = $o_data->where('lang',  'th');
            // add search query if have search from datable

            if (!empty($dt_search)) {
                $o_data = $o_data->Where(function ($query) use ($dt_search) {
                    $query->where('module', 'like', "%" . $dt_search . "%")
                        ->orwhere('method', 'like', "%" . $dt_search . "%")
                        ->orwhere('slug', 'like', "%" . $dt_search . "%");
                });
            }

            $dt_total = $o_data->count();
            // set query order & limit from datatable
            $o_data = $o_data->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
                ->offset($dt_start)
                ->limit($dt_length);

            // query brand
            $slug = $o_data->get();
            // prepare datatable for response
            $tables = DataTables::of($slug)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('slug_row')
                ->setTotalRecords($dt_total)
                ->editColumn('slug', function ($record) {
                    return limit($record->slug, 50);
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="btn-list">';
                    if (roles('admin.setting.slug.edit')) {
                        $action_btn .= '<a href="' . route('admin.setting.slug.edit', $record->id) . '" class="btn btn-sm me-1 btn-outline-primary" title="Edit"><i class="bx bx-pencil"></i></a>';
                    }
                    if (roles('admin.setting.slug.set_delete')) {
                        $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bx bx-trash"></i></a>';
                    }
                    $action_btn .= '</div>';

                    return $action_btn;
                })
                ->escapeColumns([]);

            // response datatable json
            return $tables->make(true);
        }
    }

    public function form($id)
    {
        $adminInit = $this->adminInit() ;
        $slug = Slugs::find($id);
        // dd($slug);
        $o_slug = new SlugController;
        $setting = $o_slug->getMetadata($slug->module, $slug->method, $slug->data_id);

        return view('setting::slug.form', ['data_id' => $slug->data_id, 'metadata' => $setting,'adminInit'=>$adminInit]);
    }

    public function save(Request $request)
    {
        $o_slug = new SlugController;
        if (!empty($o_slug->validatorSlugUpdate($request))) {
            $error = $o_slug->validatorSlugUpdate($request);
            $resp = ['success' => 0, 'code' => 301, 'msg' => $error['msg']];
            return response()->json($resp);
        }
        $o_slug->createMetadata($request, $request->get('data_id'));
        $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกข้อมูลสำเร็จ'];
        return response()->json($resp);
    }

    public function set_delete(Request $request)
    {
        if ($request->ajax()) {
            $slugs = Slugs::with('lang_slug')->find($request->get('id'));

            if (!empty($slugs->lang_slug)) {
                foreach ($slugs->lang_slug as $value) {
                    if (Slugs::find($value->id)->delete()) {
                        $resp = ['success' => 1, 'code' => 200, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
                    } else {
                        $resp = ['success' => 0, 'code' => 500, 'msg' => 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง!'];
                        return response()->json($resp);
                    }
                }
            }
            return response()->json($resp);
        }
    }

    public function generate_sitemap()
    {
        // return URL::to('/');
        // SitemapGenerator::create('https://app.test')->writeToFile(public_path('sitemap/sitemap.xml'));
        // $slug = Slugs::all();

        // $sitemap = new Sitemap;
        // if (!empty($slug)) {
        //     $sitemap->create();
        //     foreach ($slug as $value) {
        //         $lang = !empty($value->lang) && $value->lang == 'th' ? '/' : '/en/';
        //         $level = 0;
        //         switch ($value->level) {
        //             case 1:
        //                 $level = 0.9;
        //                 break;
        //             case 2:
        //                 $level = 0.8;
        //                 break;
        //             default:
        //                 $level = 0.7;
        //                 break;
        //         }
        //         $sitemap->add(Url::create($lang . $value->slug)->setLastModificationDate(Carbon::now())
        //             ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)->setPriority($level));
        //     }
        //     $sitemap->writeToFile(public_path('sitemap.xml'));
        // }
        // return $sitemap;
        return redirect()->back();
    }
}
