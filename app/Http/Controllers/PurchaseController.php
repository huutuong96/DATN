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
            $order = $this->createOrder($request, $voucherId, $request->delivery_address);
            $orderDetail = $this->createOrderDetail($order, $product, $request->quantity, $totalPrice);
            $product->decrement('quantity', $request->quantity);
            DB::commit();
            Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));
            $this->add_point_to_user();
            $this->check_point_to_user();
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

        // return response()->json([
        //     'distance_km' => $distance,
        //     'zone_type' => $zoneType,
        //     'shipping_fee' => $shippingFee,
        //     'shipping_type' => $shippingType,
        //     'insurance_options' => $insuranceOptions
        // ]);
        return $shippingFee;
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
    //         $voucherId = $this->applyVouchers($voucherToMainCode, $voucherToShopCode, $totalPrice);
    //         $order = $this->createOrder($request, $voucherId, $request->delivery_address);
    //         $orderDetail = $this->createOrderDetail($order, $product, $request->quantity, $totalPrice);
    //         $product->decrement('quantity', $request->quantity);
    //         DB::commit();
    //         Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));
    //         $this->add_point_to_user();
    //         $this->check_point_to_user();
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

    public function purchaseToCart(Request $request)
    {
        // $carts = [
        //     [
        //         'product_id' => 30,
        //         'quantity' => 10,
        //         'shop_id' => 3,
        //         'payment_id' => 9,
        //         'ship_id' => 3,
        //     ],                                       // DỮ LIỆU MẪU ĐẦU VÀO CỦA GIỎ HÀNG
        //     [
        //         'product_id' => 31,
        //         'quantity' => 2,
        //         'shop_id' => 3,
        //         'payment_id' => 9,
        //         'ship_id' => 3,
        //     ],
        // ];
        // dd($request->carts);
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
            foreach ($request->carts as $cart) {
                DB::beginTransaction();

                $product = $this->getProduct($cart['shop_id'], $cart['product_id']);
                // dd('ok');
                $this->checkProductAvailability($product, $cart['quantity']);

                $totalPrice = $this->calculateTotalPrice($product, $cart['quantity']);
                $voucherId = $this->applyVouchers($voucherToMainCode, $voucherToShopCode, $totalPrice);

                $order = $this->createOrder($cart, $voucherId, $request->delivery_address);
                $orderDetail = $this->createOrderDetail($order, $product, $cart['quantity'], $totalPrice);
                $product->decrement('quantity', $cart['quantity']);


                $allProduct[] = $product;
                $allQuantity[] = $cart['quantity'];
                $allOrders[] = $order;
                $allOrderDetails[] = $orderDetail;
                $totalQuantity += $cart['quantity'];
                $grandTotalPrice += $totalPrice;
                DB::commit();
            }
            // dd($allOrderDetails);
            Mail::to(auth()->user()->email)->send(new ConfirmOderToCart($allOrders, $allOrderDetails, $allProduct, $allQuantity, $totalQuantity, $grandTotalPrice));
            // Mail::to(auth()->user()->email)->send(new ConfirmOder($order, $orderDetail, $product, $request->quantity, $totalPrice));
            $this->add_point_to_user();
            $this->check_point_to_user();
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
    }
    private function check_point_to_user()
    {
        $userId = auth()->id();
        $user =  UsersModel::find($userId);
        if($user->point >= 1000){
            $user->update([
                'rank' => "Bạc",
            ]);
        }
        if($user->point >= 2000){
            $user->update([
                'rank' => "Vàng",
            ]);
        }
        if($user->point >= 3000){
            $user->update([
                'rank' => "Bạch Kim",
            ]);
        }
        if($user->point >= 4000){
            $user->update([
                'rank' => "Kim Cương",
            ]);
        }
        if($user->point >= 5000){
            $user->update([
                'rank' => "Vip",
            ]);
        }
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
                $voucherId['shop'] = $voucherToMain->id;
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

    private function createOrder($cart, $voucherId, $delivery_address)
    {
        $address = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
        $order = OrdersModel::create([
            'payment_id' => $cart['payment_id'],
            'user_id' => auth()->id(),
            'shop_id' => $cart['shop_id'],
            'voucher_id' => json_encode($voucherId),
            'ship_id' => $cart['ship_id'],
            'delivery_address' => $delivery_address ?? $address->address,
            'status' => 1,
        ]);
        return $order;
    }

    private function createOrderDetail($order, $product, $quantity, $totalPrice)
    {
        return OrderDetailsModel::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'subtotal' => $totalPrice,
            'status' => 1,
        ]);
    }

    public function ShipFee(Request $request){
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

        return response()->json([
            'distance_km' => $distance,
            'zone_type' => $zoneType,
            'shipping_fee' => $shippingFee,
            'shipping_type' => $shippingType,
            'insurance_options' => $insuranceOptions
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


}
