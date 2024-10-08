<?php

namespace App\Http\Controllers;
use App\Models\voucher_to_main;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\VoucherRequest;


use Illuminate\Http\Request;

class VoucherToMainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherMain = voucher_to_main::all();
        if($voucherMain->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại voucher main nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $voucherMain,
            ]
        );
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
    public function store(VoucherRequest $request)
    {
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'status' => $request->status,
        ];


        try {
            $voucherMain = voucher_to_main::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucherMain thành công",
                    'data' => $voucherMain,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucherMain không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucherMain = voucher_to_main::find($id);
        if(!$voucherMain){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại voucher Main nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $voucherMain,
            ]
        );
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
    public function update(VoucherRequest $request, string $id)
    {
        $voucherMain = voucher_to_main::find($id);

        // Kiểm tra xem rqt có tồn tại không
        if (!$voucherMain) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "voucher main không tồn tại",
                ],
                404
            );
        }

        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'status' => $request->status,
        ];

        try {
            // Cập nhật bản ghi
            $voucherMain->update($dataUpdate);

            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật voucher main thành công",
                    'data' => $voucherMain,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật voucher main không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $voucherMain = voucher_to_main::find($id);

            if (!$voucherMain) {
                return response()->json([
                    'status' => false,
                    'message' => 'voucher main không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $voucherMain->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa voucher main thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa voucher main không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
