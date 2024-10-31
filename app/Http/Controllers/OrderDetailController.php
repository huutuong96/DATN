<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderDetailRequest;
use App\Models\OrderDetailsModel;
use Illuminate\Support\Facades\Cache;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderDetails = Cache::remember('all_order_details', 60 * 60, function () {
            return OrderDetailsModel::all();
        });
        if ($orderDetails->isEmpty()) {
            return $this->errorResponse("Không tồn tại chi tiết đơn hàng nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $orderDetails);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderDetailRequest $request)
    {
        "Thường là tự động tạo";
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orderDetail = Cache::remember('order_detail_' . $id, 60 * 60, function () use ($id) {
            return OrderDetailsModel::find($id);
        });

        if (!$orderDetail) {
            return $this->errorResponse("Chi tiết đơn hàng không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $orderDetail);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderDetailRequest $request, string $id)
    {
        $orderDetail = OrderDetailsModel::find($id);

        if (!$orderDetail) {
            return $this->errorResponse("Chi tiết đơn hàng không tồn tại", 404);
        }

        try {
            $orderDetail->update([
                "subtotal" => $request->subtotal,
                "status" => $request->status,
                'update_by' => auth()->id(),
            ]);

            Cache::forget('order_detail_' . $id);
            Cache::forget('all_order_details');
            return $this->successResponse("Đã cập nhật chi tiết đơn hàng", $orderDetail);
        } catch (\Exception $e) {
            return $this->errorResponse("Cập nhật chi tiết đơn hàng không thành công", $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orderDetail = OrderDetailsModel::find($id);

        if (!$orderDetail) {
            return $this->errorResponse("Chi tiết đơn hàng không tồn tại", 404);
        }

        try {
            $orderDetail->update(['status' => 101]);
            Cache::forget('order_detail_' . $id);
            Cache::forget('all_order_details');
            return $this->successResponse("Xóa chi tiết đơn hàng thành công");
        } catch (\Exception $e) {
            return $this->errorResponse("Xóa chi tiết đơn hàng không thành công", $e->getMessage());
        }
    }
}
