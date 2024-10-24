<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Cache::remember('all_roles', 60 * 60, function () {
            return RolesModel::all();
        });

        if ($roles->isEmpty()) {
            return $this->errorResponse("Không tồn tại vai trò nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        
        try {
            $validatedData = $request->validated();
            $validatedData['create_by'] = $user->id;
            $role = RolesModel::create($validatedData);
            Cache::forget('all_roles');
            return $this->successResponse("Thêm vai trò thành công", $role);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm vai trò không thành công", $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Cache::remember('role_' . $id, 60 * 60, function () use ($id) {
            return RolesModel::find($id);
        });

        if (!$role) {
            return $this->errorResponse("Vai trò không tồn tại", 404);
        }

        return $this->successResponse("Lấy dữ liệu thành công", $role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        $role = Cache::remember('role_' . $id, 60 * 60, function () use ($id) {
            return RolesModel::find($id);
        });

        if (!$role) {
            return $this->errorResponse("Vai trò không tồn tại", 404);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            $validateDate = $request->validated();
            $validateDate['update_by'] = $user->id;
            $role->update($validateDate);
            Cache::forget('role_' . $id);
            Cache::forget('all_roles');
            return $this->successResponse("Cập nhật vai trò thành công", $role);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật vai trò không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = RolesModel::find($id);

        if (!$role) {
            return $this->errorResponse("Vai trò không tồn tại", 404);
        }

        try {
            $role->delete();
            Cache::forget('role_' . $id);
            Cache::forget('all_roles');
            return $this->successResponse("Xóa vai trò thành công");
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa vai trò không thành công", $th->getMessage());
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
