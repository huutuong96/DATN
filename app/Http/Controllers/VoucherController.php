<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\voucher_to_main;
use App\Models\VoucherToShop;
use App\Http\Requests\VoucherRequest;
use Illuminate\Support\Facades\Cache;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Cache::remember('all_vouchers', 60 * 60, function () {
            return Voucher::all();
        });

        if ($vouchers->isEmpty()) {
            return $this->errorResponse("Không tồn tại voucher nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $vouchers);
    }

    public function store(VoucherRequest $request)
    {
        $checkvoucher_to_main = voucher_to_main::where('code', $request->code)->exists();
        $checkVoucherToShop = VoucherToShop::where('code', $request->code)->exists();

        if (!$checkvoucher_to_main && !$checkVoucherToShop) {
            return $this->errorResponse("Mã voucher không khớp với bất kỳ voucher nào của shop hoặc sàn.");
        }

        try {
            $voucher = Voucher::create($request->validated());
            Cache::forget('all_vouchers');
            return $this->successResponse("Thêm voucher thành công", $voucher);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm voucher không thành công", $th->getMessage());
        }
    }

    public function show(string $id)
    {
        $voucher = Cache::remember('voucher_' . $id, 60 * 60, function () use ($id) {
            return Voucher::find($id);
        });

        if (!$voucher) {
            return $this->errorResponse("Không tồn tại voucher nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucher);
    }

    public function update(VoucherRequest $request, string $id)
    {
        $voucher = Cache::remember('voucher_' . $id, 60 * 60, function () use ($id) {
            return Voucher::find($id);
        });

        if (!$voucher) {
            return $this->errorResponse("Voucher không tồn tại", 404);
        }

        try {
            $voucher->update($request->validated());
            Cache::forget('voucher_' . $id);
            Cache::forget('all_vouchers');
            return $this->successResponse("Cập nhật voucher thành công", $voucher);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật voucher không thành công", $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
            $voucher->delete();
            Cache::forget('voucher_' . $id);
            Cache::forget('all_vouchers');
            return $this->successResponse("Xóa voucher thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa voucher không thành công", $th->getMessage());
        }
    }

    private function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    
    private function errorResponse($message, $error = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
