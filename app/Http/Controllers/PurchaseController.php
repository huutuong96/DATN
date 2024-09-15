<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use App\Models\voucher_to_main;
use App\Models\UsersModel;
use App\Models\AddressModel;
use App\Models\RanksModel;
use App\Models\Tax;
use App\Models\platform_fees;
use App\Models\order_tax_details;
use App\Models\order_fee_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmOder;
use App\Mail\ConfirmOderToCart;
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
            $stateTax = $this->calculateStateTax($totalPrice);
            $totalPrice += $stateTax;
            $this->addStateTaxToOrder($order, $stateTax);
            $this->addOrderFeesToTotal($order, $totalPrice);
            DB::commit();

            Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));
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
        $shippingType = $request->shipping_type ?? 'standard';
        $insuranceOptions = $request->insurance_options ?? [];

        $distance = $this->distanceService->calculateDistance($originLat, $originLng, $destinationLat, $destinationLng);

        if ($distance === null) {
            return response()->json(['error' => 'Unable to calculate distance'], 400);
        }

        // Xác định loại vùng dựa trên khoảng cách
        $zoneType = $this->determineZoneType($distance);

        $shippingFee = $this->distanceService->calculateShippingFee($distance, $zoneType, $shippingType, $insuranceOptions);

        return $shippingFee;
    }

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
        try {
            $allOrders = [];
            $allOrderDetails = [];
            $allProduct = [];
            $allQuantity = [];
            $totalQuantity = 0;
            $grandTotalPrice = 0;
            // dd($request->carts);
            DB::beginTransaction();
            $order = $this->createOrder($request);
            foreach ($request->carts as $cart) {


                $product = $this->getProduct($cart['shop_id'], $cart['product_id']);
                $this->checkProductAvailability($product, $cart['quantity']);

                $totalPrice = $this->calculateTotalPrice($product, $cart['quantity']);

                // $order = $this->createOrder($cart, $voucherId, $request->delivery_address);

                $orderDetail = $this->createOrderDetail($order, $product, $cart['quantity'], $totalPrice, $cart['shop_id']);
                $product->decrement('quantity', $cart['quantity']);



                $allProduct[] = $product;
                $allQuantity[] = $cart['quantity'];
                $allOrders[] = $order;
                $allOrderDetails[] = $orderDetail;
                $totalQuantity += $cart['quantity'];
                $grandTotalPrice += $totalPrice;

            }
            $voucherId = $this->applyVouchersToCart($voucherToMainCode, $voucherToShopCode, $grandTotalPrice);
            $order->voucher_id = $voucherId;
            $order->save();

            $shipFee = $this->calculateShippingFee($request);
            $point = $this->add_point_to_user();
            $checkRank = $this->check_point_to_user();
            $totalPrice = $this->discountsByRank($checkRank, $totalPrice);
            $totalPrice += $shipFee;
            $this->addOrderFeesToTotal($order, $grandTotalPrice);
            DB::commit();
            Mail::to(auth()->user()->email)->send(new ConfirmOderToCart($allOrders, $allOrderDetails, $allProduct, $allQuantity, $totalQuantity, $grandTotalPrice));

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
                ->first();
    }

    private function checkProductAvailability($product, $quantity)
    {
        // dd($product);
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
                $voucherId['main'] = $voucherToMain->id;
                $this->updateVoucherQuantity($voucherToMain);
            }
        }

        if ($voucherToShopCode) {
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();
            if ($voucherToShop) {
                $totalPrice -= ($totalPrice * $voucherToShop->ratio / 100);
                $voucherId['shop'] = $voucherToShop->id;
                $this->updateVoucherQuantity($voucherToShop);
            }
        }

        return $voucherId;
    }
    private function applyVouchersToCart($voucherToMainCode, $voucherToShopCode, &$totalPrice)
    {
        // dd($voucherToShopCode);
        $voucherId = ['main' => null, 'shop' => null];

        if ($voucherToMainCode) {
            $voucherToMain = voucher_to_main::where('code', $voucherToMainCode)->first();
            if ($voucherToMain) {
                $totalPrice -= ($totalPrice * $voucherToMain->ratio / 100);
                $voucherId['main'] = $voucherToMain->id;
                $this->updateVoucherQuantity($voucherToMain);
            }
        }

        if ($voucherToShopCode) {
            $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();
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
            'status' => 1,
        ]);
        return $order;
    }

    private function createOrderDetail($order, $product, $quantity, $totalPrice,)
    {
        return OrderDetailsModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'subtotal' => $totalPrice,
            'status' => 1,
        ]);
    }


    private function determineZoneType($distance) {
        if ($distance <= 10) {
            return 'noi_thanh_hcm';
        } elseif ($distance <= 30) {
            return 'ngoai_thanh_hcm';
        } else {
            return 'tinh';
        }
    }

    private function calculateStateTax($totalPrice)
    {
        $taxes = Tax::all();
        $totalTaxAmount = 0;

        foreach ($taxes as $tax) {
            $taxAmount = $totalPrice * $tax->rate;
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

    private function calculateOrderFees($order, $totalPrice)
    {
        $platformFees = platform_fees::all();
        $totalFeeAmount = 0;

        foreach ($platformFees as $fee) {
            $feeAmount = $totalPrice * $fee->rate;
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
        $order->update(['net_amount' => $newTotal]);

        return $newTotal;
    }

}
