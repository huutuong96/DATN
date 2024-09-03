<?php

namespace App\Http\Controllers;

use App\Models\VoucherToShop;
use App\Http\Requests\VoucherRequest;
use Illuminate\Support\Facades\Cache;

class VoucherToShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherShops = Cache::remember('all_voucher_shops', 60 * 60, function () {
            return VoucherToShop::all();
        });

        if ($voucherShops->isEmpty()) {
            return $this->errorResponse("Không tồn tại voucher shop nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucherShops);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoucherRequest $request)
    {
        try {
            $voucherShop = VoucherToShop::create($request->validated());
            Cache::forget('all_voucher_shops');
            return $this->successResponse("Thêm voucher shop thành công", $voucherShop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm voucher shop không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucherShop = Cache::remember('voucher_shop_' . $id, 60 * 60, function () use ($id) {
            return VoucherToShop::find($id);
        });

        if (!$voucherShop) {
            return $this->errorResponse("Không tồn tại voucher shop nào", null, 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucherShop);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VoucherRequest $request, string $id)
    {
        $voucherShop = Cache::remember('voucher_shop_' . $id, 60 * 60, function () use ($id) {
            return VoucherToShop::find($id);
        });

        if (!$voucherShop) {
            return $this->errorResponse("Voucher shop không tồn tại", null, 404);
        }

        try {
            $voucherShop->update($request->validated());
            Cache::forget('voucher_shop_' . $id);
            Cache::forget('all_voucher_shops');
            return $this->successResponse("Cập nhật voucher shop thành công", $voucherShop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật voucher shop không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucherShop = Cache::remember('voucher_shop_' . $id, 60 * 60, function () use ($id) {
            return VoucherToShop::find($id);
        });

        if (!$voucherShop) {
            return $this->errorResponse("Voucher shop không tồn tại", null, 404);
        }

        try {
            $voucherShop->delete();
            Cache::forget('voucher_shop_' . $id);
            Cache::forget('all_voucher_shops');
            return $this->successResponse("Xóa voucher shop thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa voucher shop không thành công", $th->getMessage());
        }
    }

    /**
     * Return success response
     */
    private function successResponse(string $message, $data = null, int $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Return error response
     */
    private function errorResponse(string $message, $error = null, int $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
