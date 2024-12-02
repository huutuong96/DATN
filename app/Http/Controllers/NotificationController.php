<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailEvent;
use App\Jobs\SendNotiEvent;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Notification_to_shop;
use App\Models\Notification_to_mainModel;
use App\Models\UsersModel;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->user()->id;
        $limit = $request->limit ?? 10;
        $notifications = Notification::where('user_id', $userId)->pluck('id_notification');
        $notificationToMain = Notification_to_mainModel::whereIn('id', $notifications)->paginate($limit);
        return response()->json($notificationToMain);
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

    public function send_mail_event(Request $request)
    {
        $users = UsersModel::where('status', 2)->select('email')->get();
        $today = date('d-m');
        $eventTitle = null;
        $events = [
            '01-01' => 'Chúc mừng năm mới! Năm ' . date('Y') . ' VNShop xin gửi tặng bạn Voucher nhân dịp năm mới.',
            '03-02' => 'Kỷ niệm ngày thành lập Đảng Cộng sản Việt Nam! VNShop xin gửi tặng bạn Voucher.',
            '14-02' => 'Chúc mừng ngày Valentine! VNShop xin gửi tặng bạn Voucher nhân dịp lễ tình nhân.',
            '08-03' => 'Chúc mừng ngày Quốc tế Phụ nữ 8-3! VNShop xin gửi tặng bạn Voucher đặc biệt.',
            '30-04' => 'Chào mừng ngày Giải phóng miền Nam 30-4! VNShop xin gửi tặng bạn Voucher.',
            '01-05' => 'Chào mừng ngày Quốc tế Lao động 1-5! VNShop xin gửi tặng bạn Voucher.',
            '01-06' => 'Chúc mừng ngày Quốc tế Thiếu nhi 1-6! VNShop xin gửi tặng bạn Voucher.',
            '28-06' => 'Chúc mừng ngày Gia đình Việt Nam 28-6! VNShop xin gửi tặng bạn Voucher.',
            '02-09' => 'Chúc mừng ngày Quốc khánh Việt Nam 2-9! VNShop xin gửi tặng bạn Voucher.',
            '20-10' => 'Chúc mừng ngày Phụ nữ Việt Nam 20-10! VNShop xin gửi tặng bạn Voucher đặc biệt.',
            '20-11' => 'Chúc mừng ngày Nhà giáo Việt Nam 20-11! VNShop xin gửi tặng bạn Voucher tri ân thầy cô.',
            '24-12' => 'Chúc mừng Giáng sinh 24-12! VNShop xin gửi tặng bạn Voucher.',
            '31-12' => 'Chào đón đêm giao thừa 31-12! VNShop xin gửi tặng bạn Voucher chào năm mới.',
        ];
        if (array_key_exists($today, $events)) {
            $eventTitle = $events[$today];
        }

        SendMailEvent::dispatch($users);
        // SendNotiEvent::dispatch($users);
        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mail Event thành công'
        ], 200);
    }

}
