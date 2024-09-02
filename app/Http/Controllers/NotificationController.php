<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Notification_to_shop;
use App\Models\Notification_to_mainModel;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->user()->id)->get();
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'type' => 'required|string',
        //     'user_id' => 'required|exists:users,id',
        //     'title' => 'required|string',
        //     'description' => 'nullable|string',
        //     'image' => 'nullable|string',
        //     'shop_id' => 'nullable|exists:shops,id',
        // ]);

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
        // return response()->json($notification, 201);

    }

    public function show($id)
    {
        $notification = Notification::where('user_id', auth()->user()->id)->findOrFail($id);
        if($notification->type === 'main'){
            $notificationToMain = Notification_to_mainModel::findOrFail($notification->id_notification);
            return response()->json($notificationToMain);
        }elseif($notification->type === 'shop'){
            $notificationToShops = Notification_to_shop::findOrFail($notification->id_notification);
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
            Notification_to_mainModel::destroy($notification->id_notification);
        } elseif ($notification->type === 'shop') {
            Notification_to_shop::destroy($notification->id_notification);
        }

        $notification->delete();

        return response()->json(null, 204);
    }
}
