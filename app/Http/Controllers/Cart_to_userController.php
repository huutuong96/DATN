<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart_to_userRequest;
use App\Models\Cart_to_usersModel;
use Illuminate\Http\Request;

class Cart_to_userController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart_to_user = Cart_to_usersModel::all();

        if($cart_to_user->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Cart_to_user nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $cart_to_user
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
    public function store(Cart_to_userRequest $request)
    {
        $dataInsert = [
            'status' => $request->status,
            'user_id' => $request->user_id
        ];

        try {
            $cart_to_user = Cart_to_usersModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Cart_to_user thành công",
                'data' => $cart_to_user
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Cart_to_user không thành công",
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
        $cart_to_user = Cart_to_usersModel::find($id);

        if (!$cart_to_user) {
            return response()->json([
                'status' => false,
                'message' => "Cart_to_user không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $cart_to_user
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
    public function update(Cart_to_userRequest $request, string $id)
    {
        $cart_to_user = Cart_to_usersModel::find($id);

        if (!$cart_to_user) {
            return response()->json([
                'status' => false,
                'message' => "Ship không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "status" => $request->status ?? $cart_to_user->status,
            "user_id" => $request->user_id ?? $cart_to_user->user_id,
        ];

        try {
            $cart_to_user->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Cart_to_user đã được cập nhật",
                    'data' => $cart_to_user
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Cart_to_user không thành công",
                    'error' => $th->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart_to_user = Cart_to_usersModel::find($id);

        try {
            if (!$cart_to_user) {
                return response()->json([
                    'status' => false,
                    'message' => "Cart_to_user không tồn tại"
                ], 404);
            }
    
            $cart_to_user->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Cart_to_user đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Cart_to_user không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
