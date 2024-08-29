<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Models\CouponsModel;
use Illuminate\Http\Request;
use App\Models\Coupon_to_shop;
class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = CouponsModel::all();
        if ($coupons->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Coupon nào",
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $coupons
        ], 200);
    }
    public function index_to_shop($id)
    {
        $coupons = Coupon_to_shop::where('shop_id', $id)->get();
        if ($coupons->isEmpty()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Coupon nào",
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $coupons
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store_to_shop(Request $request, $id)
    {
        $dataInsert = [
            "status" => $request->status,
            "coupon_id" => $request->coupon_id,
            "shop_id" => $id,
        ];

        try {
            $coupons = Coupon_to_shop::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Coupon thành công",
                'data' => $coupons
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Coupon không thành công",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request)
    {
        $dataInsert = [
            "status" => $request->status,
            "coupon_percentage" => $request->coupon_percentage,
            "condition" => $request->condition,
            "create_by" => $request->create_by,
        ];

        try {
            $coupons = CouponsModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Coupon thành công",
                'data' => $coupons
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Coupon không thành công",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupons = CouponsModel::find($id);

        if (!$coupons) {
            return response()->json([
                'status' => false,
                'message' => "Coupon không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $coupons
        ], 200);
    }
    public function show_to_shop(string $id)
    {
        $coupons = Coupon_to_shop::where('id', $id)->first();

        if (!$coupons) {
            return response()->json([
                'status' => false,
                'message' => "Coupon không tồn tại"
            ], 404);
        }
        $dataUpdate = [
            "status" => $request->status,
            "coupon_id" => $request->coupon_id,
            "shop_id" => $id,
        ];
        $coupons->update();
        // Coupon_to_shop::where('id', $id)->first();
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $coupons
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function update_to_shop(Request $request, string $id)
    {
        $coupons = Coupon_to_shop::find($id);

        if (!$coupons) {
            return response()->json([
                'status' => false,
                'message' => "Coupon không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "status" => $request->status,
            "coupon_id" => $request->coupon_id,
        ];

        try {
            $coupons->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Coupon đã được cập nhật",
                    'data' => $coupons
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Coupon không thành công",
                    'error' => $th->getMessage()
                ]
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, string $id)
    {
        $coupons = CouponsModel::find($id);

        if (!$coupons) {
            return response()->json([
                'status' => false,
                'message' => "Coupon không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "status" => $request->status,
            "coupon_percentage" => $request->coupon_percentage,
            "condition" => $request->condition,
            "create_by" => $request->create_by,
        ];

        try {
            $coupons->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Coupon đã được cập nhật",
                    'data' => $coupons
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Coupon không thành công",
                    'error' => $th->getMessage()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_to_shop(string $id)
    {
        $coupons = Coupon_to_shop::find($id);

        try {
            if (!$coupons) {
                return response()->json([
                    'status' => false,
                    'message' => "Coupon không tồn tại"
                ], 404);
            }

            $coupons->delete();

            return response()->json([
                'status' => true,
                'message' => "Coupon đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Coupon không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
    public function destroy(string $id)
    {
        $coupons = CouponsModel::find($id);

        try {
            if (!$coupons) {
                return response()->json([
                    'status' => false,
                    'message' => "Coupon không tồn tại"
                ], 404);
            }

            $coupons->delete();

            return response()->json([
                'status' => true,
                'message' => "Coupon đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Coupon không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
