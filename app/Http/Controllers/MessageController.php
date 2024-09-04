<?php

namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\message_detail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Shop;
use App\Models\Shop_manager;

class MessageController extends Controller
{


    public function user_send(Request $request, $shop_id){
        $message = Message::where('user_id', auth()->user()->id)->where('shop_id', $shop_id)->first();
        // Nếu chưa nhắn tin với shop lần nào thì tạo cuộc trò chuyện ở đây là bảng message
        if (!$message) {
            $message = Message::create([
                'user_id' => auth()->user()->id, // User id ở đây là khách hàng gửi cho shop
                'shop_id' => $shop_id, // Shop id ở đây là shop nhận được tin nhắn có thể truyền theo url
                'status' => 1,
            ]);
        }
        $message_detail = message_detail::create([
            'mes_id' => $message->id,
            'content' => $request->content,
            'send_by' => auth()->user()->id, // Đây là id của khách hàng gửi tin nhắn
        ]);
      
        return $this->successResponse("Gửi tin nhắn thành công", $message);
    }

    public function shop_get_message(Request $request, $shop_id){
        $user = JWTAuth::parseToken()->authenticate();
        $shop_manager = Shop_manager::where('shop_id', $shop_id)->where('user_id', $user->id)->first();
        if($shop_manager){
            $message = Message::where('shop_id', $shop_id)->get();
            return $this->successResponse("Lấy tin nhắn thành công", $message);
        }
        return $this->errorResponse("Bạn không có quyền truy cập");
    }

    public function shop_send(Request $request, $mes_id){
        $message = Message::where('id', $mes_id)->first();
        // dd($message);
        $message_detail = message_detail::create([
            'mes_id' => $message->id,
            'content' => $request->content,
            'send_by' => auth()->user()->id, // Đây là admin của shop gửi tin nhắn
        ]);
        return $this->successResponse("Gửi tin nhắn thành công", $message);
    }

    private function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    private function errorResponse($message, $error = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
