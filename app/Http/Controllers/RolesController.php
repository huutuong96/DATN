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
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
