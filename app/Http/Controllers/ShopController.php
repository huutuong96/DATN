<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Shop;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shops = Shop::all();
        if ($shops->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => "Không tồn tại Shops nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $shops,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = null; // Default to null if no image is provided
        }

        $dataInsert = [
            'Owner_id' => $request->Owner_id ?? $user->id,
            'shop_name' => $request->shop_name,
            'pick_up_address' => $request->pick_up_address,
            'slug' => $request->slug ?? Str::slug($request->shop_name, '-'),
            'image' => $imageUrl,
            'cccd' => $request->cccd,
            'status' => $request->status,
            'tax_id' => $request->tax_id,
        ];

        try {
            $shop = Shop::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm Shop thành công",
                'data' => $shop,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Shop không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại Shop nào",
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $shop,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Shop không tồn tại",
            ], 404);
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = $shop->image; // Keep the existing image if no new image is uploaded
        }

        $user = JWTAuth::parseToken()->authenticate();

        $dataUpdate = [
            'Owner_id' => $request->Owner_id ?? $user->id,
            'shop_name' => $request->shop_name,
            'pick_up_address' => $request->pick_up_address,
            'slug' => $request->slug ?? Str::slug($request->shop_name, '-'),
            'image' => $imageUrl,
            'cccd' => $request->cccd,
            'status' => $request->status,
            'tax_id' => $request->tax_id,
            'created_at' => $request->created_at ?? $shop->created_at, // Preserve the original creation date
        ];

        try {
            $shop->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật thông tin Shop thành công",
                'data' => $shop,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật thông tin Shop không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }

        try {
            $shop->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa Shop thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa Shop không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
