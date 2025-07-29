<?php
 
namespace App\Providers;
 
use App\View\Composers\AppSettingsComposer;
use App\View\Composers\FormatNumberComposer;
use App\View\Composers\FormatDateComposer;
use App\View\Composers\PermissionsListComposer;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
 
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }
 
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Using class based composers...
        // Facades\View::composer('layouts.footer', AppSettingsComposer::class);
        // Facades\View::composer('layouts.head', AppSettingsComposer::class);
        // Facades\View::composer('layouts.app', AppSettingsComposer::class);
        // Facades\View::composer('layouts.guest', AppSettingsComposer::class);
        Facades\View::composer('*', AppSettingsComposer::class);
        Facades\View::composer('*', FormatNumberComposer::class);
        Facades\View::composer('*', FormatDateComposer::class);
      
    }
}