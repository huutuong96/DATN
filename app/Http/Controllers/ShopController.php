<?php

namespace App\Http\Controllers;

use App\Models\OrderDetailsModel;
use App\Models\Tax;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Banner;
use App\Models\insurance;
use App\Models\Message;
use App\Models\Product;
use App\Models\ShipsModel;
use App\Models\BannerShop;
use App\Models\ColorModel;
use App\Models\VoucherToShop;
use Cloudinary\Cloudinary;
use App\Models\ColorsModel;
use App\Models\OrdersModel;
use App\Models\refund_order;
use Illuminate\Support\Str;
use App\Models\Shop_manager;
use Illuminate\Http\Request;
use App\Models\Follow_to_shop;
use App\Models\message_detail;
use App\Models\CategoriesModel;
use App\Models\Programme_detail;
use App\Http\Requests\ShopRequest;
use App\Models\ProgramtoshopModel;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Categori_shopsModel;
use App\Models\Learning_sellerModel;
use App\Models\AddressModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Http;


class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('SendNotification');
        $this->middleware('CheckShop')->except('store', 'done_learning_seller', 'revenueReport', 'orderReport', 'bestSellingProducts', 'create_refund_order', 'index', 'show');
    }

    private function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    private function errorResponse($message, $error = null, $status = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error
        ], $status);
    }
    public function index(Request $request)
    {
        $perPage = 10;
        $Shops = Shop::where('status', 2)->paginate($perPage);

        if ($Shops->isEmpty()) {
            return $this->errorResponse('Không tồn tại Shop nào');
        }

        return $this->successResponse('Lấy dữ liệu thành công', [
            'shops' => $Shops->items(),
            'current_page' => $Shops->currentPage(),
            'per_page' => $Shops->perPage(),
            'total' => $Shops->total(),
            'last_page' => $Shops->lastPage(),
        ]);
    }

    public function shop_manager_store($Shop, $user_id, $role, $status)
    {
        $dataInsert = [
            'status' => $status,
            'user_id' => $user_id,
            'shop_id' => $Shop->id,
            'role' => $role,
        ];
        try {
            $Shop_manager = Shop_manager::create($dataInsert);

            return $this->successResponse("Thêm thành công", $Shop_manager);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm không thành công", $th->getMessage());
        }
    }


    public function shop_manager_add(Request $request)
    {
        // dd($request->user_id);
        $dataInsert = [
            'status' => $request->status,
            'user_id' => $request->user_id,
            'shop_id' => $request->shop_id,
            'role' => $request->role,
        ];
        try {
            $Shop_manager = Shop_manager::create($dataInsert);

            return $this->successResponse("Thêm thành công", $Shop_manager);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm không thành công", $th->getMessage());
        }
    }

    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $shopExist = Shop::where('create_by', $user->id)->first();
        if ($shopExist) {
            return $this->errorResponse("Bạn đã tạo shop rồi, không thể tạo shop khác");
        }
        try {
            DB::beginTransaction();
            $dataInsert = [
                'shop_name' => $request->shop_name,
                'pick_up_address' => $request->address_shop,
                'slug' => $request->slug ?? Str::slug($request->shop_name .'-'. $user->id),
                'cccd' => $request->cccd,
                'status' => 1,
                'create_by' => $user->id,
                'tax_id' => $request->tax_id ?? null,
                'owner_id' => $user->id,
                'visits' => 0,
                'revenue' => 0,
                'rating' => 0,
                'location' => $request->location ?? $request->address_shop,
                'email' => $request->email ?? $user->email,
                'description' => $request->description,
                'contact_number' => $request->phone,
                'province' => $request->province ?? null,
                'province_id' => $request->province_id,
                'district' => $request->district ?? null,
                'district_id' => $request->district_id,
                'ward' => $request->ward ?? null,
                'ward_id' => $request->ward_id,
                'vnp_TmnCode' => $request->vnp_TmnCode ?? null,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
            }
            $Shop = Shop::create($dataInsert);
            $user = UsersModel::find($user->id);
            $user->role_id = 2;
            $user->save();
            DB::commit();
            return $this->successResponse("Tạo Shop thành công", [
                'data' => [
                    'Shop' => $Shop,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->errorResponse("Tạo Shop không thành công", $th->getMessage());
        }
    }

    public function product_to_shop_store(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name, '-'),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
            'shop_id' => $id,
            'status' => 1,
            'height' => $request->height,
            'length' => $request->length,
            'weight' => $request->weight,
            'width' => $request->width,
        ];
        $product = Product::create($dataInsert);
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                Image::create([
                    'image' => $uploadedImage['secure_url'],
                    'product_id' => $product->id,
                    'create_by' => auth()->user()->id,
                    'update_by' => auth()->user()->id,
                ]);
            }
        }

        if ($request->color) {
            foreach ($request->color as $color) {
                $colorInsert = [
                    'product_id' => $product->id,
                    'title' => $color['title'],
                    'index' => $color['index'],
                    'status' => $color['status'],
                    'create_by' => auth()->user()->id,
                    'update_by' => auth()->user()->id,
                ];
                ColorsModel::create($colorInsert);
            }
        }

        return $this->successResponse("Thêm sản phẩm thành công", $product);
    }


    public function show_shop_members(string $id)
    {

        $perPage = 10;
        $members = Shop_manager::where('shop_id', $id)->with('users')->paginate($perPage);
        if ($members->isEmpty()) {
            return $this->errorResponse("Không tồn tại thành viên nào trong Shop này");
        }
        $user = JWTAuth::parseToken()->authenticate();
        $is_member = $members->contains('user_id', $user->id);
        return $this->successResponse("Lấy dữ liệu thành viên shop $id thành công", [
            'data' => [
                'members' => $members,
                'is_current_user_member' => $is_member
            ],
        ]);
    }

    public function show(string $id)
    {
        $Shop = Shop::where('id', $id)->where('status', 1)->first();
        $tax = Tax::where('id', $Shop->tax_id)->where('status', 1)->get();
        $bannerShop = BannerShop::where('shop_id', $Shop->id)->where('status', 1)->get();
        $VoucherToShop = VoucherToShop::where('shop_id', $Shop->id)->where('status', 1)->get();
        $Programtoshop = ProgramtoshopModel::where('shop_id', $Shop->id)->get();
        foreach ($Programtoshop as $program_id) {
            $Programme_detail = Programme_detail::where('id', $program_id->program_id)->where('status', 1)->get();
        }
        $Follow_to_shop = Follow_to_shop::where('shop_id', $Shop->id)->get();
        $Categori_shops = Categori_shopsModel::where('shop_id', $Shop->id)->where('status', 1)->get();
        if (!$Shop) {
            return $this->errorResponse("Không tồn tại Shop nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", [
            'shop' => $Shop,
            'tax' => $tax,
            'banner' => $bannerShop,
            'Vouchers' => $VoucherToShop,
            'Programtoshop' => $Programtoshop,
            'Programme_detail' => $Programme_detail ?? null,
            'Follow_to_shop' => $Follow_to_shop,
            'Categori_shops' => $Categori_shops
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_shop_members(Request $request, string $id)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        $member = Shop_manager::where('id', $id)->first();
        if (!$member) {
            return $this->errorResponse("Không tồn tại thành viên trong shop này");
        }
        $member->update([
            'role' => $request->role,
        ]);

        return $this->successResponse("cập nhật thành viên shop $id thành công", $member);
    }
    public function update(Request $request, string $id)
    {
        // $IsOwnerShop =  $this->IsOwnerShop($id);
        // if (!$IsOwnerShop) {
        //     return $this->errorResponse("Bạn không phải là chủ shop");
        // }
        $shop = Shop::where('id', $id)->where('status', 1)->first();
        $user = JWTAuth::parseToken()->authenticate();
        $filteredCity = $this->get_infomaiton_province_and_city($request->input('address')['province']);
        $filteredDistrict = $this->get_infomaiton_district($request->input('address')['district']);
        $filledWard = $this->get_infomaiton_ward($filteredDistrict['DistrictID'], $request->input('address')['ward']);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataInsert['image'] = $uploadedImage['secure_url'];
        }
        $dataInsert = [
            'shop_name' => $request->shop_name ?? $shop->shop_name,
            'pick_up_address' => $request->pick_up_address ?? $shop->pick_up_address,
            'slug' => $request->slug ?? Str::slug($request->shop_name ?? $shop->shop_name, '-'),
            'cccd' => $request->cccd ?? $shop->cccd,
            'status' => $request->status ?? $shop->status,
            'tax_id' => $request->tax_id ?? $shop->tax_id,
            'update_by' => auth()->user()->id,
            'updated_at' => now(),
            'province' => $request->input('address')['province'],
            'province_id' => $filteredCity['ProvinceID'],
            'district' => $request->input('address')['district'],
            'district_id' => $filteredDistrict['DistrictID'],
            'ward' => $request->input('address')['ward'],
            'ward_id' => $filledWard,
        ];
        try {
            $shop->update($dataInsert);
            return $this->successResponse("Cập nhật thông tin Shop thành công", $shop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật thông tin Shop không thành công", $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return $this->errorResponse("Shop không tồn tại");
            }
            // Thay đổi trạng thái thay vì xóa
            $shop->status = 101;
            $shop->save();

            return $this->successResponse("Cập nhật trạng thái shop thành công", $shop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật trạng thái shop không thành công", $th->getMessage());
        }
    }
    public function destroy_members(string $id)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        try {
            $member = Shop_manager::find($id);
            if (!$member) {
                return $this->errorResponse("Thành viên không tồn tại");
            }
            $member->delete();

            return $this->successResponse("Xóa thành viên thành công", $member);
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa thành viên không thành công", $th->getMessage());
        }
    }
    public function store_banner_to_shop(Request $request, string $id)
    {
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return $this->errorResponse("Shop không tồn tại");
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
                $banner = BannerShop::create([
                    'title' => $request->title,
                    'content' => $request->content,
                    'status' => $request->status ?? 1,
                    'URL' => $dataInsert['image'],
                    'shop_id' => $shop->id,
                    'create_by' => auth()->user()->id,
                    'update_by' => auth()->user()->id,
                ]);
                return $this->successResponse("Thêm banner thành công", $banner);
            } else {
                return $this->errorResponse("Không có file hình ảnh được tải lên");
            }
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm banner không thành công", $th->getMessage());
        }
    }
    public function programe_to_shop(Request $request, string $id)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $program_detail = Programme_detail::create([
            'title' => $request->title,
            'content' => $request->content,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
        ]);
        $program = ProgramtoshopModel::create([
            'program_id' => $program_detail->id,
            'shop_id' => $shop->id,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
            'created_at' => now(),
        ]);
        return $this->successResponse("Thêm chương trình thành công", $program);
    }

    public function increase_follower(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $follow = Follow_to_shop::create([
            'user_id' => auth()->user()->id,
            'shop_id' => $shop->id,
            'created_at' => now(),
        ]);
        return $this->successResponse("Đã follow shop thành công", $follow);
    }
    public function decrease_follower(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $follow = Follow_to_shop::where('user_id', auth()->user()->id)->where('shop_id', $shop->id)->first();
        if (!$follow) {
            return $this->errorResponse("Bạn không theo dõi shop này");
        }
        $follow->delete();
        return $this->successResponse("Đã unfollow shop thành công", $follow);
    }
    public function message_to_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $message = Message::create([
            'shop_id' => $shop->id,
            'user_id' => auth()->user()->id,
            'status' => 1,
            'created_at' => now(),
        ]);
        $messageDetail = message_detail::create([
            'message_id' => $message->id,
            'content' => $request->content,
            'send_by' => auth()->user()->id,
            'status' => 1,
            'created_at' => now(),
        ]);
        return $this->successResponse("Đã gửi tin nhắn thành công", $message);
    }
    public function get_order_to_shop(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $order = OrdersModel::where('shop_id', $shop->id)->get();
        return $this->successResponse("Lấy đơn hàng thành công", $order);
    }

    public function get_order_to_shop_by_status(string $id, string $status)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $orders = OrdersModel::with('orderDetails')
        ->where('shop_id', $shop->id)
        ->where('status', $status)
        ->get();
        return $this->successResponse("Lấy đơn hàng thành công", $orders);
    }

    public function update_status_order(Request $request, string $id)
    {
        $order = OrdersModel::find($id);
        if (!$order) {
            return $this->errorResponse("Đơn hàng không tồn tại");
        }
        $order->status = $request->status;
        $order->save();
        return $this->successResponse("Cập nhật trạng thái đơn hàng thành công", $order);
    }

    public function get_product_to_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        
        if ($request->status) {
            $status = $request->status;
            $product = Product::where('shop_id', $shop->id)
                              ->where('status', $status)
                            //   ->where('status', '!=', 5)
                              ->paginate(20);
            $product->appends(['status' => $status]);
        }
        if ($request->status == 1) {
            $product = Product::where('shop_id', $shop->id)->where('status', '!=', 5)->paginate(20);
        }

        $product->load('variants', 'attributes' );
        return response()->json([
            'status' => true,
            'message' => 'Lấy sản phẩm thành công',
            'data' => $product,
        ], 200);
    }

    public function get_voucher_to_shop(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse('Shop không tồn tại', null, 404);
        }

        $perPage = 10; // Number of items per page
        $voucher_to_shop = VoucherToShop::where('shop_id', $shop->id)
            ->where('status', 1)
            ->paginate($perPage);

        return $this->successResponse('Lấy voucher thành công', [
            'voucher_to_shop' => $voucher_to_shop->items(),
            'current_page' => $voucher_to_shop->currentPage(),
            'per_page' => $voucher_to_shop->perPage(),
            'total' => $voucher_to_shop->total(),
            'last_page' => $voucher_to_shop->lastPage(),
        ]);
    }

    public function VoucherToShop(Request $request, $shop_id)
    {
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $request->image,
            'quantity' => $request->quantity,
            'limitValue' => $request->limitValue,
            'ratio' => $request->ratio,
            'code' => $request->code,
            'shop_id' => $shop_id,
            'status' => $request->status ?? 1,
        ];
        $VoucherToShop = VoucherToShop::create($dataInsert);
        return $this->successResponse("Tạo Voucher thành công", $VoucherToShop);
    }


    public function get_category_shop()
    {
        $perPage = 10; // Number of items per page
        $category_shop = Categori_shopsModel::where('status', 1)->paginate($perPage);
        return $this->successResponse("Lấy dữ liệu thành công", [
            'category_shop' => $category_shop->items(),
            'current_page' => $category_shop->currentPage(),
            'per_page' => $category_shop->perPage(),
            'total' => $category_shop->total(),
            'last_page' => $category_shop->lastPage(),
        ]);
    }
    public function category_shop_store(Request $rqt, string $id, string $category_main_id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Shop không tồn tại",
            ], 404);
        }
        $category_main = CategoriesModel::find($category_main_id);

        if (!$category_main) {
            return response()->json([
                'status' => false,
                'message' => "Danh mục không tồn tại",
            ], 404);
        }
        $dataInsert = [
            'title' => $rqt->title ?? $category_main->title,
            'slug' => $rqt->slug ?? $category_main->slug ?? Str::slug($category_main->title, '-'),
            'index' => $rqt->index ?? 1,
            'status' => $category_main->status,
            'parent_id' => $rqt->parent_id ?? $category_main->parent_id,
            'category_id_main' => $category_main_id,
            'shop_id' => $shop->id,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
        ];
        if ($rqt->hasFile('image')) {
            $image = $rqt->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataInsert['image'] = $uploadedImage['secure_url'];
        }
        $categori_shops = Categori_shopsModel::create($dataInsert);

        return response()->json([
            'status' => true,
            'message' => "Thêm Category thành công",
            'data' => $categori_shops,
        ], 201);
    }
    public function update_category_shop(Request $request, string $id)
    {
        $categori_shops = Categori_shopsModel::find($id);
        if (!$categori_shops) {
            return response()->json([
                'status' => false,
                'message' => 'Danh mục shop không tồn tại',
            ], 404);
        }
        $imageUrl = $this->uploadImage($request);
        $dataUpdate = $this->prepareDataForUpdate($request, $categori_shops, $imageUrl);
        try {
            $categori_shops->update($dataUpdate);
            return response()->json([
                'status' => true,
                'message' => 'Cập nhật danh mục shop thành công',
                'data' => $categori_shops,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Cập nhật danh mục shop không thành công',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function destroy_category_shop(string $id)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        try {
            $categori_shops = Categori_shopsModel::find($id);
            if (!$categori_shops) {
                return $this->errorResponse('Danh mục shop không tồn tại', 404);
            }
            $categori_shops->delete();
            return $this->successResponse('Xóa danh mục shop thành công');
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa danh mục shop không thành công", $th->getMessage());
        }
    }

    public function done_learning_seller(string $shopId)
    {
        $IsOwnerShop =  $this->IsOwnerShop($shopId);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }

        $learning = Learning_sellerModel::where('shop_id', $shopId)->first();
        $shop = Shop::where('id', $shopId)->first();
        if (!$learning) {
            return $this->errorResponse('Khóa học không tồn tại', 404);
        }
        $learning->status = 1; // ĐÃ HOÀN THÀNH KHÓA HỌC
        $learning->save();
        $shop->status = 1; // KÍCH HOẠT SHOP
        $shop->save();
        return $this->successResponse('Hoàn thành khóa học thành công', $learning);
    }

    public function IsOwnerShop($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $isOwner = Shop_manager::where('shop_id', $id)
            ->where('user_id', auth()->user()->id)

            ->where('role', 'owner')
            ->first();
        return $isOwner;
    }

    public function IsStaffShop($id)
    {
        $isManager = Shop_manager::where('shop_id', $id)
            ->where('user_id', auth()->user()->id)
            // ->where('role', 'manager')
            ->first();
        return $isManager;
    }

    public function revenueReport(Request $request)
    {
        $IsOwnerShop =  $this->IsOwnerShop($request->shop_id);
        if (!$IsOwnerShop) {
            return $this->errorResponse(" không phải là chủ shop");
        }

        $startDate = $request->start_date;
        $endDate = $request->end_date;
        // Tổng doanh thu
        $revenue = OrdersModel::where('shop_id', $request->shop_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('net_amount');

        // Doanh thu trung bình theo ngày
        $dailyRevenue = OrdersModel::where('shop_id', $request->shop_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(net_amount) as total')
            ->groupBy('date')
            ->get()
            ->avg('total');

        // Doanh thu trung bình theo tháng
        $monthlyRevenue = OrdersModel::where('shop_id', $request->shop_id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(net_amount) as total')
        ->groupBy('year', 'month')
        ->get()
        ->avg('total');

         // Doanh thu trung bình theo năm
        $yearlyRevenue = OrdersModel::where('shop_id', $request->shop_id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('YEAR(created_at) as year, SUM(net_amount) as total')
        ->groupBy('year')
        ->get()
        ->avg('total');

        return $this->successResponse('Lấy báo cáo doanh thu thành công', [
            'revenue' => $revenue,
            'average_daily_revenue' => $dailyRevenue,
            'average_monthly_revenue' => $monthlyRevenue,
            'average_yearly_revenue' => $yearlyRevenue,
        ]);
    }
    public function orderReport(Request $request)
    {
        $IsOwnerShop =  $this->IsOwnerShop($request->shop_id);
        if (!$IsOwnerShop) {
            return $this->errorResponse(" không phải là chủ shop");
        }
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Tổng số đơn hàng
        $totalOrders = OrdersModel::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Tổng số đơn hàng theo ngày
        $dailyOrders = OrdersModel::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders')
            ->groupBy('date')
            ->get();

        // Tổng số đơn hàng theo tháng
        $monthlyOrders = OrdersModel::whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total_orders')
        ->groupBy('year', 'month')
        ->get();

         // Tổng số đơn hàng theo năm
         $yearlyOrders = OrdersModel::whereBetween('created_at', [$startDate, $endDate])
         ->selectRaw('YEAR(created_at) as year, COUNT(*) as total_orders')
         ->groupBy('year')
         ->get();
        return $this->successResponse('Lấy báo cáo đơn hàng thành công', [
            'total_orders' => $totalOrders,
            'daily_orders' => $dailyOrders,
            'monthly_orders' => $monthlyOrders,
            'yearly_orders' => $yearlyOrders,
        ]);
    }

    public function bestSellingProducts(Request $request)
    {
        $IsOwnerShop =  $this->IsOwnerShop($request->shop_id);
        if (!$IsOwnerShop) {
            return $this->errorResponse(" không phải là chủ shop");
        }
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $shopId = $request->shop_id;

        $bestSellingProducts = Product::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('sold_count', 'desc')
            ->take(10)  // Get top 10 best-selling products
            ->get(['id', 'name', 'price', 'sold_count']);

        return $this->successResponse('Lấy báo cáo sản phẩm bán chạy thành công', [
            'best_selling_products' => $bestSellingProducts,
        ]);
    }

    public function create_refund_order(Request $request, string $id)
    {
        $order = OrdersModel::find($id);
        if (!$order) {
            return $this->errorResponse('Đơn hàng không tồn tại', 404);
        }

        $check_order_can_refund = $this->check_order_can_refund($id);
        if (!$check_order_can_refund) {
            return $this->errorResponse('Đơn hàng không thể hoàn lại', 400);
        }

        $refund = refund_order::create([
            'order_id' => $order->id,
            'shop_id' => $order->shop_id,
            'user_id' => $order->user_id,
            'status' => $request->status,
            'amount' => $order->total_amount,
            'reason' => $request->reason,
            'type' => $request->type,
        ]);

        $order->status = 7;
        $order->save();

        $this->notification_refund_order($order);
        return $this->successResponse('Đã gửi yêu cầu hoàn tiền thành công, Yêu cầu của bạn đang được xử lý', $refund);
    }

    private function check_order_can_refund(string $id)
    {
        $status_can_refund = ['pending', 'shipping'];
        $order = OrdersModel::find($id);
        if (!$order) {
            return $this->errorResponse('Đơn hàng không tồn tại', 404);
        }
        if (!in_array($order->status, $status_can_refund)) {
            return $this->errorResponse('Đơn hàng không thể hoàn lại', 400);
        }
        if ($order->created_at < now()->subDays(14)) {
            return $this->errorResponse('Đơn hàng không thể hoàn lại', 400);
        }
        return $order;
    }

    private function notification_refund_order($order)
    {
        if (!$order) {
            return $this->errorResponse('Đơn hàng không tồn tại', 404);
        }
        $notificationData = [
            'type' => 'main',
            'title' => 'Yêu cầu hoàn lại đơn hàng',
            'description' => 'Bạn đã yêu cầu hoàn lại đơn hàng, đơn hàng của bạn đang được xử lý',
            'user_id' => $order->user_id,
            'shop_id' => $order->shop_id,
        ];
        $notificationController = new NotificationController();
        $notification = $notificationController->store(new Request($notificationData));
        return $notification;
    }

    public function refund_order_list(Request $request)
    {
        $refund_order = refund_order::where('shop_id', $request->shop_id)->get();
        return $this->successResponse('Lấy danh sách yêu cầu hoàn tiền thành công', $refund_order);
    }

    public function refund_order_detail(string $id)
    {
        $refund_order = refund_order::with([
            'order',
            'shop',
            'user',
            'reviewer',
            'order.orderDetails',
            'order.orderDetails.product',
        ])->findOrFail($id);

        $formattedData = [
            'id' => $refund_order->id,
            'order' => [
                'id' => $refund_order->order->id,
                'total_amount' => $refund_order->order->total_amount,
                'status' => $refund_order->order->status,
            ],
            'shop' => [
                'id' => $refund_order->shop->id,
                'name' => $refund_order->shop->shop_name,
            ],
            'user' => [
                'id' => $refund_order->user->id,
                'name' => $refund_order->user->name,
                'email' => $refund_order->user->email,
                'phone' => $refund_order->user->phone,
                'address' => $refund_order->user->address,
            ],
            'status' => $refund_order->status,
            'reason' => $refund_order->reason,
            'amount' => $refund_order->amount,
            'note_admin' => $refund_order->note_admin,
            'type' => $refund_order->type,
            'approval_date' => $refund_order->approval_date,
            'reviewer' => $refund_order->reviewer,
            'order_details' => $refund_order->order->orderDetails->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ];
            }),
            'product' => $refund_order->order->orderDetails->map(function ($detail) {
                return [
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity,
                    'price' => $detail->price,
                ];
            }),
        ];

        return $this->successResponse('Lấy chi tiết yêu cầu hoàn tiền thành công', $formattedData);
    }

    public function refund_order_update(Request $request, string $id)
    {

        // SẼ CHECK XEM CÓ PHẢI LÀ CHỦ SHOP KHÔNG
        $isOwnerShop = $this->IsOwnerShop($request->shop_id);
        if (!$isOwnerShop) {
            return $this->errorResponse('Bạn không phải là chủ shop', 403);
        }

        $refund_order = refund_order::find($id);
        $refund_order->status = $request->status;
        $refund_order->save();
        return $this->successResponse('Cập nhật trạng thái yêu cầu hoàn tiền thành công', $refund_order);
    }

    public function register_ship_giao_hang_nhanh(Request $request)
    {
        $token = env('TOKEN_API_REGISTER_GHN');
        $response = Http::withHeaders([
            'token' => $token, // Gắn token vào header
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shop/register', [
            'district_id' => $request->district_id, // Thêm district_id vào tham số truy vấn
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);
        $result = $response->json();
        Shop::where('id', $request->shop_id)->update(['shopid_GHN' => $result['data']['shop_id']]);
        return $result;
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
    public function leadtime($shop_id, $order_id)
    {
        $order = OrdersModel::find($order_id);
        $shopData = Shop::where('id', $shop_id)->first();
        $address = AddressModel::where('user_id', auth()->id())->where('default', 1)->first();
        $token = env('TOKEN_API_GIAO_HANG_NHANH_DEV');
        $response = Http::withHeaders([
            'token' => $token,
            'ShopId' => $shopData->shopid_GHN,
            'token' => env('TOKEN_API_GIAO_HANG_NHANH_DEV'),
        ])->get('https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/leadtime', [
            "from_district_id"=> $shopData->district_id,
            "from_ward_code"=> $shopData->ward_id,
            "to_district_id"=> $address->district_id,
            "to_ward_code"=> $address->ward_id,
            "service_id"=> $order->service_id,
        ]);
        $orderLeadTime = collect($response->json());
        $formattedTime = date('Y-m-d H:i:s', $orderLeadTime['data']['leadtime']);
        return $formattedTime;
    }

    public function shop_remove_product(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }
        $product->status = 5;
        $product->save();
        return response()->json([
            'status' => true,
            'message' => 'Xóa sản phẩm thành công',
        ], 200);
    }
}
