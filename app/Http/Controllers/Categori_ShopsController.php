<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Categori_shopsModel;
use App\Http\Requests\CategoriesRequest;
use Illuminate\Support\Facades\Cache;

class Categori_ShopsController extends Controller
{
    public function index()
    {
        $categori_shops = Cache::remember('all_categori_shops', 60 * 60, function () {
            return Categori_shopsModel::all();
        });

        if ($categori_shops->isEmpty()) {
            return $this->errorResponse("Không tồn tại danh mục shop nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $categori_shops);
    }

    public function store(CategoriesRequest $request)
    {
        try {
            $imageUrl = $this->uploadImage($request);

            $dataInsert = $this->prepareDataForInsert($request, $imageUrl);

            $categori_shops = Categori_shopsModel::create($dataInsert);

            Cache::forget('all_categori_shops');

            return $this->successResponse("Thêm danh mục shop thành công", $categori_shops);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm danh mục shop không thành công", $th->getMessage());
        }
    }

    public function show(string $id)
    {
        $categori_shops = Cache::remember('categori_shop_' . $id, 60 * 60, function () use ($id) {
            return Categori_shopsModel::find($id);
        });

        if (!$categori_shops) {
            return $this->errorResponse("Không tồn tại danh mục shop nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $categori_shops);
    }

    public function update(CategoriesRequest $request, string $id)
    {
        $categori_shops = Cache::remember('categori_shop_' . $id, 60 * 60, function () use ($id) {
            return Categori_shopsModel::find($id);
        });

        if (!$categori_shops) {
            return $this->errorResponse("Danh mục shop không tồn tại", 404);
        }

        $imageUrl = $this->uploadImage($request);

        $dataUpdate = $this->prepareDataForUpdate($request, $categori_shops, $imageUrl);

        try {
            $categori_shops->update($dataUpdate);
            Cache::forget('categori_shop_' . $id);
            Cache::forget('all_categori_shops');
            return $this->successResponse("Cập nhật danh mục shop thành công", $categori_shops);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật danh mục shop không thành công", $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $categori_shops = Cache::remember('categori_shop_' . $id, 60 * 60, function () use ($id) {
                return Categori_shopsModel::find($id);
            });

            if (!$categori_shops) {
                return $this->errorResponse('Danh mục shop không tồn tại', 404);
            }

            $categori_shops->delete();

            Cache::forget('categori_shop_' . $id);
            Cache::forget('all_categori_shops');

            return $this->successResponse('Xóa danh mục shop thành công');
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa danh mục shop không thành công", $th->getMessage());
        }
    }

    private function uploadImage($request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            return $uploadedImage['secure_url'];
        }
        return null;
    }

    private function prepareDataForInsert($request, $imageUrl)
    {
        return [
            'title' => $request->title,
            'index' => $request->index,
            'slug' => Str::slug($request->title),
            'image' => $imageUrl,
            'status' => $request->status,
            'parent_id' => $request->parent_id,
            'create_by' => auth()->user()->id,
            'shop_id' => $request->shop_id,
            'category_id_main' => $request->category_id_main
        ];
    }

    private function prepareDataForUpdate($request, $categori_shops, $imageUrl)
    {
        return [
            'title' => $request->title ?? $categori_shops->title,
            'index' => $request->index ?? $categori_shops->index,
            'slug' => $request->slug ?? Str::slug($request->title),
            'image' => $imageUrl ?? $categori_shops->image,
            'status' => $request->status ?? $categori_shops->status,
            'parent_id' => $request->parent_id ?? $categori_shops->parent_id,
            'update_by' => auth()->user()->id,
            'shop_id' => $request->shop_id ?? $categori_shops->shop_id,
            'category_id_main' => $request->category_id_main ?? $categori_shops->category_id_main
        ];
    }

    private function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    private function errorResponse($message, $error = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
