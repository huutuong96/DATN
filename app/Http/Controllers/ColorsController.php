<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use App\Models\ColorsModel;
use Illuminate\Http\Request;
use App\Http\Requests\ColorRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
class ColorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $colors = ColorsModel::all();

        if ($colors->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Color nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $colors
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
    public function store(ColorRequest $request)
    {
        $image = $request->file('image');
        $cloudinary = new Cloudinary();
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());

            $dataInsert = [
                'title' => $request->title,
                'index' => $request->index,
                'image' => $uploadedImage['secure_url'],
                'status' => $request->status,
                'create_by' => $user->id,
            ];

            $colors = ColorsModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Color thành công",
                'data' => $colors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Color không thành công",
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
            $colors = ColorsModel::find($id);

            if (!$colors) {
                return response()->json([
                    'status' => false,
                    'message' => "Color không tồn tại",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin Color thành công",
                'data' => $colors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin Color",
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
    public function update(ColorRequest $request, string $id)
    {
        try {
            $colors = ColorsModel::find($id);

            if (!$colors) {
                return response()->json([
                    'status' => false,
                    'message' => "Color không tồn tại",
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
                $imageUrl = $colors->image;
            }

            $dataUpdate = [
                'title' => $request->title ?? $colors->title,
                'index' => $request->index ?? $colors->index,
                'image' => $imageUrl ?? $colors->image,
                'status' => $request->status ?? $colors->status,
                'update_by' => $request->update_by ?? $colors->update_by,
                'updated_at' => now(),
            ];

            $colors->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật Color thành công",
                'data' => $colors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật Color không thành công",
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
            $colors = ColorsModel::find($id);

            if (!$colors) {
                return response()->json([
                    'status' => false,
                    'message' => "Color không tồn tại",
                ], 404);
            }

            $colors->delete();

            return response()->json([
                'status' => true,
                'message' => "Xóa Color thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa Color không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
