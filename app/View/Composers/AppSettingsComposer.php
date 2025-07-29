<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use App\Services\CacheService;

class AppSettingsComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct(

    ) {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if(env('INSTALLATION_STATUS')){
            $appSetting = CacheService::get('appSetting');

            /**
             * appSetting show footer text else null
            */
            $view->with('footerText', $appSetting?->footer_text ?? null);
            $view->with('fevicon', $appSetting?->fevicon ?? null);
            $view->with('colored_logo', $appSetting?->colored_logo ?? null);

            /**
             * Cookie Setting
             * */
            $cookie = Cookie::get('language_data'); // Get json data
            $cookieArrayData = json_decode($cookie, true);
            $view->with('appDirection', isset($cookieArrayData['direction'])? $cookieArrayData['direction'] : 'ltr');

            /**
             * Theme Mode Settings
             * */
            $themeModeCookie = Cookie::get('theme_mode');
            $view->with('themeMode', $themeModeCookie??'light-mode');

            /**
             * Theme Manuall settings for some pages
             * Like POS, Login, Register Pages
             * */
             $view->with('themeBgColor', ($themeModeCookie=='dark-theme')? 'bg-dark' : 'bg-white');
         }
    }
}
