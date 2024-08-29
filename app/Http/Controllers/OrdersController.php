<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\OrdersModel;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = OrdersModel::all();

        if($orders->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Order nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $orders
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
    public function store(OrderRequest $request)
    {
        $dataInsert = [
            'payment_id' => $request->payment_id,
            'ship_id' => $request->ship_id,
            'voucher_id' => $request->voucher_id,
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
            'create_by' => $request->create_by
        ];

        try {
            $orders = OrdersModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Order thành công",
                'data' => $orders
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Order không thành công",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $orders = OrdersModel::find($id);

        if (!$orders) {
            return response()->json([
                'status' => false,
                'message' => "Order không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $orders
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
    public function update(OrderRequest $request, string $id)
    {
        $orders = OrdersModel::find($id);

        if (!$orders) {
            return response()->json([
                'status' => false,
                'message' => "Order không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            'payment_id' => $request->payment_id ?? $orders->payment_id,
            'ship_id' => $request->ship_id ?? $orders->ship_id,
            'voucher_id' => $request->voucher_id ?? $orders->voucher_id,
            'user_id' => $request->user_id ?? $orders->user_id,
            'shop_id' => $request->shop_id ?? $orders->shop_id,
            'status' => $request->status ?? $orders->status,
            'update_by' => $request->update_by
        ];

        try {
            $orders->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Order đã được cập nhật",
                    'data' => $orders
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Order không thành công",
                    'error' => $th->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $orders = OrdersModel::find($id);

        try {
            if (!$orders) {
                return response()->json([
                    'status' => false,
                    'message' => "Order không tồn tại"
                ], 404);
            }
    
            $orders->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Order đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Order không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
