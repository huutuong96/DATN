<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProgramtoshopRequest;
use App\Models\ProgramtoshopModel;
use Illuminate\Http\Request;

class ProgramtoshopController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Program_to_shop = ProgramtoshopModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Program_to_shop ,
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
    public function store(ProgramtoshopRequest $request)
    {
       
        $dataInsert = [
            "program_id"=> $request->program_id,
            "shop_id"=> $request->shop_id,
            "created_at"=> now(),
        ];
        ProgramtoshopModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu Program_to_shop",
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
            $Program_to_shop = ProgramtoshopModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Program_to_shop,
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
    public function update(ProgramtoshopRequest $request, string $id)
{
    $Program_to_shop = ProgramtoshopModel::findOrFail($id);

    $Program_to_shop->update([
           "program_id"=> $request->program_id,
            "shop_id"=> $request->shop_id,
            "update"=> now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu Program_to_shop",
        'roles' =>     $Program_to_shop,
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Program_to_shop = ProgramtoshopModel::findOrFail($id);
            $Program_to_shop->delete();
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
