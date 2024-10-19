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
     *     path="/api/users",
     *     tags={"users"},
     *     summary="Get list of users",
     *     @OA\Response(
     *         response=200,
     *         description="A list of users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Users"))
     *     ),
     *     security={{ "bearerAuth": {} }}
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

    public function register(UserRequest $request)
    {
        $existingUser = UsersModel::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email đã tồn tại.',
            ], 422);
        }
        $dataInsert = [
            "fullname" => $request->fullname,
            "password" => Hash::make($request->password),
            "email" => $request->email,
            "rank_id" => $request->rank_id ?? null,
            "role_id" => $request->role_id ?? null,
            "status" => 101, // 101 là tài khoản chưa được kích hoạt
            "login_at" => now(),
        ];

        $user = UsersModel::create($dataInsert);
        $token = JWTAuth::fromUser($user);
        $user->update([
            'refesh_token' => $token,
        ]);
        $dataDone = [
            'status' => true,
            'message' => "Đăng ký thành công, chưa kích hoạt",
            'user' => $user,
        ];
        // Mail::to($user->email)->send(new ConfirmMail($user, $token));
        ConfirmMailRegister::dispatch($user, $token);
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
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Tài khoản hoặc mật khẩu không đúng'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Không thể tạo token'], 500);
        }

        $user = JWTAuth::user();
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

            // Eager load related models
            $user_present->load([
                'address',
                'rank',
                'notifications',
                'orders.orderDetails.product'
            ]);

            // Extract necessary data
            $user_present_address = $user_present->address;
            $user_present_rank = $user_present->rank;
            $notifications = $user_present->notifications;
            $notification_ids = $notifications->pluck('id_notification');
            $main_notifications = Notification_to_mainModel::whereIn('id', $notification_ids)->paginate(3);
            $orders = $user_present->orders;
            $orderDetails = $orders->flatMap->orderDetails;
            $productIds = $orderDetails->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->paginate(3);

            return response()->json([
                'status' => 'success',
                'message' => 'Lấy dữ liệu thành công',
                'me' => $user_present,
                'address' => $user_present_address,
                'notifications' => $main_notifications,
                'orders' => [
                    'orderDetail' => $orderDetails,
                    'product' => $products,
                ],
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
        if(!$request->newpassword){
            return response()->json(['error' => 'vui lòng nhập mật khẩu mới'], 401);
        }
        if ($user) {
            return $this->reset_password($request, $token, $email);
        }
    }

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
            'message' => "Tài khoản đã được vô hiệu hóa trong 30 ngày",
        ];
        return response()->json($dataDone, 200);
    }

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

    public function get_infomaiton_province_and_city($province)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
        $cities = collect($response->json()['data']); // Chuyển thành Collection
        // Lọc tỉnh dựa trên tên
        $filteredCity = $cities->firstWhere('ProvinceName', $province);
        return $filteredCity;
    }

    public function get_infomaiton_district($districtName)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
        $district = collect($response->json()['data']); // Chuyển thành Collection
        $filtereddistrict = $district->firstWhere('DistrictName', $districtName);
        return $filtereddistrict;
    }
    public function get_infomaiton_ward($districtId, $wardName)
    {
        $token = env('TOKEN_API_GIAO_HANG_NHANH');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
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
}
