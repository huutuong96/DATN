<?php

namespace App\Http\Controllers;
use App\Models\Message;
use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dataInsert = [
            "status"=> $request->status,
            'created_at'=> now(),
            'user_id'=> $request->user_id,
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

    /**
     * Show the form for editing the specified resource.
     */
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
