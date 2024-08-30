<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmOder;
use Illuminate\Support\Facades\Mail;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_id' => 'required',
            'ship_id' => 'required|exists:ships,id',
        ]);

        $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode);
        $voucherToShopCode = $this->getValidVoucherCode($request->voucherToShopCode);

        try {
            DB::beginTransaction();

            $product = $this->getProduct($request->shop_id, $request->product_id);
            $this->checkProductAvailability($product, $request->quantity);

            $totalPrice = $this->calculateTotalPrice($product, $request->quantity);
            $voucherId = $this->applyVouchers($voucherToMainCode, $voucherToShopCode, $totalPrice);

            $order = $this->createOrder($request, $voucherId);
            $orderDetail = $this->createOrderDetail($order, $product, $request->quantity, $totalPrice);

            $product->decrement('quantity', $request->quantity);
            DB::commit();

            Mail::to($request->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));

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

    private function getValidVoucherCode($code)
    {
        $voucher = Voucher::where('code', $code)
            ->where('quantity', '>=', 1)
            ->where('status', 1)
            ->first();

        return $voucher ? $voucher->code : null;
    }

    private function getProduct($shopId, $productId)
    {
        return Product::where('shop_id', $shopId)
            ->where('id', $productId)
            ->firstOrFail();
    }

    private function checkProductAvailability($product, $quantity)
    {
        if ($product->quantity < $quantity) {
            throw new \Exception('Không đủ hàng');
        }
    }

    private function calculateTotalPrice($product, $quantity)
    {
        $price = $product->sale_price && $product->sale_price < $product->price
            ? $product->sale_price
            : $product->price;
        return $price * $quantity;
    }

    private function applyVouchers($voucherToMainCode, $voucherToShopCode, &$totalPrice)
    {
        $voucherId = ['main' => null, 'shop' => null];

        if ($voucherToMainCode) {
            $voucherToMain = Voucher::where('code', $voucherToMainCode)->first();
            $this->updateVoucherQuantity($voucherToMain);
            $voucherId['main'] = $voucherToMain->id;
        }

        if ($voucherToShopCode) {
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)
                ->where('status', 1)
                ->first();
            if ($voucherToShop) {
                $totalPrice -= ($totalPrice * $voucherToShop->ratio / 100);
                $voucherId['shop'] = $voucherToShop->id;
            }
        }

        return $voucherId;
    }

    private function updateVoucherQuantity($voucher)
    {
        $voucher->decrement('quantity');
        if ($voucher->quantity <= 0) {
            $voucher->update(['status' => 0]);
        }
    }

    private function createOrder($request, $voucherId)
    {
        return OrdersModel::create([
            'payment_id' => $request->payment_id,
            'user_id' => auth()->id(),
            'shop_id' => $request->shop_id,
            'voucher_id' => json_encode($voucherId),
            'ship_id' => $request->ship_id,
            'status' => 1,
        ]);
    }

    private function createOrderDetail($order, $product, $quantity, $totalPrice)
    {
        OrderDetailsModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'subtotal' => $totalPrice,
            'status' => 1,
        ]);
    }
}
