<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // auth()->user()->can(name bất kỳ)
        DB::listen(function ($query) {
            Log::info('SQL Query: '.$query->sql);
            Log::info('Bindings: '.json_encode($query->bindings));
            Log::info('Time: '.$query->time);
        });
    }
}
