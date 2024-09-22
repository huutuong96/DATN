<?php

namespace App\Http\Controllers;
use App\Models\Cart_to_usersModel;
use App\Models\ProducttocartModel;
use App\Models\Product;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        $cart_to_users_products = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
        $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
        return response()->json($all_products_to_cart_to_users, 200);
    }
    public function show($id)
    {
        $cart_to_users = Cart_to_usersModel::where('user_id', auth()->user()->id)->first();
        
        $product_to_cart = ProducttocartModel::where('cart_id', $cart_to_users->id)->where('product_id', $id)->first();
        
        if (!$product_to_cart) {
            return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
        }
    
        return response()->json($product_to_cart, 200);
    }
    
    public function store(Request $request)
    {
        $cart_to_users = Cart_to_usersModel::where('user_id', auth()->user()->id)->first();
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }
        $productExist = ProducttocartModel::where('cart_id', $cart_to_users->id)->where('product_id', $request->product_id)->first();
        if ($productExist) {
            ProducttocartModel::where('id', $productExist->id)->update([
                'quantity' => $productExist->quantity + $request->quantity ?? 1,
            ]);
            return response()->json(['success' => 'Sản phẩm đã có trong giỏ hàng, cập nhật số lượng thành công'], 404);
        }
        $product_to_cart = ProducttocartModel::create([
            'cart_id' => $cart_to_users->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity ?? 1,
            'status' => 1,
        ]);

        return response()->json($product_to_cart, 200);
    }

    public function update(Request $request, string $id)
    {
        $updated = ProducttocartModel::where('id', $id)->update([
            'quantity' => $request->quantity,
        ]);
        if ($updated) {
            $cart_to_users = Cart_to_usersModel::where('user_id', auth()->user()->id)->first();
            $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
            return response()->json($all_products_to_cart_to_users, 200);
        }
        return response()->json(['error' => 'Update failed'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = ProducttocartModel::where('id', $id)->delete();
        if ($deleted) {
            $cart_to_users = Cart_to_usersModel::where('user_id', auth()->user()->id)->first();
            $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
            return response()->json($all_products_to_cart_to_users, 200);
        }

        return response()->json(['error' => 'Delete failed'], 400);
    }
}
