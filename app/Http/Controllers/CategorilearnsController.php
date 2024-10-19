<?php

namespace App\Http\Controllers;
use App\Http\Requests\CategoriLearnRequest;
use App\Models\Categori_learnModel;
use Illuminate\Http\Request;

class CategorilearnsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Categori_learn = Categori_learnModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Categori_learn ,
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriLearnRequest $request )
    {
        $dataInsert = [
            "title"=> $request->title,
            "status"=> $request->status,
            'create_by' => auth()->user()->id,
            "created_at"=> now(),
        ];
        Categori_learnModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu categori_learn",
            'data' => Categori_learnModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Categori_learn = Categori_learnModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Categori_learn,
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
    public function update(CategoriLearnRequest $request, string $id)
{
    $Categori_learn = Categori_learnModel::findOrFail($id);

    $Categori_learn->update([
        "title" => $request->title,
        "status" => $request->status,
        'update_by' => auth()->user()->id,
        "updated_at" => now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu categori_learn",
        'roles' => Categori_learnModel::all(),
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Categori_learn = Categori_learnModel::findOrFail($id);
            $Categori_learn->delete();
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
