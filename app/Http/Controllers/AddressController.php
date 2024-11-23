<?php

namespace App\Http\Controllers;
use App\Models\AddressModel;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $Address = AddressModel::where('user_id', $user->id)->get();
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
        $user = JWTAuth::parseToken()->authenticate();
        $Address = [
            "address"=> $request->address ?? null,
            "province" => $request->province ?? null,
            "province_id" => $request->province_id ?? null,
            "district" => $request->district ?? null,
            "district_id" => $request->district_id ?? null,
            "ward" => $request->ward ?? null,
            "ward_id" => $request->ward_id ?? null,
            "type"=> $request->type ?? null,
            "default"=> $request->default ?? 0,
            "status"=> $request->status ?? 1,
            "user_id" => $user->id,
            "name"=> $request->status ?? null,
            "phone" => $request->status ?? null,
        ];
        AddressModel::create($Address);
        $dataDone = [
            'status' => true,
            'message' => "Địa chỉ đã được lưu",
            'address' => AddressModel::where('user_id', auth()->user()->id)->get(),
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
    public function update(Request $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        AddressModel::where('user_id', $user->id)->update(['default' => 0]);
        $Address = AddressModel::findOrFail($id);
        $Address->update([
            "address"=> $request->address ?? $Address->address,
            "province" => $request->province ?? $Address->province,
            "province_id" => $request->province_id ?? $Address->province_id,
            "district" => $request->district ?? $Address->district,
            "district_id" => $request->district_id ?? $Address->district_id,
            "ward" => $request->ward ?? $Address->ward,
            "ward_id" => $request->ward_id ?? $Address->ward_id,
            "type"=> $request->type ?? $Address->type,
            "default"=> $request->default ?? 0,
            "status"=> $request->status ?? 1,
            "user_id" => $user->id,
            "name"=> $request->status ?? null,
            "phone" => $request->status ?? null,
        ]);

        $dataDone = [
            'status' => true,
            'message' => "đã lưu địa chỉ",
            'data' =>  $Address  ,
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
