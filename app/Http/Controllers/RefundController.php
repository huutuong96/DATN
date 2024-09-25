<?php

namespace App\Http\Controllers;

use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\RefundModel;
use App\Models\RefundItemModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Shop_manager;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function createRefund(Request $request, $orderId)
    {
        // Kiểm tra xem user có phải là người đã đặt đơn hàng hay không
        $order = OrdersModel::with('user')->findOrFail($orderId);
        if ($order->user_id !== auth()->user()->id) {
            return response()->json(['message' => 'Bạn không thể yêu cầu hoàn tiền cho đơn hàng này'], 403);
        }
        $productsByShop = [];
        foreach ($request->products as $product) {
            $orderDetail = OrderDetailsModel::findOrFail($product['order_detail_id']); // Lấy chi tiết sản phẩm
            $shopId = $orderDetail->shop_id; // Lấy shop_id từ order_detail
    // dd($shopId);
            if (!isset($productsByShop[$shopId])) {
                $productsByShop[$shopId] = []; 
            }
            $productsByShop[$shopId][] = [
                'order_detail_id' => $orderDetail->id,
                'refund_amount' => $orderDetail->subtotal, // Sử dụng subtotal
            ];
        }
    
        // Tạo yêu cầu hoàn tiền cho mỗi shop
        foreach ($productsByShop as $shopId => $products) {
            // Tính toán tổng số tiền hoàn cho shop hiện tại
            $refund = RefundModel::create([
                'order_id' => $order->id,
                'user_id' => auth()->user()->id,
                'shop_id' => $shopId, 
                'refund_reason' => $request->input('refund_reason'),
                'refund_status' => 'yêu cầu hoàn tiền ',
               
            ]);
            // Tạo các sản phẩm yêu cầu hoàn tiền từ order_details
            foreach ($products as $product) {
                RefundItemModel::create([
                    'refund_id' => $refund->id,
                    'order_detail_id' => $product['order_detail_id'],
                    'refund_amount' => $product['refund_amount'], // Số tiền hoàn đã được tính toán
                ]);
            }
        }
        $order->update(['status' => '1']);
    
       
        return response()->json(['message' => 'Yêu cầu hoàn tiền đã được tạo thành công']);
    }
    
    
public function approveRefund(Request $request, $refundId)
{    
    $refund = RefundModel::findOrFail($refundId);
    $checkOwnerShop = new ShopController;

     if (!$checkOwnerShop->IsOwnerShop($refund->shop_id)) {
        // dd($refund->shop_id);
        return response()->json(['message' => 'Bạn không có quyền duyệt yêu cầu hoàn tiền này'], 403);
    }
    $refund->update([
        'refund_status' => 'đã hoàn tiền',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);
    $refund->order->update(['status' => '2']);

    return response()->json(['message' => 'Yêu cầu hoàn tiền đã được duyệt']);
}





}
