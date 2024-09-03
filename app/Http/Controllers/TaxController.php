<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;
use Illuminate\Support\Facades\Cache;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Cache::remember('all_taxes', 60 * 60, function () {
            return Tax::all();
        });

        if ($taxes->isEmpty()) {
            return $this->errorResponse("Không tồn tại thuế nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $taxes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaxRequest $request)
    {
        try {
            $tax = Tax::create($request->validated());
            Cache::forget('all_taxes');
            return $this->successResponse("Thêm thuế thành công", $tax);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm thuế không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tax = Cache::remember('tax_' . $id, 60 * 60, function () use ($id) {
            return Tax::find($id);
        });

        if (!$tax) {
            return $this->errorResponse("Không tồn tại thuế nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $tax);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaxRequest $request, string $id)
    {
        $tax = Cache::remember('tax_' . $id, 60 * 60, function () use ($id) {
            return Tax::find($id);
        });

        if (!$tax) {
            return $this->errorResponse("Thuế không tồn tại", 404);
        }

        try {
            $tax->update($request->validated());
            Cache::forget('tax_' . $id);
            Cache::forget('all_taxes');
            return $this->successResponse("Cập nhật thuế thành công", $tax);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật thuế không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tax = Tax::findOrFail($id);
            $tax->delete();
            Cache::forget('tax_' . $id);
            Cache::forget('all_taxes');
            return $this->successResponse("Xóa thuế thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa thuế không thành công", $th->getMessage());
        }
    }

    /**
     * Return a success response.
     */
    private function successResponse($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return an error response.
     */
    private function errorResponse($message, $error = null, $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error,
        ], $code);
    }
}
