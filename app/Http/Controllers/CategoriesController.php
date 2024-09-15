<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoriesModel;
use App\Http\Requests\CategoriesRequest;
use Illuminate\Support\Facades\Cache;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Cache::remember('all_categories_main', 60 * 60, function () {
            return CategoriesModel::all();
        });

        if ($categories->isEmpty()) {
            return response()->json(
                [
                'status' => false,
                'message' => "Không tồn tại danh mục nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $categories
        ], 200);
    }

    public function store(CategoriesRequest $request)
    {
        if($request->file('image')){
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $dataInsert['image'] = $cloudinary->uploadApi()->upload($image->getRealPath())['secure_url'];
        }
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $dataInsert = [
                'title' => $request->title,
                'slug' => $request->slug ?? Str::slug($request->title, '-'),
                'index' => $request->index ?? 1,
                'status' => $request->status ?? 1,
                'parent_id' => $request->parent_id ?? null,
                'create_by' => $user->id,
                'image' => $dataInsert['image'] ?? null,
            ];

            $categories = CategoriesModel::create($dataInsert);

            Cache::forget('all_categories_main');

            return response()->json([
                'status' => true,
                'message' => "Thêm danh mục thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm danh mục không thành công",
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
            $categories = Cache::remember('category_'.$id, 60 * 60, function () use ($id) {
                return CategoriesModel::find($id);
            });

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin danh mục thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin danh mục",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function update(CategoriesRequest $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $categories = Cache::remember('category_'.$id, 60 * 60, function () use ($id) {
                return CategoriesModel::find($id);
            });

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }
            if ($request->file('image')) {
                $image = $request->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $imageUrl = $uploadedImage['secure_url'];
            }

            $dataUpdate = [
                'title' => $request->title ?? $categories->title,
                'slug' => Str::slug($request->title),
                'index' => $request->index ?? $categories->index,
                'image' => $imageUrl ?? $categories->image,
                'status' => $request->status ?? $categories->status,
                'parent_id' => $request->parent_id ?? $categories->parent_id,
                'update_by' => $user->id,
                'updated_at' => now(),
            ];

            $categories->update($dataUpdate);

            Cache::forget('category_'.$id);
            Cache::forget('all_categories_main');

            return response()->json([
                'status' => true,
                'message' => "Cập nhật danh mục thành công",
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật danh mục không thành công",
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
            $categories = Cache::remember('category_'.$id, 60 * 60, function () use ($id) {
                return CategoriesModel::find($id);
            });

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }

            $categories->update(['status' => 0]);

            Cache::forget('category_'.$id);
            Cache::forget('all_categories_main');

            return response()->json([
                'status' => true,
                'message' => "Xóa danh mục thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
