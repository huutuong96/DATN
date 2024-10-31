<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Models\role_premissionModel;
use App\Http\Requests\RoleRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = RolesModel::all();

        if ($roles->isEmpty()) {
            return $this->errorResponse("Không tồn tại vai trò nào");
        }

        return $this->successResponse("Lấy dữ liệu thành công", $roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $role = RolesModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'create_by' => $user->id,
            'update_by' => $user->id,
        ]);
        return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'Thêm vai trò thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = RolesModel::find($id);

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
        $role = RolesModel::find($id);

        if (!$role) {
            return $this->errorResponse("Vai trò không tồn tại", 404);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            $validateDate = $request->validated();
            $validateDate['update_by'] = $user->id;
            $role->update($validateDate);
            return $this->successResponse("Cập nhật vai trò thành công", $role);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật vai trò không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        
        $role = RolesModel::find($id);
        $permissionsRole = role_premissionModel::where('role_id', $id)->get();
            if (!$role) {
                if($request->token){
                    return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'Vai trò không tồn tại!');
                }
            }   
            foreach ($permissionsRole as $permission) {
                $permission->delete();
            }
            $role->delete();
            return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'Xóa vai trò thành công!');
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
