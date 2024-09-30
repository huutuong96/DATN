<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use App\Models\VoucherToMain;
use App\Models\UsersModel;
use App\Models\AddressModel;
use App\Models\RanksModel;
use App\Models\Tax;
use App\Models\platform_fees;
use App\Models\order_tax_details;
use App\Models\order_fee_details;
use App\Models\ProducttocartModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmOder;
use App\Mail\ConfirmOderToCart;
use App\Models\Cart_to_usersModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Services\DistanceCalculatorService;

class PurchaseController extends Controller
{
    protected $distanceService;
    public function __construct(DistanceCalculatorService $distanceService)
    {
        $this->distanceService = $distanceService;
    }
    // public function purchase(Request $request)
    // {
    //     $voucherToMainCode = null;
    //     $voucherToShopCode = null;
    //     if ($request->voucherToMainCode) {
    //         $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode, 'main');
    //         if (!$voucherToMainCode) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Mã giảm giá này không hợp lệ',
    //             ], 400);
    //         }
    //     }
    //     if ($request->voucherToShopCode) {
    //         $voucherToShopCode = $this->getValidVoucherCode($request->voucherToShopCode, 'shop');
    //         if (!$voucherToShopCode) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Mã giảm giá cửa hàng không hợp lệ',
    //             ], 400);
    //         }
    //     }
    //     try {
    //         DB::beginTransaction();
    //         $product = $this->getProduct($request->shop_id, $request->product_id);
    //         $this->checkProductAvailability($product, $request->quantity);
    //         $totalPrice = $this->calculateTotalPrice($product, $request->quantity);
    //         $shipFee = $this->calculateShippingFee($request);
    //         $totalPrice += $shipFee;
    //         $voucherId = $this->applyVouchers($voucherToMainCode, $voucherToShopCode, $totalPrice);
    //         $order = $this->createOrder($request, $voucherId);
    //         $order->voucher_id = $voucherId;
    //         $order->save();
    //         $orderDetail = $this->createOrderDetail($order, $product, $request->quantity, $totalPrice);
    //         $product->decrement('quantity', $request->quantity);
    //         $point = $this->add_point_to_user();
    //         $checkRank = $this->check_point_to_user();
    //         $totalPrice = $this->discountsByRank($checkRank, $totalPrice);
    //         $customerPay = $totalPrice;
    //         $stateTax = $this->calculateStateTax($totalPrice);
    //         $totalPrice -= $stateTax;
    //         $this->addStateTaxToOrder($order, $stateTax);
    //         $this->addOrderFeesToTotal($order, $totalPrice);
    //         DB::commit();

    //         Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $customerPay));
    //         $notificationData = [
    //             'type' => 'main',
    //             'title' => 'Đặt hàng thành công',
    //             'description' => 'Bạn đã đặt hàng thành công, đơn hàng của bạn đang được xử lý',
    //             'user_id' => auth()->id(),
    //         ];
    //         $notificationController = new NotificationController();
    //         $notification = $notificationController->store(new Request($notificationData));
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Đặt hàng thành công',
    //             'data' => [
    //                 'order' => $order,
    //                 'orderDetail' => $orderDetail,
    //                 'product' => $product,
    //                 'quantity' => $request->quantity,
    //                 // 'shipFee' => $shipFee,
    //                 'totalPrice' => $totalPrice,
    //             ],
    //             'point' => auth()->user()->point,
    //             'notification' => $notification
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Đặt hàng thất bại',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    public function purchase(Request $request)
    {
        $voucherToMainCode = null;
        $voucherToShopCode = null;
        if ($request->voucherToMainCode) {
            $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode, 'main');
            if (!$voucherToMainCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mã giảm giá này không hợp lệ',
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
            $shipFee = $this->calculateShippingFee($request);
            $totalPrice += $shipFee;
            $voucherId = $this->applyVouchers($voucherToMainCode, $voucherToShopCode, $totalPrice);
            $order = $this->createOrder($request, $voucherId);
            $order->voucher_id = $voucherId;
            $order->save();
            $orderDetail = $this->createOrderDetail($order, $product, $request->quantity, $totalPrice);
            $product->decrement('quantity', $request->quantity);
            $point = $this->add_point_to_user();
            $checkRank = $this->check_point_to_user();
            $totalPrice = $this->discountsByRank($checkRank, $totalPrice);
            $customerPay = $totalPrice;
            $stateTax = $this->calculateStateTax($totalPrice);
            $totalPrice -= $stateTax;
            $this->addStateTaxToOrder($order, $stateTax);
            $this->addOrderFeesToTotal($order, $totalPrice);
            DB::commit();

            Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $customerPay));
            $notificationData = [
                'type' => 'main',
                'title' => 'Đặt hàng thành công',
                'description' => 'Bạn đã đặt hàng thành công, đơn hàng của bạn đang được xử lý',
                'user_id' => auth()->id(),
            ];
            $notificationController = new NotificationController();
            $notification = $notificationController->store(new Request($notificationData));
            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order' => $order,
                    'orderDetail' => $orderDetail,
                    'product' => $product,
                    'quantity' => $request->quantity,
                    'shipFee' => $shipFee,
                    'totalPrice' => $totalPrice,
                ],
                'point' => auth()->user()->point,
                'notification' => $notification
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

    private function calculateShippingFee(Request $request)
    {
        $originLat = $request->origin_lat;
        $originLng = $request->origin_lng;
        $destinationLat = $request->destination_lat;
        $destinationLng = $request->destination_lng;
        // $shippingType = $request->shipping_type;
        $insuranceOptions = $request->insurance_options ?? [];

        $distance = $this->distanceService->calculateDistance($originLat, $originLng, $destinationLat, $destinationLng);
        if ($distance === null) {
            return response()->json(['error' => 'Unable to calculate distance'], 400);
        }

        // Xác định loại vùng dựa trên khoảng cách

        $shippingFee = $this->distanceService->calculateShippingFee($distance);
        return $shippingFee;
    }

    // public function purchaseToCart(Request $request)
    // {
    //     $voucherToMainCode = null;
    //     $voucherToShopCode = null;

    //     if ($request->voucherToMainCode) {
    //         $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode, 'main');
    //         if (!$voucherToMainCode) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Mã giảm giá này không hợp lệ',
    //             ], 400);
    //         }
    //     }
    //     if ($request->voucherToShopCode) {
    //         $voucherToShopCode = $this->getValidVoucherCode($request->voucherToShopCode, 'shop');
    //         if (!$voucherToShopCode) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Mã giảm giá cửa hàng không hợp lệ',
    //             ], 400);
    //         }
    //     }
    //     try {
    //         $allOrders = [];
    //         $allOrderDetails = [];
    //         $allProduct = [];
    //         $allQuantity = [];
    //         $totalQuantity = 0;  // giá của tổng các sản phẩm
    //         $grandTotalPrice = 0; //giá user phải trả
    //         $carts = ProducttocartModel::whereIn('id', $request->carts)->get();
    //         DB::beginTransaction();
    //         foreach ($carts as $cart) {
    //             $variant = $this->getProduct($cart['product_id'], $cart['variant_id'], $cart['quantity']);
    //             $this->checkProductAvailability($variant, $cart['quantity']);
    //             $totalPrice = $this->calculateTotalPrice($variant, $cart['quantity']);
    //             $order = $this->createOrder($request);
    //             $orderDetail = $this->createOrderDetail($order, $variant, $cart['quantity'], $totalPrice);
    //             $variant->decrement('stock', $cart['quantity']);
    //             $allProduct[] = $variant->product;
    //             $allQuantity[] = $cart['quantity'];
    //             $allOrders[] = $order;
    //             $allOrderDetails[] = $orderDetail;
    //             $totalQuantity += $cart['quantity'];
    //             $grandTotalPrice += $totalPrice;
    //         };
    //         $voucherId = $this->applyVouchersToCart($voucherToMainCode, $voucherToShopCode, $grandTotalPrice);
    //         $order->voucher_id = $voucherId;
    //         $order->update(['status' => OrdersModel::STATUS_PENDING_CONFIRMATION]);
    //         $shipFee = $this->calculateShippingFee($request);
    //         $point = $this->add_point_to_user();
    //         $checkRank = $this->check_point_to_user();
    //         $totalPriceOfShop = $grandTotalPrice;
    //         $grandTotalPrice = $this->discountsByRank($checkRank, $grandTotalPrice);
    //         $grandTotalPrice += $shipFee;
    //         $tax = $this->calculateStateTax($grandTotalPrice);
    //         $totalPriceOfShop -= $tax;
    //         $this->addStateTaxToOrder($order, $tax);
    //         $this->addOrderFeesToTotal($order, $totalPriceOfShop);
    //         $order->total_amount = $grandTotalPrice;
    //         $order->save();
    //         DB::commit();
    //         Mail::to(auth()->user()->email)->send(new ConfirmOderToCart($allOrders, $allOrderDetails, $allProduct, $allQuantity, $totalQuantity, $grandTotalPrice));

    //         $notificationData = [
    //             'type' => 'main',
    //             'title' => 'Đặt hàng thành công',
    //             'description' => 'Bạn đã đặt hàng thành công, đơn hàng của bạn đang được xử lý',
    //             'user_id' => auth()->id(),
    //         ];
    //         $notificationController = new NotificationController();
    //         $notification = $notificationController->store(new Request($notificationData));
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Đặt hàng thành công',
    //             'status_order' => $order->status_label,
    //             'data' => [
    //                 'order' => $order,
    //                 'orderDetail' => $orderDetail,
    //                 'product' => $variant->product_id,
    //                 'variant' => $variant->id,
    //                 'quantity' => $request->quantity,
    //                 'totalPrice' => $grandTotalPrice,
    //             ],
    //             'point' => auth()->user()->point,
    //             'notification' => $notification
    //         ], 200);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Đặt hàng thất bại',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    public function purchaseToCart(Request $request)
{
        $voucherToMainCode = null;
        $voucherToShopCode = null;

        if ($request->voucherToMainCode) {
            $voucherToMainCode = $this->getValidVoucherCode($request->voucherToMainCode, 'main');
            if (!$voucherToMainCode) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mã giảm giá này không hợp lệ',
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

    // Validate vouchers
    if ($voucherToMainCode && !$this->getValidVoucherCode($voucherToMainCode, 'main')) {
        return response()->json(['status' => false, 'message' => 'Mã giảm giá chung không hợp lệ'], 400);
    }

    try {
        DB::beginTransaction();

        $carts = ProducttocartModel::whereIn('id', $request->carts)->with('product')->with('variant')->get();

        $ordersByShop = [];
        $grandTotalPrice = 0;
        $totalQuantity = 0;

        // Group cart items by shop
        foreach ($carts as $cart) {
            $shopId = $cart->shop_id;
            if (!isset($ordersByShop[$shopId])) {
                $ordersByShop[$shopId] = [
                    'items' => [],
                    'totalPrice' => 0,
                    'order' => null,
                ];
            }
            $ordersByShop[$shopId]['items'][] = $cart;
        }

        // Process each shop's order
        foreach ($ordersByShop as $shopId => &$shopOrder) {
            $order = $this->createOrder($request);
            $order->shop_id = $shopId;
            $order->save();
            $shopOrder['order'] = $order;
            $shopOrder['orderDetails'] = [];
            $shopTotalPrice = 0;
            foreach ($shopOrder['items'] as $cart) {
                $variant = $this->getProduct($cart->product_id, $cart->variant_id, $cart->quantity);
                $this->checkProductAvailability($variant, $cart->quantity);
                $totalPrice = $this->calculateTotalPrice($variant, $cart->quantity);

                $orderDetail = $this->createOrderDetail($order, $variant, $cart->quantity, $totalPrice);
                $shopOrder['orderDetails'][] = $orderDetail;

                $variant->decrement('stock', $cart->quantity);

                $shopTotalPrice += $totalPrice;
                $totalQuantity += $cart->quantity;
            }
            $shopOrder['totalPrice'] = $shopTotalPrice;
            $grandTotalPrice += $shopTotalPrice;
            // Additional processing for each shop's order (e.g., shipping, taxes)
            $shipFee = $this->calculateShippingFee($request);
            // dd($shipFee);
            $shopTotalPrice += $shipFee;
            $tax = $this->calculateStateTax($shopTotalPrice);
            $this->addStateTaxToOrder($order, $tax);
            $this->addOrderFeesToTotal($order, $shopTotalPrice - $tax);

            $order->total_amount = $shopTotalPrice;
            $order->status = OrdersModel::STATUS_PENDING_CONFIRMATION;
            if ($voucherToShopCode) {
                $grandTotalPrice = $this->applyVouchersToShop($voucherToShopCode, $shopTotalPrice, $shopId);
            }
            $order->save();
        }
        if ($voucherToMainCode) {
            $grandTotalPrice = $this->applyVouchersToMain($voucherToMainCode, $grandTotalPrice);
        }
        // User point and rank processing
        $point = $this->add_point_to_user();
        $checkRank = $this->check_point_to_user();
        $grandTotalPrice = $this->discountsByRank($checkRank, $grandTotalPrice);

        DB::commit();
        Mail::to(auth()->user()->email)->send(new ConfirmOderToCart($ordersByShop, $grandTotalPrice, $carts, $totalQuantity, $shipFee));
        return response()->json([
            'status' => true,
            'message' => 'Đặt hàng thành công',
            'data' => [
                'orders' => array_map(function($shopOrder) {
                    return $shopOrder['order'];
                }, $ordersByShop),
                'totalPrice' => $grandTotalPrice,
            ],
            'point' => auth()->user()->point,
            // 'notification' => $notification
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
        $userId = auth()->id();
        $user =  UsersModel::find($userId);
        $user->update([
            'point' => $user->point + 100,
        ]);
        return $user->point;
    }
    private function check_point_to_user()
    {
        $userId = auth()->id();
        $user = UsersModel::find($userId);
        $ranks = RanksModel::orderBy('condition', 'desc')->get();

        foreach ($ranks as $rank) {
            if ($user->point >= $rank->condition) {
                $user->update(['rank_id' => $rank->id]);
                break;
            }
        }
        return $user->rank_id;
    }
    private function discountsByRank($checkRank, $totalPrice)
    {
        $rank = RanksModel::where('id', $checkRank)->first();
        if (!$rank) {
            return $totalPrice; // Không có rank, không áp dụng giảm giá
        }
        $discountPercentage = $rank->value; // Giả sử value là phần trăm giảm giá (0.2 = 20%)
        $maxDiscount = $rank->limitValue; // Giả sử limitValue là giá trị giảm tối đa
        $discountAmount = $totalPrice * $discountPercentage;
        $discountAmount = min($discountAmount, $maxDiscount); // Đảm bảo giảm giá không vượt quá giới hạn
        $discountedPrice = $totalPrice - $discountAmount;

        return $discountedPrice;
    }
    private function getValidVoucherCode($code, $type)
    {

        if ($type === 'main') {
            $voucher = voucherToMain::where('code', $code)
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

    private function getProduct($productId, $variantId, $quantity)
    {

        $result = Product::with(['variants' => function ($query) use ($variantId) {
            $query->where('id', $variantId);
        }])
        ->where('id', $productId)
        ->first();
        $result->increment('sold_count', $quantity);
        $variant = $result->variants->first();

        return $variant;

    }


    private function checkProductAvailability($variant, $quantity)
    {
        // dd($product);
        if ($variant->stock < $quantity) {
            throw new \Exception('Không đủ hàng');
        }
    }

    private function calculateTotalPrice($variant, $quantity)

    {
        $price = $variant->sale_price && $variant->sale_price < $variant->price
            ? $variant->sale_price
            : $variant->price;
        return $price * $quantity;
    }

    private function applyVouchers($voucherToMainCode, $voucherToShopCode, &$totalPrice)
    {
        $voucherId = ['main' => null, 'shop' => null];

        if ($voucherToMainCode) {
            $voucherToMain = voucherToMain::where('code', $voucherToMainCode)->first();
            if ($voucherToMain) {
                $discountAmount = min($totalPrice * $voucherToMain->ratio / 100, $voucherToMain->limitValue);
                $totalPrice -= $discountAmount;
                $voucherId['main'] = $voucherToMain->id;
                $this->updateVoucherQuantity($voucherToMain);
            }
        }

        if ($voucherToShopCode) {
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();
            if ($voucherToShop) {
                $discountAmount = min($totalPrice * $voucherToShop->ratio / 100, $voucherToShop->limitValue);
                $totalPrice -= $discountAmount;
                $voucherId['shop'] = $voucherToShop->id;
                $this->updateVoucherQuantity($voucherToShop);
            }
        }
        return $voucherId;
    }
    private function applyVouchersToShop($voucherToShopCode, &$totalPrice, $shopId)
    {
        // dd($voucherToShopCode);
        if ($voucherToShopCode) {
            // $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)
                                  ->where('status', 1)
                                  ->where('shop_id', $shopId)
                                  ->first();
            if ($voucherToShop) {
                $discountAmount = min($totalPrice * $voucherToShop->ratio / 100, $voucherToShop->limitValue);
                $totalPrice -= $discountAmount;
                $this->updateVoucherQuantity($voucherToShop);
            }
        }
        return $totalPrice;
    }
    private function applyVouchersToMain($voucherToMainCode, &$totalPrice)
    {
        // dd($voucherToShopCode);

        if ($voucherToMainCode) {
            $voucherToMain = voucherToMain::where('code', $voucherToMainCode)->first();
            if ($voucherToMain) {
                $totalPrice -= ($totalPrice * $voucherToMain->ratio / 100);
                $this->updateVoucherQuantity($voucherToMain);
            }
        }

        return $totalPrice;
    }


    private function updateVoucherQuantity($voucher)
    {
        if ($voucher) {
            $voucher->decrement('quantity');
            if ($voucher->quantity <= 0) {
                $voucher->update(['status' => 0]);
            }
        }
    }

    private function createOrder(Request $request)
    {

        $address = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
        $order = OrdersModel::create([
            'payment_id' => $request->payment_id,
            'user_id' => auth()->id(),
            'ship_id' => $request->ship_id,
            'delivery_address' => $request->delivery_address ?? $address->address,
            'shop_id' => $request->shop_id,
            'status' => 1,
        ]);
        return $order;
    }


    private function createOrderDetail($order, $variant, $quantity, $totalPrice)

    {
        // dd($variant);
        return OrderDetailsModel::create([
            'order_id' => $order->id,
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
            'quantity' => $quantity,
            'subtotal' => $totalPrice,
            'status' => 1,
        ]);
    }

    private function calculateStateTax($totalPriceOfShop)
    {
        $taxes = Tax::all();
        $totalTaxAmount = 0;

        foreach ($taxes as $tax) {

            $taxAmount = $totalPriceOfShop * $tax->rate;

            $totalTaxAmount += $taxAmount;
        }

        // Round to 2 decimal places
        $totalTaxAmount = round($totalTaxAmount, 2);

        return $totalTaxAmount;
    }

    private function addStateTaxToOrder($order, $taxAmount)
    {
        $taxes = Tax::all();
        foreach ($taxes as $tax) {
            order_tax_details::create([
                'order_id' => $order->id,
                'tax_id' => $tax->id,
                'amount' => $taxAmount
            ]);
        }
    }

    private function calculateOrderFees($order, $totalPriceOfShop)
    {
        $platformFees = platform_fees::all();
        $totalFeeAmount = 0;

        foreach ($platformFees as $fee) {
            $feeAmount = $totalPriceOfShop * $fee->rate;

            // dd( $feeAmount );
            $totalFeeAmount += $feeAmount;

            order_fee_details::create([
                'order_id' => $order->id,
                'platform_fee_id' => $fee->id,
                'amount' => round($feeAmount, 2)
            ]);
        }

        return round($totalFeeAmount, 2);
    }

    private function addOrderFeesToTotal($order, $totalPrice)
    {

        $feeAmount = $this->calculateOrderFees($order, $totalPrice);
        $newTotal = $totalPrice - $feeAmount;
        $taxAmount = $this->calculateStateTax($totalPrice);
        $newTotal = $newTotal - $taxAmount;
        $order->update(['net_amount' => $newTotal]);

        return $newTotal;
    }

}
