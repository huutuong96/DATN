<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use App\Http\Requests\ShopRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Shop;
use App\Models\Shop_manager;
use App\Models\Tax;
use App\Models\Categori_shopsModel;
use App\Models\CategoriesModel;
use App\Models\Product;
use App\Models\Image;
use App\Models\ColorModel;
use App\Models\BannerShop;
use App\Models\ProgramtoshopModel;
use App\Models\Programme_detail;
use App\Models\Follow_to_shop;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function __construct() {
        $this->middleware('SendNotification');
    }

    public function index()
    {
        $Shops = Cache::remember('all_shops', 60*60, function () {
            return Shop::all();
        });

        if($Shops->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Shops nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $Shops,
            ]
        );
    }



    /**
     * Store a newly created resource in storage.
     */
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

            return response()->json([
                'status' => true,
                'message' => "Thêm thành công",
                'data' => $Shop_manager,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function store(ShopRequest $rqt)
    {
        try {
            $dataInsert = [
                'shop_name' => $rqt->shop_name,
                'pick_up_address' => $rqt->pick_up_address,
                'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
                'cccd' => $rqt->cccd,
                'status' => 101,
                'create_by' => auth()->user()->id,
                'update_by' => auth()->user()->id,
            ];

            if ($rqt->hasFile('image')) {
                $image = $rqt->file('image');
                $cloudinary = new Cloudinary();
                $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                $dataInsert['image'] = $uploadedImage['secure_url'];
            }
            $tax = Tax::find($rqt->tax_id);
            if (!$tax) {
                return response()->json([
                    'status' => false,
                    'message' => "Mã số thuế không tồn tại",
                ], 404);
            }
            $dataInsert['tax_id'] = $tax->id;
            $Shop = Shop::create($dataInsert);
            $this->shop_manager_store($Shop, auth()->user()->id, 'owner', 1);
            $learningInsert = [
                'learn_id' => $request->learn_id,
                'shop_id' => $Shop->id,
                'status' => 101, // Chưa học
                'create_by' => auth()->user()->id
            ];
            $learning_seller = Learning_sellerModel::create($learningInsert);

            Cache::forget('all_shops');

            return response()->json([
                'status' => true,
                'message' => "Tạo Shop thành công",
                'data' => $Shop,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Tạo Shop không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function category_shop_store(Request $rqt,string $id, string $category_main_id)
    {
        $shop = Cache::remember('shop_'.$id, 60*60, function () use ($id) {
            return Shop::find($id);
        });

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Shop không tồn tại",
            ], 404);
        }
        $category_main = Cache::remember('category_'.$category_main_id, 60*60, function () use ($category_main_id) {
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

        Cache::forget('shop_'.$id);
        Cache::forget('category_'.$category_main_id);

        return response()->json([
            'status' => true,
            'message' => "Thêm Category thành công",
            'data' => $categori_shops,
        ], 201);
    }

    public function product_to_shop_store(Request $rqt, string $id)
    {
        $shop = Cache::remember('shop_'.$id, 60*60, function () use ($id) {
            return Shop::find($id);
        });

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => "Shop không tồn tại",
            ], 404);
        }
        $dataInsert = [
            'name' => $rqt->name,
            'slug' => $rqt->slug ?? Str::slug($rqt->name, '-'),
            'description' => $rqt->description,
            'infomation' => $rqt->infomation,
            'price' => $rqt->price,
            'sale_price' => $rqt->sale_price,
            'image' => $dataInsert['image'][0],
            'quantity' => $rqt->quantity,
            'category_id' => $rqt->category_id,
            'brand_id' => $rqt->brand_id,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
        ];
        $product = Product::create($dataInsert);
        if($rqt->hasFile('image')){
            foreach($rqt->file('image') as $image){
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
            foreach($rqt->color as $color){
                $colorInsert = [
                    'product_id' => $product->id,
                    'title' => $color['title'],
                    'index' => $color['index'],
                    'status' => $color['status'],
                    'create_by' => auth()->user()->id,
                    'update_by' => auth()->user()->id,
                ];
                ColorModel::create($colorInsert);
            }

        }

        Cache::forget('shop_'.$id);

        return response()->json([
            'status' => true,
            'message' => "Thêm sản phẩm thành công",
            'data' => $product,
        ], 201);
    }


    public function show_shop_members(string $id)
    {
        $members = Cache::remember('shop_members_'.$id, 60*60, function () use ($id) {
            return Shop_manager::where('shop_id', $id)->with('user')->get();
        });

        if ($members->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại thành viên nào trong Shop này",
            ], 404);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $is_member = $members->contains('user_id', $user->id);

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành viên shop $id thành công",
            'data' => [
                'members' => $members,
                'is_current_user_member' => $is_member
            ],
        ]);
    }

    public function show(string $id)
    {
        $Shops = Cache::remember('shop_'.$id, 60*60, function () use ($id) {
            return Shop::find($id);
        });

        if(!$Shops){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại Shop nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $Shops,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_shop_members(Request $rqt, string $id)
    {
        $member = Shop_manager::where('id', $id)->first();
        if(!$member){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại thành viên trong shop này",
                ]
            );
        }
        $member->update([
            'role' => $rqt->role,
        ]);

        Cache::forget('shop_members_'.$member->shop_id);

        return response()->json(
            [
                'status' => true,
                'message' => "cập nhật thành viên shop $id thành công",
                'data' => $member,
            ]
        );
    }
    public function update(Request $rqt, string $id)
    {
        $shop = Shop::findOrFail($id);

        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        if($rqt->hasFile('image')){
            $image = $rqt->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $dataInsert['image'] = $uploadedImage['secure_url'];
        }
        $dataInsert = [
            'shop_name' => $rqt->shop_name ?? $shop->shop_name,
            'pick_up_address' => $rqt->pick_up_address ?? $shop->pick_up_address,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name ?? $shop->shop_name, '-'),
            'cccd' => $rqt->cccd ?? $shop->cccd,
            'status' => $rqt->status ?? $shop->status,
            'tax_id' => $rqt->tax_id ?? $shop->tax_id,
            'update_by' => auth()->user()->id,
            'updated_at' => now(),
        ];
        try {
            $shop->update($dataInsert);

            Cache::forget('shop_'.$id);
            Cache::forget('all_shops');

            return response()->json([
                'status' => true,
                'message' => "Cập nhật thông tin Shop thành công",
                'data' => $shop,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật thông tin Shop không thành công",
                'error' => $th->getMessage(),
            ], 500);
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
                return response()->json([
                    'status' => false,
                    'message' => 'shop không tồn tại',
                ], 404);
            }
            // Thay đổi trạng thái thay vì xóa
            $shop->status = 101;
            $shop->save();

            Cache::forget('shop_'.$id);
            Cache::forget('all_shops');

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật trạng thái shop thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Cập nhật trạng thái shop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
    public function destroy_members(string $id)
    {
        try {
            $member = Shop_manager::find($id);
            if (!$member) {
                return response()->json([
                    'status' => false,
                    'message' => 'Thành viên không tồn tại',
                ], 404);
            }
            $member->delete();

            Cache::forget('shop_members_'.$member->shop_id);

            return response()->json([
                'status' => true,
                'message' => 'Xóa thành viên thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Xóa thành viên không thành công',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function store_banner_to_shop(Request $rqt, string $id)
    {
        try {
            $shop = Shop::find($id);
            if (!$shop) {
                return response()->json([
                    'status' => false,
                    'message' => 'Shop không tồn tại',
                ], 404);
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
                return response()->json([
                    'status' => true,
                    'message' => 'Thêm banner thành công',
                    'data' => $banner,
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Không có file hình ảnh được tải lên',
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Thêm banner không thành công',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function programe_to_shop(Request $rqt, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
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
        return response()->json([
            'status' => true,
            'message' => 'Thêm chương trình thành công',
            'data' => $program_detail,
        ], 201);
    }

    public function increase_follower(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        $follow = Follow_to_shop::create([
            'user_id' => auth()->user()->id,
            'shop_id' => $shop->id,
            'created_at' => now(),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Đã follow shop thành công',
            'data' => $follow,
        ], 200);
    }
    public function decrease_follower(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        $follow = Follow_to_shop::where('user_id', auth()->user()->id)->where('shop_id', $shop->id)->first();
        if (!$follow) {
            return response()->json([
                'status' => false,
                'message' => 'Bạn không theo dõi shop này',
            ], 404);
        }
        $follow->delete();
        return response()->json([
            'status' => true,
            'message' => 'Đã unfollow shop thành công',
            'data' => $follow,
        ], 200);
    }
    public function message_to_shop(Request $rqt, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
        }
        $message = Message::create([
            'shop_id' => $shop->id,
            'user_id' => auth()->user()->id,
            'status' => 1,
            'created_at' => now(),
        ]);
        $messageDetail = Message_detail::create([
            'message_id' => $message->id,
            'content' => $rqt->content,
            'send_by' => auth()->user()->id,
            'status' => 1,
            'created_at' => now(),
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Đã gửi tin nhắn thành công',
            'data' => $message,
        ], 200);
    }
    public function get_order_to_shop(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return response()->json([
                'status' => false,
                'message' => 'Shop không tồn tại',
            ], 404);
    }
        $order = Order::where('shop_id', $shop->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Lấy đơn hàng thành công',
            'data' => $order,
        ], 200);
    }
    public function get_order_detail_to_shop(string $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng không tồn tại',
            ], 404);
        }
        $orderDetail = Order_detail::where('order_id', $order->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => $orderDetail,
        ], 200);
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
        $voucher_to_shop = Voucher_to_shop::where('shop_id', $shop->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Lấy voucher thành công',
            'data' => [
                'voucher' => $voucher,
                'voucher_to_shop' => $voucher_to_shop,
            ],
        ], 200);
    }

}
