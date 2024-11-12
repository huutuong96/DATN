<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Notification_to_shop;
use App\Models\Notification_to_mainModel;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        $notifications = Notification::where('user_id', $userId)->paginate(10);
        return response()->json($notifications);
    }
    public function get_noti_admin (Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $notifications = Notification::where('type', 'main')->paginate(10);
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notification = new Notification();
        $notification->type = $request->type;
        $notification->user_id = $user->id;

        if ($request->image) {
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
            $notificationToMain->shop_id = $request->shop_id;
            $notificationToMain->save();

            $notification->id_notification = $notificationToMain->id;
        } elseif ($request->type === 'shop') {
            $notificationToShops = new Notification_to_shop();
            $notificationToShops->title = $request->title;
            $notificationToShops->description = $request->description;
            $notificationToShops->image = $image ?? null;
            $notificationToShops->shop_id = $request->shop_id;
            $notificationToShops->create_by = $user->id;
            $notificationToShops->save();

            $notification->id_notification = $notificationToShops->id;
        }
        $notification->save();

        return response()->json($notification, 201);
    }

    public function show($id)
    {
        $userId = auth()->user()->id;
        $notification = Notification::where('user_id', $userId)->findOrFail($id);

        if ($notification->type === 'main') {
            $notificationToMain = Notification_to_mainModel::findOrFail($notification->id_notification);
            return response()->json($notificationToMain);
        } elseif ($notification->type === 'shop') {
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
        Notification::destroy($id);
        if ($notification->type === 'main') {
            Notification_to_mainModel::destroy($notification->id_notification);
        } elseif ($notification->type === 'shop') {
            Notification_to_shop::destroy($notification->id_notification);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'xóa thành công'
        ], 200);
    }

    public function delete_notify(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $notificationIds = explode(',', $request->ids);
    
        foreach ($notificationIds as $id) {
            Notification::where('id_notification', $id)->delete();
            Notification_to_mainModel::where('id', $id)->delete();
        }
    
        if ($request->token) {
            return redirect()->back()->with('success', 'Xóa thông báo thành công');
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'xóa thành công'
        ], 200);
    }
}
