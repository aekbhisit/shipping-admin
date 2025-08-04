<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Slugs;
use Modules\Core\Http\Controllers\SlugController;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;

use Spatie\Sitemap\Tags\Url;
use Yajra\DataTables\DataTables;

class SlugAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('core::slug.index');
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

            $o_data = $o_data->where('lang',  'th');
            // add search query if have search from datable

            if (!empty($dt_search)) {
                $o_data = $o_data->orwhere('module', 'like', "%" . $dt_search . "%")
                    ->orwhere('method', 'like', "%" . $dt_search . "%")
                    ->orwhere('slug', 'like', "%" . $dt_search . "%");
            }

            $dt_total = $o_data->count();
            // set query order & limit from datatable
            // $o_data = $o_data->orderBy($dt_name_column[$dt_order_column], $dt_order_dir)
            //     ->offset($dt_start)
            //     ->limit($dt_length);

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
                    if (roles('admin.core.slug.edit')) {
                        $action_btn .= '<a href="' . route('admin.core.slug.edit', $record->slug_uid) . '" class="btn btn-outline-primary"><i class="fa fa-pencil"></i></a>';
                    }
                    if ($record->type == 0 && roles('admin.core.slug.set_delete')) {
                        $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="fa fa-trash"></i></a>';
                    }
                    $action_btn .= '</div>';

                    return $action_btn;
                })
                ->escapeColumns([]);

            // response datatable json
            return $tables->make(true);
        }
    }

    public function form($slug_uid = '')
    {
        $mode = 'add';
        $slug = [];
        $metadata = [];
        if (!empty($slug_uid)) {
            $mode = 'edit';
            $slug = Slugs::where('slug_uid', $slug_uid)->first();
            $o_slug = new SlugController;
            $metadata = $o_slug->getMetadata($slug->module, $slug->method, $slug->data_id);
        }
        return view('core::slug.form', ['slug' => $slug, 'metadata' => $metadata, 'mode' => $mode]);
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
        $slug = Slugs::all();

        $sitemap = new Sitemap;
        if (!empty($slug)) {
            $sitemap->create();
            foreach ($slug as $value) {
                $lang = !empty($value->lang) && $value->lang == 'th' ? '/' : '/en/';
                $level = 0;
                switch ($value->level) {
                    case 1:
                        $level = 0.9;
                        break;
                    case 2:
                        $level = 0.8;
                        break;
                    default:
                        $level = 0.7;
                        break;
                }
                $sitemap->add(Url::create($lang . $value->slug)->setLastModificationDate(Carbon::now())
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)->setPriority($level));
            }
            $sitemap->writeToFile(public_path('sitemap.xml'));
        }
        // return $sitemap;
        return redirect()->back();
    }
}
