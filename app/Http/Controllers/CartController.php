<?php

namespace App\Http\Controllers;

use App\Models\Cart_to_usersModel;
use App\Models\ProducttocartModel;
use App\Models\product_variants;
use App\Models\Product;
use App\Models\Shop;
use App\Models\variantattribute;
use App\Models\AddressModel;
use App\Models\Tax;
use App\Models\tax_category;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $user = JWTAuth::parseToken()->authenticate();
    //     $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
    //     $cart_to_users_products = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
    //     $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
    //     if ($all_products_to_cart_to_users->isEmpty()) {
    //         return response()->json(['error' => 'Giỏ hàng của bạn đang trống'], 404);
    //     }
    //     $all_products_to_cart_to_users->load(['product' => function($query) {
    //         $query->select('id', 'name', 'price'); // specify the fields you want to select, excluding 'json_variants'
    //     }, 'variant' => function($query) {
    //         $query->select('id', 'product_id', 'name', 'price', 'stock');
    //     }, 'shop' => function($query) {
    //         $query->select('id', 'shop_name');
    //     }]);
    //     $groupedProducts = $all_products_to_cart_to_users->groupBy('shop_id')->map(function ($shopGroup) {
    //         $shop = $shopGroup->first()->shop;
    //         $products = $shopGroup->groupBy('product_id')->map(function ($productGroup) {
    //             $product = $productGroup->first()->product;
    //             $variants = $productGroup->map(function ($item) {
    //                 return $item->variant;
    //             });
    //             return [
    //                 'product' => $product,
    //                 'variants' => $variants,
    //             ];
    //         });
    //         return [
    //             'shop_name' => $shop->shop_name,
    //             'products' => $products,
    //         ];
    //     });
        
    //     return response()->json($groupedProducts, 200);
    //     // return response()->json($all_products_to_cart_to_users, 200);
    // }

  public function index()
{
    $user = JWTAuth::parseToken()->authenticate();
    $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
    $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
    foreach ($all_products_to_cart_to_users as $cart) {
        if ($cart->variant_id != null) {
            $variantStock = product_variants::where('id', $cart->variant_id)->pluck('stock')->first();
            if ($variantStock <= 0) {
                $cart->quantity = 0;
                $cart->save();
            }
        }else{
            $productStock = Product::where('id', $cart->product_id)->pluck('quantity')->first();
            if ($productStock <= 0) {
                $cart->quantity = 0;
                $cart->save();
            }
        }
    }
    $shop = Shop::whereIn('id', $all_products_to_cart_to_users->pluck('shop_id'))->select('id', 'shop_name')
        ->select('id', 'shop_name', 'slug')
        ->get();
    return response()->json([
        'cart' => $all_products_to_cart_to_users,
        'shop' => $shop,
    ], 200);
}
    public function miniCart()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->take(5)->get();
        if (!$all_products_to_cart_to_users) {
            return response()->json(['error' => 'Không có sản phẩm nào!'], 404);
        }
        $all_products_to_cart_to_users->load(['product' => function($query) {
            $query->select('id', 'name', 'price'); // specify the fields you want to select, excluding 'json_variants'
        }, 'variant' => function($query) {
            $query->select('id', 'product_id', 'name', 'price', 'stock');
        }]);
        return response()->json($all_products_to_cart_to_users, 200);
    }

    // public function store(Request $request)
    // {
    //     $user = JWTAuth::parseToken()->authenticate();
    //     $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
    //     if (!$cart_to_users) {
    //         $cart_to_users = Cart_to_usersModel::create([
    //             'user_id' => $user->id,
    //             'status' => 1,
    //         ]);
    //     }
    //     $product = Product::where('id', $request->product_id)->where('status', 2)->where('shop_id', $request->shop_id)->first();
    //     if (!$product) {
    //         return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
    //     }
    //     if ($request->variant_id) {
    //         $productVariant = product_variants::where('product_id', $product->id)->where('id', $request->variant_id)->first();
    //         if (!$productVariant) {
    //             return response()->json(['error' => 'Sản phẩm không có biến thể này'], 404);
    //         }if ($productVariant->stock < $request->quantity) {
    //             return response()->json(['error' => 'Số lượng sản phẩm không đủ'], 400);
    //         }
    //         $product_to_cart = ProducttocartModel::where('cart_id', $cart_to_users->id)->where('product_id', $product->id)->where('variant_id', $productVariant->id)->first();
    //         if ($product_to_cart) {
    //            if ($product_to_cart->variant_id != null && $product_to_cart->variant_id == $productVariant->id) {
    //                $product_to_cart->quantity += $request->quantity;
    //                $product_to_cart->save(); 
    //                if ($productVariant->stock < $product_to_cart->quantity) {
    //                     $product_to_cart->quantity = $productVariant->stock;
    //                     $product_to_cart->save(); 
    //                }
    //                return response()->json([
    //                    'status' => true,
    //                    'message' => "Sản phẩm đã tồn tại trong giỏ hàng của bạn, Thêm só lượng sản phẩm thành công",
    //                    'product' => $product_to_cart,
    //                ], 200);
    //            }elseif ($product_to_cart->variant_id != null && $product_to_cart->variant_id != $productVariant->id) {
    //                  $product_to_cart = ProducttocartModel::create([
    //                       'cart_id' => $cart_to_users->id,
    //                       'product_id' => $product->id,
    //                       'quantity' => $request->quantity ?? 1,
    //                       'variant_id' => $productVariant->id ?? null,
    //                       'shop_id' => $request->shop_id,
    //                       'ship_code' => $request->ship_code ?? 'STANDARD',
    //                       'status' => 2,
    //                  ]);
    //                  return response()->json([
    //                       'status' => true,
    //                       'message' => "Sản phẩm đã được thêm vào giỏ hàng",
    //                       'product' => $product_to_cart,
    //                  ], 200);
    //            }
    //         }
    //     }else{
    //         $productVariant = null;
    //         if ($product->quantity < $request->quantity) {
    //             return response()->json(['error' => 'Số lượng sản phẩm không đủ'], 400);
    //         }
    //     }
    //     $product_to_cart = ProducttocartModel::where('cart_id', $cart_to_users->id)->where('product_id', $product->id)->first();
    //     if ($product_to_cart && !$request->variant_id && $product_to_cart->product_id == $product->id) {
    //             $product_to_cart->quantity += $request->quantity;
    //             $product_to_cart->save();
    //             if ($product->quantity < $product_to_cart->quantity) {
    //                 $product_to_cart->quantity = $product->quantity;
    //                 $product_to_cart->save(); 
    //             }
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => "Sản phẩm đã tồn tại trong giỏ hàng của bạn, Thêm só lượng sản phẩm thành công",
    //                 'product' => $product_to_cart,
    //             ], 200);
    //     }
    //     $product_to_cart = ProducttocartModel::create([
    //         'cart_id' => $cart_to_users->id,
    //         'product_id' => $product->id,
    //         'quantity' => $request->quantity ?? 1,
    //         'variant_id' => $productVariant->id ?? null,
    //         'shop_id' => $request->shop_id,
    //         'ship_code' => $request->ship_code ?? 'STANDARD',
    //         'status' => 2,
    //     ]);
    //     return response()->json([
    //         'status' => true,
    //         'message' => "Sản phẩm đã được thêm vào giỏ hàng",
    //         'product' => $product_to_cart,
    //     ], 200);
    // }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        if (!$cart_to_users) {
            $cart_to_users = Cart_to_usersModel::create([
                'user_id' => $user->id,
                'status' => 1,
            ]);
        }
        $shop = Shop::where('id', $request->shop_id)->first();
        $product = Product::where('id', $request->product_id)->where('status', 2)->where('shop_id', $request->shop_id)->first();
        
        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }
        
        $tax_category = tax_category::where('category_id', $product->category_id)->first();
        $taxes = Tax::find($tax_category->tax_id);
        // $taxAmount = $request->price * $taxes->rate;
        if ($request->variant_id) {
            $productVariant = product_variants::where('id', $request->variant_id)->first();
            if (!$productVariant) {
                return response()->json(['error' => 'Sản phẩm không có biến thể này'], 404);
            }
            if ($productVariant->stock < $request->quantity) {
                return response()->json(['error' => 'Số lượng sản phẩm không đủ'], 400);
            }
            $product_to_cart = ProducttocartModel::where('variant_id', $productVariant->id)->where('cart_id', $cart_to_users->id)->first();
            if ($product_to_cart) {
               if ($product_to_cart->variant_id != null && $product_to_cart->variant_id == $productVariant->id) {
                   $product_to_cart->quantity += $request->quantity;
                   $product_to_cart->save(); 
                   if ($productVariant->stock < $product_to_cart->quantity) {
                        $product_to_cart->quantity = $productVariant->stock;
                        $product_to_cart->save(); 
                    }
                   return response()->json([
                       'status' => true,
                       'message' => "Sản phẩm đã tồn tại trong giỏ hàng của bạn, Thêm só lượng sản phẩm thành công",
                       'product' => $product_to_cart,
                   ], 200);
               }
            }else {
                // dd($productVariant->images);
                $price_after_tax = $productVariant->price * ($taxes->rate + 1);
                $product_to_cart = ProducttocartModel::create([
                     'cart_id' => $cart_to_users->id,
                     'quantity' => $request->quantity ?? 1,
                     'variant_id' => $productVariant->id ?? null,
                     'variant_name' => $productVariant->name,
                     'variant_price' => $price_after_tax ?? $productVariant->price,
                     'variant_image' => $productVariant->images,
                        'product_id' => $productVariant->product_id,
                     'product_name' => $product->name,
                     'product_slug' => $product->slug,
                     'shop_id' => $request->shop_id,
                     'shop_name' => $shop->shop_name,
                     'shop_slug' => $shop->slug,
                     'ship_code' => $request->ship_code ?? 'STANDARD',
                     'status' => 2,
                ]);
                return response()->json([
                     'status' => true,
                     'message' => "Sản phẩm đã được thêm vào giỏ hàng",
                     'product' => $product_to_cart,
                ], 200);
          }
        }else{
            $productVariant = null;
            if ($product->quantity < $request->quantity) {
                return response()->json(['error' => 'Số lượng sản phẩm không đủ'], 400);
            }
        }
        if (!$request->variant_id) {
            $product_to_cart = ProducttocartModel::where('cart_id', $cart_to_users->id)->where('product_id', $product->id)->first();
            if ($product_to_cart && !$request->variant_id && $product_to_cart->product_id == $product->id) {
                    $product_to_cart->quantity += $request->quantity;
                    $product_to_cart->save();
                    if ($product->quantity < $product_to_cart->quantity) {
                        $product_to_cart->quantity = $product->quantity;
                        $product_to_cart->save(); 
                    }
                    return response()->json([
                        'status' => true,
                        'message' => "Sản phẩm đã tồn tại trong giỏ hàng của bạn, Thêm só lượng sản phẩm thành công",
                        'product' => $product_to_cart,
                    ], 200);
            }
            $price_after_tax = $product->price * ($taxes->rate + 1);
            $product_to_cart = ProducttocartModel::create([
                'cart_id' => $cart_to_users->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_price' => $price_after_tax ?? $product->price,
                'product_image' => $product->image ?? null,
                'quantity' => $request->quantity ?? 1,
                'shop_id' => $request->shop_id,
                'shop_name' => $shop->shop_name,
                'shop_slug' => $shop->slug,
                'ship_code' => $request->ship_code ?? 'STANDARD',
                'status' => 2,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Sản phẩm đã được thêm vào giỏ hàng",
                'product' => $product_to_cart,
            ], 200);
        }
        
    }

    public function update(Request $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        if ($cart_to_users) {
            if ($request->quantity == 0) {
                $deleted = ProducttocartModel::where('id', $id)->delete();
                if ($deleted) {
                    return response()->json([
                        'status' => true,
                        'message' => "Sản phẩm đã được xóa khỏi giỏ hàng",
                    ], 200);
                }if (!$deleted) {
                    return response()->json([
                        'status' => false,
                        'message' => "Sản phẩm không tồn tại trong giỏ hàng",
                    ], 404);
                }
            }
            if ($request->quantity < 0) {
                return response()->json([
                    'status' => false,
                    'message' => "Số lượng sản phẩm không hợp lệ",
                ], 400);
            }
            if ($request->quantity > 0) {

                $updated = ProducttocartModel::where('id', $id)->update([
                    'quantity' => $request->quantity,
                ]);
                return response()->json([
                    'status' => true,
                    'message' => "Sản phẩm đã được thêm vào giỏ hàng",
                    'product' => $updated,
                ], 200);
            }
        }
    }

    public function destroy(string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        if ($cart_to_users) {
            $deleted = ProducttocartModel::where('id', $id)->delete();
            if ($deleted) {
                return response()->json([
                    'status' => true,
                    'message' => "Sản phẩm đã được xóa khỏi giỏ hàng",
                ], 200);
            }if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => "Sản phẩm không tồn tại trong giỏ hàng",
                ], 404);
            }
        }
    }

    public function calculateShipFees_giao_hang_nhanh(Request $request)
    {
        $data = [];
        $user = JWTAuth::parseToken()->authenticate();
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $inputArray = $request->all();
        foreach ($inputArray as $input) {
            $shopData = Shop::where('id', $input['shop_id'])->first();
            $addressUser = AddressModel::where('user_id', $user->id)->first();
            $response = Http::withHeaders([
                'token' => $token, // Gắn token vào header
            ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                    "shop_id"=>$shopData->shopid_GHN,
                    "from_district" => $shopData->district_id,
                    "to_district"=>$addressUser->district_id,
            ]);
            $service = $response->json();
            if ($service['data'] != null) {
                foreach ($input['items'] as $item) {
                    $result = ProducttocartModel::where('id', $item)->first();
                    $product = Product::where('id', $result->product_id)->first();
                    $response = Http::withHeaders([
                        'token' => $token, // Gắn token vào header
                    ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
                            "from_district_id" => $shopData->district_id,
                            "from_ward_code"=>$shopData->ward_id,
                            // "service_id"=>$service_id,
                            "service_id"=> 53320,
                            "service_type_id"=>null,
                            "to_district_id"=>$addressUser->district_id,
                            "to_ward_code"=>$addressUser->ward_id,
                            "height"=>$product->height ?? 10,
                            "length"=>$product->length ?? 10,
                            "weight"=>$product->weight ?? 10,
                            "width"=>$product->width ?? 10,
                            "insurance_value"=>0,
                            "cod_failed_amount"=>2000,
                            "coupon"=> null,
                            "items"=> [
                                    [
                                    "name" =>$result->name,
                                    "quantity" => $result->quantity,
                                    "height"=>$product->height ?? 10,
                                    "length"=>$product->length ?? 10,
                                    "weight"=>$product->weight ?? 10,
                                    "width"=>$product->width ?? 10,
                                    ]
                            ]
                            
                        ]);;
                        $OrderFee = $response->json();
                        // return $OrderFee['data']['total'];
                        // if ($OrderFee['data']['total'] > 50000) {
                        //     $OrderFee['data']['total'] = 25700;
                        // }
                        // return $OrderFee['data']['total'];
                }
            }
            $shipFee = $OrderFee['data']['total'] ?? 25700;
            $data[] = [
                'shop_id' => $input['shop_id'],
                'ship_fee' => $shipFee,
            ];
        }
        return response()->json($data, 200);
    }




}
