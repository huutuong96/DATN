<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use App\Models\voucher_to_main;
use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmOder;
use Tymon\JWTAuth\Facades\JWTAuth;
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

        $voucherToMainCode = null;
        $voucherToShopCode = null;

        if ($request->voucherToMainCode) {
            $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode, 'main');
            if (!$voucherToMainCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mã giảm giá chính không hợp lệ',
                ], 400);
            }
        }

        if ($request->voucherToShopCode) {
            $voucherToShopCode = $this->getValidVoucherCode($request->voucherToShopCode, 'shop');
            if (!$voucherToShopCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mã giảm giá cửa hàng không hợp lệ',
                ], 400);
            }
        }


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

            Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));
            $this->add_point_to_user();
            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công',
                'data' => $order,
                'point' => auth()->user()->point
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

    private function add_point_to_user()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user = UsersModel::find($user->id);
        $user->update([
            'point' => $user->point + 100,
        ]);
    }

    private function getValidVoucherCode($code, $type)
    {
        if ($type === 'main') {
            $voucher = voucher_to_main::where('code', $code)
            ->where('quantity', '>=', 1)
            ->where('status', 1)
            ->first();
            return $voucher ? $voucher->code : null;
        }
        if ($type === 'shop') {
            $voucher = VoucherToShop::where('code', $code)
            ->where('quantity', '>=', 1)
            ->where('status', 1)
            ->first();
            return $voucher ? $voucher->code : null;
        }
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
            $voucherToMain = voucher_to_main::where('code', $voucherToMainCode)->first();
            if ($voucherToMain) {
                $totalPrice -= ($totalPrice * $voucherToMain->ratio / 100);
                $voucherId['shop'] = $voucherToMain->id;
                $this->updateVoucherQuantity($voucherToMain);
            }
        }

        if ($voucherToShopCode) {
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)
                ->where('status', 1)
                ->first();
            if ($voucherToShop) {
                $totalPrice -= ($totalPrice * $voucherToShop->ratio / 100);
                $voucherId['shop'] = $voucherToShop->id;
                $this->updateVoucherQuantity($voucherToShop);
            }
        }

        return $voucherId;
    }

    private function updateVoucherQuantity($voucher)
    {
        // dd($voucher);
        if ($voucher) {
            $voucher->decrement('quantity');
            if ($voucher->quantity <= 0) {
                $voucher->update(['status' => 0]);
            }
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
