<?php

namespace App\Http\Controllers;

use App\Http\Requests\Learning_sellerRequest;
use App\Models\Learning_sellerModel;
use Illuminate\Http\Request;
use App\Models\Shop;

class Learning_sellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($shop_id)
    {
        $learning_seller = Shop::with('learns')->find($shop_id);
        if (!$learning_seller) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Learning_seller nào"
                ]
            );
        }
        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $learning_seller
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
    public function store(Learning_sellerRequest $request)
    {
        $dataInsert = [
            'learn_id' => $request->learn_id,
            'shop_id' => $request->shop_id,
            'status' => $request->status,
            'create_by' => $request->create_by
        ];

        try {
            $learning_seller = Learning_sellerModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Order thành công",
                'data' => $learning_seller
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Learning_seller không thành công",
                'error' => $th->getMessage()
            ];
            return response()->json($dataDone);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($shop_id, string $id)
    {
        $learning_seller = Shop::with('learns')->find($shop_id);

        if (!$learning_seller) {
            return response()->json([
                'status' => false,
                'message' => "Learning_seller không tồn tại"
            ], 404);
        }
        foreach ($learning_seller->learns as $key => $learn) {
            if($learn->id == $id){
                return response()->json([
                    'status' => true,
                    'message' => "Lấy dữ liệu thành công",
                    'data' => $learn
                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'message' => "Learning_seller không tồn tại"
        ], 404);

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
    public function update(Learning_sellerRequest $request, string $id)
    {
        $learning_seller = Learning_sellerModel::find($id);

        if (!$learning_seller) {
            return response()->json([
                'status' => false,
                'message' => "Order không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            'shop_id' => $request->shop_id ?? $learning_seller->shop_id,
            'learn_id' => $request->learn_id ?? $learning_seller->learn_id,
            'status' => $request->status ?? $learning_seller->status,
            'update_by' => $request->update_by
        ];

        try {
            $learning_seller->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Learning_seller đã được cập nhật",
                    'data' => $learning_seller
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Learning_seller không thành công",
                    'error' => $th->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $learning_seller = Learning_sellerModel::find($id);

        try {
            if (!$learning_seller) {
                return response()->json([
                    'status' => false,
                    'message' => "Learning_seller không tồn tại"
                ], 404);
            }

            $learning_seller->delete();

            return response()->json([
                'status' => true,
                'message' => "Learning_seller đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Learning_seller không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
