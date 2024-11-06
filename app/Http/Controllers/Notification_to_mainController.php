<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Notification_to_mainModel;
use App\Http\Requests\Notification_to_mainRequest;

class Notification_to_mainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $notification_to_main = Notification_to_mainModel::all();

        if ($notification_to_main->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Notification nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $notification_to_main
        ], 200);
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
    public function store(Notification_to_mainRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $image = $request->file('image');
        $cloudinary = new Cloudinary();

        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());

            $dataInsert = [
                'title' => $request->title,
                'description' => $request->description,
                'image' => $uploadedImage['secure_url'],
                'status' => $request->status,
                'create_by' =>auth()->user()->id,
            ];

            $notification_to_main = Notification_to_mainModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Notification thành công",
                'data' => $notification_to_main,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Notification không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $notification_to_main = Notification_to_mainModel::find($id);

            if (!$notification_to_main) {
                return response()->json([
                    'status' => false,
                    'message' => "Notification không tồn tại",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin Notification thành công",
                'data' => $notification_to_main,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin Notification",
                'error' => $th->getMessage(),
            ], 500);
        }
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
    public function update(Notification_to_mainRequest $request, string $id)
    {
        try {
            $notification_to_main = Notification_to_mainModel::find($id);

            if (!$notification_to_main) {
                return response()->json([
                    'status' => false,
                    'message' => "Notification không tồn tại",
                ], 404);
            }

            $image = $request->file('image');

            // Check xem co anh moi duoc tai len khong
            if ($image) {
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $imageUrl = $uploadedImage['secure_url'];
            } else {
                // Neu khong co anh moi thi giu nguyen URL cua anh hien tai
                $imageUrl = $notification_to_main->image;
            }

            $dataUpdate = [
                'title' => $request->title,
                'description' => $request->description ?? $notification_to_main->description,
                'image' => $imageUrl ?? $notification_to_main->image,
                'status' => $request->status,
                'update_by' => $request->update_by ?? $notification_to_main->update_by,
                'updated_at' => now(),
            ];

            $notification_to_main->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật Notification thành công",
                'data' => $notification_to_main,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật Notification không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $notification_to_main = Notification_to_mainModel::find($id);

            if (!$notification_to_main) {
                return response()->json([
                    'status' => false,
                    'message' => "Notification không tồn tại",
                ], 404);
            }

            $notification_to_main->delete();

            return response()->json([
                'status' => true,
                'message' => "Xóa Notification thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa Notification không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
