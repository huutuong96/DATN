<?php

namespace App\Http\Controllers;
use App\Models\CommentsModel;
use App\Http\Requests\CommentsRequest;
use Illuminate\Http\Request;
use JWTAuth;
class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $Comments = CommentsModel::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Dữ liệu được lấy thành công',
                'data' =>  $Comments ,
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
    public function store(CommentsRequest $request )
    {
        $user = JWTAuth::parseToken()->authenticate();
        $dataInsert = [
            "title"=> $request->title,
            "content"=> $request->content,
            "rate"=> $request->rate,
            "status"=> $request->status,
            "parent_id"=> $request->parent_id,
            "product_id"=> $request->product_id,
            "user_id"=> $user->id,
        ];
        CommentsModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "đã lưu Comments",
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
            $Comments = CommentsModel::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $Comments,
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
    public function update(CommentsRequest $request, string $id)
{
    $Comments = CommentsModel::findOrFail($id);
    if (!$Comments) {
        return response()->json([
            'status' => false,
            'message' => "Ship không tồn tại"
        ], 404);
    }
    $Comments->update([
        "title"=> $request->title,
        "content"=> $request->content,
        "rate"=> $request->rate,
        "status"=> $request->status,
        // "parent_id"=> $request->parent_id,
        // "product_id"=> $request->product_id,
        // "user_id"=> $request->user_id,
        "updated_at" => now(),
    ]);

    $dataDone = [
        'status' => true,
        'message' => "đã lưu Comments",
        'Comments' => $Comments,
    ];
    return response()->json($dataDone, 200);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $Comments = CommentsModel::findOrFail($id);
            $Comments->delete();
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
