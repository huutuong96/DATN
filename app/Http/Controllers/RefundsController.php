<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use App\Models\RefundsModel;
use Illuminate\Http\Request;
use App\Models\OrderDetailsModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\ShopController;
use App\Models\Shop_manager;

class RefundsController extends Controller
{
    public function createRefund(Request $request, $orderId)
    {
        $order = OrdersModel::find($orderId);
        $user = JWTAuth::parseToken()->authenticate();
        if (!$order) {
            return response()->json(['error' => 'Đơn hàng không tồn tại'], 404);
        }

        if ($order->user_id != $user->id) {
            return response()->json(['error' => 'Bạn không có quyền tạo yêu cầu hoàn tiền cho đơn hàng này'], 403);
        }

        if ($order->status != 1) {
            return response()->json(['error' => 'Đơn hàng chưa giao hoặc không đủ điều kiện để hoàn tiền'], 400);
        }

        $productIds = $request->product_ids;

        foreach ($productIds as $productId) {
            $orderItem = OrderDetailsModel::where('order_id', $orderId)
                ->where('product_id', $productId)
                ->first();

            if (!$orderItem) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong đơn hàng'], 404);
            }

            RefundsModel::create([
                'order_item_id' => $orderItem->id,
                'user_id' => auth()->id(),
                'shop_id' => $orderItem->shop_id,
                'reason' => $request->reason,
                'refund_amount' => $orderItem->subtotal,
                'status' => 'pending',
            ]);
        }
        return response()->json(['message' => 'Yêu cầu hoàn tiền của bạn đang được xử lý'], 200);
    }


    public function approveRefund(Request $request)
    {
        $refundIds = $request->refund_ids;

        foreach ($refundIds as $refundId) {
            $processRefund = RefundsModel::find($refundId);
            $orderItem = OrderDetailsModel::where('id', $processRefund->order_item_id);
            if (!$processRefund) {
                return response()->json(['error' => 'Yêu cầu hoàn tiền không tồn tại'], 404);
            }
            $checkOwnerShop = new ShopController;

            if (!$checkOwnerShop->IsOwnerShop($processRefund->shop_id)) {
                return response()->json(['error' => 'Bạn không có quyền duyệt yêu cầu hoàn tiền này'], 403);
            }

            $processRefund->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $orderItem->update(['status' => 0]);
        }

        return response()->json(['message' => 'Yêu cầu hoàn tiền đã được duyệt'], 200);
    }

    public function getRefundDetails($orderId) {
        $refunds = RefundsModel::with('orderItem')->whereHas('orderItem', function($query) use ($orderId) {
            $query->where('order_id', $orderId);
        })->get();
    
        return response()->json($refunds, 200);
    }
}
