<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Modules\Course\Entities\CourseGroup;
use Modules\Customer\Entities\CustomerPromotions;
use Modules\Customer\Entities\CustomerRedeems;
use Modules\Customer\Entities\CustomerShops;
use Modules\Gift\Entities\Gifts;
use Modules\Job\Entities\Jobs;
use Modules\Setting\Entities\Settings;
use Modules\User\Http\Controllers\RoleController;
use Modules\Core\Entities\AdminMenus;



function core_test()
{
    echo 'test';
}

function setFlatCategory($categories)
{

    $traverse = function ($categories, $prefix = '-', $level = 0, &$result = []) use (&$traverse) {
        foreach ($categories as  $category) {
            $category->level = $level;

            if ($level > 0) {
                $category->show_prefix = str_pad('', $level, $prefix);
                // $category->name =  $show_prefix . ' ' . $category->name;
            }
            if (!empty($category->children)) {
                $new_category = $category;
                unset($new_category->children);
                $result[$category->id] = $new_category;
                $traverse($category->children, '-', $level + 1, $result);
            } else {
                $result[$category->id] = $category;
            }
        }
        return $result;
    };

    return $traverse($categories);
}

function getTextString($str)
{
    return htmlspecialchars_decode(html_entity_decode($str));
}

function setTextString($str)
{
    return htmlentities(htmlspecialchars($str));
}

/* Check File In Server */
function CheckFileInServer($file)
{
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . parse_url($file, PHP_URL_PATH))) {
        return true;
    } else {
        return false;
    }
}

// function core_calParentCourseGroup($parent)
// {

//     if ($parent == '') {
//         $level = 1;
//     } else {
//         $i = 2;
//         $id = '';
//         while ($i > 0) {
//             $level = $i;
//             if ($i == 2) {
//                 $course_group = CourseGroup::find($parent);
//                 $id = $course_group->parent_id;
//             } else {
//                 $course_group = CourseGroup::find($id);
//                 $id = $course_group->parent_id;
//             }
//             if (empty($course_group->parent_id)) {
//                 break;
//             }
//             $i++;
//         }
//     }

//     return $level;
// }

// function core_roles($route, $id = 0)
// {
//     $role = new RoleController();
//     return $role->checkAccessControl($route, $id);
// }

function core_route($route_name, $param = [])
{
    $default_locale = config('app.fallback_locale');
    // echo  $default_locale.' ='.config('app.fallback_locale'); 
    if ($default_locale == app()->getLocale()) {
        if (!empty($param)) {
            return  route($route_name, $param);
        } else {
            return  route($route_name);
        }
    } else {
        $new_param = array_merge([app()->getLocale()], $param);
        if (!empty($new_param)) {
            return  route('lang.' . $route_name, $new_param);
        } else {
            return  route('lang.' . $route_name);
        }
    }
}

function limit($value, $limit = 100, $end = '...')
{
    if (mb_strwidth($value, 'UTF-8') <= $limit) {
        return $value;
    }
    if (strlen($value) < $limit) {
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8'));
    } else {
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }
}

function core_getCurrencyData($currency)
{
    $_ALL_CURRENCY = array(
        'THB' => array('name' => 'Thailand Baht', 'sign' => '฿', 'align' => '1'),
        'USD' => array('name' => 'United States Dollar', 'sign' => '$', 'align' => '2'),
        'GBP' => array('name' => 'United Kingdom Pound', 'sign' => '£', 'align' => '2'),
        'JPY' => array('name' => '日本語 Japan Yen', 'sign' => '¥', 'align' => '2'),
        'CNY' => array('name' => '中國 China Yuan Renminbi', 'sign' => '¥', 'align' => '2'),
        'KRW' => array('name' => '한국의 Korea Won', 'sign' => '₩', 'align' => '2'),
        'FRF' => array('name' => 'français (French)', 'sign' => 'francs', 'align' => '1'),
        'ESP' => array('name' => 'español (Spanish)', 'sign' => '€', 'align' => '2'),
        'RUB' => array('name' => 'русский язык Belarus Ruble', 'sign' => 'p.', 'align' => '1'),
        'LAK' => array('name' => 'ພາສາລາວ Laos Kip', 'sign' => '₭', 'align' => '1'),
        'VND' => array('name' => 'Tiếng Việt Viet Nam Dong', 'sign' => '₫', 'align' => '1'),
        'MMK' => array('name' => 'မြန်မာဘာသာ (Burmese)', 'sign' => 'K', 'align' => '2'),
        'AUD' => array('name' => 'Australian Dollar', 'sign' => 'AUD', 'align' => '2'),
        'EUR' => array('name' => 'Euro', 'sign' => '€', 'align' => '2'),
        'KHR' => array('name' => 'ភាសាខ្មែរ Cambodia Riel', 'sign' => '៛', 'align' => '1'),
        'INR' => array('name' => 'India', 'sign' => '₹', 'align' => '2'),
        'EGP' => array('name' => 'Eygpt', 'sign' => '£', 'align' => '2')
    );

    return $_ALL_CURRENCY[$currency];
}

function core_pre($array)
{
    echo '<pre>';
    print_r($array);
    echo '<pre>';
}

function str_phone($p_number)
{
    $res = '';
    $o_number = '';
    foreach (str_getcsv($p_number, '-') as $value) {
        $o_number .= $value;
    }
    $d = str_split($o_number);
    if (count($d) < 10) {
        foreach ($d as  $i => $val) {
            if ($i == 2 || $i == 5) {
                $res .= '-' . $val;
            } else {
                $res .=   $val;
            }
        }
    } else {
        foreach ($d as  $i => $val) {
            if ($i == 3 || $i == 6) {
                $res .= '-' . $val;
            } else {
                $res .=   $val;
            }
        }
    }
    return $res;
}

function setPagination($page = 1, $totel = 1)
{
    $data = [];
    if ($totel <= 6) {
        $f = 0;
        for ($i = 1; $i <= $totel; $i++) {
            $data[$f++] = $i;
        }
    } else {
        if ($page < 5) {
            $f = 0;
            for ($i = 1; $i <= 5; $i++) {
                $data[$f] = $i;
                $f++;
            }
            $data[$f] = '...';
            $data[++$f] = $totel;
        }
        if ($page > $totel - 4) {
            $data[0] = 1;
            $data[1] = '...';
            $h = 2;
            for ($i = $totel - 4; $i <= $totel; $i++) {
                $data[$h] = $i;
                $h++;
            }
        }
        if ($page >= 5 && $page <= $totel - 4) {
            $data[0] = 1;
            $data[1] = '...';
            $r = 2;
            for ($l = $page - 1; $l <= $page + 1; $l++) {
                $data[$r] = $l;
                $r++;
            }
            $data[$r] = '...';
            $data[++$r] = $totel;
        }
    }
    return $data;
}

function setting()
{
    try {
        $setting = Settings::find(1);
        if (!empty($setting)) return $setting;
        
        // Return fallback settings if no settings found
        return (object)[
            'logo_header' => '/assets/images/logo-icon.png',
            'meta_title' => 'Shiper Admin',
        ];
    } catch (\Exception $e) {
        // Fallback settings if Settings model doesn't exist
        return (object)[
            'logo_header' => '/assets/images/logo-icon.png',
            'meta_title' => 'Shiper Admin',
        ];
    }
}


// file upload
function image_upload(
    $id = 1,
    $name = '',
    $label = '',
    $image = '',
    $size_recommend = '',
    $is_gallery = false,
    $delete_function = false
) {

    $ele = '<label class="form-label">' . $label . '</label>';
    $ele .= '<label for="upload_image' . $id . '" class="label-upload i">';
    $ele .= '<div class="image-upload mwz-image-upload upload_image' . $id . '" data-id="' . $id . '">';
    if ($delete_function == false) {
        $ele .= '<button type="button" id="btn_delete' . $id . '" 
        class="btn btn-outline-danger btn-upload " 
        data-confirm-del-txt="' . __('admin.upload_confirm_del_txt') . '" 
        data-confirm-txt="' . __('admin.upload_confirm_txt') . '" 
        data-cancel-txt="' . __('admin.upload_cancel_txt') . '" 
        data-upload-click-to-upload-txt="' . __('admin.upload_click_to_upload') . '" 
        >' . __('admin.upload_delete_txt') . '</button>';
    }
    $ele .= '<div class="dz-message upload_show_img_container ">';
    if (!empty($image)) {
        $ele .= '<div id="upload_show_img_' . $id . '">';

        if ($is_gallery) {
            $ele .= '<ul id="upload_lightgallery_' . $id . '" class="list-unstyled d-upload-image">';
            $ele .= '<li data-responsive="' . $image . '" data-src="' . $image . '">';
            $ele .= '<a href=""><img style="max-height: 150px" class="img-responsive" src="' . $image . '" alt="Thumb-1"></a>';
            $ele .= '</li>';
            $ele .= '</ul>';
            // $ele .= '</div>';
        } else {
            $ele .= '<div id="upload_show_upload_' . $id . '" class="d-upload-image" >';
            $ele .= '<p><b><img style="max-height: 150px" class="img-responsive" src="' . $image . '" alt="Thumb-1"></b></p>';
            $ele .= '</div>';
        }

        $ele .= '</div>';
    } else {
        $ele .= '<div class="d-upload-image">';
        $ele .= '<i class="ion-upload"></i>';
        $ele .= '<p><b>' . __('admin.upload_click_to_upload') . '</b></p>';
        $ele .= '</div>';
    }
    $ele .= '</div>';
    $ele .= '<div class="input-upload">';
    $ele .= '<input name="' . $name . '" id="upload_image' . $id . '" type="file" />';
    $ele .= '<input name="' . $name . '_del" id="upload_image' . $id . '_del" type="hidden" value="0" />';
    $ele .= '<input name="' . $name . '_old" id="upload_image' . $id . '_old" type="hidden" value="' . $image . '" />';
    $ele .= '</div>';
    $ele .= '</div>';
    $ele .= '</label>';
    $ele .= '<span>' . __('admin.upload_size_recommend') . $size_recommend . '</span>';
    return $ele;
}

function set_image_upload($request, $image_name, $path = "public", $file_name = "")
{
    $image_path = false;

    if ($request->hasFile($image_name)) {
        $image = $request->file($image_name);
        $new_filename = $file_name . time() . "." . $image->extension();
        $path = $image->storeAs(
            $path,
            $new_filename
        );
        $image_path = Storage::url($path);
        if (!empty($request->get($image_name . '_old'))) {
            $old_path = str_replace('storage', 'public', $request->get($image_name . '_old'));
            Storage::delete($old_path);
        }
        return $image_path;
    } else {
        if (!empty($request->get($image_name . '_del')) && $request->get($image_name . '_del') == 1) {
            $old_path = str_replace('storage', 'public', $request->get($image_name . '_old'));
            Storage::delete($old_path);
            $image_path = '';
            return $image_path;
        }
    }

    return $image_path;
}

function image_upload_multiple(
    $id = "image",
    $name = "image",
    $files = "",
    $action = "",
    $thumbnail_path = "",
    $accept_files = ".jpg,.png,.jpeg,.gif",
    $max_file = 5,
    $upload_msg = "Drop image here (or click) to capture/upload",
    $remove_msg = "remove",
    $max_file_msg = "You can not upload any more files.",
    $inputs = [
        'name_th' => 'ชื่อสินค้า (TH)',
        'name_en' => 'ชื่อสินค้า (EN)'
    ]
) {
    $upload = '';

    $upload .= '<div class="dropzone dz-clickable" id="' . $id . '" data-param-name="' . $name . '"  data-action="' . $action . '" data-thumbnail-path="' . $thumbnail_path . '" data-accepted-files="' . $accept_files . '" data-max-file="' . $max_file . '" data-upload-msg="' . $upload_msg . '" data-remove-msg="' . $remove_msg . '" data-max-file-msg="' . $max_file_msg . '" >';

    $upload .= '<input type="hidden" class="file_list" name="' . $name . '_file_list" id="' . $name . '_file_list"  value=\'' . json_encode($files) . '\'>';
    $upload .= '<input type="hidden" class="file_removed" name="' . $name . '_file_removed" id="' . $name . '_file_removed" >';

    $upload .= '</div>';

    $upload .= image_upload_multiple_preview($name, $inputs);
    return $upload;
}

function image_upload_multiple_preview($name, $inputs)
{
    $preview = '';
    $preview .= '<div id="preview-template" style="display:none;">';
    $preview .= '<div class="dz-preview dz-file-preview">';
    $preview .= '<div class="dz-image"><img data-dz-thumbnail /></div>';
    $preview .= '<div class="dz-details">';
    $preview .= '<div class="dz-size"><span data-dz-size></span></div>';
    $preview .= '<div class="dz-filename"><span data-dz-name></span></div>';
    $preview .= '</div>';
    $preview .= '<div class="dz-progress">';
    $preview .= '<span class="dz-upload" data-dz-uploadprogress></span>';
    $preview .= '</div>';
    $preview .= '<div class="dz-error-message"><span data-dz-errormessage></span></div>';
    $preview .= '<div class="dz-success-mark">';
    $preview .= '<svg width="54" height="54" viewBox="0 0 54 54" fill="white" xmlns="http://www.w3.org/2000/svg" >';
    $preview .= '<path d="M10.2071 29.7929L14.2929 25.7071C14.6834 25.3166 15.3166 25.3166 15.7071 25.7071L21.2929 31.2929C21.6834 31.6834 22.3166 31.6834 22.7071 31.2929L38.2929 15.7071C38.6834 15.3166 39.3166 15.3166 39.7071 15.7071L43.7929 19.7929C44.1834 20.1834 44.1834 20.8166 43.7929 21.2071L22.7071 42.2929C22.3166 42.6834 21.6834 42.6834 21.2929 42.2929L10.2071 31.2071C9.81658 30.8166 9.81658 30.1834 10.2071 29.7929Z" />';
    $preview .= '</svg>';
    $preview .= '</div>';
    $preview .= '<div class="dz-error-mark">';
    $preview .= '<svg width="54" height="54" viewBox="0 0 54 54" fill="white" xmlns="http://www.w3.org/2000/svg" >';
    $preview .= '<path d="M26.2929 20.2929L19.2071 13.2071C18.8166 12.8166 18.1834 12.8166 17.7929 13.2071L13.2071 17.7929C12.8166 18.1834 12.8166 18.8166 13.2071 19.2071L20.2929 26.2929C20.6834 26.6834 20.6834 27.3166 20.2929 27.7071L13.2071 34.7929C12.8166 35.1834 12.8166 35.8166 13.2071 36.2071L17.7929 40.7929C18.1834 41.1834 18.8166 41.1834 19.2071 40.7929L26.2929 33.7071C26.6834 33.3166 27.3166 33.3166 27.7071 33.7071L34.7929 40.7929C35.1834 41.1834 35.8166 41.1834 36.2071 40.7929L40.7929 36.2071C41.1834 35.8166 41.1834 35.1834 40.7929 34.7929L33.7071 27.7071C33.3166 27.3166 33.3166 26.6834 33.7071 26.2929L40.7929 19.2071C41.1834 18.8166 41.1834 18.1834 40.7929 17.7929L36.2071 13.2071C35.8166 12.8166 35.1834 12.8166 34.7929 13.2071L27.7071 20.2929C27.3166 20.6834 26.6834 20.6834 26.2929 20.2929Z"/>';
    $preview .= '</svg>';
    $preview .= '</div>';
    $preview .= '<div class="show-lable">';
    if (!empty($inputs)) {
        foreach ($inputs as $input => $label) {
            $input_name = $input; //$name.'_input['.$input.']' ;
            $preview .= '<div class="label-' . $label . '" >';
            $preview .= '<div class="form-group frm-name">';
            $preview .= '<label class="form-label">' . $label . '</label>';
            $preview .= '<input type="text" class="form-control" data-name="' . $input_name . '"
                                        placeholder="' . $label . '"
                                        value="">';
            $preview .= '</div>';
            $preview .= '</div>';
        }
    }
    $preview .= '</div>';
    $preview .= '</div>';
    $preview .= '</div>';
    return $preview;
}


/**
 * Function : Create Slug format
 * Dev : Soft
 * Update Date : 26 Oct 2021
 * @param Slug name
 * @return Slug name
 */
function createSlugText($title)
{
    $title = strip_tags($title);
    // Preserve escaped octets.
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    // Remove percent signs that are not part of an octet.
    $title = str_replace('%', '', $title);
    // Restore octets.
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

    if (seems_utf8($title)) {
        if (function_exists('mb_strtolower')) {
            $title = mb_strtolower($title, 'UTF-8');
        }
        $title = utf8_uri_encode($title, 2048);
    }

    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); // kill entities
    $title = str_replace('.', '-', $title);
    $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
    $title = preg_replace('/\s+/', '-', $title);
    $title = preg_replace('|-+|', '-', $title);
    $title = trim($title, '-');

    return $title;
}

function seems_utf8($str)
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

function utf8_uri_encode($utf8_string, $length = 0)
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

function roles($permission, $group = '', $get_current_route = false)
{
    try {
        // Use Spatie Permission package
        if (auth()->check()) {
            $user = auth()->user();
            
            // If no permission specified, return true for authenticated users
            if (empty($permission)) {
                return true;
            }
            
            // Check if user has the permission
            return $user->hasPermissionTo($permission);
        }
        
        return false;
    } catch (\Exception $e) {
        // Fallback to allow all permissions for testing
        return true;
    }
}

function sort_button($route_name, $id, $functiom_name = 'setUpdateSort')
{
    $action_btn = '';
    if (roles($route_name)) {
        $action_btn = '<div class="btn-list">';
        $action_btn .= '<a onclick="' . $functiom_name . '(' . $id . ',\'up\');"  href="javascript:void(0);" class="btn btn-sm btn-outline-default"><i class="fa fa-arrow-up" aria-hidden="true"></i></a>';
        $action_btn .= '<a onclick="' . $functiom_name . '(' . $id . ',\'down\');" href="javascript:void(0);" class="btn btn-sm btn-outline-default"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>';
        $action_btn .= '</div>';
    }
    return $action_btn;
}

function get_side_admin_menu()
{
    try {
        $menus = AdminMenus::where('status', 1)->orderBy('sequence', 'asc')->get()->toTree();
        return $menus;
    } catch (\Exception $e) {
        // Fallback to static menu if admin_menus table doesn't exist
        return collect([
            (object)[
                'id' => 1,
                'name' => 'Dashboard',
                'icon' => 'bx bx-home-circle',
                'route_name' => 'admin.dashboard.index',
                'url' => '/admin/dashboard',
                'link_type' => 1,
                'target' => '',
                'attr_class' => 'dashboard-menu',
                'show_badge' => false,
                'children' => collect([])
            ],
            (object)[
                'id' => 2,
                'name' => 'User Management',
                'icon' => 'bx bx-user',
                'route_name' => '',
                'url' => '#',
                'link_type' => 1,
                'target' => '',
                'attr_class' => 'user-menu',
                'show_badge' => false,
                'children' => collect([
                    (object)[
                        'id' => 21,
                        'name' => 'Users',
                        'icon' => 'bx bx-user-circle',
                        'route_name' => 'admin.user.user.index',
                        'url' => '/admin/user',
                        'link_type' => 1,
                        'target' => '',
                        'attr_class' => 'user-list-menu',
                        'show_badge' => false,
                        'children' => collect([])
                    ]
                ])
            ]
        ]);
    }
}

function side_roles($data)
{
    $roles = false;
    if (!empty($data->children) && count($data->children) > 0) {
        foreach ($data->children as  $value) {
            $roles = $roles || side_roles($value);
        }
    } else {
        $roles = $roles || roles($data->route_name);
    }
    return $roles;
}


function pre($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}


// ----------- old ------------//


if (!function_exists('optionalString')) {


    function optionalString($string, $before = null, $after = '')
    {
        if (!empty($string)) {
            return $before . $string . $after;
        }
    }
}

if (!function_exists('bapIsset')) {


    function bapIsset($array, $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
    }
}


if (!function_exists('sectionSlug')) {


    function sectionSlug($string)
    {
        try {
            return \Stringy\Stringy::create($string)->slugify('_');
        } catch (\Exception $exception) {

            return $string;
        }
    }
}

function webHook($arrData, $key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://webhook.site/" . $key);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrData, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
    return $result = curl_exec($ch);
}

function datetime()
{
    return date('Y-m-d H:i:s');
}

function getDatetime($set = null, $date = null)
{

    if (empty($date)) {
        $date = date("Y-m-d H:i:s");
    }
    if (!empty($set)) {
        $date = date("Y-m-d H:i:s", strtotime($set, strtotime($date)));
    }

    return $date;
}

function rp_microtime()
{
    $new = str_replace(array("."), array(""), microtime(TRUE));
    return $new;
}

// Function to get the client IP address
function get_client_ip()
{
    $ipaddress = '';

    if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
        $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
    else if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 *
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */
function encrypter($action, $string, $key = "IcMINuKnsdywvN1vTfscwqbP0GfzSAwvJvyNCbJ22DClGYJZkW", $salt = "qydB2A6XtUobfMVB5USYPkHKeihbqo")
{

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = $key;
    $secret_iv = $salt;
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function pusher($message, $event, $channel = 'maxmunbet')
{

    $options = array(
        'cluster' => 'ap1',
        'useTLS' => true
    );

    $pusher = new \Pusher\Pusher(
        '379fb6d599735678cb89',
        '5a9779b2e82d32c76dec',
        '1482891',
        $options
    );

    return $pusher->trigger($channel, $event, $message);
}


function notify_message($message, $token = "", $img = "", $sticker = null)
{

    define('LINE_API', "https://notify-api.line.me/api/notify");

    if ($token == "") {
        $token = ""; //
    }

    $queryData['message'] = $message;
    if ($img != "") {
        $queryData['imageFile'] = $img;
    }

    if (!empty($sticker)) {
        $queryData['stickerPackageId'] = $sticker['stickerPackageId'];
        $queryData['stickerId'] = $sticker['stickerId'];
    }

    $queryData = http_build_query($queryData, '', '&');

    $ch = curl_init(LINE_API);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: ' . strlen($queryData),
            'Authorization: Bearer ' . $token,
        )
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);

    // execute!
    $response = curl_exec($ch);
    $res = json_decode($response);

    // close the connection, release resources used
    curl_close($ch);

    //print_r($queryData);
    return $res;
}


function sendSMSTwilio($number, $message, $MessagingServiceSid = "MGdb4ba0cf2ca895f3f9edf718b48e634d")
{

    $phone = "+66" . substr($number, 1, 9);

    $body = [
        'To' => $phone,
        'Body' => $message,
        'MessagingServiceSid' => $MessagingServiceSid,
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/AC7204946f109a085d207e0b1e1083feeb/Messages.json',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($body),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic QUM3MjA0OTQ2ZjEwOWEwODVkMjA3ZTBiMWUxMDgzZmVlYjo3MGYyNTU0Yjg5ZjlmY2ZlMTU3MDllMGMyMDAxZWFmYQ==',
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

function rand12($length = 1, $special = null)
{
    $characters = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    if (!empty($special)) {
        $randomString .= $special;
    }

    return $randomString;
}

function randNumber($length = 1, $special = null)
{
    $characters = '1234567890123456789012345678901234567890123456789012345678901234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    if (!empty($special)) {
        $randomString .= $special;
    }

    return $randomString;
}

function genDate($change, $full = true)
{

    $date = date('Y-m-d H:i:s', strtotime($change, strtotime(date('Y-m-d H:i:s'))));
    $datestr = strtotime($date);
    if (!$full) {
        $date = date('Y-m-d', $datestr);
    }

    return $date;
}

function dateLoop($from, $to)
{
    $begin = new DateTime($from);
    $end = new DateTime($to);

    $arrDate = [];
    for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
        $arrDate[] = $i->format("Y-m-d");
    }

    return $arrDate;
}

function telebotapi($text, $chat_id, $url = "https://api.telegram.org/bot5642245195:AAFLlbTTw63MnlpmNMi65Dl1KsTNiUqtlMw/sendMessage")
{
    $arr = [
        "text" => $text,
        "chat_id" => $chat_id
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/json'));
    return $result = curl_exec($ch);
}

function timeAgo($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'ปี',
        'm' => 'เดือน',
        'w' => 'สัปดาห์',
        'd' => 'วัน',
        'h' => 'ชั่วโมง',
        'i' => 'นาที',
        's' => 'วินาที',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ที่แล้ว' : 'ล่าสุด';
}

function randomString($length = 6)
{
    $str = "";
    $characters = array_merge(range('a', 'z'));
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}

function randomStringTH($length = 6)
{
    $str = "";
    $characters = ['ก', 'ข', 'ฃ', 'ค', 'ฅ', 'ฆ', 'ง', 'จ', 'ฉ', 'ช', 'ซ', 'ฌ', 'ญ', 'ฎ', 'ฏ', 'ฐ', 'ฑ', 'ฒ', 'ณ', 'ด', 'ต', 'ถ', 'ท', 'ธ', 'น', 'บ', 'ป', 'ผ', 'ฝ', 'พ', 'ฟ', 'ภ', 'ม', 'ย', 'ร', 'ฤ', 'ล', 'ฦ', 'ว', 'ศ', 'ษ', 'ส', 'ห', 'ฬ', 'อ', 'ฮ'];
    $max = count($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $max);
        $str .= $characters[$rand];
    }
    return $str;
}
