<?php

namespace App\Http\Controllers;
use App\Models\ProducttocartModel;
use App\Http\Requests\ProducttocartRequest;
use Illuminate\Http\Request;

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
        $dataInsert = [
            'quantity' => $request->quantity,
            'status' => $request->status,
            'cart_id' => $request->cart_id,
            'product_id' => $request->product_id,
        ];

        try {
            $Product_to_cart = ProducttocartModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Product_to_cart thành công",
                'data' => $Product_to_cart
            ];
            return response()->json($dataDone, 200);
        } catch (\Exception $e ) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Product_to_cart không thành công",
                'error' =>$e->getMessage()
            ];
            return response()->json($dataDone);
        }

    }

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
        'cart_id' => $request->cart_id,
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
}
