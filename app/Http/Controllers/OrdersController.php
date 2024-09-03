<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\OrdersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Cache::remember('all_orders', 60 * 60, function () {
            return OrdersModel::all();
        });

        if ($orders->isEmpty()) {
            return $this->errorResponse("Không tồn tại Order nào", 404);
        }

        return $this->successResponse('Lấy dữ liệu thành công', $orders);
    }

    public function indexOrderToShop($id)
    {
        $orders = Cache::remember('orders_shop_' . $id, 60 * 60, function () use ($id) {
            return OrdersModel::where('shop_id', $id)->get();
        });

        if ($orders->isEmpty()) {
            return $this->errorResponse("Không tồn tại Order nào", 404);
        }

        return $this->successResponse('Lấy dữ liệu thành công', $orders);
    }

    public function show(string $id)
    {
        $order = Cache::remember('order_' . $id, 60 * 60, function () use ($id) {
            return OrdersModel::find($id);
        });

        if (!$order) {
            return $this->errorResponse("Order không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $order);
    }

    public function update(OrderRequest $request, string $id)
    {
        $order = Cache::remember('order_' . $id, 60 * 60, function () use ($id) {
            return OrdersModel::find($id);
        });

        if (!$order) {
            return $this->errorResponse("Order không tồn tại", 404);
        }

        $dataUpdate = [
            'status' => $request->status ?? $order->status,
            'update_by' => auth()->id()
        ];

        try {
            $order->update($dataUpdate);
            Cache::forget('order_' . $id);
            Cache::forget('all_orders');
            Cache::forget('orders_shop_' . $order->shop_id);
            return $this->successResponse("Order đã được cập nhật", $order);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật Order không thành công", $th->getMessage());
        }
    }

    public function destroy(string $id)
    {
        $order = Cache::remember('order_' . $id, 60 * 60, function () use ($id) {
            return OrdersModel::find($id);
        });

        if (!$order) {
            return $this->errorResponse("Order không tồn tại", 404);
        }

        try {
            $order->update(['status' => 101]);
            Cache::forget('order_' . $id);
            Cache::forget('all_orders');
            Cache::forget('orders_shop_' . $order->shop_id);
            return $this->successResponse("Order đã được xóa");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa Order không thành công", $th->getMessage());
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
