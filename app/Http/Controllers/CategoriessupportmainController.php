<?php

namespace App\Http\Controllers;
use App\Models\Categoriessupportmain;
use App\Http\Requests\CategoriessupportmainRequest;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
class CategoriessupportmainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Categori_learn = Categoriessupportmain::all();
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
    public function store(request $request )
    {   
        $user = JWTAuth::parseToken()->authenticate();
        $dataInsert = [
            "content"=> $request->content,
            "status"=> $request->status,
            "index"=> $request->index,
            'create_by' => $user->id,
            "created_at"=> now(),
        ];
        $category_suport_main =Categoriessupportmain::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu categori_learn",
            'data' => $category_suport_main,
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $Categori_learn = Categoriessupportmain::findOrFail($id);
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
    public function update(Request $request, string $id)
{
    $user = JWTAuth::parseToken()->authenticate();
    $Categori_learn = Categoriessupportmain::findOrFail($id);

    $Categori_learn->update([
        "content" => $request->content,
        "status" => $request->status,
        "index"=> $request->index,
        'update_by' =>  $user->id,
        'updated_at' => now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu categori",
        'roles' =>  $Categori_learn,
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Categori_learn = Categoriessupportmain::findOrFail($id);
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
