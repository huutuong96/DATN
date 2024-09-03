<?php

namespace App\Http\Controllers;
use App\Models\AddressModel;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Address = AddressModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Address ,
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
    public function store(AddressRequest $request)
    {
        $Address = [
            "address"=> $request->address,
            "type"=> $request->type,
            "default"=> $request->default,
            "status"=> $request->status,
            "user_id" => $request->user_id
        ];
        AddressModel::create($Address);
        $dataDone = [
            'status' => true,
            'message' => "address Đã được lưu",
            'address' => AddressModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Address = AddressModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Address,
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
    public function update(AddressRequest $request, string $id)
    {
        $Address = AddressModel::findOrFail($id);

        $Address->update([
            "address"=> $request->address,
            "type"=> $request->type,
            "status"=> $request->status,
            "default"=> $request->default,
            "updated_at"=> now(),
            "user_id" => $request->user_id
        ]);
    
        $dataDone = [
            'status' => true,
            'message' => "đã lưu Address",
            'roles' => AddressModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Address = AddressModel::findOrFail($id);
            $Address->delete();
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
