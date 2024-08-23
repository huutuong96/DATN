<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\UsersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenController extends Controller
{
    public function index()
    {
        try {
            // Xác thực người dùng bằng token JWT
            // $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => UsersModel::all(),
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

    public function register(UserRequest $request)
    {
        $dataInsert = [
            "fullname"=> $request->fullname,
            "password"=> $request->password,
            "email"=> $request->email,
            "rank_id"=> $request->rank_id,
            "role_id"=> $request->role_id,
            "login_at"=> now(),
        ];
        $user = UsersModel::create($dataInsert);
        // Cấp JWT token
        try {
            $token = JWTAuth::fromUser($user);
            $user->update([
                'refesh_token' => $token,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
        $dataDone = [
            'status' => true,
            'message' => "user Đã được lưu",
            'token' => $token,
            'users' => UsersModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    public function login(Request $request)
    {
        $user = UsersModel::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
        }
        if ($user->password != $request->password) {
            return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], 401);
        }
        if ($user->password == $request->password) {
            $token = JWTAuth::fromUser($user);
            $user->update([
                'refesh_token' => $token,
            ]);
            $dataDone = [
                'status' => true,
                'message' => "Đăng nhập thành công",
                'token' => $token,
            ];
            return response()->json($dataDone, 200);
        }
    }

    public function show(string $id)
    {
        try {
            // Xác thực người dùng bằng token JWT
            $user = UsersModel::where('id', $id)->first();
            // dd($user);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $user,
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

    public function me(Request $request)
    {
        try {
            // Xác thực người dùng bằng token JWT
            $user = JWTAuth::parseToken()->authenticate();
            // dd($user);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $user,
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
    public function update(UserRequest $request, string $id)
    {
        $dataUpdate = [
            "fullname"=> $request->fullname,
            "password"=> $request->password,
            "phone"=> $request->phone,
            "email"=> $request->email,
            "description"=> $request->description,
            "genre"=> $request->genre,
            "datebirth"=> $request->datebirth,
            "avatar"=> $request->avatar,
            "rank_id"=> $request->rank_id,
            "role_id"=> $request->role_id,
            "address_id"=> $request->address_id,
            "login_at"=> now(),
        ];
        $user = UsersModel::where('id', $id)->update($dataUpdate);

        $dataDone = [
            'status' => true,
            'message' => "user Đã được cập nhật",
            'users' => UsersModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dataUpdate = [
            "status"=> 4,
        ];
        $user = UsersModel::where('id', $id)->update($dataUpdate);

        $dataDone = [
            'status' => true,
            'message' => "user đã được vô hiệu hóa",
            'users' => UsersModel::all(),
        ];
        return response()->json($dataDone, 200);
    }
}
