<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\RanksModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Notification_to_mainModel;
use App\Models\Notification;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use App\Mail\ConfirmMailChangePassword;
use App\Models\Cart_to_usersModel;


class AuthenController extends Controller
{
    public function index()
    {
        try {
            $list_users = UsersModel::all();

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $list_users,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc không tồn tại',
                'error' => $e->getMessage(),
            ], 401);
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
        $existingUser = UsersModel::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email đã tồn tại.',
            ], 422);
        }

        $role = RolesModel::where('title', 'user')->first();
        $rank = RanksModel::where('title', 'đồng')->first();
        $dataInsert = [
            "fullname" => $request->fullname,
            "password" => Hash::make($request->password),
            "email" => $request->email,
            "rank_id" => $rank->id,
            "role_id" => $role->id,
            "status" => 101, // 101 là tài khoản chưa được kích hoạt
            "login_at" => now(),
        ];
        $user = UsersModel::create($dataInsert);
        $token = JWTAuth::fromUser($user);
        $user->update([
            'refesh_token' => $token,
        ]);
        // Send confirm mail
        $notificationData = [
            'type' => 'main',
            'user_id' => $user->id,
            'title' => 'Vui lòng xác nhận đăng ký tài khoản',
            'description' => 'Cảm ơn bạn đã đăng ký tài khoản. Vui lòng xác nhận đăng ký tài khoản để hoàn tất quá trình đăng ký.',
        ];

        $notificationController = new NotificationController();
        $notification = $notificationController->store(new Request($notificationData));

        $dataDone = [
            'status' => true,
            'message' => "Đăng ký thành công, chưa kích hoạt",
            'user' => $user,
            'notification' => $notification,
        ];

        Mail::to($user->email)->send(new ConfirmMail($user, $notificationData, $token));
        return response()->json($dataDone, 201);
    }

    public function confirm($token)
    {
        $user = UsersModel::where('refesh_token', $token)->first();
        if ($user) {
            $user->update([
                'status' => 1,
            ]);

            $cart_to_users = Cart_to_usersModel::create([
                'user_id' => $user->id,
                'status' => 1,
            ]);
            // dd($cart_to_users);

            $activeDone = [
                'status' => true,
                'message' => "Tài khoản đã được kích hoạt, vui lòng đăng nhập lại",
            ];

            return response()->json($activeDone, 200);
        } else {
            $activeFail = [
                'status' => true,
                'message' => "Tài khoản không tồn tại, Vui lòng đăng ký lại",
            ];
            return response()->json($activeFail, 200);
        }
    }


    public function login(Request $request)
    {
        $user = UsersModel::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user_present' => $user,
        ], 200);
    }

    public function show(string $id)
    {
        try {
            $user = UsersModel::find($id);

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
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user_present = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $user_present,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc không tồn tại',
                'error' => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $dataUpdate = [
            "fullname" => $request->fullname ?? $user->fullname,
            "password" => $request->password ?? $user->password,
            "phone" => $request->phone ?? $user->phone,
            "email" => $request->email ?? $user->email,
            "description" => $request->description ?? $user->description,
            "genre" => $request->genre ?? $user->genre,
            "datebirth" => $request->datebirth ?? $user->datebirth,
            "avatar" => $request->avatar ?? $user->avatar,
            "login_at" => now(),
        ];
        $user = UsersModel::where('id', $id)->update($dataUpdate);

        $dataDone = [
            'status' => true,
            'message' => "Tài khoản đã được cập nhật",
            'users' => UsersModel::all(),
        ];
        return response()->json($dataDone, 200);
    }

    public function update_profile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $dataUpdate = [
            "fullname" => $request->fullname ?? $user->fullname,
            "phone" => $request->phone ?? $user->phone,
            "email" => $request->email ?? $user->email,
            "description" => $request->description ?? $user->description,
            "genre" => $request->genre ?? $user->genre,
            "datebirth" => $request->datebirth ?? $user->datebirth,
            "avatar" => $request->avatar ?? $user->avatar,
            "updated_at" => now(),
        ];

        UsersModel::where('id', $user->id)->update($dataUpdate);
        $user = UsersModel::find($user->id);

        $dataDone = [
            'status' => true,
            'message' => "Tài khoản đã được cập nhật",
        ];
        return response()->json($dataDone, 200);
    }

    public function change_password(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Mật khẩu không đúng'], 401);
        }
        $dataUpdate = [
            "password" => Hash::make($request->new_password),
            "updated_at" => now(),
        ];

        UsersModel::where('id', $user->id)->update($dataUpdate);
        $user = UsersModel::find($user->id);

        $dataDone = [
            'status' => true,
            'message' => "Mật khẩu đã được thay đổi thành công",
        ];
        return response()->json($dataDone, 200);
    }

    public function fogot_password(Request $request)
    {
        $user = UsersModel::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
        }
        $token = JWTAuth::fromUser($user);
        $user->update([
            'refesh_token' => $token,
        ]);

        Mail::to($user->email)->send(new ConfirmMailChangePassword($user, $token));
        $dataDone = [
            'status' => true,
            'message' => "Đã gửi mã xác nhận đến email",
            'user' => $user->email,
        ];
        return response()->json($dataDone, 200);
    }

    public function confirm_mail_change_password(Request $request, $token, $email)
    {
        $user = UsersModel::where('email', $email)->first();

        if ($user) {
            return $this->reset_password($request, $token, $email);
        }
    }

    public function reset_password(Request $request, $token, $email)
    {
        $user = UsersModel::where('email', $email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            $dataDone = [
                'status' => true,
                'message' => "Mật khẩu đã được thay đổi thành công",
            ];
            return response()->json($dataDone, 200);
        }
    }

    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->update([
            'refesh_token' => null,
        ]);

        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => true,
            'message' => "Đăng xuất thành công",
        ], 200);
    }

    public function destroy(string $id)
    {
        $dataUpdate = [
            "status" => 102,
        ];
        $user = UsersModel::where('id', $id)->update($dataUpdate);

        $dataDone = [
            'status' => true,
            'message' => "Tài khoản đã được vô hiệu hóa",
        ];
        return response()->json($dataDone, 200);
    }
}
