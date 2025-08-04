<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetLocale
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
        $languages = ['th','en'];
        $segments = $request->segments();
        $locale = isset($segments[0])?$segments[0]:'';


        if (in_array($locale, $languages)) {
            unset($segments[0]);
            $lang_switch_url  = [];
            foreach($languages as $l){
                if(config('app.fallback_locale')==$l){
                    $lang_switch_url[$l] = '/'.implode('/', $segments) ;
                }else{
                    $lang_switch_url[$l] = '/'.$l.'/'.implode('/', $segments) ;
                }
            }
            if(empty($request->get('lang_switch'))){
                $request->request->add(['lang_switch' => $lang_switch_url]);
            }
            app()->setLocale($locale);  
        }else{

            $lang_switch_url  = [];
            foreach($languages as $l){
                if(config('app.fallback_locale')==$l){
                    $lang_switch_url[$l] = '/'.implode('/', $segments) ;
                }else{
                    $lang_switch_url[$l] = '/'.$l.'/'.implode('/', $segments) ;
                }
            }
            if(empty($request->get('lang_switch'))){
                $request->request->add(['lang_switch' => $lang_switch_url ]);
            }

            $segments = (!empty($segments))?array_merge([$languages[1]], $segments):[$languages[1]];

            app()->setLocale($languages[0]);  
        }

        $request->route()->forgetParameter('locale');

        // print_r($request->all());
        
        return $next($request);
    }
}
