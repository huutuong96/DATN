<?php

namespace App\Http\Controllers;
use App\Models\Cart_to_usersModel;
use App\Models\ProducttocartModel;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
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
        $all_products_to_cart_to_users = Cache::remember('all_products_to_cart_to_users', 60 * 60, function () use ($cart_to_users_products, $cart_to_users) {
            return ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
        });
        return response()->json($all_products_to_cart_to_users, 200);
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cart_to_users = Cart_to_usersModel::where('user_id', $user->id)->first();
        dd($cart_to_users);
        $all_products = Cache::get('all_products');
        if (!$all_products) {
            $all_products = Product::all();
            Cache::put('all_products', $all_products, 60 * 60);
        }
        $product = $all_products->where('id', $request->product_id)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        $product_to_cart = ProducttocartModel::create([
            'cart_id' => $cart_to_users->id,
            'product_id' => $product->id,
            'quantity' => $request->quantity,
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
            Cache::put('all_products_to_cart_to_users', $all_products_to_cart_to_users, 60 * 60);
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
            Cache::put('all_products_to_cart_to_users', $all_products_to_cart_to_users, 60 * 60);
            return response()->json($all_products_to_cart_to_users, 200);
        }

        return response()->json(['error' => 'Delete failed'], 400);
    }
}
