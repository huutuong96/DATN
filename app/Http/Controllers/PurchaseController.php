<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\AddressModel;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use App\Models\VoucherToMain;
use App\Models\UsersModel;
use App\Models\RanksModel;
use App\Models\Tax;
use App\Models\platform_fees;
use App\Models\order_tax_details;
use App\Models\order_fee_details;
use App\Models\ProducttocartModel;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\ConfirmOder;
use App\Mail\ConfirmOderToCart;
use App\Models\Cart_to_usersModel;
use App\Models\ShipsModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Services\DistanceCalculatorService;
use Illuminate\Support\Facades\Http;


class PurchaseController extends Controller
{
    protected $distanceService;
    public function __construct(DistanceCalculatorService $distanceService)
    {
        $this->distanceService = $distanceService;
    }

    private function calculateShippingFee(Request $request, $ship_id)
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

        $shippingFee = $this->distanceService->calculateShippingFee($distance, $ship_id);
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
        $total_amount = 0;
        $addressUser = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
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
            $ship_id = ShipsModel::where('code', $cart->ship_code)->first();
            $order = $this->createOrder($request, $ship_id);
            $order->shop_id = $shopId;
            $order->save();
            $shopOrder['order'] = $order;
            $shopOrder['orderDetails'] = [];
            $shopTotalPrice = 0;
            $height = 0;
            $length = 0;
            $weight = 0;
            $width = 0;
            foreach ($shopOrder['items'] as $cart) {
                $variant = $this->getProduct($cart->product_id, $cart->variant_id, $cart->quantity);
                $this->checkProductAvailability($variant, $cart->quantity);
                $totalPrice = $this->calculateTotalPrice($variant, $cart->quantity);
                $orderDetail = $this->createOrderDetail($order, $variant, $cart->quantity, $totalPrice, $cart->product_id);
                $height += $orderDetail->height;
                $length += $orderDetail->length;
                $weight += $orderDetail->weight;
                $width += $orderDetail->width;
                $shopOrder['orderDetails'][] = $orderDetail;
                $variant->decrement('stock', $cart->quantity);
                $shopTotalPrice += $totalPrice;
                $totalQuantity += $cart->quantity;
            }
            $order->height = $height;
            $order->length = $length;
            $order->weight = $weight;
            $order->width = $width;
            $shopOrder['totalPrice'] = $shopTotalPrice;
            $grandTotalPrice += $shopTotalPrice;
            $shopData = Shop::find($shopId);
            $service = $this->get_infomaiton_services($shopData, $addressUser);
            $shipFee = $this->calculateOrderFees_giao_hang_nhanh($shopData, $addressUser, $service, $order, $shopTotalPrice);
            $grandTotalPrice += $shipFee;
            $tax = $this->calculateStateTax($shopTotalPrice);
            $this->addStateTaxToOrder($order, $tax);
            $this->addOrderFeesToTotal($order, $shopTotalPrice - $tax);
            $order->total_amount = $shopTotalPrice;
            $order->status = OrdersModel::STATUS_PENDING_CONFIRMATION;
            if ($voucherToShopCode) {
                $totalAdded = $this->applyVouchersToShop($voucherToShopCode, $shopTotalPrice, $shopId);
                $totalPrice -= $totalAdded;
            }
            $order->total_amount = $totalPrice;
            $total_amount += $order->total_amount;
            $order->save();
        }
        if ($voucherToMainCode) {
            $total_amount = $this->applyVouchersToMain($voucherToMainCode, $total_amount);
        }
        // User point and rank processing
        $point = $this->add_point_to_user();
        $checkRank = $this->check_point_to_user();
        $total_amount = $this->discountsByRank($checkRank, $total_amount);
        DB::commit();
        Mail::to(auth()->user()->email)->send(new ConfirmOderToCart($ordersByShop, $total_amount, $carts, $totalQuantity, $shipFee));
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
                'orders' => array_map(function($shopOrder) {
                    return $shopOrder['order'];
                }, $ordersByShop),
                'totalPrice' => $grandTotalPrice,
                'total_amount' => $total_amount,
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
            $voucher = voucherToMain::where('code', $code)
                ->where('quantity', '>=', 1)
                ->where('status', 1)
                ->first();
            return $voucher ? $voucher->code : null;
        }
        if ($type === 'shop') {
            $voucher = VoucherToShop::whereIn('code', $code)
                ->where('quantity', '>=', 1)
                ->where('status', 1)
                ->pluck('code');
            // dd($voucher);
            return $voucher;
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
        if ($voucherToShopCode) {
            // $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();
            $voucherToShop = VoucherToShop::whereIn('code', $voucherToShopCode)
                                  ->where('status', 1)
                                  ->where('shop_id', $shopId)
                                  ->first();
            if ($voucherToShop) {

                $discountAmount = $totalPrice * $voucherToShop->ratio / 100;

                // Kiểm tra limitValue
                if ($voucherToShop->limitValue !== null && $voucherToShop->limitValue > 0) {
                    // Sử dụng min() với mảng
                    $discountAmount = min($discountAmount, $voucherToShop->limitValue);
                }
                $this->updateVoucherQuantity($voucherToShop);
            }
        }

        return $discountAmount;
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
        // dd($totalPrice);
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

    private function createOrder(Request $request, $ship_id)
    {

        $address = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
        $order = OrdersModel::create([
            'payment_id' => $request->payment_id,
            'user_id' => auth()->id(),
            'ship_id' => $request->ship_id,
            'delivery_address' => $request->delivery_address ?? $address->address,
            'ship_id' => $ship_id->id,
            'status' => 1,
        ]);
        return $order;
    }


    private function createOrderDetail($order, $variant, $quantity, $totalPrice, $product_id)

    {
        $product = Product::find($product_id);
        return OrderDetailsModel::create([
            'order_id' => $order->id,
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
            'quantity' => $quantity,
            'subtotal' => $totalPrice,
            'status' => 1,
            'height' => $product->height,
            'length' => $product->length,
            'weight' => $product->weight,
            'width' => $product->width,
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

    public function get_infomaiton_province_and_city()
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $cities = $response->json();
        return $cities;
    }

    public function get_infomaiton_district()
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
        $district = $response->json();
        return $district;
    }
    public function get_infomaiton_ward(Request $request)
    {
        // Lấy district_id từ yêu cầu
        $districtId = $request->district_id;
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $districtId, // Thêm district_id vào tham số truy vấn
        ]);
        $ward = $response->json();
        return $ward;
    }

    public function get_address_user(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $address = AddressModel::where('user_id', $user->id)->where('default', 1)->first();
        return $address;
    }

    public function get_infomaiton_services($shopData, $addressUser)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
            "shop_id" => $shopData->shopid_GHN,
            "from_district"=> $shopData->district_id,
            "to_district"=> $addressUser->district_id
        ]);
        $service = $response->json();
        return $service['data'];
    }

    public function calculateOrderFees_giao_hang_nhanh($shopData, $addressUser, $service, $order, $shopTotalPrice)
    {
        if ($order->weight >= 2000) {
            $service_id = 100039;
        } else {
            $service_id = 53320;
        }
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
            "service_id" => $service_id,
            "insurance_value" => $shopTotalPrice,
            "from_district_id" => $shopData->district_id,
            "to_district_id" => $addressUser->district_id,
            "to_ward_code" => $addressUser->ward_id,
            "height" => $order->height,
            "length" => $order->length,
            "weight" => $order->weight,
            "width" => $order->width,
        ]);
        $OrderFee = $response->json();
        return $OrderFee['data']['total'];
    }





}

