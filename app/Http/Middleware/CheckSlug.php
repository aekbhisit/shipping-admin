<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Modules\Mwz\Entities\Slugs;

class CheckSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {   
        $default_lang = 'th';
        $segments = $request->segments();
        
        if(app()->getLocale() == $default_lang){
            $slug_name = isset($segments[1])?$segments[1]:'';
        }else{
            $slug_name = isset($segments[2])?$segments[2]:'';
        }
        
        if(!empty($slug_name)){
            $slug = Slugs::where('lang',app()->getLocale())->where('slug',$slug_name)->first();
            if(!empty($slug->id)){
                $slug_route_name =  $slug->route;
                $slug_route_param =  json_decode($slug->param);
                return redirect()->to(mwz_route($real_route_name,$slug_route_param));
            }else{
                return $next($request); 
            }   
        }else{
            return $next($request);
        }
    }
}
