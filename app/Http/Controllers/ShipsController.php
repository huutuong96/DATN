<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipRequest;
use App\Models\ShipsModel;
use Illuminate\Support\Facades\Cache;

class ShipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ships = Cache::remember('all_ships', 60 * 60, function () {
            return ShipsModel::all();
        });

        if ($ships->isEmpty()) {
            return $this->errorResponse("Không tồn tại Ship nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $ships);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShipRequest $request)
    {
        try {
            $ship = ShipsModel::create($request->validated());
            Cache::forget('all_ships');
            return $this->successResponse("Thêm Ship thành công", $ship);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm Ship không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ship = Cache::remember('ship_' . $id, 60 * 60, function () use ($id) {
            return ShipsModel::find($id);
        });

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $ship);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShipRequest $request, string $id)
    {
        $ship = Cache::remember('ship_' . $id, 60 * 60, function () use ($id) {
            return ShipsModel::find($id);
        });

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        try {
            $ship->update($request->validated());
            Cache::forget('ship_' . $id);
            Cache::forget('all_ships');
            return $this->successResponse("Ship đã được cập nhật", $ship);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật Ship không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ship = ShipsModel::find($id);

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        try {
            $ship->delete();
            Cache::forget('ship_' . $id);
            Cache::forget('all_ships');
            return $this->successResponse("Ship đã được xóa");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa Ship không thành công", $th->getMessage());
        }
    }
}
