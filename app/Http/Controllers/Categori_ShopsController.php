<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Categori_shopsModel;
use App\Http\Requests\CategoriesRequest;

class Categori_ShopsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categori_shops = Categori_shopsModel::all();
        if ($categori_shops->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Categori_Shop nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $categori_shops,
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
    public function store(CategoriesRequest $request)
    {
        $image = $request->file('image');
        $cloudinary = new Cloudinary();

        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());

            $dataInsert = [
                'title' => $request->title,
                'index' => $request->index,
                'slug' => Str::slug($request->title),
                'image' => $uploadedImage['secure_url'],
                'status' => $request->status,
                'parent_id' => $request->parent_id,
                'create_by' => $request->create_by,
                'shop_id' => $request->shop_id,
                'category_id_main' => $request->category_id_main
            ];

            $categori_shops = Categori_shopsModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Categori_Shop thành công",
                'data' => $categori_shops,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Categori_Shop không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categori_shops = Categori_shopsModel::find($id);

        if (!$categori_shops) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Categori_Shop nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $categori_shops,
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
    public function update(CategoriesRequest $request, string $id)
    {
        $categori_shops = Categori_shopsModel::find($id);
        // Kiểm tra xem banner có tồn tại không
        if (!$categori_shops) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Categori_Shop không tồn tại",
                ],
                404
            );
        }

        // Check xem co anh moi duoc tai len khong
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            // Neu khong co anh moi thi giu nguyen URL cua anh hien tai
            $imageUrl = $categori_shops->image;
        }


        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $request->title ?? $categori_shops->title,
            'index' => $request->index ?? $categori_shops->index,
            'slug' => Str::slug($request->title),
            'image' => $imageUrl,
            'status' => $request->status ?? $categori_shops->status,
            'parent_id' => $request->parent_id ?? $categori_shops->parent_id,
            'create_by' => $request->create_by ?? $categori_shops->create_by,
            'shop_id' => $request->shop_id ?? $categori_shops->shop_id,
            'category_id_main' => $request->category_id_main ?? $categori_shops->category_id_main 
        ];


        try {
            // Cập nhật bản ghi
            $categori_shops->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật Categori_Shop thành công",
                    'data' => $categori_shops,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Categori_Shop không thành công",
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
            $categori_shops = Categori_shopsModel::find($id);

            if (!$categori_shops) {
                return response()->json([
                    'status' => false,
                    'message' => 'Categori_Shop không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $categori_shops->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa Categori_Shop thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Categori_Shop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
