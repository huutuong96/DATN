<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;

class Notifications extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = Notification::all();
        if($notifications->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại thông báo nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $notifications,
            ]
        );
    }
    public function store(Request $request)
    {
        $dataInsert = [
            'id_notification' => $request->id_notification,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'type' => $request->type,
        ];
        // dd($dataInsert);
        try {
            $notification = Notification::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm thông báo thành công",
                    'data' => $notification,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm thông báo không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notification = Notification::find($id);

        if(!$notification){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại thông báo nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $notification,
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
        $notification = Notification::find($id);
        // Kiểm tra xem banner có tồn tại không
        if (!$notification) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Banner không tồn tại",
                ],
                404
            );
        }
        // Cập nhật dữ liệu
        $dataUpdate = [
            'id_notification' => $request->id_notification ?? $notification->id_notification,
            'user_id' => $request->user_id ?? $notification->user_id,
            'status' => $request->status ?? $notification->status,
            'type' => $request->type ?? $notification->type,
        ];


        try {
            // Cập nhật bản ghi
            $notification->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật thông báo thành công",
                    'data' => $notification,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật thông báo không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $notification = Notification::find($id);

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'notification không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $notification->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa notification thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa notification không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
