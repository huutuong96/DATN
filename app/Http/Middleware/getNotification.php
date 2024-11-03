<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
class getNotification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $notify = Notification::where('user_id', $user->id)->orderby('created_at')->get();
            // $notifyMain = [];

            foreach ($notify as $noti) {
                $notifyMain = Notification_to_mainModel::where('id', $noti->id_notification)->orderby('created_at', 'desc')->take(5)->get();
            }
            if (empty($notifyMain)) {
                $notifyMain = [];
            }

            // Chia sẻ dữ liệu với tất cả các view
            view()->share('notifyMain', $notifyMain);

        } catch (TokenExpiredException | JWTException $e) {
            return response()->json([
                'status' => 'Token is Invalid'
            ]);
        }

        return $next($request);
    }
}
