<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\NavbarController;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {


        // Global rate limiter with a custom response
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->response(function (Request $request, array $headers) {
                return response(
                    'AKALIN TECH has restricted access. 🤨 🤫🫣🤗😶😏🥴😵🤯🤠🥸🫤 Custom response...😉😉', 
                    429, 
                    $headers
                );
            });
        });


        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar','tr','es','ka','en'
                // ,'bs','ca','ckb','cs','da','de','el','fa','fi','fr','he','hi','hr','hu','hy','id','it','ja','km','ko','ku','lt','lv','mn','ms','my','nl','no','np','np','pl','pt_BR','pt_PT','ro','ru','sk','sl','sq','sv','sw','th','tr','uk','uz','vi','zh_CN','zh_TW',
            ]); // also accepts a closure
        });

        // Navbar verilerini tüm view'lere gönder
        View::composer('layouts.navbar', function ($view) {
            $navbarController = new NavbarController();
            $categories = $navbarController->getNavbarData();
            $view->with('categories', $categories);
        });
        
    }
}
