<?php

namespace App\Http\Controllers;

use App\Models\voucherToMain;
use App\Http\Requests\VoucherRequest;

class VoucherToMainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherMains = voucherToMain::all();

        if ($voucherMains->isEmpty()) {
            return $this->errorResponse('Không tồn tại voucher main nào');
        }

        return $this->successResponse('Lấy dữ liệu thành công', $voucherMains);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoucherRequest $request)
    {
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $request->image,
            'quantity' => $request->quantity,
            'limitValue' => $request->limitValue,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'status' => $request->status,
        ];
        try {
            $voucherMain = voucherToMain::create($dataInsert);
            return $this->successResponse("Thêm voucher main thành công", $voucherMain);
        } catch (\Throwable $th) {
            return $this->errorResponse('Thêm voucher main không thành công', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucherMain = voucherToMain::find($id);

        if (!$voucherMain) {
            return $this->errorResponse("Không tồn tại voucher main nào", null, 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $voucherMain);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VoucherRequest $request, string $id)
    {
        $voucherMain = voucherToMain::find($id);

        if (!$voucherMain) {
            return $this->errorResponse("Voucher main không tồn tại", null, 404);
        }

        try {
            $voucherMain->update($request->validated());
            return $this->successResponse("Cập nhật voucher main thành công", $voucherMain);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật voucher main không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $voucherMain = voucherToMain::find($id);

        if (!$voucherMain) {
            return $this->errorResponse("Voucher main không tồn tại", null, 404);
        }

        try {
            $voucherMain->delete();
            return $this->successResponse("Xóa voucher main thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa voucher main không thành công", $th->getMessage());
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
