<?php

namespace App\Http\Controllers;
use App\Models\RanksModel;
use App\Http\Requests\RankRequest;
use Illuminate\Http\Request;

class RanksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ranks = RanksModel::all();
        if($ranks->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại rank nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $ranks,
            ]
        );
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
    public function store(RankRequest $request)
    {
        $dataInsert = [
            "title"=> $request->title,
            "description"=> $request->description,
            "status"=> $request->status,
        ];
        RanksModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "Rank Đã được lưu",
            'Ranks' => RanksModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rank = RanksModel::find($id);
        if(!$rank){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại rank nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $rank,
            ]
        );
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
    public function update(RankRequest $request, string $id)
    {
        // Tìm rank theo ID
        $rank = RanksModel::find($id);

        // Kiểm tra xem rqt có tồn tại không
        if (!$rank) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "rank không tồn tại",
                ],
                404
            );
        }

        // Cập nhật dữ liệu
        $dataUpdate = [
            "title"=> $request->title,
            "description"=> $request->description ?? null,
            "status"=> $request->status,
            'created_at' => $request->created_at ?? $rank->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];

        try {
            // Cập nhật bản ghi
            $rank->update($dataUpdate);

            return response()->json(
                [
                    'status' => true,
                    'message' => "Cập nhật rank thành công",
                    'data' => $rank,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật rank không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $rank = RanksModel::find($id);

            if (!$rank) {
                return response()->json([
                    'status' => false,
                    'message' => 'rank không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $rank->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa rank thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa rank không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}