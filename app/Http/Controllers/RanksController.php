<?php

namespace App\Http\Controllers;

use App\Models\RanksModel;
use App\Http\Requests\RankRequest;
use Illuminate\Support\Facades\Cache;

class RanksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ranks = Cache::remember('all_ranks', 60 * 60, function () {
            return RanksModel::all();
        });

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
        try {
            $rank = RanksModel::create($request->validated());
            Cache::forget('all_ranks');
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
        $rank = Cache::remember('rank_' . $id, 60 * 60, function () use ($id) {
            return RanksModel::find($id);
        });

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
        $rank = Cache::remember('rank_' . $id, 60 * 60, function () use ($id) {
            return RanksModel::find($id);
        });

        if (!$rank) {
            return $this->errorResponse("Rank không tồn tại", 404);
        }

        try {
            $rank->update($request->validated());
            Cache::forget('rank_' . $id);
            Cache::forget('all_ranks');
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
            Cache::forget('rank_' . $id);
            Cache::forget('all_ranks');
            return $this->successResponse("Xóa rank thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa rank không thành công", $th->getMessage());
        }
    }


}
