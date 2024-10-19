<?php

namespace App\Http\Controllers;
use App\Models\Follow_to_shop;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
class FollowToShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $follow = Follow_to_shop::all();
        if($follow->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại follow nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $follow,
            ]
        );
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        // dd($user);
        $dataInsert = [
            'user_id'=> $user->id,
            'shop_id' => $request->shop_id,
        ];
           $follow = Follow_to_shop::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "follow Đã được lưu",
            'follows' => $follow,
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $follow = Follow_to_shop::where('shop_id', $id)->get();
        if($follow->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại follow nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "danh sách khách hàng follow theo shop",
                'data' => $follow,
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $follow = Follow_to_shop::where('user_id', $user->id)->where('shop_id', $id)->first();

            if (!$follow) {
                return response()->json([
                    'status' => false,
                    'message' => 'bạn chưa follow shop này',
                ], 404);
            }

            // Xóa bản ghi
            $follow->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'unfollow thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "unfollow thất bại",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
