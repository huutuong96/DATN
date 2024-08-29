<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VoucherToShop;
use App\Http\Requests\VoucherRequest;

class VoucherToShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
<<<<<<< HEAD
        $voucherShop = voucherToShop::all();
        if($voucherShop->isEmpty()){
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
                'data' => $voucherShop,
            ]
        );
=======
        $voucherMain = VoucherToShop::all(); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'
        if ($voucherMain->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => "Không tồn tại voucher main nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $voucherMain,
        ]);
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a
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
            'shop_id' => $request->shop_id,
            'status' => $request->status,
        ];

        try {
<<<<<<< HEAD
            $voucherShop = voucherToShop::create( $dataInsert );

            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucherShop thành công",
                    'data' => $voucherShop,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Thêm voucherShop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
=======
            $voucherMain = VoucherToShop::create($dataInsert); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'

            return response()->json([
                'status' => true,
                'message' => "Thêm voucher main thành công",
                'data' => $voucherMain,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false, // Sửa lại 'status' thành false để thể hiện lỗi
                'message' => "Thêm voucher main không thành công",
                'error' => $th->getMessage(),
            ]);
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
<<<<<<< HEAD
        $voucherShop = voucherToShop::find($id);
        if(!$voucherShop){
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
                'data' => $voucherShop,
            ]
        );
=======
        $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'
        if (!$voucherMain) {
            return response()->json([
                'status' => true,
                'message' => "Không tồn tại voucher main nào",
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $voucherMain,
        ]);
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a
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
<<<<<<< HEAD
        $voucherShop = voucherToShop::find($id);

        // Kiểm tra xem rqt có tồn tại không
        if (!$voucherShop) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "voucher main không tồn tại",
                ],
                404
            );
=======
        $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'

        if (!$voucherMain) {
            return response()->json([
                'status' => false,
                'message' => "voucher main không tồn tại",
            ], 404);
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a
        }

        $dataUpdate = [
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'condition' => $request->condition,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
        ];

        try {
<<<<<<< HEAD
            // Cập nhật bản ghi
            $voucherShop->update($dataUpdate);

            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật voucher main thành công",
                    'data' => $voucherShop,
                ]
            );
=======
            $voucherMain->update($dataUpdate);

            return response()->json([
                'status' => true,
                'message' => "Cập nhật voucher main thành công",
                'data' => $voucherMain,
            ]);
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật voucher main không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
<<<<<<< HEAD
            $voucherShop = voucherToShop::find($id);
=======
            $voucherMain = VoucherToShop::find($id); // Sửa tên class từ 'voucherToShop' thành 'VoucherToShop'
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a

            if (!$voucherShop) {
                return response()->json([
                    'status' => false,
                    'message' => 'voucher main không tồn tại',
                ], 404);
            }

<<<<<<< HEAD
            // Xóa bản ghi
            $voucherShop->delete();
=======
            $voucherMain->delete();
>>>>>>> cb7d0c16db4fd4033cb6c9a53ce8c43771a9054a

            return response()->json([
                'status' => true,
                'message' => 'Xóa voucher main thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa voucher main không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
