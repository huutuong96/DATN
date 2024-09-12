<?php

namespace App\Http\Controllers;

use App\Http\Requests\LearnRequest;
use App\Models\LearnModel;

use Illuminate\Http\Request;

class LearnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Learn = LearnModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Learn ,
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
    public function store(Request $request)
    {
       
        $dataInsert = [
            "title"=> $request->title,
            "content"=> $request->content,
            "status"=> $request->status,
            "category_id"=> $request->category_id,
            'create_by' => auth()->user()->id,
            "created_at"=> now(),
        ];
        LearnModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu Learn",
            'data' => LearnModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Learn = LearnModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Learn,
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
    public function update(LearnRequest $request, string $id)
{
    $Learn = LearnModel::findOrFail($id);

    $Learn->update([
        "title" => $request->title,
        "content" => $request->content,
        "status" => $request->status,
        "category_id"=>$request->category_id,
        'update_by' => auth()->user()->id,
        "updated_at" => now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu Learn",
        'roles' => LearnModel::all(),
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Learn = LearnModel::findOrFail($id);
            $Learn->delete();
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
