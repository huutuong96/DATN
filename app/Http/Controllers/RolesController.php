<?php

namespace App\Http\Controllers;
use App\Models\RolesModel;
use App\Http\Requests\RoleRequest;

use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Roles = RolesModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Roles ,
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
    public function store(RoleRequest $request)
    {
        $dataInsert = [
            "title"=> $request->title,
            "description"=> $request->description,
            "status"=> $request->status,
            "create_by"=> $request->create_by,
            "created_at"=> now(),
        ];
        RolesModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "Role Đã được lưu",
            'roles' => RolesModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Role = RolesModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Role,
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
    public function update(RoleRequest $request, string $id)
    {
        $Role = RolesModel::findOrFail($id);

        $Role->update([
            "title"=> $request->title,
            "description"=> $request->description,
            "status"=> $request->status,
            "create_by"=> $request->create_by,
            "updated_at"=> now(),
        ]);
    
        $dataDone = [
            'status' => true,
            'message' => "đã lưu Role",
            'roles' => RolesModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Role = RolesModel::findOrFail($id);
            $Role->delete();
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
