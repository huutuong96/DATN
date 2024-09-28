<?php

namespace App\Http\Controllers;

use App\Models\Cart_to_usersModel;
use App\Models\ProducttocartModel;
use App\Models\product_variants;
use App\Models\Product;
use App\Models\variantattribute;
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


        // $product = Product::where('sku', $request->sku)->first();
        $productVariant = null;
        // Tìm variant dựa trên các attribute_id và value_id đã chọn
        $query = variantattribute::query()
            ->select('variant_id')
            ->whereIn('attribute_id', $request->attribute_id)
            ->whereIn('value_id', $request->value_id)
            ->where('shop_id', $request->shop_id)
            ->where('product_id', $request->product_id)
            ->groupBy('variant_id');

        // Thêm điều kiện để đảm bảo mỗi cặp attribute_id và value_id đều tồn tại
        foreach ($request->attribute_id as $index => $attr_id) {
            $value_id = $request->value_id[$index];
            $query->havingRaw('SUM(CASE WHEN attribute_id = ? AND value_id = ? THEN 1 ELSE 0 END) > 0', [$attr_id, $value_id]);
        }
        // Đảm bảo số lượng attribute khớp với số lượng đã chọn
        $query->havingRaw('COUNT(DISTINCT attribute_id) = ?', [count($request->attribute_id)]);
        $result = $query->first();
        if (!$result) {
            return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
        }
        $productVariant = product_variants::where('id', $result->variant_id)->first();

        $productExist = ProducttocartModel::where('cart_id', $cart_to_users->id)

            ->where('product_id', $productVariant->product_id)
            ->where('variant_id', $productVariant->id)

            ->first();

        if ($productExist) {
            ProducttocartModel::where('id', $productExist->id)->update([
                'quantity' => $productExist->quantity + ($request->quantity ?? 1),
            ]);
            return response()->json(['success' => 'Sản phẩm đã có trong giỏ hàng, cập nhật số lượng thành công'], 200);
        }

        $product_to_cart = ProducttocartModel::create([
            'cart_id' => $cart_to_users->id,
            'product_id' => $productVariant->product_id,
            'quantity' => $request->quantity ?? 1,

            'variant_id' => $productVariant->id,
            'shop_id' => $request->shop_id,
            'status' => 1,
        ]);

        return response()->json($product_to_cart, 200);
    }



    public function update(Request $request, string $id)
    {
        $updated = ProducttocartModel::where('id', $id)->update([
            'quantity' => $request->quantity,
        ]);
        // dd($updated);
        if ($updated) {
            $cart_to_users = Cart_to_usersModel::where('user_id', auth()->user()->id)->first();
            $all_products_to_cart_to_users = ProducttocartModel::where('cart_id', $cart_to_users->id)->get();
            return response()->json($all_products_to_cart_to_users, 200);
        }
        return response()->json(['error' => 'Update failed'], 400);
    }

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
