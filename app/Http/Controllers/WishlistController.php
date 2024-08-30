<?php

namespace App\Http\Controllers;
use App\Models\WishlistModel;
use Illuminate\Http\Request;
use App\Http\Requests\WishlistRequest;
class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Wishlist = WishlistModel::all();

        if($Wishlist->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Wishlist nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $Wishlist
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
    public function store(WishlistRequest $request)
    {
        $dataInsert = [
            'status' => $request->status,
            'user_id' => $request->user_id,
            'product_id' => $request->product_id
        ];

        try {
            $Wishlist = WishlistModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Wishlist thành công",
                'data' => $Wishlist
            ];
            return response()->json($dataDone, 200);
        } catch (\Exception $e ) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Wishlist không thành công",
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
        $Wishlist = WishlistModel::find($id);

        if (!$Wishlist) {
            return response()->json([
                'status' => false,
                'message' => "Wishlist không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $Wishlist
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
    public function update(WishlistRequest $request, string $id)
    {
        $Wishlist = WishlistModel::find($id);

        if (!$Wishlist) {
            return response()->json([
                'status' => false,
                'message' => "Ship không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "status" => $request->status ?? $Wishlist->status,
            'product_id' => $request->product_id,
        ];

        try {
            $Wishlist->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Wishlist đã được cập nhật",
                    'data' => $Wishlist
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Wishlist không thành công",
                    'error' => $th->getMessage()
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Wishlist = WishlistModel::find($id);

        try {
            if (!$Wishlist) {
                return response()->json([
                    'status' => false,
                    'message' => "Wishlist không tồn tại"
                ], 404);
            }
    
            $Wishlist->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Wishlist đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Wishlist không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
