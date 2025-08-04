<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Modules\Core\Entities\Menus;
use Modules\Core\Http\Controllers\SlugController;
use Modules\product\Entities\ProductCategories;
class AdminMenuController extends Controller
{
    
    public function getMenu($location=1)
    {
        $menus = Menus::with('slug')->where('location',$location)->where('status', 1)->defaultOrder()->get()->toTree();
        $data = [];
        if ($menus) {
            function mapping_menu($menus,$data){
                foreach ($menus as $k => $menu) {
                    $data[$k]['id'] = $menu->id;
                    if (empty(App::currentLocale()) || App::currentLocale() == 'th') {
                        $data[$k]['name'] = $menu->name_th;
                    } else {
                        $data[$k]['name'] = $menu->name_en;
                    }

                    switch($menu->link_type){
                        case 1 :
                            // link to slug
                            $data[$k]['url']= '';
                            if (!empty($menu->slug)) {
                                $data[$k]['url'] = URL::to($menu->slug->slug);
                            }
                        break;
                        case 2 :
                            // link to url
                            $data[$k]['url'] = $query->url;
                        break;
                        case 3 :
                            // link to category
                            switch($menu->category_link){
                                case "product":
                                    $product_category =  ProductCategories::with('slug')->get()->toTree();
                                    $data[$k]['children'] = [];
                                    $data[$k]['children'] = mapping_menu($product_category,$data[$k]['children']);
                                break;
                            }  
                        break;
                        default:
                            $data[$k]['url']= '';
                            if (!empty($menu->slug)) {
                                $data[$k]['url'] = URL::to($menu->slug->slug);
                            }
                        break ;
                    }
                  
                    if(!empty($menu->children->toArray())){
                        $data[$k]['children'] = [];
                        $data[$k]['children'] = mapping_menu($menu->children,$data[$k]['children']);
                    }
                   
                }

                return $data ; 
            }
            $data = mapping_menu($menus,$data);
           
        }
        return $data;
    }
}
