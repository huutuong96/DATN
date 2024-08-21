<?php

namespace App\Http\Controllers;
use App\Models\PermissionsModel;
use App\Models\role_permissionModel;
use App\Models\RolesModel;
use Illuminate\Http\Request;
use App\Http\Requests\PermissionsRequest;
class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Xác thực người dùng bằng token JWT
            // $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => PermissionsModel::all(),
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc không tồn tại',
                'error' => $e->getMessage(),
            ], 401); // Sử dụng 401 cho lỗi xác thực
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(PermissionsRequest $request)
    {
        $dataInsert = [
            "premissionName"=> $request->premissionName,
            "create_at"=> now(),
        ];
        $permission = PermissionsModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "Quyền truy cập Đã được lưu",
            'permissions' => PermissionsModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Xác thực người dùng bằng token JWT
            $permission = PermissionsModel::where('id', $id)->first();
            // dd($permission);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $permission,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function grant_access(Request $request)
    {
        $roleExist = RolesModel::where('id', $request->role_id)->first();
        if (!$roleExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role này không tồn tại',
            ], 404);
        }
        // Kiểm tra xem quyền đã tồn tại cho role chưa
        $permissionExist = role_permissionModel::where([
            ['role_id', '=', $request->role_id],
            ['premission_id', '=', $request->premission_id]
        ])->first();

        if ($permissionExist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role này đã có quyền truy cập này rồi',
            ], 400); // Sử dụng mã lỗi 400 (Bad Request)
        }

        $dataInsert = [
            "role_id"=> $request->role_id,
            "premission_id"=> $request->premission_id,
            "create_at"=> now(),
        ];
        $permission = role_permissionModel::create($dataInsert);
        $dataDone = [
            'status' => true,
            'message' => "Đã cấp quyền truy cập cho Role",
            'permissions' => role_permissionModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    public function destroy($id)
    {
        // Code để xóa tài nguyên theo $id
    }

    public function delete_access(Request $request)
    {


            $roleExist = RolesModel::where('id', $request->role_id)->first();
            if (!$roleExist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role này không tồn tại',
                ], 404);
            }
            // Kiểm tra xem quyền đã tồn tại cho role chưa
            $permissionExist = role_permissionModel::where([
                ['role_id', '=', $request->role_id],
                ['premission_id', '=', $request->premission_id]
            ])->first();
            if (!$permissionExist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role này chưa được cấp quyền truy cập này',
                ], 400); // Sử dụng mã lỗi 400 (Bad Request)
            }
            if ($permissionExist->premission_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền truy cập chức năng này',
                ], 404);
            }
            if ($permissionExist) {
                $permission = role_permissionModel::where('premission_id', $request->premission_id)->delete();
                $dataDone = [
                    'status' => true,
                    'message' => "Xóa quyền truy cập của Role thành công",
                    'permissions' => role_permissionModel::all(),
                ]  ;
                return response()->json($dataDone, 200);
            }

    }

}

