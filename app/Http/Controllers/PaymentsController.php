<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\PaymentsModel;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $payments = PaymentsModel::all();

        if($payments->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Payment nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $payments
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
    public function store(PaymentRequest $request)
    {

        $dataInsert = [
            "name" => $request->name,
            "description" => $request->description,
            "status" => $request->status,
        ];

        try {
            $payments = PaymentsModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Payment thành công",
                'data' => $payments
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Payment không thành công",
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

        $payments = PaymentsModel::find($id);

        if (is_null($payments)) {
            return response()->json([
                'status' => false,
                'message' => "Payment không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $payments
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
    public function update(PaymentRequest $request, string $id)
    {

        $payments = PaymentsModel::find($id);

        if (!$payments) {
            return response()->json([
                'status' => false,
                'message' => "Payment không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "name" => $request->name ?? $payments->name,
            "description" => $request->description ?? $payments->description,
            "status" => $request->status ?? $payments->status,
        ];

        try {
            $payments->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Payment đã được cập nhật",
                    'data' => $payments
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Payment không thành công",
                    'error' => $th->getMessage()
                ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $paymens = PaymentsModel::find($id);

        try {
            if (!$paymens) {
                return response()->json([
                    'status' => false,
                    'message' => "Paymen không tồn tại"
                ], 404);
            }

            $paymens->delete();

            return response()->json([
                'status' => true,
                'message' => "Paymen đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Paymen không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
