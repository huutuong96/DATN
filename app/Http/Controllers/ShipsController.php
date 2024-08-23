<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipRequest;
use App\Models\ShipsModel;
use Illuminate\Http\Request;

class ShipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $ships = ShipsModel::all();

        if($ships->isEmpty()){
            return response()->json(
                [
                    'status' => false,
                    'message' => "Không tồn tại Ship nào",
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy dữ liệu thành công',
            'data' => $ships
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
    public function store(ShipRequest $request)
    {

        $dataInsert = [
            "name" => $request->name,
            "description" => $request->description,
            "status" => $request->status,
        ];

        try {
            $ships = ShipsModel::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "Thêm Ship thành công",
                'data' => $ships
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            $dataDone = [
                'status' => false,
                'message' => "Thêm Ship không thành công",
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

        $ships = ShipsModel::find($id);

        if (!$ships) {
            return response()->json([
                'status' => false,
                'message' => "Ship không tồn tại"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $ships
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
    public function update(ShipRequest $request, string $id)
    {

        $ships = ShipsModel::find($id);

        if (!$ships) {
            return response()->json([
                'status' => false,
                'message' => "Ship không tồn tại"
            ], 404);
        }

        $dataUpdate = [
            "name" => $request->name,
            "description" => $request->description,
            "status" => $request->status,
        ];

        try {
            $ships->update($dataUpdate);
            return response()->json(
                [
                    'status' => true,
                    'message' => "Ship đã được cập nhật",
                    'data' => $ships
                ], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật Ship không thành công",
                    'error' => $th->getMessage()
                ]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $ships = ShipsModel::find($id);

        try {
            if (!$ships) {
                return response()->json([
                    'status' => false,
                    'message' => "Ship không tồn tại"
                ], 404);
            }
    
            $ships->delete();
    
            return response()->json([
                'status' => true,
                'message' => "Ship đã được xóa"
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa Ship không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
