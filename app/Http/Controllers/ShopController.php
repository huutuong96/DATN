<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
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
        $Shops = Shop::all();
        if($Shops->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Shops nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $Shops,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ShopRequest $rqt)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $rqt)
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

        $user = JWTAuth::parseToken()->authenticate();

        $dataInsert = [
            'Owner_id' => $rqt->Owner_id ?? $user->id,
            'shop_name' => $rqt->shop_name,
            'pick_up_address' => $rqt->pick_up_address ,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
            'image' => $uploadedImage['secure_url'] ?? "",// thêm ?? để tranh lỗi khi test băng post man
            'cccd' => $rqt->cccd,
            'status' => $rqt->status ,
            'tax_id' => $rqt->tax_id,
        ];
        try {
            $Shop = Shop::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm Shop thành công",
                    'data' => $Shop,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm Shop không thành công",
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
        $Shops = Shop::find($id);

        if(!$Shops){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Shop nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $Shops,
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
    public function update(Request $rqt, string $id)
    {
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "shop không tồn tại",
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


        $user = JWTAuth::parseToken()->authenticate();
        
        $dataInsert = [
            'Owner_id' => $rqt->Owner_id ?? $user->id,
            'Owner_id' => $rqt->Owner_id,
            'shop_name' => $rqt->shop_name,
            'pick_up_address' => $rqt->pick_up_address ,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
            'image' => $imageUrl,
            'cccd' => $rqt->cccd,
            'status' => $rqt->status ,
            'tax_id' => $rqt->tax_id,
            'created_at' => $rqt->created_at ?? $shop->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];
        try {
            $shop->update($dataInsert);

            return response()->json(
                [
                    'status' => true,
                    'message' => "cập nhật thông tin Shop thành công",
                    'data' => $shop,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "cập nhật thông tin Shop không thành công",
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
            $shop = Shop::find($id);

            if (!$shop) {
                return response()->json([
                    'status' => false,
                    'message' => 'shop không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $shop->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa shop thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa shop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
