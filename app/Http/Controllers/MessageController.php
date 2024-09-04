<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\message_detail;
use App\Models\Shop;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function saveTest()
    {
        $response = $this->index();
        $content = $response->getContent();
        $messages = json_decode($content, true);

        if ($messages['status'] === true) {
            foreach ($messages['data'] as $key => $message) {
                dd($message);
            }
        }
    }

    public function index()
    {
        $messages = Message::all();

        if ($messages->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại message nào",
                ]
            );
        }

        foreach ($messages as $key => $message) {
            $message["messageDetail"] = $this->index_message_detail($message->id);
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $messages,
            ]
        );
    }
    public function index_message_detail($id) // có dùng nhưng không liên quan đên route
    {
        $message = message_detail::where('mes_id', $id)->orderBy("created_at", "desc")->get();
        if ($message->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
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


    public function store(Request $request, $shop_id, $user_id)
    {
        try {

            $message = Message::where("shop_id", $shop_id)
                ->where("user_id", $user_id)
                ->first();

            if (!$message) {
                $dataInsert = [
                    "status" => 1,
                    'created_at' => now(),
                    'user_id' => $user_id,
                    'shop_id' => $request->shop_id,
                ];
                $message = Message::create($dataInsert);
            }

            $data = [
                "mes_id" => $message->id,
                'content' => $request->content,
                'status' => 1,
                // 'send_by'=> $user_id,
                'created_at' => now(),
            ];
            message_detail::create($data);


            $dataDone = [
                'status' => true,
                'message' => "message Đã được lưu",
                'messages' => $data,
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "thêm message không thành công ",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }
    }


    /**
     * Display the specified resource.
     */
    public function showByStore()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $store = Shop::where("owner_id", $user->id)->first();
        if (!$store) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "bạn chưa có đăng ký cửa hàng",
                ]
            );
        }
        $value = Cache::remember("$user->id-$store->id", 10, function () {
            $messages = Message::where('shop_id', $store_id)->get();
            if ($messages->isEmpty()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Không tồn tại message nào",
                    ]
                );
            }
            foreach ($messages as $key => $message) {
                $message["messageDetail"] = $this->index_message_detail($message->id);
            }
            return response()->json(
                [
                    'status' => true,
                    'message' => "Lấy dữ liệu thành công",
                    'data' => $messages,
                ]
            );
        });
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $value,
            ]
        );
    }
    public function showByUser()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $value = Cache::remember("$user->id-$store->id", 10, function () {
            $messages = Message::where('user_id', $user->id)->get();
            if ($messages->isEmpty()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Không tồn tại message nào",
                    ]
                );
            }
            foreach ($messages as $key => $message) {
                $message["messageDetail"] = $this->index_message_detail($message->id);
            }
            return response()->json(
                [
                    'status' => true,
                    'message' => "Lấy dữ liệu thành công",
                    'data' => $messages,
                ]
            );
        });
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $value,
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
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
