<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
/**
 * Paginate a collection.
 *
 * @param  Collection  $collection
 * @param  int  $perPage
 * @param  int|null  $page
 * @param  array  $options
 * @return LengthAwarePaginator
 */
/**
 * @OA\Schema(
 *     schema="Users",
 *     type="object",
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="The username of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="email",
 *         description="The email of the user"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="The phone of the user"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="The password of the user"
 *     ),
 *     @OA\Property(
 *         property="gender",
 *         type="string",
 *         description="The gender of the user"
 *     ),
 *  *     @OA\Property(
 *         property="nationality",
 *         type="string",
 *         description="The nationality of the user"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the user"
 *     ),
 *     @OA\Property(
 *         property="birthday",
 *         type="date",
 *         description="The birthday of the user"
 *     ),
 *     required={"name", "username", "email", "password", "gender", "nationality", "update_by", "delete_by"}
 * )
 */
class AuthenController extends Controller
{
/**
 * @OA\Get(
 *     path="api/users",
 *     summary="Get list of active users",
 *     description="Retrieves a paginated list of users with status 1.",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=200,
 *         description="Data retrieved successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="fullname", type="string", example="John Doe"),
 *                         @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *                         @OA\Property(property="status", type="integer", example=1),
 *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
 *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-01T12:00:00Z")
 *                     )
 *                 ),
 *                 @OA\Property(property="first_page_url", type="string", example="http://example.com?page=1"),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=10),
 *                 @OA\Property(property="last_page_url", type="string", example="http://example.com?page=10"),
 *                 @OA\Property(property="next_page_url", type="string", example="http://example.com?page=2"),
 *                 @OA\Property(property="path", type="string", example="http://example.com"),
 *                 @OA\Property(property="per_page", type="integer", example=20),
 *                 @OA\Property(property="prev_page_url", type="string", example=null),
 *                 @OA\Property(property="to", type="integer", example=20),
 *                 @OA\Property(property="total", type="integer", example=200)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Data retrieval failed",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thất bại"),
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */
    public function index()
    {
        try {
            $list_users = UsersModel::where('status', 1)->paginate(20);
            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'data' => $list_users,
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
 * @OA\Post(
 *     path="api/register",
 *     summary="Register a new user",
 *     description="Registers a new user and returns a JWT token.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"fullname", "password", "email"},
 *             @OA\Property(property="fullname", type="string", example="John Doe"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *             @OA\Property(property="rank_id", type="integer", example=1),
 *             @OA\Property(property="role_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đăng ký thành công, chưa kích hoạt"),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="fullname", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *                 @OA\Property(property="rank_id", type="integer", example=1),
 *                 @OA\Property(property="role_id", type="integer", example=2),
 *                 @OA\Property(property="status", type="integer", example=101),
 *                 @OA\Property(property="login_at", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
 *                 @OA\Property(property="refesh_token", type="string", example="jwt_token_here")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Email already exists",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Email đã tồn tại.")
 *         )
 *     )
 * )
 */
    public function register(UserRequest $request)
    {
        $existingUser = UsersModel::where('email', $request->email)->first();

        $dataInsert = [
            "fullname" => $request->fullname,
            "password" => Hash::make($request->password),
            "email" => $request->email,
            "rank_id" => $request->rank_id ?? null,
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
    }

    public function confirm(Request $request)
    {
        $user = UsersModel::where('refesh_token', $request->token)->first();
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
                'status' => true,
                'message' => "Tài khoản không tồn tại, Vui lòng đăng ký lại",
            ];
            return response()->json($activeFail, 200);
        }
    }

    public function confirmVerifyCode(Request $request)
    {
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
    }

/**
 * @OA\Post(
 *     path="api/login",
 *     summary="User login",
 *     description="Logs in a user and returns a JWT token.",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đăng nhập thành công"),
 *             @OA\Property(property="token", type="string", example="jwt_token_here")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Tài khoản hoặc mật khẩu không đúng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Token creation failed",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Không thể tạo token")
 *         )
 *     )
 * )
 */
    public function login(Request $request)
    {
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
                // 'user' => $user,
            ],
        ], 200);
    }


    public function adminLogin(Request $request)
    {
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
        $user->save();
        $user->load('role', 'address');
        $user = auth::user();
        // dd(auth()->user()->refesh_token);
        $notification = Notification::where('user_id', $user->id)->get();
        $notificationIds = $notification->pluck('id_notification'); // Lấy danh sách các ID từ collection
        $notifyMain = Notification_to_mainModel::whereIn('id', $notificationIds)->get();
        session(['notifyMain' => $notifyMain]);
        return redirect()->route('dashboard', ['token' => auth()->user()->refesh_token]);
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
            $shop = Shop::where('owner_id', $user_present->id)->first();
            $cartUser = Cart_to_usersModel::where('user_id', $user_present->id)->first();
            $rank = RanksModel::where('id', $user_present->rank_id)->first();
            $user_present->shop_id = $shop?->id;
            $user_present->cart_id = $cartUser?->id;
            $user_present->rank = $rank;
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

    /**
 * @OA\Put(
 *     path="api/users/{id}",
 *     summary="Update user status",
 *     description="Updates the status of a user to 103 (account locked).",
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
 *         description="Account locked successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Tài khoản đã bị khóa")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found or inactive",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="User not found or inactive")
 *         )
 *     )
 * )
 */
    public function update(Request $request, string $id)
    {
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
    }

    
    public function update_profile(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cloudinary = new Cloudinary();
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $uploadedavatar = $cloudinary->uploadApi()->upload($avatar->getRealPath());
            $avatarUrl = $uploadedavatar['secure_url'];
        }
        $dataUpdate = [
            "fullname" => $request->fullname ?? $user->fullname,
            "phone" => $request->phone ?? $user->phone,
            "email" => $request->email ?? $user->email,
            "description" => $request->description ?? $user->description,
            "genre" => $request->genre ?? $user->genre,
            "datebirth" => $request->datebirth ?? $user->datebirth,
            "updated_at" => now(),
            "avatar" => $avatarUrl ?? $user->avatar,
            "description" => $request->description ?? $user->description,
        ];
        UsersModel::where('id', $user->id)->where('status', 1)->update($dataUpdate);
        if($request->input('address')){
            if ($request->input('address')['default'] == 1) {
                AddressModel::where('default', 1)->update(['default' => null]);
            }
            $filteredCity = $this->get_infomaiton_province_and_city($request->input('address')['province']);
            $filteredDistrict = $this->get_infomaiton_district($request->input('address')['district']);
            $filledWard = $this->get_infomaiton_ward($filteredDistrict['DistrictID'], $request->input('address')['ward']);
            AddressModel::where('id', $request->input('address')['id'])->where('user_id', $user->id)->update([
                "province" => $request->input('address')['province'],
                "province_id" => $filteredCity['ProvinceID'],
                "district" => $request->input('address')['district'],
                "district_id" => $filteredDistrict['DistrictID'],
                "ward" => $request->input('address')['ward'],
                "ward_id" => $filledWard,
                "address" => $request->input('address')['address'],
                "user_id" => $user->id,
                "default" => $request->input('address')['default'] ?? null,
                "type" => $request->input('address')['type'] ?? null,
            ]);
        }
        $dataDone = [
            'status' => true,
            'message' => "Cập nhật thành công!",
        ];
        if($request->token){
            return redirect()->back()->with('message', 'Cập nhật thành công!');
        }

        return response()->json($dataDone, 200);
    }

 /**
 * @OA\Post(
 *     path="api/change_password",
 *     summary="Change user password",
 *     description="Changes the password of the authenticated user.",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"password", "new_password"},
 *             @OA\Property(property="password", type="string", example="current_password"),
 *             @OA\Property(property="new_password", type="string", example="new_password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password changed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Mật khẩu đã được thay đổi thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Tài khoản không tồn tại"),
 *             @OA\Property(property="error_detail", type="string", example="Mật khẩu không đúng")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Password change failed",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Cập nhật thất bại"),
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */
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
        if ($request->token) {
            return redirect()->back()->with('message', 'Mật khẩu đã được thay đổi thành công');
        }
        return response()->json($dataDone, 200);
    }

    /**
 * @OA\Post(
 *     path="api/fogot_password",
 *     summary="Forgot password",
 *     description="Sends a password reset token to the user's email.",
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
 *         description="Password reset token sent",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đã gửi mã xác nhận đến email"),
 *             @OA\Property(property="user", type="string", example="john.doe@example.com")
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
        if(!$request->newpassword){
            return response()->json(['error' => 'vui lòng nhập mật khẩu mới'], 401);
        }
        if ($user) {
            return $this->reset_password($request, $token, $email);
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
    }

    /**
 * @OA\Post(
 *     path="api/logout",
 *     summary="User logout",
 *     description="Logs out the authenticated user and invalidates the JWT token.",
 *     tags={"Authentication"},
 *     @OA\Response(
 *         response=200,
 *         description="Logout successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Đăng xuất thành công")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid or missing token",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Token không hợp lệ hoặc không tồn tại"),
 *             @OA\Property(property="error", type="string", example="Error message")
 *         )
 *     )
 * )
 */
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

    public function adminLogout()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->update([
            'refesh_token' => null,
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

    
}
