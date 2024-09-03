<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use App\Models\Notification_to_shop;
use Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    private function updateCache($key, $data)
    {
        Cache::put($key, $data, 60 * 60 * 24); // Cache for 24 hours
    }

    public function index()
    {
        $userId = auth()->user()->id;
        $cacheKey = 'notifications_' . $userId;

        $notifications = Cache::remember($cacheKey, 60 * 60, function () use ($userId) {
            return Notification::where('user_id', $userId)->get();
        });

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $notification = new Notification();
        $notification->type = $request->type;
        $notification->user_id = $request->user_id;

        if($request->image){
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $image = $uploadedImage['secure_url'];
        }

        if ($request->type === 'main') {
            $notificationToMain = new Notification_to_mainModel();
            $notificationToMain->title = $request->title;
            $notificationToMain->description = $request->description;
            $notificationToMain->image = $image ?? null;
            $notificationToMain->save();

            $notification->id_notification = $notificationToMain->id;
        } elseif ($request->type === 'shop') {
            $notificationToShops = new Notification_to_shop();
            $notificationToShops->title = $request->title;
            $notificationToShops->description = $request->description;
            $notificationToShops->image = $image ?? null;
            $notificationToShops->shop_id = $request->shop_id;
            $notificationToShops->save();

            $notification->id_notification = $notificationToShops->id;
        }

        $notification->save();

        // Update cache
        $this->updateCache('notifications_' . $request->user_id, Notification::where('user_id', $request->user_id)->get());

        // return response()->json($notification, 201);
    }

    public function show($id)
    {
        $userId = auth()->user()->id;
        $cacheKey = 'notification_' . $userId . '_' . $id;

        $notification = Cache::remember($cacheKey, 60 * 60, function () use ($userId, $id) {
            return Notification::where('user_id', $userId)->findOrFail($id);
        });

        if($notification->type === 'main'){
            $notificationToMain = Cache::remember('notification_main_' . $notification->id_notification, 60 * 60, function () use ($notification) {
                return Notification_to_mainModel::findOrFail($notification->id_notification);
            });
            return response()->json($notificationToMain);
        }elseif($notification->type === 'shop'){
            $notificationToShops = Cache::remember('notification_shop_' . $notification->id_notification, 60 * 60, function () use ($notification) {
                return Notification_to_shop::findOrFail($notification->id_notification);
            });
            return response()->json($notificationToShops);
        }
    }

    public function update(Request $request, $id)
    {
        dd("Thường là không cần update");
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification->type === 'main') {
            NotificationToMain::destroy($notification->id_notification);
            Cache::forget('notification_main_' . $notification->id_notification);
        } elseif ($notification->type === 'shop') {
            NotificationToShops::destroy($notification->id_notification);
            Cache::forget('notification_shop_' . $notification->id_notification);
        }

        $notification->delete();

        // Update cache
        $this->updateCache('notifications_' . $notification->user_id, Notification::where('user_id', $notification->user_id)->get());
        Cache::forget('notification_' . $notification->user_id . '_' . $id);

        return response()->json(null, 204);
    }
}
