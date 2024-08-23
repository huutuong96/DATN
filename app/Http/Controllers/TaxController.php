<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TaxRequest;
use App\Models\Tax;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxs = Tax::all();
        if($taxs->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại tax nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $taxs,
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
    public function store(TaxRequest $rqt)
    {
        $dataInsert = [
            'title' => $rqt->title,
            'type' => $rqt->type,
            'tax_number' => $rqt->tax_number,
            'status' => $rqt->status,
        ];

        try {
            $tax = Tax::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm tax thành công",
                    'data' => $tax,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm tax không thành công",
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
        $tax = Tax::find($id);
        if(!$tax){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại tax nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $tax,
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
    public function update(taxRequest $rqt, string $id)
    {
        // Tìm tax theo ID
        $tax = Tax::find($id);

        // Kiểm tra xem rqt có tồn tại không
        if (!$tax) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "tax không tồn tại",
                ],
                404
            );
        }

        // Cập nhật dữ liệu
        $dataUpdate = [
            'title' => $rqt->title,
            'type' => $rqt->type,
            'tax_number' => $rqt->tax_number,
            'status' => $rqt->status,
            'created_at' => $rqt->created_at ?? $tax->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];

        try {
            // Cập nhật bản ghi
            $tax->update($dataUpdate);

            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật Tax thành công",
                    'data' => $tax,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật tax không thành công",
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
            $tax = Tax::find($id);

            if (!$tax) {
                return response()->json([
                    'status' => false,
                    'message' => 'tax không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $tax->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa tax thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa tax không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
