<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\voucherToMain;
use App\Models\VoucherToShop;
use App\Http\Requests\VoucherRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::all();

        if ($vouchers->isEmpty()) {
            return $this->errorResponse("Không tồn tại voucher nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $vouchers);
    }

    public function store(VoucherRequest $request)
    {
        $checkvoucherToMain = voucherToMain::where('code', $request->code)->exists();
        $checkVoucherToShop = VoucherToShop::where('code', $request->code)->exists();

        if (!$checkvoucherToMain && !$checkVoucherToShop) {
            return $this->errorResponse("Mã voucher không khớp với bất kỳ voucher nào của shop hoặc sàn.");
        }

        try {
            $voucher = Voucher::create($request->validated());
            return $this->successResponse("Thêm voucher thành công", $voucher);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm voucher không thành công", $th->getMessage());
        }
    }

    public function show(string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return $this->errorResponse("Không tồn tại voucher nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucher);
    }

    public function update(VoucherRequest $request, string $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return $this->errorResponse("Voucher không tồn tại", 404);
        }

        try {
            $voucher->update($request->validated());
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

    public function addVoucherByCode(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $voucher_added = Voucher::where('code', $request->code)->where('user_id', $user->id)->first();
        if ($voucher_added) {
            return $this->errorResponse("Bạn đã thêm voucher này rồi, Tham lam quá");
        }
        $voucherMain = voucherToMain::where('code', $request->code)->where('status', 2)->first();
        if ($voucherMain) {
            $voucherMain->quantity = $voucherMain->quantity - 1;
            $voucherMain->save();
        }
        if (!$voucherMain) {
            $voucherShop = VoucherToShop::where('code', $request->code)->where('status', 2)->first();
            if ($voucherShop) {
                $voucherShop->quantity = $voucherShop->quantity - 1;
                $voucherShop->save();
            }
        }
        if (!$voucherMain && !$voucherShop) {
            return $this->errorResponse("Mã voucher không tồn tại hoặc đã hết hạn");
        }
        // dd($voucherMain);
        Voucher::create([
            'type' => $voucherMain ? 'main' : 'shop',
            'status' => 2,
            'create_by' => $user->id,
            'update_by' => $user->id,
            'code' => $request->code,
            'user_id' => $user->id,
            'shop_id' => $voucherShop->shop_id ?? null,
            'max' => $voucherMain->limitValue ?? $voucherShop->limitValue,
            'min' => $voucherMain->min ?? $voucherShop->min,
            'ratio' => $voucherMain->ratio ?? $voucherShop->ratio,
            'title' => $voucherMain->title ?? $voucherShop->title,
            'description' => $voucherMain->description ?? $voucherShop->description,
        ]);
        return $this->successResponse("Lấy dữ liệu thành công", $voucherMain ? $voucherMain : $voucherShop);
    }


    public function get_voucher_by_user(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $vouchers = Voucher::where('user_id', $user->id)->get();
        if ($vouchers->isEmpty()) {
            return $this->errorResponse("Không tồn tại voucher nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", $vouchers);
    }   
}
