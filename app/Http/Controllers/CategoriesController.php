<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CategoriesModel;
use App\Models\categoryattribute;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\CategoriesRequest;
use App\Models\tax_category;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = CategoriesModel::where('status', 1)->get();

        if ($categories->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại danh mục nào"
                ]
            );
        }
        foreach ($categories as $category) {
            $category->id = intval($category->id);
            $category->index = intval($category->index);
            $category->status = intval($category->status);
            $category->parent_id = intval($category->parent_id);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $categories
        ], 200);
    }

    public function store(CategoriesRequest $request)
    {
        
        
        $dataInsert = [];

        // Kiểm tra và upload ảnh
        if ($request->file('image')) {
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
                'tax_id' => $request->tax_id
            ];

            $category = CategoriesModel::create($dataInsert);
            $tax_category = tax_category::create([
                'category_id' => $category->id,
                'tax_id' => $request->tax_id,
            ]);
            $parentId = $category->parent_id;
            while ($parentId) {
                $parentAttributes = CategoryAttribute::where('category_id', $parentId)->get();

                foreach ($parentAttributes as $parentAttribute) {
                    $parentAttribute->status = 0;
                    $parentAttribute->save();
                }

                $parentCategory = CategoriesModel::find($parentId);
                $parentId = $parentCategory ? $parentCategory->parent_id : null;
            }
            $subCategories = CategoriesModel::where('parent_id', $category->id)->get();
            if ($subCategories->isEmpty()) {
                $attributeIds = $request->attribute_ids;
                if (is_array($attributeIds) && count($attributeIds) > 0) {
                    foreach ($attributeIds as $attributeId) {
                        categoryattribute::insert([
                            'category_id' => $category->id,
                            'attribute_id' => $attributeId,
                        ]);
                    }
                }
            }
            if($request->back == 1){
                session()->put('message', 'Tạo thành công!');
                // return redirect()->route('list_category', ['token' => auth()->user()->refesh_token])->with('message', 'Tạo thành công!');
                // return back()->with('message', 'Tạo thành công!');
            }
            return response()->json([
                'status' => true,
                'message' => "Thêm danh mục thành công",
                'data' => $category,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm danh mục không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }




    public function show(string $id)
    {
        try {
            $category = CategoriesModel::find($id);

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }


            $category->subCategories = $this->getSubCategories($category->id);

            if ($category->subCategories->isEmpty()) {
                $category->attributes = $category->attributes()->get();
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin danh mục thành công",
                'data' => $category,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin danh mục",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function getSubCategories($categoryId)
    {
        $subCategories = CategoriesModel::where('parent_id', $categoryId)->get();

        foreach ($subCategories as $subCategory) {
            $sub_SubCategories = CategoriesModel::where('parent_id', $subCategory->id)->get();
            if ($sub_SubCategories->isEmpty()) {
                $subCategory->attributes = $subCategory->attributes()->get();
            } else {
                $subCategory->subCategories = $this->getSubCategories($subCategory->id);
            }
        }

        return $subCategories;
    }


    public function update(CategoriesRequest $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $categories = CategoriesModel::find($id);

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
            $tax_category = tax_category::create([
                'category_id' => $categories->id,
                'tax_id' => $request->tax_id,
            ]);
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
            $categories = CategoriesModel::find($id);

            if (!$categories) {
                return response()->json([
                    'status' => false,
                    'message' => "Danh mục không tồn tại",
                ], 404);
            }

            $categories->update(['status' => 0]);

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
