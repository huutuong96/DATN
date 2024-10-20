<?php

namespace App\Http\Controllers;
use App\Models\Notification_to_shop;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
class Notification_to_shopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notification = Notification_to_shop::all();
        if($notification->isEmpty()){
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
        $image = $request->file('image');
        if ($image) {
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        }
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $uploadedImage['secure_url'] ?? null,
            'status' => $request->status,
            'shop_id' => $request->shop_id,
            'created_at' => now(),
            'create_by'=>auth()->user()->id,
            // 'update_by',
        ];

        try {
            $notification = Notification_to_shop::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "tạo thông báo thành công",
                    'data' => $notification,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "tạo thông báo không thành công",
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
        $notification = Notification_to_shop::find($id);

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
        $image = $request->file('image');
        if ($image) {
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        }
        // Tìm banner theo ID
        $notification = Notification_to_shop::find($id);
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
            'title' => $request->title ?? $notification->title,
            'description' => $request->description ?? $notification->description,
            'image' => $uploadedImage['secure_url'] ?? $notification->URL,
            'status' => $request->status ?? $notification->status,
            'shop_id' => $request->shop_id ?? $notification->shop_id,
            'update_at' => now(), // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];


        try {
            // Cập nhật bản ghi
            $notification->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "cập nhật thông báo thành công",
                    'data' => $notification,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "cập nhật thông báo không thành công",
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
            $notification = Notification_to_shop::find($id);

            if (!$notification) {
                return response()->json([
                    'status' => false,
                    'message' => 'thông báo không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $notification->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa thông báo thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa thông báo không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
