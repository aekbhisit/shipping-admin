<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Entities\Settings;
use Modules\Core\Http\Controllers\AdminController;

class SettingAdminController extends AdminController
{
    /**
     * Function : __construct check admin login
     * Dev : pop
     * Update Date : 14 Jul 2021
     * @param Get
     * @return if not login redirect to /admin
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    /**
     * Function : add con$contactus form
     * Dev : pop
     * Update Date : 04 August 2021
     * @param GET
     * @return category form view
     */
    public function form()
    {
        $adminInit = $this->adminInit() ;
        $setting = Settings::find(1);
        // dd($setting);
        return view('setting::setting.form', ['setting' => $setting,'adminInit'=>$adminInit]);
    }

    /**
     * Function :  websettings save
     * Dev : Poom
     * Update Date : 19 Jan 2022
     * @param POST
     * @return json response status
     */
    public function save(Request $request)
    {
        $attributes = [
            "link_login" => $request->get('link_login'),
            "meta_title" => $request->get('meta_title'),
            "meta_keywords" => $request->get('meta_keywords'),
            "meta_description" => $request->get('meta_description'),
            "google_analytics" => $request->get('google_analytics'),
        ];
        if ($request->hasFile('logo_header')) {
            $image = $request->file('logo_header');
            $new_filename = time() . "header." . $image->extension();
            $path = $image->storeAs(
                'public/setting/logo/header',
                $new_filename
            );
            $attributes['logo_header'] = Storage::url($path);
            if (!empty($request->get('logo_header_old'))) {
                $old_path = str_replace('storage', 'public', $request->get('logo_header_old'));
                Storage::delete($old_path);
            }
        } else {
            if (!empty($request->get('logo_header_del')) && $request->get('logo_header_del') == 1) {
                $old_path = str_replace('storage', 'public', $request->get('logo_header_old'));
                Storage::delete($old_path);
                $attributes['logo_header'] = '';
            }
        }

        if ($request->hasFile('logo_footer')) {
            $image = $request->file('logo_footer');
            $new_filename = time() . "footer." . $image->extension();
            $path = $image->storeAs(
                'public/setting/logo/footer',
                $new_filename
            );
            $attributes['logo_footer'] = Storage::url($path);
            if (!empty($request->get('logo_footer_old'))) {
                $old_path = str_replace('storage', 'public', $request->get('logo_footer_old'));
                Storage::delete($old_path);
            }
        } else {
            if (!empty($request->get('logo_footer_del')) && $request->get('logo_footer_del') == 1) {
                $old_path = str_replace('storage', 'public', $request->get('logo_footer_old'));
                Storage::delete($old_path);
                $attributes['logo_footer'] = '';
            }
        }

        if ($request->hasFile('seo_image')) {
            $image = $request->file('seo_image');
            $new_filename = time() . "seo." . $image->extension();
            $path = $image->storeAs(
                'public/setting/seo',
                $new_filename
            );
            $attributes['seo_image'] = Storage::url($path);
            if (!empty($request->get('seo_image_old'))) {
                $old_path = str_replace('storage', 'public', $request->get('seo_image_old'));
                Storage::delete($old_path);
            }
        } else {
            if (!empty($request->get('seo_image_del')) && $request->get('seo_image_del') == 1) {
                $old_path = str_replace('storage', 'public', $request->get('seo_image_old'));
                Storage::delete($old_path);
                $attributes['seo_image'] = '';
            }
        }
        $attr = array('id' => 1);
        Settings::updateOrCreate($attr, $attributes);
        $resp = ['success' => 1, 'code' => 200, 'msg' => 'อัปเดตข้อมูลสำเร็จ'];

        return response()->json($resp);
    }
}
