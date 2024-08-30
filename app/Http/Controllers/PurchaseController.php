<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\PaymentsModel;
use App\Models\OrderDetailModel;
use Illuminate\Http\Request;
use App\Models\ProducttoshopModel;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'ship_id' => 'required|exists:ships,id',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $productToShop = ProducttoshopModel::where('product_id', $product->id)->first();
            if (!$productToShop) {
                throw new \Exception('Sản phẩm không thuộc về shop nào');
            }
            $shopId = $productToShop->shop_id;

            if ($product->quantity < $request->quantity) {
                throw new \Exception('Không đủ hàng');
            }

            $totalPrice = $product->sale_price && $product->sale_price < $product->price
                            ? $product->sale_price * $request->quantity
                            : $product->price * $request->quantity;

            // Create payment
            $payment = PaymentsModel::create([
                'name' => $request->payment_method,
                'status' => 1,
            ]);

            // Create order
            $order = OrdersModel::create([
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
                'shop_id' => $shopId,
                'ship_id' => $request->ship_id,
                'status' => 1,
                'create_by' => auth()->id(),
            ]);

            // Create order detail
            $orderDetail = OrderDetailModel::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'sale_price' => $product->sale_price,
                'total' => $totalPrice,
            ]);

            // Update product quantity
            $product->decrement('quantity', $request->quantity);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công',
                'data' => $order
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Đặt hàng thất bại',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
