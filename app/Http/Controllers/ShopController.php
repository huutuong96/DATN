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
use Illuminate\Pagination\Paginator;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('SendNotification');
        $this->middleware('CheckShop')->except('store', 'done_learning_seller');
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
        $Shops = Shop::where('status', 1)->paginate($perPage);

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

    public function shop_manager_store(Shop $Shop, $user_id, $role, $status)
    {
        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
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

    public function store(ShopRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $shopExist = Shop::where('create_by', $user->id)->first();
        if ($shopExist) {
            return $this->errorResponse("Bạn đã tạo shop rồi, không thể tạo shop khác");
        }
        try {
            $dataInsert = [
                'shop_name' => $request->shop_name,
                'pick_up_address' => $request->pick_up_address,
                'slug' => $request->slug ?? Str::slug($request->shop_name),
                'cccd' => $request->cccd,
                'status' => 101,
                'create_by' => $user->id,
            ];

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
            }
            $tax = Tax::find($request->tax_id);
            if (!$tax) {
                return $this->errorResponse("Mã số thuế không tồn tại");
            }
            $dataInsert['tax_id'] = $tax->id;
            $Shop = Shop::create($dataInsert);
            $this->shop_manager_store($Shop, $user->id, 'owner', 1);
            $learningInsert = [
                'learn_id' => $request->learn_id,
                'shop_id' => $Shop->id,
                'status' => 101, // Chưa học
                'create_by' => $user->id
            ];
            $learning_seller = Learning_sellerModel::create($learningInsert);
            return $this->successResponse("Tạo Shop thành công", [
                'data' => [
                    'Shop' => $Shop,
                    'Learning_seller' => $learning_seller
                ],
            ]);
        } catch (\Throwable $th) {
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
            'Programme_detail' => $Programme_detail,
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

        $IsOwnerShop =  $this->IsOwnerShop($id);
        if (!$IsOwnerShop) {
            return $this->errorResponse("Bạn không phải là chủ shop");
        }
        $shop = Shop::where('id', $id)->where('status', 1)->first();
        $user = JWTAuth::parseToken()->authenticate();

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
    public function get_order_detail_to_shop(string $id)
    {
        $order = OrdersModel::find($id);
        if (!$order) {
            return $this->errorResponse("Đơn hàng không tồn tại");
        }
        $orderDetail = OrderDetailsModel::where('order_id', $order->id)->get();
        return $this->successResponse("Lấy chi tiết đơn hàng thành công", $orderDetail);
    }
    public function get_product_to_shop(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        $product = Product::where('shop_id', $shop->id)->get();
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
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->first();
        return $isOwner;
    }
}
