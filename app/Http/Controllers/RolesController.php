<?php

namespace App\Http\Controllers;

use App\Models\RolesModel;
use App\Models\role_premissionModel;
use App\Http\Requests\RoleRequest;
use App\Models\UsersModel;
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
            'status' => $request->status ?? 2,
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
    public function update(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $role = RolesModel::find($request->id);
        
        $role->title = $request->title ?? $role->title;
        $role->description = $request->description ?? $role->description;
        $role->status = $request->status ?? $role->status;
        $role->update_by = $user->id;
        $role->updated_at = now();
        $role->save();
        return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'cập nhật vai trò thành công!');
    }

    public function change_role(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        $role = RolesModel::find($request->id);
        $role->status = $request->status ?? $role->status;
        $role->save();
        return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'cập nhật vai trò thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        
        $role = RolesModel::find($id);
        $permissionsRole = role_premissionModel::where('role_id', $id)->get();
        $users = UsersModel::where('role_id', $role->id)->get();
            if (!$role) {
                if($request->token){
                    return redirect()->route('list_role', ['token' => auth()->user()->refesh_token])->with('message', 'Vai trò không tồn tại!');
                }
            }   
            foreach ($permissionsRole as $permission) {
                $permission->delete();
            }
            foreach ($users as $user) {
                $user->role_id = 1;
                $user->save();
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
