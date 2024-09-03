<?php

namespace App\Http\Controllers;
use App\Models\ProducttocartModel;
use App\Models\Cart_to_usersModel;
use App\Http\Requests\ProducttocartRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProducttocartController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Product_to_cart = ProducttocartModel::all();

        if($Product_to_cart->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Product_to_cart nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $Product_to_cart
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProducttocartRequest $request)
{
    // Xác thực người dùng từ JWT Token
    $user = JWTAuth::parseToken()->authenticate();

    // Tạo giỏ hàng cho user 
    $cart_user = $this->createCart_to_user($request, $user->id);

    // Lấy giỏ hàng của user
    $cart_user = Cart_to_usersModel::where('user_id', $user->id)->first();

    // Kiểm tra nếu sản phẩm đã tồn tại trong giỏ hàng
    $cartItem = ProducttocartModel::where('cart_id', $cart_user->id)
                    ->where('product_id', $request->product_id)
                    ->first();

    if ($cartItem) {
        // Nếu sản phẩm đã có trong giỏ hàng, tăng số lượng
        $cartItem->quantity += $request->quantity;
        $cartItem->save();

        return response()->json([
            'status' => true,
            'message' => "Số lượng sản phẩm đã được cập nhật",
            'cartItem' => $cartItem,
        ], 200);
    } else {
        // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới vào giỏ hàng
        $dataInsert = [
            'cart_id' => $cart_user->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ];

        try {
            $cartItem = ProducttocartModel::create($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "Sản phẩm đã được thêm vào giỏ hàng",
                'cartItem' => $cartItem,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm sản phẩm vào giỏ hàng không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

    

        // $dataInsert = [
        //     'quantity' => $request->quantity,
        //     'status' => $request->status,
        //     'cart_id' => $request->cart_id,
        //     'product_id' => $request->product_id,
        // ];

        // try {
        //     $Product_to_cart = ProducttocartModel::create($dataInsert);
        //     $dataDone = [
        //         'status' => true,
        //         'message' => "Thêm Product_to_cart thành công",
        //         'data' => $Product_to_cart
        //     ];
        //     return response()->json($dataDone, 200);
        // } catch (\Exception $e ) {
        //     $dataDone = [
        //         'status' => false,
        //         'message' => "Thêm Product_to_cart không thành công",
        //         'error' =>$e->getMessage()
        //     ];
        //     return response()->json($dataDone);
        // }
        
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Product_to_cart = ProducttocartModel::find($id);

        if (!$Product_to_cart) {
            return response()->json([
                'status' => false,
                'message' => "Product_to_cart không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $Product_to_cart
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProducttocartRequest $request, string $id)
{
    $Product_to_cart = ProducttocartModel::findOrFail($id);
    if (!$Product_to_cart) {
        return response()->json([
            'status' => false,
            'message' => "Ship không tồn tại"
        ], 404);
    }
    $Product_to_cart->update([
        'quantity' => $request->quantity,
        'status' => $request->status,
        // 'cart_id' => $request->cart_id,
        'product_id' => $request->product_id,
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu Product_to_cart",
        'Product_to_cart' => $Product_to_cart,
    ];
    return response()->json($dataDone, 200);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Product_to_cart = ProducttocartModel::find($id);

        try {
            if (!$Product_to_cart) {
                return response()->json([
                    'status' => false,
                    'message' => "Product_to_cart không tồn tại"
                ], 404);
            }
    
            $Product_to_cart->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Product_to_cart đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Product_to_cart không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }




    ////
    private function createCart_to_user($request, $user_id)
    {
        return Cart_to_usersModel::create([
            'status' => $request->status,
            'user_id' => $user_id
        ]);
    }
    
}
