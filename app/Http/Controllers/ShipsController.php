<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipRequest;
use App\Models\ShipsModel;
use Illuminate\Pagination\Paginator;

class ShipsController extends Controller
{
    public function index()
    {
        $perPage = 10; // Number of items per page
        $ships = ShipsModel::where('status', 1)->paginate($perPage);

        if ($ships->isEmpty()) {
            return $this->errorResponse("Không tồn tại Ship nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", [
            'ships' => $ships->items(),
            'current_page' => $ships->currentPage(),
            'per_page' => $ships->perPage(),
            'total' => $ships->total(),
            'last_page' => $ships->lastPage(),
        ]);
    }

    public function store(ShipRequest $request)
    {
        try {
            $ship = ShipsModel::create($request->validated());
            return $this->successResponse("Thêm Ship thành công", $ship);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm Ship không thành công", $th->getMessage());
        }
    }

    public function show(string $id)
    {
        $ship = ShipsModel::find($id);

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $ship);
    }

    public function update(ShipRequest $request, string $id)
    {
        $ship = ShipsModel::find($id);

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        try {
            $ship->update($request->validated());
            return $this->successResponse("Ship đã được cập nhật", $ship);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật Ship không thành công", $th->getMessage());
        }
    }

    public function destroy($id)
    {
        $ship = ShipsModel::find($id);

        if (!$ship) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }

        try {
            $ship->delete();
            return $this->successResponse("Ship đã được xóa");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa Ship không thành công", $th->getMessage());
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

    public function errorResponse($message, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], 400);
    }
}
