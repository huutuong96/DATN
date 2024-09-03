<?php

namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\message_detail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function saveTest(){
        $messages = Message::all()->groupBy('user_id');
        foreach ($messages as $key => $message) {
            $message[0]["messageDetail"] = message_detail::where('mes_id', $message[0]->id)->orderBy("created_at", "desc")->get();
            Cache::put("user:".$message[0]->user_id, $message, 2);
        }
        $messages = Message::all()->groupBy('shop_id');
        foreach ($messages as $key => $message) {
            $message[0]["messageDetail"] = message_detail::where('mes_id', $message[0]->id)->orderBy("created_at", "desc")->get();
            Cache::put("shop:".$message[0]->shop_id, $message, 2);
        }
        return "tạo dữ liệu thành công";
    }

    public function index()
    {
        $messages = Message::all();
        
        // dd($messages);
        if($messages->isEmpty()){
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
    public function index_message_detail($id) 
    {
        $message = message_detail::where('mes_id', $id)->orderBy("created_at", "desc")->get();
        if($message->isEmpty()){
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

            if(!$message){
                $dataInsert = [
                    "status"=> 1,
                    'created_at'=> now(),
                    'user_id'=> $user_id,
                    'shop_id' => $request->shop_id,
                ];
                $message = Message::create($dataInsert);
            }

            $data = [
                "mes_id"=> $message->id,
                'content'=> $request->content,
                'status'=> 1,
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
    public function showByStore($shop_id)
    {   
        $user = JWTAuth::parseToken()->authenticate();
        dd(Cache::get("shop:$shop_id"));
        $value = Cache::remember("shop:$shop_id", 10, function () {
            $messages = Message::where('shop_id', $shop_id)->get();
            if($messages->isEmpty()){
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
    public function showByUser($user_id)
    {
        $value = Cache::remember("user:$user_id", 10, function () {
            $messages = Message::where('user_id', $user_id)->get();
            if($messages->isEmpty()){
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
