<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Requests\BannerRequest;
use App\Models\BannerShop;

class BannerShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = BannerShop::all();
        if ($banners->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại banner nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $banners,
        ]);
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
    public function store(BannerRequest $rqt)
    {
        $image = $rqt->file('image');
        $cloudinary = new Cloudinary();

        try {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());

            $dataInsert = [
                'shop_id' => $rqt->shop_id,
                'title' => $rqt->title,
                'content' => $rqt->content,
                'URL' => $uploadedImage['secure_url'],
                'status' => $rqt->status,
                'index' => $rqt->index,
            ];

            $banners = BannerShop::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Thêm BannerShop thành công",
                'data' => $banners,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm BannerShop không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = BannerShop::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại banner nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $banner,
        ]);
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
    public function update(BannerRequest $rqt, string $id)
    {
        // Tìm banner theo ID
        $banner = BannerShop::find($id);
        // Kiểm tra xem banner có tồn tại không
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => "Banner không tồn tại",
            ], 404);
        }

        // Kiểm tra xem có ảnh mới được tải lên không
        if ($rqt->hasFile('image')) {
            $image = $rqt->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            // Nếu không có ảnh mới thì giữ nguyên URL của ảnh hiện tại
            $imageUrl = $banner->URL; // Sửa lại từ `$brands->image` thành `$banner->URL`
        }

        // Cập nhật dữ liệu
        $dataUpdate = [
            'shop_id' => $rqt->shop_id,
            'title' => $rqt->title ?? $banner->title,
            'content' => $rqt->content ?? $banner->content,
            'URL' => $imageUrl,
            'status' => $rqt->status ?? $banner->status,
            'index' => $rqt->index ?? $banner->index,
        ];

        try {
            // Cập nhật bản ghi
            $banner->update($dataUpdate);
            return response()->json([
                'status' => true,
                'message' => "Cập nhật banner thành công",
                'data' => $banner,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật banner không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $banner = BannerShop::find($id);

            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Banner không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $banner->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa banner thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa banner không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
