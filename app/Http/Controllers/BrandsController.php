<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use App\Models\BrandsModel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $brands = BrandsModel::all();

        if ($brands->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Brand nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $brands
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
    public function store(BrandRequest $request)
    {
        $image = $request->file('image');
        $cloudinary = new Cloudinary();
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataInsert = [
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'image' => $uploadedImage['secure_url'],
                'status' => $request->status,
                'parent_id' => $request->parent_id,
                'create_by' => $user->id,
            ];
            $brands = BrandsModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Brand thành công",
                'data' => $brands,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Brand không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $brands = BrandsModel::find($id);

            if (!$brands) {
                return response()->json([
                    'status' => false,
                    'message' => "Brand không tồn tại",
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => "Lấy thông tin Brand thành công",
                'data' => $brands,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi lấy thông tin Brand",
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
    public function update(BrandRequest $request, $id)

    {

        $user = JWTAuth::parseToken()->authenticate();

        try {
            $brands = BrandsModel::find($id);

            if (!$brands) {
                return response()->json([
                    'status' => false,
                    'message' => "Brand không tồn tại",
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
                $imageUrl = $brands->image;
            }

            $dataUpdate = [
                'title' => $request->title ?? $brands->title,
                'slug' => Str::slug($request->title),
                'image' => $imageUrl ?? $brands->image,
                'status' => $request->status ?? $brands->status,
                'parent_id' => $request->parent_id ?? $brands->parent_id,
                'update_by' => $user->id,
                'updated_at' => now(),
            ];

            $brands->update($dataUpdate);
            
            return response()->json([
                'status' => true,
                'message' => "Cập nhật Brand thành công",
                'data' => $brands,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật Brand không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $brands = BrandsModel::find($id);

            if (!$brands) {
                return response()->json([
                    'status' => false,
                    'message' => "Brand không tồn tại",
                ], 404);
            }

            $brands->delete();

            return response()->json([
                'status' => true,
                'message' => "Xóa Brand thành công",
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa Brand không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
