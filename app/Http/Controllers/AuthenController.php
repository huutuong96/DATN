<?php

namespace App\Http\Controllers;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Http\Requests\UserRequest;
use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\RanksModel;
use App\Models\AddressModel;
use App\Models\Notification;
use App\Models\OrdersModel;
use App\Models\OrderDetailsModel;
use App\Models\Product;
use App\Models\shop_manager;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Notification_to_mainModel;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmMail;
use App\Mail\ConfirmMailChangePassword;
use App\Mail\ConfirmRestoreAccount;
use App\Models\Cart_to_usersModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Cloudinary\Cloudinary;
use App\Jobs\ConfirmMailRegister;
use App\Models\Follow_to_shop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

/**
 * Paginate a collection.
 *
 * @param  Collection  $collection
 * @param  int  $perPage
 * @param  int|null  $page
 * @param  array  $options
 * @return LengthAwarePaginator
 */

class AuthenController extends Controller
{



    public function index()
    {
        try {
            $list_users = UsersModel::where('status', 1)->paginate(20);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $list_users,
            ], 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Lấy dữ liệu thất bại',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

  
    public function register(UserRequest $request)
    {
        try {
            $existingUser = UsersModel::where('email', $request->email)->first();

            $dataInsert = [
                "fullname" => $request->fullname,
                "password" => Hash::make($request->password),
                "email" => $request->email,
                "rank_id" => $request->rank_id ?? 1,
                "phone" => $request->phone ?? null,
                "role_id" => 1,
                "status" => 101, // 101 là tài khoản chưa được kích hoạt
                "login_at" => now(),
            ];

            $user = UsersModel::create($dataInsert);
            $token = JWTAuth::fromUser($user);
            $verifyCode = rand(10000, 99999);
            $user->update([
                'refesh_token' => $token,
                'verify_code' => $verifyCode,
            ]);
            $dataDone = [
                'status' => true,
                'message' => "Đăng ký thành công, chưa kích hoạt",
                'user' => $user,
                'token' => $token,
            ];
            Mail::to($user->email)->send(new ConfirmMail($user, $token));
            // ConfirmMailRegister::dispatch($user, $token, $verifyCode);
            return response()->json($dataDone, 201);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function confirm(Request $request)
    {
        try {
            $user = UsersModel::where('refesh_token', $request->token)->first();
            if ($user) {
                $user->update([
                    'status' => 1,
                ]);

                $cart_to_users = Cart_to_usersModel::create([
                    'user_id' => $user->id,
                    'status' => 2,
                ]);
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
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function confirmVerifyCode(Request $request)
    {
        try {
            if (!$request->verify_code) {
                $activeFail = [
                    'status' => 403,
                    'message' => "Mã xác nhận không hợp lệ",
                ];
                return response()->json($activeFail, 404);
            }
            $user = UsersModel::where('verify_code', $request->verify_code)->first();

            if ($user) {
                $user->update([
                    'status' => 1,
                ]);

                $cart_to_users = Cart_to_usersModel::create([
                    'user_id' => $user->id,
                    'status' => 1,
                ]);
                $activeDone = [
                    'status' => true,
                    'message' => "Tài khoản đã được kích hoạt, vui lòng đăng nhập lại",
                ];
                return response()->json($activeDone, 200);
            } else {
                $activeFail = [
                    'status' => 404,
                    'message' => "Tài khoản không tồn tại, Vui lòng đăng ký lại",
                ];
                return response()->json($activeFail, 404);
            }
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Không thể tạo token'], 500);
            }
            $user = UsersModel::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'Tài khoản không tồn tại'], 404);
            }
            if ($user->status == 101) {
                return response()->json(['error' => 'Tài khoản chưa được xác thực'], 401);
            }
            if ($user->status == 2) {
                return response()->json(['error' => 'Tài khoản đã bị khóa'], 401);
            }
            $token = JWTAuth::fromUser($user);
            $user->refesh_token = $token;
            $user->save();
            $user = auth::user();
            // return $credentials;
            return response()->json([
                'status' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'token' => $token,
                    // 'user' => $user,
                ],
            ], 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function login_test(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Không thể tạo token'], 500);
            }
            $user = UsersModel::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'Tài khoản không tồn tại'], 404);
            }
            if ($user->status == 101) {
                return response()->json(['error' => 'Tài khoản chưa được xác thực'], 401);
            }
            $user->refesh_token = $token;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Đăng nhập thành công',
                'data' => [
                    'token' => $token,
                    'user' => $user,
                ],
            ], 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }


    public function adminLogin(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return view("login")->with('error', 'Tài khoản và mật khẩu không đúng!');
                }
            } catch (JWTException $e) {
                return view("login")->with('error', 'Không thể tạo token!');
            }
            $user = UsersModel::where('email', $request->email)->first();
            if (!$user) {
                return view("login")->with('error', 'Tài khoản không tồn tại!');
            }
            if ($user->status == 101) {
                return view("login")->with('error', 'Tài khoản và mật khẩu không đúng!');
            }
            $token = JWTAuth::fromUser($user);
            $user->refesh_token = $token;
            // $user->is_login = 1;
            $user->save();
            $user->load('role', 'address');
            $user = auth::user();
            $notification = Notification::where('user_id', $user->id)->get();
            $notificationIds = $notification->pluck('id_notification'); // Lấy danh sách các ID từ collection
            $notifyMain = Notification_to_mainModel::whereIn('id', $notificationIds)->get();
            session(['notifyMain' => $notifyMain]);
            return redirect()->route('dashboard', ['token' => auth()->user()->refesh_token]);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function show(string $id)
    {
        try {
            $user = UsersModel::where('id', $id)->where('status', 1)->first();
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $user,
            ], 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }


    public function me()
    {
        try {
            $user_present = JWTAuth::parseToken()->authenticate();
            $shop = Shop::where('owner_id', $user_present->id)->first();
            $cartUser = Cart_to_usersModel::where('user_id', $user_present->id)->first();
            $rank = RanksModel::where('id', $user_present->rank_id)->first();
            $followers = Follow_to_shop::where('user_id', $user_present->id)->select('shop_id')->get();
            $user_present->shop_id = $shop?->id;
            $user_present->cart_id = $cartUser?->id;
            $user_present->rank = $rank;
            $user_present->followers = $followers;
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $user_present,
            ], 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            
            $user = UsersModel::where('id', $id)->where('status', 1)->first();
            $dataUpdate = [
                "status" => 103, //tài khoản bị khóa
            ];
            $user = UsersModel::where('id', $id)->update($dataUpdate);

            $dataDone = [
                'status' => true,
                'message' => "Tài khoản đã bị khóa",
            ];
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }

    
    public function update_profile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();
            // if ($request->hasFile('avatar')) {
            //     $avatar = $request->file('avatar');
            //     $uploadedavatar = $cloudinary->uploadApi()->upload($avatar->getRealPath());
            //     $avatarUrl = $uploadedavatar['secure_url'];
            // }
            $dataUpdate = [
                "fullname" => $request->fullname ?? $user->fullname,
                "phone" => $request->phone ?? $user->phone,
                "email" => $request->email ?? $user->email,
                "genre" => $request->genre ?? 1,
                "datebirth" => $request->datebirth ? date('Y-m-d', strtotime($request->datebirth)) : null,
                "updated_at" => now(),
                "avatar" => $request->avatar ?? $user->avatar,
                "description" => $request->description ?? $user->description,
            ];
            UsersModel::where('id', $user->id)->where('status', 1)->update($dataUpdate);
            // if($request->input('address')){
            //     if ($request->input('address')['default'] == 1) {
            //         AddressModel::where('default', 1)->where('user_id', $user->id)->update(['default' => 0]);
            //     }
            //     AddressModel::where('id', $request->input('address')['id'])->where('user_id', $user->id)->update([
            //         "province" => $request->input('address')['province'],
            //         "province_id" => $request->input('address')['province_id'],
            //         "district" => $request->input('address')['district'],
            //         "district_id" => $request->input('address')['district_id'],
            //         "ward" => $request->input('address')['ward'],
            //         "ward_id" => $request->input('address')['ward_id'],
            //         "address" => $request->input('address')['address'],
            //         "user_id" => $user->id,
            //         "default" => $request->input('address')['default'] ?? 0,
            //         "type" => $request->input('address')['type'] ?? null,
            //         "name" => $request->name ?? $user->fullname,
            //         "phone" => $request->phone ?? $user->phone,
            //     ]);
            // }
            $dataDone = [
                'status' => true,
                'message' => "Cập nhật thành công!",
            ];
            if($request->token){
                return redirect()->back()->with('message', 'Cập nhật thành công!');
            }

            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    }

    public function change_password(Request $request)
    {
        try {
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
            if ($request->token) {
                return redirect()->back()->with('message', 'Mật khẩu đã được thay đổi thành công');
            }
            return response()->json($dataDone, 200);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }

    
    public function fogot_password(Request $request)
    {
        try {
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
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }

    public function confirm_mail_change_password(Request $request, $token, $email)
    {
        try {
            $user = UsersModel::where('email', $email)->first();
            if (!$request->newpassword) {
                return response()->json(['error' => 'vui lòng nhập mật khẩu mới'], 401);
            }
            if ($user) {
                return $this->reset_password($request, $token, $email);
            }
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json('error', 'Có lỗi xảy ra!');
        }
    
    }

    /**
 * @OA\Post(
 *     path="api/reset_password/{token}/{email}",
 *     summary="Reset password",
 *     description="Resets the password for the user identified by the email and token.",
 *     tags={"Authentication"},
 *     @OA\Parameter(
 *         name="token",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The token for password reset"
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="email"),
 *         description="The email of the user"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"newpassword"},
 *             @OA\Property(property="newpassword", type="string", example="new_password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Mật khẩu đã được thay đổi thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Tài khoản không tồn tại")
 *         )
 *     )
 * )
 */
    public function reset_password(Request $request, $token, $email)
    {
        try {
            $user = UsersModel::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->newpassword),
                ]);

                $dataDone = [
                    'status' => true,
                    'message' => "Mật khẩu đã được thay đổi thành công",
                ];
                return response()->json($dataDone, 200);
            }
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return view("login")->with('error', 'Có lỗi xảy ra!');
        }
    }

   
    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->update([
            'refesh_token' => null,
            // 'is_login' => 0,
        ]);
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'status' => true,
            'message' => "Đăng xuất thành công",
        ], 200);
    }

    public function adminLogout()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->update([
            'refesh_token' => null,
            // 'is_login' => 0,
        ]);
        JWTAuth::invalidate(JWTAuth::getToken());
        session()->forget('token');
        return redirect()->route('login');
    }

    /**
 * @OA\Delete(
 *     path="api/users/{id}",
 *     summary="Deactivate user account",
 *     description="Deactivates a user account for 30 days by setting the status to 102.",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The ID of the user"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account deactivated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tài khoản đã được vô hiệu hóa trong 30 ngày")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     )
 * )
 */
    public function destroy(string $id)
    {
        $dataUpdate = [
            "status" => 102,
        ];
        $user = UsersModel::where('id', $id)->update($dataUpdate);

        $dataDone = [
            'status' => true,
            'message' => "Tài khoản đã được vô hiệu hóa trong 30 ngày",
        ];
        return response()->json($dataDone, 200);
    }

    /**
 * @OA\Post(
 *     path="api/restore_account",
 *     summary="Restore user account",
 *     description="Sends a confirmation email to restore the user account.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Confirmation email sent",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đã gữi mã xác nhận đến email"),
 *             @OA\Property(property="email", type="string", example="john.doe@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Tài khoản không tồn tại")
 *         )
 *     )
 * )
 */
    public function restore_account(Request $request)
    {
        $user = UsersModel::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
        }
        $token = JWTAuth::fromUser($user);
        $user->update([
            'refesh_token' => $token,
        ]);
        Mail::to($user->email)->send(new ConfirmRestoreAccount($user, $token));
        $dataDone = [
            'status' => true,
            'message' => "Đã gữi mã xác nhận đến email",
            'email' => $user->email,
        ];
        return response()->json($dataDone, 200);
    }

/**
 * @OA\Post(
 *     path="api/confirm_restore_account/{token}/{email}",
 *     summary="Confirm restore account",
 *     description="Confirms the account restoration using a token and email, and reactivates the account.",
 *     tags={"Authentication"},
 *     @OA\Parameter(
 *         name="token",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The token for account restoration"
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="email"),
 *         description="The email of the user"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account restored successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tài khoản đã khôi phục thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Tài khoản không tồn tại")
 *         )
 *     )
 * )
 */
    public function confirm_restore_account(Request $request, $token, $email)
    {
        $user = UsersModel::where('email', $email)->first();
        if ($user) {
            $user->status = 1;
            $user->save();
            return response()->json(['error' => 'Tài khoản đã khôi phục thành công'], 200);
        }
        return response()->json(['error' => 'Tài khoản không tồn tại'], 401);
    }

    /**
 * @OA\Get(
 *     path="api/get_infomaiton_province_and_city/{province}",
 *     summary="Get information about a province and city",
 *     description="Retrieves information about a province and city based on the province name.",
 *     tags={"Location"},
 *     @OA\Parameter(
 *         name="province",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The name of the province"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Province information retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="ProvinceID", type="integer", example=1),
 *             @OA\Property(property="ProvinceName", type="string", example="Hanoi"),
 *             @OA\Property(property="CountryID", type="integer", example=1),
 *             @OA\Property(property="CountryName", type="string", example="Vietnam")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Province not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Province not found")
 *         )
 *     )
 * )
 */
    public function get_infomaiton_province_and_city($province)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $cities = collect($response->json()['data']); // Chuyển thành Collection
        // Lọc tỉnh dựa trên tên
        $filteredCity = $cities->firstWhere('ProvinceName', $province);
        return $filteredCity;
    }

    /**
 * @OA\Get(
 *     path="api/get_infomaiton_district/{districtName}",
 *     summary="Get information about a district",
 *     description="Retrieves information about a district based on the district name.",
 *     tags={"Location"},
 *     @OA\Parameter(
 *         name="districtName",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The name of the district"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="District information retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="DistrictID", type="integer", example=1),
 *             @OA\Property(property="DistrictName", type="string", example="Hoan Kiem"),
 *             @OA\Property(property="ProvinceID", type="integer", example=1),
 *             @OA\Property(property="ProvinceName", type="string", example="Hanoi")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="District not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="District not found")
 *         )
 *     )
 * )
 */
    public function get_infomaiton_district($districtName)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district');
        $district = collect($response->json()['data']); // Chuyển thành Collection
        $filtereddistrict = $district->firstWhere('DistrictName', $districtName);
        return $filtereddistrict;
    }

/**
 * @OA\Get(
 *     path="api/get_infomaiton_ward/{districtId}/{wardName}",
 *     summary="Get information about a ward",
 *     description="Retrieves information about a ward based on the district ID and ward name.",
 *     tags={"Location"},
 *     @OA\Parameter(
 *         name="districtId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="The ID of the district"
 *     ),
 *     @OA\Parameter(
 *         name="wardName",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="The name of the ward"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ward information retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="WardCode", type="string", example="12345"),
 *             @OA\Property(property="WardName", type="string", example="Phuc Tan"),
 *             @OA\Property(property="DistrictID", type="integer", example=1),
 *             @OA\Property(property="DistrictName", type="string", example="Hoan Kiem")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Ward not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Ward not found")
 *         )
 *     )
 * )
 */
    public function get_infomaiton_ward($districtId, $wardName)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id', [
            'district_id' => $districtId, // Thêm district_id vào tham số truy vấn
        ]);
        $ward = collect($response->json());
        foreach ($ward['data'] as $key => $value) {
            if($ward['data'][$key]['WardName'] == $wardName){
                $ward_id = $ward['data'][$key]['WardCode'];
            }
        }
        return $ward_id;
    }


    public function admin_profile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate() ?? null;
        $user->load('role', 'address');
        return view('profile.profile', ['user' => $user]);

    }   

    public function handleGoogleCallback(Request $request){
        $googleUser = Socialite::driver('google')->user();

        $user = UsersModel::where('email', $googleUser->email)->first();
        
        if ($user) {
            Auth::login($user);
            $token = JWTAuth::fromUser($user);
            $user->refesh_token = $token;
            $user->status = 1;
            $user->save();
            return redirect()->away("https://test.vnshop.top/auth/verify_google?token={$token}");
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Đăng nhập thành công',
            //     'data' => [
            //         'token' => $user->refesh_token,
            //         // 'user' => $user,
            //     ],
            // ], 200);
        } else {
            $user = UsersModel::create([
                'fullname' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => Hash::make($googleUser->id),
                'login_at' => Carbon::now(),
                'google_id' => $googleUser->id,
                'role_id' => 1,
                'rank_id' => 1,
                'status' => 1,
            ]);
    
            Auth::login($user);
        }
        $token = JWTAuth::fromUser($user);
        $user->refesh_token = $token;
        $user->save();
        $cart_to_users = Cart_to_usersModel::create([
            'user_id' => $user->id,
            'status' => 1,
        ]);
        return redirect()->away("https://test.vnshop.top/auth/verify_google?token={$token}");
        // return response()->json([
        //     'status' => true,
        //     'message' => 'Đăng nhập thành công',
        //     'url' => "http://test.vnshop.top",
        //     'data' => [
        //         'token' => $user->refesh_token,
        //         // 'user' => $user,
        //     ],
        // ], 200);
    }

    public function login_with_token($token){
        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'token' => $token,
                // 'user' => $user,
            ],
        ], 200);
    }
}
