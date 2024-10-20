<?php

namespace App\Http\Controllers;

use App\Models\RanksModel;
use App\Http\Requests\RankRequest;

class RanksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ranks = RanksModel::all();

        if ($ranks->isEmpty()) {
            return $this->errorResponse("Không tồn tại rank nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $ranks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RankRequest $request)
    {
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'condition' => $request->condition,
            'value' => $request->value,
            'limitValue' => $request->limitValue,
        ];
        try {
            $rank = RanksModel::create($dataInsert);
            return $this->successResponse("Thêm rank thành công", $rank);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm rank không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rank = RanksModel::find($id);

        if (!$rank) {
            return $this->errorResponse("Rank không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $rank);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RankRequest $request, string $id)
    {
        $rank = RanksModel::find($id);

        if (!$rank) {
            return $this->errorResponse("Rank không tồn tại", 404);
        }

        try {
            $rank->update($request->validated());
            return $this->successResponse("Cập nhật rank thành công", $rank);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật rank không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rank = RanksModel::find($id);

        if (!$rank) {
            return $this->errorResponse("Rank không tồn tại", 404);
        }

        try {
            $rank->delete();
            return $this->successResponse("Xóa rank thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa rank không thành công", $th->getMessage());
        }
    }

    public function successResponse($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }
    private function errorResponse($message, $error = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
