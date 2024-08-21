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
    public function store(AddressRequest $request)
    {
        $dataInsert = [
            "address"=> $request->address,
            "create_by"=> $request->create_by,
        ];
        AddressModel::create($dataInsert);
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
