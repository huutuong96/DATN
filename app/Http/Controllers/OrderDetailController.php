<?php

namespace App\Http\Controllers;
use App\Http\Requests\OrderDetailRequest;
use App\Models\OrderDetailsModel;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $OrderDetails = OrderDetailsModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $OrderDetails ,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
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
    public function store(OrderDetailRequest $request)
    {
       
        $dataInsert = [
            "subtotal"=> $request->subtotal,
            "status"=> $request->status,
            "order_id"=> $request->order_id,
            "product_id"=> $request->category_id,
            'create_by' => $request->input('create_by') ?? null,
            "created_at"=> now(),
        ];
        OrderDetailsModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu OrderDetail",
            'data' => $dataInsert,
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $OrderDetails = OrderDetailsModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $OrderDetails,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 400);
        }
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
    public function update(OrderDetailRequest $request, string $id)
{
    $OrderDetails = OrderDetailsModel::findOrFail($id);

    $OrderDetails->update([
            "subtotal"=> $request->subtotal,
            "status"=> $request->status,
            // "order_id"=> $request->order_id,
            // "product_id"=> $request->product_id,
            'update_by' => $request->input('update_by') ?? null,
            "created_at"=> now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu Learn",
        'roles' =>     $OrderDetails,
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $OrderDetails = OrderDetailsModel::findOrFail($id);
            $OrderDetails->delete();
            return response()->json([
                'status' => "success",
                'message' => 'Xóa thành công',
                'data' => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
