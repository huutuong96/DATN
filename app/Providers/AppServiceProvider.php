<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\ConfigModel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        // // auth()->user()->can(name bất kỳ)
        // DB::listen(function ($query) {
        //     Log::info('SQL Query: '.$query->sql);
        //     Log::info('Bindings: '.json_encode($query->bindings));
        //     Log::info('Time: '.$query->time);
        // });
        // $config = ConfigModel::where('is_active', 1)->first();
        // // dd($config);
        // // $config = (new configController)->
        // View::share('config', $config);

        View::composer('*', function ($view) {
            $config = ConfigModel::where('is_active', 1)->first();
            $view->with('config', $config);
        });
    }
}
