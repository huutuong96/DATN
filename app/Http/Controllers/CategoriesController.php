<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoriesModel;
use App\Http\Requests\CategoriesRequest;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoriesModel::all();

        if ($categories->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Categories nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $categories
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
    public function store(CategoriesRequest $request)
    {
        $image = $request->file('image');
        $cloudinary = new Cloudinary();
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());

            $dataInsert = [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'index' => $request->index,
                'image' => $uploadedImage['secure_url'],
                'status' => $request->status,
                'parent_id' => $request->parent_id,
                'create_by' => $user->id
            ];

            $categories = CategoriesModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Categories thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Categories không thành công",
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
            $categories = CategoriesModel::find($id);

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Categories không tồn tại",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin Categories thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin Categories",
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
    public function update(CategoriesRequest $request, string $id)
    {
        try {
            $categories = CategoriesModel::find($id);

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Categories không tồn tại",
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
                $imageUrl = $categories->image;
            }

            $dataUpdate = [
                'title' => $request->title ?? $categories->title,
                'slug' => Str::slug($request->title),
                'index' => $request->index ?? $categories->index,
                'image' => $imageUrl ?? $categories->image,
                'status' => $request->status ?? $categories->status,
                'parent_id' => $request->parent_id ?? $categories->parent_id,
                'update_by' => $request->update_by ?? $categories->update_by,
                'updated_at' => now(),
            ];

            $categories->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật Categories thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật Categories không thành công",
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
            $categories = CategoriesModel::find($id);

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Categories không tồn tại",
                ], 404);
            }

            $categories->delete();

            return response()->json([
                'status' => true,
                'message' => "Xóa Categories thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa Categories không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
