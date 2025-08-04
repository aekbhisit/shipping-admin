<?php

namespace Modules\Core\Http\Controllers;

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

use Modules\Core\Entities\AdminMenus;

class AdminMenusAdminController extends Controller
{
    public function IconsClassList()
    {
        $icons = ['transfer','money','adjust', 'album', 'align-left', 'align-middle', 'align-right', 'anchor', 'aperture', 'archive', 'archive-in', 'archive-out', 'arrow-back', 'at', 'award', 'bar-chart', 'bar-chart-alt', 'bar-chart-square', 'barcode', 'basketball', 'battery', 'battery-charging', 'battery-full', 'battery-low', 'behance', 'bell', 'bell-minus', 'bell-off', 'bell-plus', 'block', 'bluetooth', 'body', 'bold', 'bolt', 'book', 'book-bookmark', 'bookmark', 'bookmark-minus', 'bookmark-plus', 'bookmarks', 'briefcase', 'broadcast', 'bug', 'building', 'bulb', 'bullseye', 'buoy', 'calendar', 'calendar-add', 'calendar-alt', 'calendar-check', 'calendar-minus', 'calendar-remove', 'camera', 'camera-off', 'captions', 'cart', 'cast', 'chart', 'check', 'checkbox', 'checkbox-checked', 'checkbox-square', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'chevrons-down', 'chevrons-left', 'chevrons-right', 'chevrons-up', 'chip', 'chrome', 'clipboard', 'cloud', 'cloud-download', 'cloud-drizzle', 'cloud-light-rain', 'cloud-lightning', 'cloud-rain', 'cloud-snow', 'cloud-upload', 'code', 'code-curly', 'codepen', 'coffee', 'cog', 'collapse', 'columns', 'compass', 'contact', 'copy', 'copyright', 'credit-card', 'crop', 'crosshair', 'crown', 'cube', 'cut', 'data', 'desktop', 'detail', 'diamond', 'directions', 'dislike', 'dollar', 'dollar-circle', 'dots-horizontal', 'dots-horizontal-rounded', 'dots-vertical', 'dots-vertical-rounded', 'down-arrow-circle', 'down-arrow-outline', 'download', 'download-alt', 'downvote', 'dribbble', 'drink', 'droplet', 'duplicate', 'edit', 'envelope', 'eraser', 'error', 'error-circle', 'exclamation', 'exit-fullscreen', 'expand', 'export', 'eyedropper', 'facebook', 'fast-forward', 'fast-forward-circle', 'female', 'file', 'files', 'film', 'filter', 'first-aid', 'first-page', 'flag', 'flask', 'folder', 'folder-minus', 'folder-open', 'folder-plus', 'font', 'fullscreen', 'gift', 'git-branch', 'git-commit', 'git-compare', 'git-merge', 'git-pull-request', 'git-repo-forked', 'github', 'globe', 'globe-alt', 'google', 'grid', 'group', 'hash', 'hdd', 'heading', 'headphone', 'heart', 'hexagon', 'hide', 'history', 'home', 'home-alt', 'horizontal-center', 'hot', 'idea', 'image', 'image-alt', 'images', 'import', 'inbox', 'info', 'info-circle', 'instagram', 'italic', 'joystick', 'joystick-alt', 'justify', 'key', 'laptop', 'last-page', 'layer', 'left-arrow-circle', 'left-arrow-outline', 'left-bottom-arrow-circle', 'left-indent', 'left-top-arrow-circle', 'like', 'link', 'link-alt', 'link-external', 'list', 'list-add', 'list-bullet', 'list-check', 'list-remove', 'lock', 'lock-open', 'log-in', 'log-out', 'male', 'map', 'map-alt', 'menu', 'message', 'message-detail', 'message-rounded', 'message-rounded-alt', 'microphone', 'microphone-off', 'minus', 'minus-circle', 'mobile', 'mobile-alt', 'moon', 'mouse', 'move', 'music', 'navigation', 'news', 'newsletter', 'notification', 'octagon', 'package', 'paperclip', 'paragraph', 'paste', 'pause', 'pause-circle', 'pen', 'pencil', 'phone', 'phone-call', 'phone-incoming', 'phone-outgoing', 'pie-chart', 'pie-chart-alt', 'pin', 'play', 'play-circle', 'plus', 'plus-circle', 'poll', 'popular', 'power-off', 'printer', 'pulse', 'purchase-tag', 'question-mark', 'quote-left', 'quote-right', 'radar', 'radio', 'radio-circle', 'radio-circle-marked', 'rectangle', 'redo', 'rename', 'reply', 'reply-all', 'repost', 'reset', 'revision', 'rewind', 'ribbon', 'right-arrow-circle', 'right-arrow-outline', 'right-down-arrow-circle', 'right-indent', 'right-top-arrow-circle', 'rotate', 'rss', 'ruler', 'save', 'screenshot', 'search', 'select-multiple', 'send', 'server', 'share', 'share-alt', 'shield', 'shield-alt', 'shopping-bag', 'shopping-bag-alt', 'show', 'shuffle', 'shuffle-alt', 'sidebar', 'sitemap', 'skip-next', 'skip-next-circle', 'skip-previous', 'skip-previous-circle', 'smiley-happy', 'smiley-sad', 'snowflake', 'sort', 'spreadsheet', 'star', 'stop', 'stop-circle', 'stopwatch', 'store', 'stumble-upon', 'subdirectory-left', 'subdirectory-right', 'sun', 'support', 'sync', 'tab', 'table', 'tag', 'tag-remove', 'target-lock', 'task', 'tennis-ball', 'terminal', 'text', 'time', 'timer', 'timer-alt', 'to-top', 'toggle', 'toggle-left', 'toggle-right', 'trash', 'trending-down', 'trending-up', 'triangle', 'trophy', 'truck', 'tumblr', 'twitter', 'underline', 'undo', 'up-arrow-circle', 'up-arrow-outline', 'upload', 'upvote', 'usb', 'user', 'user-check', 'user-circle', 'user-detail', 'user-minus', 'user-plus', 'user-remove', 'vertical-center', 'video', 'video-off', 'videos', 'vimeo', 'voicemail', 'volume', 'volume-full', 'volume-low', 'volume-mute', 'watch', 'whatsapp', 'widget', 'wifi', 'window', 'window-open', 'windows', 'x', 'x-circle', 'youtube', 'zap', 'zoom-in', 'zoom-out'] ;
        return $icons;
    }

   
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
   
    public function index(Request $request)
    {
        // $side_admin_menus = get_side_admin_menu();
        // // pre($side_admin_menus); exit;
        return view('core::menu.index');
    }


    public function datatable_ajax(Request $request)
    {
        if ($request->ajax()) {

            //init datatable
            $dt_name_column = array('sequence', 'icon', 'name', 'link', 'updated_at', 'action');

            // Check if order parameters exist before accessing them
            $order = $request->get('order');
            $dt_order_column = 0; // Default to first column
            $dt_order_dir = 'asc'; // Default direction
            
            if (!empty($order) && isset($order[0]['column'])) {
                $dt_order_column = $order[0]['column'];
            }
            if (!empty($order) && isset($order[0]['dir'])) {
                $dt_order_dir = $order[0]['dir'];
            }
            
            $dt_start = $request->get('start');
            $dt_length = $request->get('length');
            $dt_search = $request->get('search')['value'];

            // Get all menus in tree order first
            $all_menus = AdminMenus::withDepth()->defaultOrder()->get();

            // Apply search filter if provided
            if (!empty($dt_search)) {
                $all_menus = $all_menus->filter(function ($menu) use ($dt_search) {
                    return stripos($menu->name, $dt_search) !== false;
                });
            }

            // Get total count for pagination
            $dt_total = $all_menus->count();

            // Apply pagination
            $menus = $all_menus->slice($dt_start, $dt_length);

            // prepare datatable for response
            $tables = DataTables::of($menus)
                ->addIndexColumn()
                ->setRowId('id')
                ->setRowClass('menu_row')
                ->setTotalRecords($dt_total)
                ->setFilteredRecords($dt_total)
                //->setOffset($dt_start)
                ->editColumn('sequence', function ($record) {
                    return $record->sequence ?? $record->_lft;
                })
                ->editColumn('updated_at', function ($record) {
                    return $record->updated_at->format('d/m/Y H:i:s');
                })
                ->editColumn('name', function ($record) {
                    $result = array();

                    $result = str_repeat(' - ', $record->depth) . $record->name;

                    return $result;
                })
                ->addColumn('link', function ($record) {
                    if($record->link_type==1){
                        return $record->route_name ;
                    }else{
                        return $record->url ;
                    }
                })
                ->editColumn('icon', function ($record) {
                    if (!empty($record->icon)) {
                        $icon = '<i class="' . $record->icon . '" ></i>';
                    } else {
                        $icon = 'no icon';
                    }
                    return $icon;
                })
                ->addColumn('sort', function ($record) {
                    return sort_button('admin.admin_menu.admin_menu.sort', $record->id, 'setUpdateSort');
                })
                ->addColumn('action', function ($record) {
                    $action_btn = '<div class="square-buttons d-flex flex-wrap gap-1">';
                   
                        if ($record->status == 1) {
                            $action_btn .= '<a onclick="setStatus(' . $record->id . ',0)" href="javascript:void(0);" class="btn btn-outline-success"><i class="lni lni-checkmark"></i></a></a>';
                        } else {
                            $action_btn .=  '<a onclick="setStatus(' . $record->id . ',1)" href="javascript:void(0);"  class="btn btn-outline-warning"><i class="lni lni-close"></i></a></a>';
                        }

                        $action_btn .= '<a href="' . route('admin.admin_menu.admin_menu.edit', $record->id) . '" class="btn btn-outline-primary"><i class="lni lni-pencil"></i></a></a>';
                    
                        
                        $action_btn .= '<a onclick="setDelete(' . $record->id . ')" href="javascript:void(0);" class="btn btn-outline-danger"><i class="lni lni-trash"></i></a></a>';

                       
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

            $menu = AdminMenus::find($id);
            $menu->status = $status;

            if ($menu->save()) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => __('core::menu.menu_admin_admin.save_success')];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => __('core::menu.menu_admin_admin.error_try_again')];
            }

            return response()->json($resp);
        }
    }

    public function set_delete(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $menu_cat = AdminMenus::find($id);
            if ($menu_cat->delete()) {
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => __('core::menu.menu_admin_admin.save_success')];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => __('core::menu.menu_admin_admin.error_try_again')];
            }

            return response()->json($resp);
        }
    }

    public function form(Request $request, $id = 0)
    {
        $mode = 'add';
       
        $menu = [];

        if (!empty($id)) {
            $menu = AdminMenus::find($id);
        }

        $parents = AdminMenus::all()->totree();
        $parents = setFlatCategory($parents);
        // print_r($menu);
        $lang = Lang::get('menu::module');
        $icons = $this->IconsClassList();
        return view('core::menu.form', ['data' => $menu, 'parents' => $parents, 'icons' => $icons]);
    }

    /**
     * Function : menu save
     * Dev : tong
     * Update Date : 20 Sep 2022
     * @param POST
     * @return json response status
     */
    public function save(Request $request)
    {
      
        //validate post data
        $validator = Validator::make($request->all(), [
            'id' => 'integer',
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $resp = ['success' => 0, 'code' => 301, 'msg' => $errors->first(), 'error' => $errors];
            return response()->json($resp);
        }

        $attributes = [
            "icon" => $request->get('icon'),
            "name" => $request->get('name'),
            "link_type" => $request->get('link_type'),
            "route_name" => $request->get('route_name'),
            "url" => $request->get('url'),
            'target' => $request->get('target'),
            "status" => (!empty($request->get('status')))?1:0,
            "parent_id" => $request->get('parent_id')
        ];

        

        if (!empty($request->get('id'))) {
            $data_id = $request->get('id');
            $menu = AdminMenus::find($data_id);
            $old_parent =  $menu->parent_id;
            $node = AdminMenus::where('id',  $data_id)->update($attributes);
            if ($old_parent != $attributes['parent_id']) {
                AdminMenus::fixTree();
            }

            $resp = ['success' => 1, 'code' => 201, 'msg' => 'บันทึกการเปลี่ยนแปลงสำเร็จ'];
        } else {

            $sequence = AdminMenus::max('sequence');
            (int)$sequence += 1;
            $attributes["sequence"] = $sequence;

            $node = AdminMenus::create($attributes);
            $data_id = $node->id;

            $this->re_order();
            $resp = ['success' => 1, 'code' => 200, 'msg' => __('core::menu.menu_admin_admin.save_success')];
        }

        return response()->json($resp);
    }

    public function re_order()
    {
        $all_cat = AdminMenus::orderBy('_lft', 'asc')->get();
        $cnt = 0;
        foreach ($all_cat as $cat) {
            $cnt++;
            $cat->sequence = $cnt;
            $cat->save();
        }
    }

    public function sort(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->get('id');
            $move = $request->get('move');
            $node = AdminMenus::find($id);
            $is_move = false;
            if ($move == 'up') {
                $is_move = $node->up();
            }

            if ($move == 'down') {
                $is_move = $node->down();
            }

            $this->re_order();

            if ($is_move) {
                $resp = ['success' => 1, 'code' => 200, 'msg' => __('core::menu.menu_admin_admin.save_success')];
            } else {
                $resp = ['success' => 0, 'code' => 500, 'msg' => __('core::menu.menu_admin_admin.error_try_again')];
            }


            return response()->json($resp);
        }
    }

    public function set_move_node(Request $request)
    {
        if ($request->ajax()) {

            if (!empty($request->get('node_id')) && !empty($request->get('next_by'))) {
                $node = AdminMenus::find($request->get('node_id'));
                $neighbor = AdminMenus::find($request->get('next_by'));
                // $move_status = $node->prependToNode($parent)->save(); 
                $move_status = $node->afterNode($neighbor)->save();
                $this->re_order();
                $resp = ['success' => 1, 'code' => 200, 'msg' => __('core::menu.menu_admin_admin.order_success'), 'move_status' => $move_status];
            } else {
                $resp = ['success' => 0, 'code' => 300, 'msg' => __('core::menu.menu_admin_admin.no_need_order')];
            }

            return response()->json($resp);
        }
    }


    public function get_menu(Request $request)
    {

        $menus = AdminMenus::withDepth()->defaultOrder()->get();

        $result = [];
        foreach ($menus as $menu) {
            $showname = str_repeat(' - ', $menu->depth) . $menu->name;
            $result[] = [
                'id' => $menu->id,
                'text' => $showname,
                'image' => $menu->icon
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

}
