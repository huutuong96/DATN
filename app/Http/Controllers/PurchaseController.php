<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\PaymentsModel;
use App\Models\OrderDetailsModel;
use Illuminate\Http\Request;
use App\Models\ProducttoshopModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        // $request->validate([
        //     'product_id' => 'required|exists:products,id',
        //     'quantity' => 'required|integer|min:1',
        //     'payment_id' => 'required',
        //     'ship_id' => 'required|exists:ships,id',
        // ]);
            $voucherToMain = voucher::where("code", $request->voucherToMainCode ?? null)->where("quantity", ">=", 1)->where("status", 1)->first();
            if($voucherToMain){
                $voucherToMainCode = $voucherToMain->code;
            }else{
                $voucherToMainCode = null;
            }

            $voucherToShop = voucher::where("code", $request->voucherToShopCode ?? null)->where("quantity", ">=", 1)->where("status", 1)->first();
            if($voucherToShop){
                $voucherToShopCode = $voucherToShop->code;
            }else{
                $voucherToShopCode = null;
            }

        // dd($voucherToMainCode, $voucherToShopCode);
        DB::beginTransaction();

        try {
            // $product = Product::findOrFail($request->product_id);
            // $productToShop = ProducttoshopModel::where('product_id', $product->id)->firstOrFail();
            // $shopId = $productToShop->shop_id;

            $product = Product::where('shop_id', $request->shop_id)
                   ->where('id', $request->product_id)
                   ->first();
            
            if ($product->quantity < $request->quantity) {
                throw new \Exception('Không đủ hàng');
            }

            $price = $product->sale_price && $product->sale_price < $product->price
                ? $product->sale_price
                : $product->price;
            $totalPrice = $price * $request->quantity;
            
            //combine voucher

            $checkVoucherToMain = voucher_to_main::where('code', $voucherToMainCode)->where("status", 1)->first();
            $checkVoucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where("status", 1)->first();

            if($checkVoucherToMain){
                $totalPrice = $totalPrice - ($totalPrice * $checkVoucherToMain->ratio / 100);
            };
            if($checkVoucherToShop){
                $totalPrice = $totalPrice - ($totalPrice * $checkVoucherToShop->ratio / 100);
            };

            // Create payment
            // $payment = PaymentsModel::create([
            //     'name' => $request->payment_method,
            //     'status' => 1,
            // ]);
            $voucher_id = [
                "main"=>($checkVoucherToMain->id ?? null),
                "shop"=>($checkVoucherToShop->id ?? null)
            ];

            // Create order
            $order = OrdersModel::create([
                'payment_id' => $request->payment_id,
                'user_id' => auth()->id(),
                'shop_id' => $request->shop_id,
                'voucher_id' => json_encode($voucher_id),
                'ship_id' => $request->ship_id,
                'status' => 1,

            ]);

            // Create order detail
            $orderDetail = OrderDetailsModel::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'subtotal' => $totalPrice,
                'status' => 1,
            ]);

            // Update product quantity
            $product->decrement('quantity', $request->quantity);

            DB::commit();
            if ($checkVoucherToMain) {
                $myVoucher = voucher::where("code", $checkVoucherToMain->code)->first();
                $myVoucher->quantity -= 1;
                if($myVoucher->quantity <= 0){
                    $myVoucher->status = 0;
                }
                $myVoucher->save();
            }
            if ($checkVoucherToShop) {
                $myVoucher = voucher::where("code", $checkVoucherToShop->code)->first();
                $myVoucher->quantity -= 1;
                if($myVoucher->quantity <= 0){
                    $myVoucher->status = 0;
                }
                $myVoucher->save();
            }

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

