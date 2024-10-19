<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramtoshopRequest;
use App\Models\ProgramtoshopModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProgramtoshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programToShops = Cache::remember('all_program_to_shops', 60 * 60, function () {
            return ProgramtoshopModel::all();
        });

        if ($programToShops->isEmpty()) {
            return $this->errorResponse("Không tồn tại liên kết chương trình-cửa hàng nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $programToShops);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgramtoshopRequest $request)
    {
        try {
            $programToShop = ProgramtoshopModel::create($request->validated());
            Cache::forget('all_program_to_shops');
            return $this->successResponse("Thêm liên kết chương trình-cửa hàng thành công", $programToShop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm liên kết chương trình-cửa hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $programToShop = Cache::remember('program_to_shop_' . $id, 60 * 60, function () use ($id) {
            return ProgramtoshopModel::find($id);
        });

        if (!$programToShop) {
            return $this->errorResponse("Liên kết chương trình-cửa hàng không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $programToShop);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgramtoshopRequest $request, string $id)
    {
        $programToShop = ProgramtoshopModel::find($id);

        if (!$programToShop) {
            return $this->errorResponse("Liên kết chương trình-cửa hàng không tồn tại", 404);
        }

        try {
            $programToShop->update($request->validated());
            Cache::forget('program_to_shop_' . $id);
            Cache::forget('all_program_to_shops');
            return $this->successResponse("Cập nhật liên kết chương trình-cửa hàng thành công", $programToShop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật liên kết chương trình-cửa hàng không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $programToShop = ProgramtoshopModel::find($id);

        if (!$programToShop) {
            return $this->errorResponse("Liên kết chương trình-cửa hàng không tồn tại", 404);
        }

        try {
            $programToShop->delete();
            Cache::forget('program_to_shop_' . $id);
            Cache::forget('all_program_to_shops');
            return $this->successResponse("Xóa liên kết chương trình-cửa hàng thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa liên kết chương trình-cửa hàng không thành công", $th->getMessage());
        }
    }
}
