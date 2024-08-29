<?php

namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\message_detail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $message = Message::all();
        if($message->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại message nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $message,
            ]
        );
    }
    public function index_message_detail($id)
    {
        $message = message_detail::where('mes_id', $id)->get();
        if($message->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại message nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $message,
            ]
        );
    }


    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $dataInsert = [
            "status"=> $request->status,
            'created_at'=> now(),
            'user_id'=> $user->id,
            'shop_id' => $request->shop_id,
        ];
        Message::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "message Đã được lưu",
            'messages' => Message::all(),
        ];
        return response()->json($dataDone, 200);
    }


    public function store_message_detail(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // dd($user->id);
        $dataInsert = [
            "mes_id"=> $request->mes_id,
            'content'=> $request->content,
            'status'=> $request->status,
            'send_by'=> $user->id,
            'created_at' => now(),
        ];
        message_detail::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "message Đã được lưu",
            'messages' => message_detail::all(),
        ];
        return response()->json($dataDone, 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $message = Message::where('id', $id)->get();
        if($message->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại message nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $message,
            ]
        );
    }

    public function show_message_detail (Request $request, $id)
    {
        $message = message_detail::where('mes_id', $id)->get();


        if($message->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "chưa có tin nhắn nào trong đoạn hồi thoại này",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $message,
            ]
        );
    }
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
