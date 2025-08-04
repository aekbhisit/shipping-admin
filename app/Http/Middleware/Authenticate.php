<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if($request->is('admin') || $request->is('admin/*')){
                 return route('admin.login');
            }elseif($request->is('api') || $request->is('api/*')){
                return route('api.login');
            }else{
                $languages = ['th', 'en'];
                $segments = $request->segments();
                $locale = isset($segments[0]) ? $segments[0] : '';

                if (in_array($locale, $languages)) {
                    if (config('app.fallback_locale') == $locale) {
                        return route('login');
                    } else {
                        return route('lang.login',[$locale]);
                       
                    }
                }

            }
           
        }
    }
}
