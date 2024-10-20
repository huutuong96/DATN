<?php

namespace App\Http\Controllers;

use App\Models\insurance;
use App\Models\ShipsModel;
use App\Models\ship_companies;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function ship_companies_index()
    {
        $ship_companies = ship_companies::where('status', 1)->get();
        if ($ship_companies->isEmpty()) {
            return $this->errorResponse("Không tồn tại Ship nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", $ship_companies);
    }

    public function ship_companies_store(Request $request)
    {
        $ship_companies = ship_companies::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);
        return $this->successResponse("Thêm dữ liệu thành công", $ship_companies);
    }

    public function ship_companies_show(string $id)
    {
        $ship_companies = ship_companies::find($id);
        if (!$ship_companies) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }
        return $this->successResponse("Lấy dữ liệu thành công", $ship_companies);
    }

    public function ship_companies_update(Request $request, string $id)
    {
        $ship_companies = ship_companies::find($id);
        if (!$ship_companies) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }
        $ship_companies->update([
            'name' => $request->name ?? $ship_companies->name,
            'code' => $request->code ?? $ship_companies->code,
            'status' => $request->status ?? $ship_companies->status,
        ]);
        return $this->successResponse("Cập nhật dữ liệu thành công", $ship_companies);
    }

    public function ship_companies_destroy(string $id)
    {
        $ship_companies = ship_companies::find($id);
        if (!$ship_companies) {
            return $this->errorResponse("Ship không tồn tại", 404);
        }
        $ship_companies->status = 0;
        $ship_companies->save();
        return $this->successResponse("Xóa dữ liệu thành công");
    }

    // ship service
    public function add_ship_service(Request $request)
    {
        $ship_service = ShipsModel::create([
            'name' => $request->name,
            'code' => strtoupper($request->code ?? Str::slug($request->name)),
            'description' => $request->description,
            'status' => $request->status,
            'fees' => $request->fees,
            'shop_id' => $request->shop_id,
            'ship_company_id' => $request->ship_company_id,
            'distance' => $request->distance,
        ]);
        return $this->successResponse('Thêm dịch vụ vận chuyển thành công', $ship_service);
    }

    public function ship_service_update(Request $request)
    {
        $ship_service = ShipsModel::find($id);
        if (!$ship_service) {
            return $this->errorResponse('Dịch vụ vận chuyển không tồn tại', 404);
        }
        $ship_service->update([
            'name' => $request->name ?? $ship_service->name,
            'code' => strtoupper($request->code ?? Str::slug($request->name) ?? $ship_service->code),
            'description' => $request->description ?? $ship_service->description,
            'status' => $request->status ?? $ship_service->status,
            'fees' => $request->fees ?? $ship_service->fees,
            'distance' => $request->distance ?? $ship_service->distance,
        ]);
        return $this->successResponse('Cập nhật dịch vụ vận chuyển thành công', $ship_service);
    }

    public function ship_service_delete(string $id)
    {
        $ship_service = ShipsModel::find($id);
        if (!$ship_service) {
            return $this->errorResponse('Dịch vụ vận chuyển không tồn tại', 404);
        }
        $ship_service->status = 0;
        $ship_service->save();
        return $this->successResponse('Xóa dịch vụ vận chuyển thành công');
    }

    public function ship_service_get_one(string $id)
    {
        $ship_service = ShipsModel::where('shop_id', $id)->where('status', 1)->first();
        if (!$ship_service) {
            return $this->errorResponse('Dịch vụ vận chuyển không tồn tại', 404);
        }
        return $this->successResponse('Lấy dịch vụ vận chuyển thành công', $ship_service);
    }

    public function ship_service_get_all(string $ship_companies_id)
    {
        $ship_service = ShipsModel::where('ship_company_id', $ship_companies_id)->get();
        if (!$ship_service) {
            return $this->errorResponse('Dịch vụ vận chuyển không tồn tại', 404);
        }
        return $this->successResponse('Lấy dịch vụ vận chuyển thành công', $ship_service);
    }

    public function insurance_store(Request $request)
    {
        $insurance_store = insurance::create([
            'name' => $request->name,
            'price' => $request->price,
            'code' => strtoupper($request->code ?? Str::slug($request->name)),
            'status' => $request->status,
            'ship_company_id' => $request->ship_company_id,
        ]);
        return $this->successResponse('Thêm bảo hiểm mua hàng thành công', $insurance_store);
    }

    public function insurance_update(Request $request, string $id)
    {
        $insurance = insurance::find($id);
        if (!$insurance) {
            return $this->errorResponse('Bảo hiểm không tồn tại', 404);
        }
        $insurance->update([
            'name' => $request->name ?? $insurance->name,
            'price' => $request->price ?? $insurance->price,
            'code' => $request->code ?? $insurance->code,
            'status' => $request->status ?? $insurance->status,
            'ship_company_id' => $request->ship_company_id ?? $insurance->ship_company_id,
        ]);
        return $this->successResponse('Cập nhật bảo hiểm mua hàng thành công', $insurance);
    }

    public function insurance_delete(string $id)
    {
        $insurance = insurance::find($id);
        if (!$insurance) {
            return $this->errorResponse('Bảo hiểm không tồn tại', 404);
        }
        $insurance->status = 0;
        $insurance->save();
    }

    public function insurance_get_one(string $id)
    {
        $insurance = insurance::find($id);
        if (!$insurance) {
            return $this->errorResponse('Bảo hiểm không tồn tại', 404);
        }
        return $this->successResponse('Lấy bảo hiểm thành công', $insurance);
    }

    public function insurance_get_all(string $id)
    {
        $insurance = insurance::where('ship_company_id', $id)->get();

        if (!$insurance) {
            return $this->errorResponse('Bảo hiểm không tồn tại', 404);
        }
        return $this->successResponse('Lấy bảo hiểm thành công', $insurance);
    }

}
