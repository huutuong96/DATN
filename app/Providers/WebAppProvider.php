<?php

namespace App\Providers;
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
            $view->with('apps', $apps);
        });
    }
}
