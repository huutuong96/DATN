<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProducttoshopModel;
use App\Http\Requests\ProducttoshopRequest;
class ProducttoshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Producttoshop = ProducttoshopModel::all();

        if($Producttoshop->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Producttoshop nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $Producttoshop
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
    public function store(ProducttoshopRequest $request)
    {
        $dataInsert = [
            'url_share' => $request->url_share,
            'status' => $request->status,
            'product_id' => $request->product_id,
            'shop_id' => $request->shop_id,
            'created_at' => now(),
        ];

        try {
            $Producttoshop = ProducttoshopModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Producttoshop thành công",
                'data' => $Producttoshop
            ];
            return response()->json($dataDone, 200);
        } catch (\Exception $e ) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Producttoshop không thành công",
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
        $Producttoshop = ProducttoshopModel::find($id);

        if (!$Producttoshop) {
            return response()->json([
                'status' => false,
                'message' => "Producttoshop không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $Producttoshop
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
    public function update(ProducttoshopRequest $request, string $id)
    {
        $Categori_learn = ProducttoshopModel::findOrFail($id);
    
        $Categori_learn->update([
           'url_share' => $request->url_share,
            'status' => $request->status,
            'product_id' => $request->product_id,
            'shop_id' => $request->shop_id,
            'updated_at' => now(),
        ]);
    
        $dataDone = [
            'status' => true,
            'message' => "đã lưu categori_learn",
            'roles' => $Categori_learn,
        ];
        return response()->json($dataDone, 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Producttoshop = ProducttoshopModel::find($id);

        try {
            if (!$Producttoshop) {
                return response()->json([
                    'status' => false,
                    'message' => "Producttoshop không tồn tại"
                ], 404);
            }
    
            $Producttoshop->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Producttoshop đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Producttoshop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
