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
        if($banners->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại banner nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $banners,
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
    public function store(BannerRequest $rqt)
    {
       // Check xem co anh moi duoc tai len khong
       if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            // Neu khong co anh moi thi giu nguyen URL cua anh hien tai
            $imageUrl = $brands->image;
        }

        $dataInsert = [
            'shop_id'=>$rqt->shop_id,
            'title' => $rqt->title,
            'content' => $rqt->content,
            'URL' => $imageUrl,
            'status' => $rqt->status,
            'index' => $rqt->index,
        ];

        try {
            $banner = BannerShop::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm banner thành công",
                    'data' => $banner,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm banner không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $banner = BannerShop::find($id);

        if(!$banner){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại banner nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $banner,
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
    public function update(BannerRequest $rqt, $id)
    {
        // Tìm banner theo ID
        $banner = BannerShop::find($id);
        // Kiểm tra xem banner có tồn tại không
        if (!$banner) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Banner không tồn tại",
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
            $imageUrl = $brands->image;
        }


        // Cập nhật dữ liệu
        $dataUpdate = [
            'shop_id'=>$rqt->shop_id,
            'title' => $rqt->title ?? $banner->title,
            'content' => $rqt->content ?? $banner->content,
            'URL' => $imageUrl,
            'status' => $rqt->status ?? $banner->status,
            'index' => $rqt->index ?? $banner->index,
            'created_at' => $rqt->created_at ?? $banner->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];


        try {
            // Cập nhật bản ghi
            $banner->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật banner thành công",
                    'data' => $banner,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật banner không thành công",
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
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa banner không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
