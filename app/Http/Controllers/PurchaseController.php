<?php

namespace App\Http\Controllers;
use App\Models\tax_category;
use App\Models\Product;
use App\Models\AddressModel;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Voucher;
use App\Models\VoucherToShop;
use App\Models\voucherToMain;
use App\Models\UsersModel;
use App\Models\RanksModel;
use App\Models\Tax;
use App\Models\platform_fees;
use App\Models\order_tax_details;
use App\Models\order_fee_details;
use App\Models\ProducttocartModel;
use App\Models\PaymentsModel;
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
use App\Jobs\SendMail;
use App\Jobs\SendNotification;
use App\Jobs\AddPointUser;
use App\Jobs\deleteProductToCart;
use App\Models\product_variants;
use App\Models\vnpay_transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PurchaseController extends Controller
{
    protected $recipe_rank;
    protected $recipe_voucher_shop;
    protected $recipe_voucher_main;
    public function __construct()
    {
       
    }
    public function purchaseToCart(Request $request)
    {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user->phone) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng nhập số điện thoại',
                ], 400);
            }
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
                $payment = PaymentsModel::where('id', $request->payment)->first();
                if (!$payment) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Phương thức thanh toán không hợp lệ.',
                    ], 400);
                }
                $carts = ProducttocartModel::whereIn('id', $request->carts)->with('product')->with('variant')->get();
    
                $ordersByShop = [];
                $grandTotalPrice = 0;
                $totalQuantity = 0;
                $total_amount = 0;
                $addressUser = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
                if (!$addressUser) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Vui lòng cập nhật địa chỉ giao hàng',
                    ], 400);
                }
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
                $groupOrderIds = time() . '-' . auth()->id(); // Tạo mã đặc thù cho từng phiên mua hàng
                foreach ($ordersByShop as $shopId => &$shopOrder) {
                    $ship_id = ShipsModel::where('code', $cart->ship_code)->first();
                    $order = $this->createOrder($request, $ship_id, $groupOrderIds, $payment);
                    $order->shop_id = $shopId;
                    $order->save();
                    $shopOrder['order'] = $order;
                    $shopOrder['orderDetails'] = [];
                    $shopTotalPrice = 0;
                    $height = 0;
                    $length = 0;
                    $weight = 0;
                    $width = 0;
                    $productIds = [];
                    $images = [];
                    foreach ($shopOrder['items'] as $cart) {
                        if ($cart->variant_image != null) {
                            $images[] = $cart->variant_image;
                        }else {
                            $images[] = $cart->product_image;
                        }
                        $productIds[] = $cart->product_id;
                        $result = $this->getProduct($cart->product_id, $cart->variant_id, $cart->quantity);
                        if ($result == 'PRO') {
                            return response()->json([
                                'status' => false,
                                'message' => 'Sản phẩm ' . $cart->product_name . ' đã hết hàng',
                            ], 400);
                        }elseif ($result == 'VAR') {
                            return response()->json([
                                'status' => false,
                                'message' => 'Sản phẩm ' . $cart->variant_name . ' đã hết hàng',
                            ], 400);
                        }
                        $this->checkProductAvailability($result, $cart->quantity, $cart->variant_id);
                        $totalPrice = $this->calculateTotalPrice($result, $cart->quantity);
                        $orderDetail = $this->createOrderDetail($order, $result, $cart->quantity, $totalPrice, $cart->product_id, $cart->variant_id);
                        $height += $orderDetail->height;
                        $length += $orderDetail->length;
                        $weight += $orderDetail->weight;
                        $width += $orderDetail->width;
                        $shopOrder['orderDetails'][] = $orderDetail;
                        $shopTotalPrice += $totalPrice;
                        $totalQuantity += $cart->quantity;
                    }
                    $tax = $this->calculateStateTax($shopTotalPrice, $cart->product_id);
                        // $shopTotalPrice += $tax;
                        if (!$tax) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Danh mục của sản phẩm chưa có thuế, Vui lòng liên hệ ADMIN',
                            ], 400);
                        }
                        $this->addStateTaxToOrder($order, $tax, $cart->product_id);
                    $order->vat = $tax;
                    $order->price_before_vat = $shopTotalPrice;
                    $order->price_after_vat = $shopTotalPrice + $tax;
                    $shopTotalPrice = $shopTotalPrice + $tax;
                    $order->height = $height;
                    $order->length = $length;
                    $order->weight = $weight;
                    $order->width = $width;
                    $shopOrder['totalPrice'] = $shopTotalPrice;
                    $grandTotalPrice += $shopTotalPrice;
                    $shopData = Shop::find($shopId);
                    $service = $this->get_infomaiton_services($shopData, $addressUser);
                    $productForShip = $this->getProductForShip($productIds);
                    $shipFee = $this->calculateOrderFees_giao_hang_nhanh($shopData, $addressUser, $service, $order, $shopTotalPrice, $result, $cart->quantity);
                    $order->ship_fee = $shipFee;
                    AddPointUser::dispatch(auth()->id());
                    $checkRank = $this->check_point_to_user();
                    $get_discountsByRank = $this->get_discountsByRank($checkRank, $shopTotalPrice);
                    $newtotal = $this->addOrderFeesToTotal($order, $shopTotalPrice);
                    $shopTotalPrice = $this->discountsByRank($checkRank, $shopTotalPrice);
                    $order->disscount_by_rank = $get_discountsByRank;
                    $order->total_amount = $shopTotalPrice;
                    $discountShopVoucher = 0;
                    $totalAdded = 0;
                    if ($voucherToShopCode != null) {
                        $totalAdded = $this->applyVouchersToShop($voucherToShopCode, $shopTotalPrice, $shopId);
                        $discountShopVoucher = $totalAdded;
                        $shopTotalPrice -= $totalAdded;
                    }
                    
                    $order->net_amount -= $totalAdded;
                    $shopData->wallet = $shopData->wallet + $order->net_amount;
                    $order->total_amount = $shopTotalPrice;
                    $total_amount = $shopTotalPrice;
                    $order->voucher_shop_disscount = $discountShopVoucher;
                    $discountMainVoucher = 0;
                    if ($voucherToMainCode) {
                        $total_disscount_main = $this->applyVouchersToMain($voucherToMainCode, $total_amount);
                        $discountMainVoucher = $this->get_price_discount($voucherToMainCode, $total_amount);
                        $order->total_amount = $total_disscount_main;
                        $order->save();
                    }
                    $order->voucher_disscount = $discountMainVoucher;
                    $order->total_amount = $shipFee + $order->total_amount;
                    $this->order_update_infomaion($order, $service, $productForShip, $shopData, $addressUser, $shipFee , $shopOrder['orderDetails'], $total_amount);
                    $order->save();
                }
                // return $ordersByShop;
                // return ($grandTotalPrice + $shipFee) - $discountMainVoucher;
                DB::commit();
                if($payment->code == 'COD'){
                    $orderInfomation = $this->shippingOrderCreate($order, $service, $productForShip, $shopData, $addressUser, $shipFee , $shopOrder['orderDetails'], $total_amount);
                    $order->order_infomation = $orderInfomation;
                    $order->save();
                }
                $orders = OrdersModel::where('group_order_id', $groupOrderIds)->get();
                $orderDetails = OrderDetailsModel::whereIn('order_id', $orders->pluck('id'))->get();
                $products = Product::whereIn('id', $orderDetails->pluck('product_id'))->get();
                $variants = null;
                if ($orderDetails->first()->variant_id != null) {
                    $variants = product_variants::whereIn('id', $orderDetails->pluck('variant_id'))->get();
                }
                $user = jwtAuth::parseToken()->authenticate();
                if ($payment->code == 'VNPAY') {
                    $PaymentsController = new PaymentsController();
                    $orderInfomation = $this->shippingOrderCreate($order, $service, $productForShip, $shopData, $addressUser, $shipFee , $shopOrder['orderDetails'], $total_amount);
                    $order->order_infomation = $orderInfomation;
                    $order->save();
                    DB::table("data_mail")->insert( [
                        'groupOrderIds' => $groupOrderIds,
                        'ordersByShop' => json_encode($ordersByShop),
                        'total_amount' => $total_amount,
                        'carts' => $carts,
                        'total_quantity' => json_encode($totalQuantity),
                        'ship_fee' => $shipFee,
                        'email' => auth()->user()->email,
                    ]);
                        ProducttocartModel::whereIn('id', $request->carts)->delete();
                    // deleteProductToCart::dispatch($request->carts);
                    $url = $PaymentsController->vnpay_payment($request, $total_amount, $groupOrderIds);
                    return response()->json([
                        'status' => true,
                        'message' => 'Vui lòng thanh toán',
                        'url' => $url,
                    ], 200);
                }
                $total_amount = ($grandTotalPrice + $shipFee) - $discountMainVoucher;
                SendMail::dispatch($orders, $total_amount, $carts, $orderDetails, $shipFee, $products, $variants, auth()->user()->email, $payment->name, $user, $discountMainVoucher);
                SendNotification::dispatch('Đặt hàng thành công', "Mã đơn hàng: $groupOrderIds", auth()->id(), $groupOrderIds, null);
                ProducttocartModel::whereIn('id', $request->carts)->delete();
                // deleteProductToCart::dispatch($request->carts);   
                return response()->json([
                    'status' => true,
                    'message' => 'Đặt hàng thành công',
                    'data' => $groupOrderIds,
                ], 200);
        
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'message' => 'Đặt hàng thất bại',
                    'error' => $e->getMessage()
                ], 400);
            }
    }

    public function handlePaymenAndSendEmail($groupOrderIds){
        
            $vnpay_transaction = vnpay_transaction::where("vnp_TxnRef", $groupOrderIds)->first();
            $order = OrdersModel::where('group_order_id', $groupOrderIds)->first();
            if(!$vnpay_transaction->vnp_ResponseCode == "00"){
                //thay đổi trạng thái order hoắc xóa nếu không thành công
                //$order->delete();
                //xóa luôn phần gửi lên giao hàng nhanh
                return response()->json([
                    'status' => 400,
                    'message' => 'Đặt hàng thất bại',
                ], 400);
            }
            $response = Http::withHeaders([
                'Token' => env('TOKEN_API_GIAO_HANG_NHANH_DEV'),
                'Content-Type' => 'application/json',
            ])->post('https://services.giaohangtietkiem.vn/services/shipment/update', [
                "order_id" => $groupOrderIds, // Mã đơn hàng đã tạo với GHTK
                "cod_amount" => 0
            ]);
           
            $order->update([
                "cod_amount" => 0
            ]);
            $order->save();
            $data = DB::table("data_mail")->where("groupOrderIds", $groupOrderIds)->first();
            $user = UsersModel::where("email", $data->email)->first();
            DB::table('data_mail')->where("groupOrderIds", $groupOrderIds)->delete();
            // dd(response()->json([
            //     'status' => true,
            //     'message' => 'Đặt hàng thành công',]));
            SendNotification::dispatch('Đặt hàng thành công', 'Bạn đã đặt hàng thành công, đơn hàng của bạn đang được xử lý', $user->id);
            
            return response()->json([
                'status' => true,
                'message' => 'Đặt hàng thành công'
            ], 200);
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
    private function get_discountsByRank($checkRank, $totalPrice)
    {
        $rank = RanksModel::where('id', $checkRank)->first();
        if (!$rank) {
            return $totalPrice; // Không có rank, không áp dụng giảm giá
        }
        $discountPercentage = $rank->value; // Giả sử value là phần trăm giảm giá (0.2 = 20%)
        $maxDiscount = $rank->limitValue; // Giả sử limitValue là giá trị giảm tối đa
        $discountAmount = $totalPrice * $discountPercentage;
        $discountAmount = min($discountAmount, $maxDiscount); // Đảm bảo giảm giá không vượt quá giới hạn
        
        return $discountAmount;
    }
    private function getValidVoucherCode($code, $type)
    {
        if ($type === 'main') {
            $voucher = voucherToMain::where('code', $code)
                ->where('quantity', '>=', 1)
                ->first();
            return $voucher ? $voucher->code : null;
        }
        if ($type === 'shop') {
            $voucher = VoucherToShop::whereIn('code', $code)
                ->where('quantity', '>=', 1)
                ->pluck('code');
            // dd($voucher);
            return $voucher;
        }
    }

    private function getProduct($productId, $variantId, $quantity)
    {
        $product = Product::where('id', $productId)->first();
        $product->increment('sold_count', $quantity);
        
        if ($variantId == null) {
            $result = Product::where('id', $productId)->first();
            $result->decrement('quantity', $quantity);
            if ($result->quantity < 0) {
                $result->update(['status' => 0]);
                return $result = 'PRO';
            }
            return $result;
        }else{
            $result = product_variants::where('id', $variantId)->first();
            $result->decrement('stock', $quantity);
            if ($result->stock < 0) {
                $result->update(['status' => 0]);
                return $result = 'VAR';
            }
            return $result;
        }
    }

    private function getProductForShip($productId)
    {
        $result = Product::whereIn('id', $productId)->get();
        return $result;
    }


    private function checkProductAvailability($result, $quantity, $variant_id)
    {
        // dd($product);
        if ($variant_id != null) {
            if ($result->stock < $quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm ' . $result->name . ' không đủ hàng',
                ], 400);
            }
        }else {
            if ($result->quantity < $quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sản phẩm ' . $result->name . ' không đủ hàng',
                ], 400);
            }
        }
        
    }

    private function calculateTotalPrice($result, $quantity)
    {
        return $result->price * $quantity;
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
    private function applyVouchersToShop($voucherToShopCode, $totalPrice, $shopId)
    {
        $discountAmount = null;
        if ($voucherToShopCode) {
            // $voucherToShop = VoucherToShop::where('code', $voucherToShopCode)->where('status', 1)->first();

            $voucherToShop = VoucherToShop::whereIn('code', $voucherToShopCode)
                                  ->where('status', 2)
                                  ->where('shop_id', $shopId)
                                  ->first();
            
            if ($voucherToShop) { 

                $discountAmount = $totalPrice * $voucherToShop->ratio;

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
        if ($voucherToMainCode) {
            $voucherToMain = voucherToMain::where('code', $voucherToMainCode)->first();
            
            if ($voucherToMain) {
                $priceDiscount = $totalPrice * $voucherToMain->ratio;
               
                if ($priceDiscount > $voucherToMain->limitValue) {
                    $totalPrice -= $voucherToMain->limitValue;
                }else {
                    $totalPrice -= $priceDiscount;
                }
            }
        }
        return $totalPrice;
    }

    private function get_price_discount($voucherToMainCode, &$totalPrice)
    {
        if ($voucherToMainCode) {
            $voucherToMain = voucherToMain::where('code', $voucherToMainCode)->first();
            if ($voucherToMain) {
                $priceDiscount = $totalPrice * $voucherToMain->ratio;
                if ($priceDiscount > $voucherToMain->limitValue) {
                    $totalPrice = $voucherToMain->limitValue;
                }else {
                    $totalPrice = $priceDiscount;
                }
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


    private function createOrder(Request $request, $ship_id, $groupOrderIds, $payment)
    {
        $address = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
        $status = 1;
        $order_status = 0;
        if ($payment->code == 'VNPAY') {
            $status = 2;
            // $order_status = 12;
        }
        $order = OrdersModel::create([
            'payment_id' => $payment->id,
            'group_order_id' => $groupOrderIds,
            'user_id' => auth()->id(),
            'ship_id' => $request->ship_id,
            'delivery_address' => $request->delivery_address ?? $address->address,
            'ship_id' => $ship_id->id,
            'status' => $status,
            'order_status' => $order_status ?? 0,
            'updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        return $order;
    }


    private function createOrderDetail($order, $result, $quantity, $totalPrice, $product_id, $variant_id)
    {
        if ($variant_id == null) {
            $product = Product::find($result->id);
            return OrderDetailsModel::create([
                'order_id' => $order->id,
                'category_id'=> $product->category_id,
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'subtotal' => $totalPrice,
                'status' => 1,
                'height' => $product->height,
                'length' => $product->length,
                'weight' => $product->weight,
                'width' => $product->width,
            ]);
        }else {
            $variant = product_variants::find($result->id);
            return OrderDetailsModel::create([
                'order_id' => $order->id,
                'category_id'=> $variant->category_id,
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'subtotal' => $totalPrice,
                'status' => 1,
                'height' => $variant->height,
                'length' => $variant->length,
                'weight' => $variant->weight,
                'width' => $variant->width,
            ]);
        }
        
    }

    private function calculateStateTax($totalPriceOfShop, $product_id)
    {
        $product = Product::find($product_id);
        $tax_category = tax_category::where('category_id', $product->category_id)->first();
        $taxes = Tax::find($tax_category->tax_id);
        $totalTaxAmount = 0;
        $taxAmount = $totalPriceOfShop * $taxes->rate;
        $totalTaxAmount += $taxAmount;
        return (int)$totalTaxAmount;
    }

    private function addStateTaxToOrder($order, $tax, $product_id)
    {
        $product = Product::find($product_id);
        $tax_category = tax_category::where('category_id', $product->category_id)->first();
        $taxes = Tax::find($tax_category->tax_id);
        order_tax_details::create([
            'order_id' => $order->id,
            'tax_id' => $taxes->id,
            'amount' => $tax
        ]);

    }

    private function calculateOrderFees($order, $totalPriceOfShop)
    {
        $platformFees = platform_fees::all();
        $totalFeeAmount = 0;
        foreach ($platformFees as $fee) {
            $feeAmount = $totalPriceOfShop * $fee->rate;
            $totalFeeAmount += $feeAmount;

            order_fee_details::create([
                'order_id' => $order->id,
                'platform_fee_id' => $fee->id,
                'amount' => $feeAmount,
            ]);
        }
        $order->platform_fee = (int)$totalFeeAmount;
        $order->save();
        return (int)$totalFeeAmount;
    }

    private function addOrderFeesToTotal($order, $totalPrice)
    {   
        //246960.0 
        $feeAmount = $this->calculateOrderFees($order, $totalPrice);
        $newTotal = $totalPrice - $feeAmount;
        $order->update(['net_amount' => $newTotal]);
        // dd($newTotal);
        return $newTotal;
    }

    public function get_infomaiton_province_and_city()
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $cities = $response->json();
        return $cities;
    }

    public function get_infomaiton_district()
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district');
        $district = $response->json();
        return $district;
    }
    public function get_infomaiton_ward(Request $request)
    {
        // Lấy district_id từ yêu cầu
        $districtId = $request->district_id;
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id', [
            'district_id' => $districtId, // Thêm district_id vào tham số truy vấn
        ]);
        $ward = $response->json();
        return $ward;
    }

    public function get_infomaiton_services($shopData, $addressUser)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
            "shop_id" => $shopData->shopid_GHN,
            "from_district"=> $shopData->district_id,
            "to_district"=> $addressUser->district_id
        ]);
        $service = $response->json();
        return $service['data'];
    }

    public function calculateOrderFees_giao_hang_nhanh($shopData, $addressUser, $service, $order, $shopTotalPrice, $result, $quantity)
    {

        if ($order->weight >= 2000) {
            $service_id = 100039;
        } else {
            $service_id = 53320;
        }
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                "shop_id"=>$shopData->shopid_GHN,
                "from_district" => $shopData->district_id,
                "to_district"=>$addressUser->district_id,
        ]);
        $service = $response->json();
        if ($service['data'] != null) {
            $response = Http::withHeaders([
                'token' => $token, // Gắn token vào header
            ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
                
                    "from_district_id" => $shopData->district_id,
                    "from_ward_code"=>$shopData->ward_id,
                    "service_id"=>53320,
                    "service_type_id"=>null,
                    "to_district_id"=>$addressUser->district_id,
                    "to_ward_code"=>$addressUser->ward_id,
                    "height"=>10,
                    "length"=>10,
                    "weight"=>10,
                    "width"=>10,
                    "insurance_value"=>0,
                    "cod_failed_amount"=>2000,
                    "coupon"=> null,
                    "items"=> [
                            [
                            "name" =>$result->name,
                            "quantity" => $quantity,
                            "height" => 10,
                            "weight" => 10,
                            "length" => 10,
                            "width" => 10
                            ]
                      ]
                    
                ]);
                $OrderFee = $response->json();
        }
        $ShipFree = $OrderFee['data']['total'] ?? 25700;
        return $ShipFree;
    }


    public function shippingOrderCreate($order, $service, $productForShip, $shopData, $addressUser, $shipFee, $orderDetails, $cod_amount = 0){
        $user = JWTAuth::parseToken()->authenticate();
        $address = AddressModel::where('user_id', $user->id)->where('default', 1)->first();
        // Tạo mảng items bằng array_map
        $items = array_map(function($detail) {
            $product = $detail->product;
            $variant = $detail->variant;
            return [
                "name" => $product->name ?? "Sản phẩm không xác định",
                "code" => $variant->sku ?? $product->sku ?? "SKU không xác định",
                "quantity" => 1,
                "price" => $detail->subtotal,
                "length" => $detail->length,
                "width" => $detail->width,
                "weight" => $detail->weight,
                "height" => $detail->height,
                "category" => [
                    "level1" => $product->category->title ?? "Danh mục không xác định"
                ]
            ];
        }, $orderDetails);
        $service_id = $order->weight >= 2000 ? 100039 : 53320;
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token,
            'Content-Type' => 'application/json',
            'ShopId' => $shopData->shopid_GHN,
        ])->post('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/create', [
            "payment_type_id" => 2,
            "note" => $request->note ?? "",
            "required_note" => $request->required_note ?? "KHONGCHOXEMHANG",
            "return_phone" => intval($shopData->contact_number),
            "return_address" => $shopData->pick_up_address,
            "return_district_id" => $shopData->district_id,
            "return_ward_code" => $shopData->ward_id,
            "client_order_code" => "order-" . $order->id,
            "from_name" => $shopData->shop_name,
            "from_phone" => $shopData->contact_number,
            "from_address" => $shopData->pick_up_address . ", " . $shopData->ward . ", " . $shopData->district . ", " . $shopData->province . ", Vietnam",
            "from_ward_name" => $shopData->ward,
            "from_district_name" => $shopData->district,
            "from_province_name" => $shopData->province,
            "to_name" => $user->fullname,
            "to_phone" => $user->phone,
            "to_address" => $address->address . ", " . $address->ward . ", " . $address->district . ", " . $address->province . ", Vietnam",
            "to_ward_name" => $address->ward,
            "to_district_name" => $address->district,
            "to_province_name" => $address->province,
            "cod_amount" => $cod_amount,
            "content" => $request->content ?? "",
            "weight" => 1000,
            "length" =>  20,
            "width" =>  20,
            "height" => 30,
            "cod_failed_amount" => 6000,
            "pick_station_id" => 1444,
            "deliver_station_id" => null,
            "insurance_value" => $order->total_amount ?? 0,
            "service_id" => 0,
            "service_type_id" => 2,
            "coupon" => null,
            "pickup_time" => 1692840132,
            "pick_shift" => [2],
            "items" => $items,
        ]);
        // lưu đơn hàng lên db
        $orderShipGHN = $response->json();

        if ($orderShipGHN['data'] == null) {
            $orderShipGHN['data']['order_code'] = null;
            $orderShipGHN = "GIAO HÀNG NHANH";
        }

        // Cập nhật đơn hàng chỉ một lần
        $order->update([
            "payment_type_id" => 2,
            "note" => $request->note ?? "",
            "required_note" => $request->required_note ?? "KHONGCHOXEMHANG",
            "return_phone" => $shopData->contact_number,
            "return_address" => $shopData->pick_up_address,
            "return_district_id" => $shopData->district_id,
            "return_ward_code" => $shopData->ward_id,
            "client_order_code" => $orderShipGHN['data']['order_code'] ?? null,
            "from_name" => $shopData->shop_name,
            "from_phone" => $shopData->contact_number,
            "from_address" => $shopData->pick_up_address . ", " . $shopData->ward . ", " . $shopData->district . ", " . $shopData->province . ", Vietnam",
            "from_ward_name" => $shopData->ward,
            "from_district_name" => $shopData->district,
            "from_province_name" => $shopData->province,
            "to_name" => $user->fullname,
            "to_phone" => $user->phone,
            "to_address" => $address->address . ", " . $address->ward . ", " . $address->district . ", " . $address->province . ", Vietnam",
            "to_ward_name" => $address->ward,
            "to_district_name" => $address->district,
            "to_province_name" => $address->province,
            "cod_amount" => $cod_amount,
            "content" => $request->content ?? "",
            "weight" => $order->weight ?? 1000,
            "length" => $order->length ?? 20,
            "width" => $order->width ?? 20,
            "height" => $order->height,
            "cod_failed_amount" => 0,
            "pick_station_id" => 1444,
            "deliver_station_id" => null,
            "insurance_value" => 10000,
            "service_id" => $service_id,
            "service_type_id" => 2,
            "coupon" => null,
            "pickup_time" => 1692840132,
            "pick_shift" => [2],
            "items" => $items,
        ]);
        return $orderShipGHN;
    }

    public function order_update_infomaion($order, $service, $productForShip, $shopData, $addressUser, $shipFee, $orderDetails, $cod_amount = 0){
        $user = JWTAuth::parseToken()->authenticate();
        $address = AddressModel::where('user_id', $user->id)->where('default', 1)->first();
        // Tạo mảng items bằng array_map
        $items = array_map(function($detail) {
            $product = $detail->product;
            $variant = $detail->variant;
            return [
                "name" => $product->name ?? "Sản phẩm không xác định",
                "code" => $variant->sku ?? $product->sku ?? "SKU không xác định",
                "quantity" => 1,
                "price" => $detail->subtotal,
                "length" => $detail->length,
                "width" => $detail->width,
                "weight" => $detail->weight,
                "height" => $detail->height,
                "category" => [
                    "level1" => $product->category->title ?? "Danh mục không xác định"
                ]
            ];
        }, $orderDetails);
        $service_id = $order->weight >= 2000 ? 100039 : 53320;
        // Cập nhật đơn hàng chỉ một lần
        $order->update([
            "payment_type_id" => 2,
            "note" => $request->note ?? "",
            "required_note" => $request->required_note ?? "KHONGCHOXEMHANG",
            "return_phone" => $shopData->contact_number,
            "return_address" => $shopData->pick_up_address,
            "return_district_id" => $shopData->district_id,
            "return_ward_code" => $shopData->ward_id,
            "client_order_code" => $orderShipGHN['data']['order_code'] ?? null,
            "from_name" => $shopData->shop_name,
            "from_phone" => $shopData->contact_number,
            "from_address" => $shopData->pick_up_address . ", " . $shopData->ward . ", " . $shopData->district . ", " . $shopData->province . ", Vietnam",
            "from_ward_name" => $shopData->ward,
            "from_district_name" => $shopData->district,
            "from_province_name" => $shopData->province,
            "to_name" => $user->fullname,
            "to_phone" => $user->phone,
            "to_address" => $address->address . ", " . $address->ward . ", " . $address->district . ", " . $address->province . ", Vietnam",
            "to_ward_name" => $address->ward,
            "to_district_name" => $address->district,
            "to_province_name" => $address->province,
            "cod_amount" => $cod_amount,
            "content" => $request->content ?? "",
            "weight" => $order->weight ?? 1000,
            "length" => $order->length ?? 20,
            "width" => $order->width ?? 20,
            "height" => $order->height,
            "cod_failed_amount" => 0,
            "pick_station_id" => 1444,
            "deliver_station_id" => null,
            "insurance_value" => 10000,
            "service_id" => $service_id,
            "service_type_id" => 2,
            "coupon" => null,
            "pickup_time" => 1692840132,
            "pick_shift" => [2],
            "items" => $items,
        ]);
        return $order;
    }
    //----------------------------------------------------------------------

    public function checkoutdone(Request $request){
        // xử lý
        $PaymentsController = new PaymentsController();
        $data = ($PaymentsController->vnpay_return($request));
        // dd($data);
        // $insertData = [
        //     'vnp_Amount' => $data["vnp_Amount"] ?? 0, // Giá trị mặc định nếu không có
        //     'vnp_BankCode' => "".$data['vnp_BankCode']."",
        //     'vnp_BankTranNo' => $data["vnp_BankTranNo"] ?? '',
        //     'vnp_CardType' => $data["vnp_CardType"] ?? '',
        //     // 'vnp_OrderInfo' => $data["vnp_OrderInfo"] ?? '',
        //     'vnp_PayDate' => $data["vnp_PayDate"], // Định dạng ngày giờ
        //     'vnp_ResponseCode' => $data["vnp_ResponseCode"] ?? '',
        //     'vnp_TmnCode' => $data["vnp_TmnCode"] ?? '',
        //     'vnp_TransactionNo' => $data["vnp_TransactionNo"] ?? '',
        //     'vnp_TransactionStatus' => $data["vnp_TransactionStatus"] ?? '',
        //     'vnp_TxnRef' => $data["vnp_TxnRef"] ?? '',
        //     'vnp_SecureHash' => "".$data['vnp_SecureHash']."",
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ];
        // // dd($insertData);

        // // Chèn dữ liệu vào bảng
        // vnpay_transaction::create($insertData);
        // $this->handlePaymenAndSendEmail($data["vnp_TxnRef"]);
        
    }
}