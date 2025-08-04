<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Entities\Slugs;

class SlugController extends Controller
{
    public function getSlug($slug)
    {
        $o_slugs  =  Slugs::with('lang_slug')->where('lang', app()->getLocale())->where('slug', $slug)->first();
        $lang_switch = [];
        if (!empty($o_slugs->lang_slug)) {
            foreach ($o_slugs->lang_slug as $slg) {
                $param = (!empty($slg->param)) ? '?' . http_build_query(json_decode($slg->param, 1)) : '';
                if (config('app.fallback_locale') == $slg->lang) {
                    $lang_switch[$slg->lang] = '/' . $slg->slug . $param;
                } else {
                    $lang_switch[$slg->lang] = '/' . $slg->lang . '/' . $slg->slug . $param;
                }
            }
        }

        return ['slug' => $o_slugs, 'lang_switch' => $lang_switch];
    }

    public function getSlugById($id)
    {
        $slug = Slugs::with('lang_slug')->where('id', $id)->first();
        $data = [];
        if (!empty($slug->lang_slug)) {
            foreach ($slug->lang_slug as $slg) {
                $param = (!empty($slg->param)) ? '?' . http_build_query(json_decode($slg->param, 1)) : '';
                if (config('app.fallback_locale') == $slg->lang) {
                    $data['url'][$slg->lang] = '/' . $slg->slug . $param;
                } else {
                    $data['url'][$slg->lang] = '/' . $slg->lang . '/' . $slg->slug . $param;
                }
            }
        }
        return $data;
    }

    public function getSlugUrl($module, $method, $data_id)
    {
        $data = [];
        $slug = Slugs::with('lang_slug')->where([['module', $module], ['method', $method], ['data_id', $data_id]])->first();
        if (!empty($slug->lang_slug)) {
            foreach ($slug->lang_slug as $slg) {
                $param = (!empty($slg->param)) ? '?' . http_build_query(json_decode($slg->param, 1)) : '';
                if (config('app.fallback_locale') == $slg->lang) {
                    $data['url'][$slg->lang] = '/' . $slg->slug . $param;
                } else {
                    $data['url'][$slg->lang] = '/' . $slg->lang . '/' . $slg->slug . $param;
                }
            }
        }
        return $data;
    }
    
    public function getMetadata($module, $method, $data_id)
    {
        $slugs = Slugs::where('module', $module)->where('method', $method)->where('data_id', $data_id)->get();
        $metadata = [];
        if (!empty($slugs)) {
            foreach ($slugs as $slug) {
                $metadata[$slug->lang] = [
                    'slug_uid' => $slug->slug_uid,
                    'slug' => $slug->slug,
                    'level' => $slug->level,
                    'module' => $slug->module,
                    'method' => $slug->method,
                    'data_id' => $slug->data_id,
                    'param' => $slug->param,
                    'meta_auther' => $slug->meta_auther,
                    'meta_title' => $slug->meta_title,
                    'meta_keywords' => $slug->meta_keywords,
                    'meta_description' => $slug->meta_description,
                    'meta_robots' => $slug->meta_robots,
                    'meta_image' => $slug->meta_image
                ];
            }
        }
        return $metadata;
    }

    public function DuplicateMetadata($module, $method, $data_id,$new_id)
    {
        $slugs = Slugs::where('module', $module)->where('method', $method)->where('data_id', $data_id)->get();
        if(!empty($slugs)){
            foreach($slugs as $slug){
                $new_slug = $slug->replicate();
                $new_slug->data_id = $new_id;
                $new_slug->slug = $slug->slug.'-'.$new_id;
                $new_slug->created_at = date('Y-m-d H:i:s');
                $new_slug->updated_at = date('Y-m-d H:i:s');
                $new_save =  $new_slug->save();
                $new_slug->slug_uid = $slug->module.'-'.$slug->method.'-'.$new_id;
                $new_slug->save();
            }
        }
    }

    public function checkSlug(Request $request)
    {
        $chk_slug = Slugs::where('lang', $request->get('lang'))->where('slug', $request->get('slug'))->first();
        if (!empty($chk_slug) && !empty($chk_slug->id)) {
            return false;
        } else {
            return true;
        }
    }

    public function createMetadata(Request $request, $data_id = 0)
    {
        $metadata = $request->get('metadata');
        foreach ($metadata as $lang => $meta) {
            // $slug = $this->createSlugText($meta['slug']);
            // $chk_slug = Slugs::where('lang', $lang)->where('slug', $slug)->first();
            // if (empty($chk_slug->id)) {
            $attributes = [];
            $attributes['slug'] = $this->createSlugText($meta['slug']);
            $attributes['level'] = !empty($meta['level']) ? $meta['level'] : '';
            $attributes['module'] = !empty($meta['module']) ? $meta['module'] : '';
            $attributes['method'] = !empty($meta['method']) ? $meta['method'] : '';
            $attributes['data_id'] = $data_id;
            $attributes['param'] = !empty($meta['param']) ? json_encode($meta['param'], JSON_UNESCAPED_UNICODE) : '';


            $attributes['meta_auther'] = !empty($meta['meta_auther']) ? $meta['meta_auther'] : '';
            $attributes['meta_title'] = !empty($meta['meta_title']) ? $meta['meta_title'] : '';
            $attributes['meta_keywords'] = !empty($meta['meta_keywords']) ? $meta['meta_keywords'] : '';
            $attributes['meta_description'] = !empty($meta['meta_description']) ? $meta['meta_description'] : '';
            $attributes['meta_robots'] = !empty($meta['meta_robots']) ? $meta['meta_robots'] : '';
            // $attributes['meta_image'] = !empty($meta['meta_image']) ? $meta['meta_image'] : '';
            if ($request->hasFile("meta_image_$lang")) {
                $image = $request->file("meta_image_$lang");
                $new_filename = $lang . '-' . time() . '.' . $image->extension();
                $path = $image->storeAs(
                    "public/meta/image/$lang",
                    $new_filename
                );
                $attributes['meta_image'] = Storage::url($path);
                if (!empty($request->get('meta_image_' . $lang . '_old'))) {
                    $old_path = str_replace('storage', 'public', $request->get('meta_image_' . $lang . '_old'));
                    Storage::delete($old_path);
                    $attributes['meta_image'] = '';
                }
            } else {
                if (!empty($request->get('meta_image_' . $lang . '_del'))) {
                    $old_path = str_replace('storage', 'public', $request->get('meta_image_' . $lang . '_old'));
                    Storage::delete($old_path);
                    $attributes['meta_image'] = '';
                }
            }
            $attr_main = array('lang' => $lang, 'slug_uid' => $attributes['module'] . '-' . $attributes['method'] . '-' . $attributes['data_id']);
            Slugs::updateOrCreate($attr_main, $attributes);
            // }
        }
    }

    public function updateMetadata(Request $request)
    {
        $metadata = $request->get('metadata');
        foreach ($metadata as $lang => $meta) {
            $slug = $this->createSlugText($meta['slug']);
            $chk_slug = Slugs::where('lang', $lang)->where('slug', $slug)->first();
            if (empty($chk_slug->id)) {
                $attributes = [];
                $attributes['slug'] = $slug;
                $attributes['lang'] = $lang;
                $attributes['param'] = (!empty($meta['param'])) ? json_encode($meta['param'], JSON_UNESCAPED_UNICODE) : '';

                $attributes['meta_auther'] = (!empty($meta['meta_auther'])) ? $meta['meta_auther'] : '';
                $attributes['meta_title'] = (!empty($meta['meta_title'])) ? $meta['meta_title'] : '';
                $attributes['meta_keywords'] = (!empty($meta['meta_keywords'])) ? $meta['meta_keywords'] : '';
                $attributes['meta_description'] = (!empty($meta['meta_description'])) ? $meta['meta_description'] : '';
                $attributes['meta_robots'] = (!empty($meta['meta_robots'])) ? $meta['meta_robots'] : '';
                $attributes['meta_image'] = (!empty($meta['meta_image'])) ? $meta['meta_image'] : '';
                Slugs::where([['method', $meta['method']], ['module', $meta['module']], ['data_id', $meta['data_id']]])->update($attributes);
            }
        }
    }

    public function validatorSlug(Request $request)
    {
        $text['th'] = 'ภาษาไทย!';
        $text['en'] = 'ภาษาอังกฤษ!';

        $metadata = $request->get('metadata');
        foreach ($metadata as $lang => $meta) {
            if (empty($meta['slug'])) {
                $resp = ['error' => 0, 'msg' => 'โปรดระบุ SEO Slug ' . $text[$lang]];
                return $resp;
            }
            $slug = $this->createSlugText($meta['slug']);
            $chk_slug = Slugs::where([['lang', $lang], ['slug', $slug], ['slug_uid', '<>', $meta['module'] . '-' . $meta['method'] . '-' . $request->get('id')]])->first();
            if (!empty($chk_slug)) {
                $resp = ['error' => 1,  'msg' => 'SEO Slug ' . $text[$lang] . ' มีการใช้งานแล้ว'];
                return $resp;
            }
        }
    }
    public function validatorSlugUpdate(Request $request)
    {
        $text['th'] = 'ภาษาไทย!';
        $text['en'] = 'ภาษาอังกฤษ!';

        $metadata = $request->get('metadata');
        foreach ($metadata as $lang => $meta) {
            if (empty($meta['slug'])) {
                $resp = ['error' => 0, 'msg' => 'โปรดระบุ SEO Slug ' . $text[$lang]];
                return $resp;
            }
            $slug = $this->createSlugText($meta['slug']);
            $chk_slug = Slugs::where([['lang', $lang], ['slug', $slug], ['slug_uid', '<>', $meta['module'] . '-' . $meta['method'] . '-' . $request->get('data_id')]])->first();

            if (!empty($chk_slug)) {
                $resp = ['error' => 1,  'msg' => 'SEO Slug ' . $text[$lang] . ' มีการใช้งานแล้ว'];
                return $resp;
            }
        }
    }

    public function createSlugText($title)
    {
        $title = strip_tags($title);
        // Preserve escaped octets.
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
        // Remove percent signs that are not part of an octet.
        $title = str_replace('%', '', $title);
        // Restore octets.
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

        if ($this->seems_utf8($title)) {
            if (function_exists('mb_strtolower')) {
                $title = mb_strtolower($title, 'UTF-8');
            }
            $title = $this->utf8_uri_encode($title, 2048);
        }

        $title = strtolower($title);
        $title = preg_replace('/&.+?;/', '', $title); // kill entities
        $title = str_replace('.', '-', $title);
        $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        $title = preg_replace('|-+|', '-', $title);
        $title = trim($title, '-');

        return urldecode($title);
    }

    public function seems_utf8($str)
    {
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80) $n = 0; # 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0) $n = 1; # 110bbbbb
            elseif (($c & 0xF0) == 0xE0) $n = 2; # 1110bbbb
            elseif (($c & 0xF8) == 0xF0) $n = 3; # 11110bbb
            elseif (($c & 0xFC) == 0xF8) $n = 4; # 111110bb
            elseif (($c & 0xFE) == 0xFC) $n = 5; # 1111110b
            else return false; # Does not match any model
            for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }

    public function utf8_uri_encode($utf8_string, $length = 0)
    {
        $unicode = '';
        $values = array();
        $num_octets = 1;
        $unicode_length = 0;

        $string_length = strlen($utf8_string);
        for ($i = 0; $i < $string_length; $i++) {

            $value = ord($utf8_string[$i]);

            if ($value < 128) {
                if ($length && ($unicode_length >= $length))
                    break;
                $unicode .= chr($value);
                $unicode_length++;
            } else {
                if (count($values) == 0) $num_octets = ($value < 224) ? 2 : 3;

                $values[] = $value;

                if ($length && ($unicode_length + ($num_octets * 3)) > $length)
                    break;
                if (count($values) == $num_octets) {
                    if ($num_octets == 3) {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                        $unicode_length += 9;
                    } else {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                        $unicode_length += 6;
                    }

                    $values = array();
                    $num_octets = 1;
                }
            }
        }

        return $unicode;
    }

     /**
     * Function :  get slug
     * Dev : Tong
     * Update Date : 19 Sep 2022
     * @param POST
     * @return json response update slug
     */
    public function get_slug(Request $request)
    {
        $slugs = Slugs::where('lang',app()->getLocale())->orderBy('slug','asc')->get() ;
        $result = [];
        foreach($slugs as $slug){
            $result[] = [
                'id'=>$slug->id,
                'text'=>$slug->slug,
            ];
        }

        $resp = ['success' => 1, 'code' => 200, 'msg' => 'success','results'=>$result];

        return response()->json($resp, 200, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'],
        JSON_UNESCAPED_UNICODE);
    }
}
