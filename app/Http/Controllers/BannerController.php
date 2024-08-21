<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();
        if($banners->isEmpty()){
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
    public function store(BannerRequest $rqt )
    {

        $image = $rqt->file('image');
        $cloudinary = new Cloudinary();

        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());



        $dataInsert = [
            'title' => $rqt->title,
            'content' => $rqt->content,
            'URL' => $uploadedImage['secure_url'],
            'status' => $rqt->status,
            'index' => $rqt->index,
            // 'create_by',
            // 'update_by',
        ];
        try {
            $banner = Banner::create( $dataInsert );

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
        $banner = Banner::find($id);

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
    public function update(Request $rqt, $id)
    {
        // Tìm banner theo ID
        $banner = Banner::find($id);
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
        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $rqt->title,
            'content' => $rqt->content,
            'URL' => $rqt->URL,
            'status' => $rqt->status,
            'index' => $rqt->index,
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
            $banner = Banner::find($id);

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
