<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoucherToShop;
use App\Http\Requests\VoucherRequest;

class VoucherToShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherMain = VoucherToShop::all(); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'
        if ($voucherMain->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => "Không tồn tại voucher main nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $voucherMain,
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
    public function store(VoucherRequest $request)
    {
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
        ];

        try {
            $voucherMain = VoucherToShop::create($dataInsert); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'

            return response()->json([
                'status' => true,
                'message' => "Thêm voucher main thành công",
                'data' => $voucherMain,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, // Sửa lại 'status' thành false để thể hiện lỗi
                'message' => "Thêm voucher main không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'
        if (!$voucherMain) {
            return response()->json([
                'status' => true,
                'message' => "Không tồn tại voucher main nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $voucherMain,
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
    public function update(VoucherRequest $request, string $id)
    {
        $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'

        if (!$voucherMain) {
            return response()->json([
                'status' => false,
                'message' => "voucher main không tồn tại",
            ], 404);
        }

        $dataUpdate = [
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
        ];

        try {
            $voucherMain->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật voucher main thành công",
                'data' => $voucherMain,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật voucher main không thành công",
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
            $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'

            if (!$voucherMain) {
                return response()->json([
                    'status' => false,
                    'message' => 'voucher main không tồn tại',
                ], 404);
            }

            $voucherMain->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa voucher main thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa voucher main không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
