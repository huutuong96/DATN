<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Shop;
use App\Models\Shop_manager;
use Illuminate\Support\Str;
use App\Models\UsersModel;

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
     * Store a newly created resource in storage.
     */
    public function shop_manager_store(Request $rqt)
    {
        $dataInsert = [
            'status' => $rqt->status,
            'user_id' => $rqt->user_id,
            'shop_id' => $rqt->shop_id,
            'role' => $rqt->role,
        ];
        try {
            $Shop_manager = Shop_manager::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm thành công",
                    'data' => $Shop_manager,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    public function store(ShopRequest $rqt)
    {
        $image = $rqt->file('image');
        $cloudinary = new Cloudinary();
        $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
        $dataInsert = [
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
    public function show_shop_members(string $id)
    {
        $member = Shop_manager::where('shop_id', $id)->pluck('user_id');

        if(!$member){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Shop nào",
                ]
            );
        }
        $user = JWTAuth::parseToken()->authenticate();
        $check_member = false;

        foreach ($member as $user_id) {
            if ($user_id == $user->id) {
                $check_member = true;
            }
        }

        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành viên shop $id thành công",
                'data' => $user,
            ]
        );
    }

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
     * Update the specified resource in storage.
     */
    public function update_shop_members(Request $rqt, string $id)
    {
        $member = Shop_manager::where('id', $id)->first();
        if(!$member){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại thành viên trong hóp này",
                ]
            );
        }
        $member->update([
            'role' => $rqt->role,
        ]);
        return response()->json(
            [
                'status' => true,
                'message' => "cập nhật thành viên shop $id thành công",
                'data' => $member,
            ]
        );
    }
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
        $image = $rqt->file('image');
        $cloudinary = new Cloudinary();
        // $uploudinary = $cloudinary->uploadApi()->upload($image->getRealPath());

        // $user = JWTAuth::parseToken()->authenticate();
        // lấy địa chỉ của usẻr để thêm vào dòn 59 ?? $user->address_id



        $dataInsert = [
            'shop_name' => $rqt->shop_name,
            'pick_up_address' => $rqt->pick_up_address ,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
            'image' => $uploadedImage['secure_url'] ?? "",// thêm ?? để tranh lỗi khi test băng post man
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
    public function destroy_members(string $id)
    {
        try {
            $member = Shop_manager::find($id);

            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'thành viên không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $member->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa thành viên thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa thành viên không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
