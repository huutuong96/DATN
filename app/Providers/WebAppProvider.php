<?php

namespace App\Providers;

use App\Models\UsersModel;
use App\Models\web_app;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class WebAppProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $apps = web_app::all(); 
            // $useronline = UsersModel::where('is_login', 1)->count();
            // $view->with('apps', $apps)->with('useronline', $useronline);
            $view->with('apps', $apps);

        });
    }
}
