<?php

namespace App\Http\Controllers;

use App\Models\OrderDetailsModel;
use App\Models\Tax;
use App\Models\Shop;
use App\Models\Image;
use App\Models\Banner;
use App\Models\Message;
use App\Models\Product;
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
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('SendNotification');
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
    public function index()
    {
        $Shops = Cache::remember('all_shops', 60 * 60, function () {
            return Shop::all();
        });

        if ($Shops->isEmpty()) {
            return $this->errorResponse('Không tồn tại Shop nào');
        }
        return $this->successResponse('Lấy dữ liệu thành công', $Shops);
    }

    public function shop_manager_store(Shop $Shop, $user_id, $role, $status)
    {
        $dataInsert = [
            'status' => $status,
            'user_id' => $user_id,
            'shop_id' => $Shop->id,
            'role' => $role,
        ];
        try {
            $Shop_manager = Shop_manager::create($dataInsert);

            Cache::forget('all_shops');

            return $this->successResponse("Thêm thành công", $Shop_manager);
        } catch (\Throwable $th) {
            return $this->errorResponse("Thêm không thành công", $th->getMessage());
        }
    }

    public function store(ShopRequest $rqt)
    {
        $user = JWTAuth::parseToken()->authenticate();
        try {
            $dataInsert = [
                'shop_name' => $rqt->shop_name,
                'pick_up_address' => $rqt->pick_up_address,
                'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
                'cccd' => $rqt->cccd,
                'status' => 101,
                'create_by' => $user->id,
                'Owner_id' => $user->id,
            ];
            if ($rqt->hasFile('image')) {
                $image = $rqt->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
            }
            $tax = Tax::find($rqt->tax_id);
            if (!$tax) {
                return $this->errorResponse("Mã số thuế không tồn tại");
            }
            $dataInsert['tax_id'] = $tax->id;
            $Shop = Shop::create($dataInsert);
            $this->shop_manager_store($Shop, $user->id, 'owner', 1);

            $learningInsert = [
                'learn_id' => $rqt->learn_id,
                'shop_id' => $Shop->id,
                'status' => 101, // Chưa học
                'create_by' => $user->id
            ];
            $learning_seller = Learning_sellerModel::create($learningInsert);

            Cache::forget('all_shops');

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



    public function product_to_shop_store(Request $rqt, string $id)
    {
        $shop = Cache::remember('shop_' . $id, 60 * 60, function () use ($id) {
            return Shop::find($id);
        });

        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $dataInsert = [
            'name' => $rqt->name,
            'slug' => $rqt->slug ?? Str::slug($rqt->name, '-'),
            'description' => $rqt->description,
            'infomation' => $rqt->infomation,
            'price' => $rqt->price,
            'sale_price' => $rqt->sale_price,
            'quantity' => $rqt->quantity,
            'category_id' => $rqt->category_id,
            'brand_id' => $rqt->brand_id,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
        ];
        $product = Product::create($dataInsert);
        if ($rqt->hasFile('image')) {
            foreach ($rqt->file('image') as $image) {
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $uploadedImage['secure_url'];
                Image::create([
                    'image' => $uploadedImage['secure_url'],
                    'product_id' => $product->id,
                    'create_by' => auth()->user()->id,
                    'update_by' => auth()->user()->id,
                ]);
            }
        }

        if ($rqt->color) {
            foreach ($rqt->color as $color) {
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

        Cache::forget('shop_' . $id);

        return $this->successResponse("Thêm sản phẩm thành công", $product);
    }


    public function show_shop_members(string $id)
    {
        $members = Cache::remember('shop_members_' . $id, 60 * 60, function () use ($id) {
            return Shop_manager::where('shop_id', $id)->with('users')->get();
        });

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
        $Shops = Cache::remember('shop_' . $id, 60 * 60, function () use ($id) {
            return Shop::find($id);
        });

        if (!$Shops) {
            return $this->errorResponse("Không tồn tại Shop nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", $Shops);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_shop_members(Request $rqt, string $id)
    {
        $member = Shop_manager::where('id', $id)->first();
        if (!$member) {
            return $this->errorResponse("Không tồn tại thành viên trong shop này");
        }
        $member->update([
            'role' => $rqt->role,
        ]);

        Cache::forget('shop_members_' . $member->shop_id);

        return $this->successResponse("cập nhật thành viên shop $id thành công", $member);
    }
    public function update(Request $rqt, string $id)
    {
        $shop = Shop::findOrFail($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        
        $dataInsert = [
            'shop_name' => $rqt->shop_name ?? $shop->shop_name,
            'pick_up_address' => $rqt->pick_up_address ?? $shop->pick_up_address,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name ?? $shop->shop_name, '-'),
            'cccd' => $rqt->cccd ?? $shop->cccd,
            'status' => $rqt->status ?? $shop->status,
            'tax_id' => $rqt->tax_id ?? $shop->tax_id,
            'update_by' => $user->id,
            'Owner_id' => $rqt->Owner_id ?? $user->id,
            'updated_at' => now(),
        ];

        if ($rqt->hasFile('image')) {
            $image = $rqt->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataInsert['image'] = $uploadedImage['secure_url'];
        }

        try {
            $shop->update($dataInsert);

            Cache::forget('shop_' . $id);
            Cache::forget('all_shops');

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
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return $this->errorResponse("shop không tồn tại");
            }
            // Thay đổi trạng thái thay vì xóa
            $shop->status = 101;
            $shop->save();

            Cache::forget('shop_' . $id);
            Cache::forget('all_shops');

            return $this->successResponse("Cập nhật trạng thái shop thành công", $shop);
        } catch (\Throwable $th) {
            return $this->errorResponse("Cập nhật trạng thái shop không thành công", $th->getMessage());
        }
    }
    public function destroy_members(string $id)
    {
        try {
            $member = Shop_manager::find($id);
            if (!$member) {
                return $this->errorResponse("Thành viên không tồn tại");
            }
            $member->delete();

            Cache::forget('shop_members_' . $member->shop_id);

            return $this->successResponse("Xóa thành viên thành công", $member);
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa thành viên không thành công", $th->getMessage());
        }
    }
    public function store_banner_to_shop(Request $rqt, string $id)
    {
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return $this->errorResponse("Shop không tồn tại");
            }
            if ($rqt->hasFile('image')) {
                $image = $rqt->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
                $banner = Banner::create([
                    'image' => $dataInsert['image'],
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
    public function programe_to_shop(Request $rqt, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $program_detail = Programme_detail::create([
            'title' => $rqt->title,
            'content' => $rqt->content,
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
    public function message_to_shop(Request $rqt, string $id)
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
            'content' => $rqt->content,
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
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        $voucher_to_shop = VoucherToShop::where('shop_id', $shop->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Lấy voucher thành công',
            'data' => [
                'voucher_to_shop' => $voucher_to_shop,
            ],
        ], 200);
    }

    public function get_category_shop()
    {
        $category_shop = Cache::remember('category_shop', 60 * 60, function () {
            return Categori_shopsModel::where('status', 1)->get();
        });
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $category_shop,
        ]);
    }
    public function category_shop_store(Request $rqt, string $id, string $category_main_id)
    {
        $shop = Cache::remember('shop_' . $id, 60 * 60, function () use ($id) {
            return Shop::find($id);
        });

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Shop không tồn tại",
            ], 404);
        }
        $category_main = Cache::remember('category_' . $category_main_id, 60 * 60, function () use ($category_main_id) {
            return CategoriesModel::find($category_main_id);
        });

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

        Cache::forget('shop_' . $id);
        Cache::forget('category_' . $category_main_id);

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
            Cache::forget('categori_shop_' . $id);
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
        try {
            $categori_shops = Categori_shopsModel::find($id);
            if (!$categori_shops) {
                return $this->errorResponse('Danh mục shop không tồn tại', 404);
            }
            $categori_shops->delete();
            Cache::forget('categori_shop');
            return $this->successResponse('Xóa danh mục shop thành công');
        } catch (\Throwable $th) {
            return $this->errorResponse("Xóa danh mục shop không thành công", $th->getMessage());
        }
    }
}