<?php

namespace App\Providers;
use App\Models\Notification_to_mainModel;
use App\Models\Notification;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
class Notifications extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
       
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // $user = JWTAuth::parseToken()->authenticate();
        // $notify = Notification::where('user_id', $user->id)->orderby('created_at')->get();
        // $notifyMain = [];
        // foreach ($notify as $noti) {
        //         $notifyMain[] = Notification_to_mainModel::where('id', $noti->id_notification)->orderby('created_at')->get();
        // }
        // view()->share('notifyMain', $notifyMain);
    }
}
