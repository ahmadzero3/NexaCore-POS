<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\LanguageController;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * Language Settings
        */
        if($cookie = $request->cookie('language_data')) {
          // Get json data
          $data = json_decode($cookie, true);

          App::setLocale($data['language_code']); 
        }else{
            //Load Default
            $language = new LanguageController();
            $language->setDefaultLanguage();
        }

        /**
         * Theme Settings
         * */
        if($cookie = $request->cookie('theme_mode')) {
          // Get json data
          $data = $cookie;
          
        }else{
            //Load Default
            $language = new LanguageController();
            $language->switchTheme();
        }

        return $next($request);
    }
}
