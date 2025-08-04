<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Entities\Settings;
use Modules\Setting\Entities\Tags;

class SettingController extends Controller
{

    public function getSetting()
    {
        $setting = Settings::find(1);
        $data = [];
        if (!empty($setting)) {
            $data['logo']['header'] = $setting->logo_header;
            $data['logo']['logo'] = $setting->logo_footer;
            $data['link_login'] = $setting->link_login;
        }
        return $data;
    }
    public function getMeta()
    {
        $setting = Settings::find(1);
        $data = [];
        if (!empty($setting)) {
            $data['title'] = $setting->meta_title;
            $data['keywords'] = $setting->meta_keywords;
            $data['description'] = $setting->meta_description;
            $data['seo_image'] = $setting->seo_image;
        }
        return $data;
    }

    public function getTag()
    {
        $tag = Tags::where('status', 1)->get();
        $data = [];
        if (!empty($tag)) {
            foreach ($tag as $value) {
                array_push($data, [
                    'head' => core_getTextString($value->head),
                    'body' => core_getTextString($value->body)
                ]);
            }
        }
        return $data;
    }
}
