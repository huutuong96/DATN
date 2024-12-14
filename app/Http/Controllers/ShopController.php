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
use App\Models\history_get_cash_shops;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\product_variants;
use App\Models\ProducttocartModel;
use App\Models\UsersModel;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('SendNotification');
        $this->middleware('CheckShop')->except('store', 'done_learning_seller', 'revenueReport', 'orderReport', 'bestSellingProducts', 'create_refund_order', 'index', 'show','getShopByCategory','showClient');
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
                'created_at' => now(),
                'updated_at' => now(),
                'tax_id' => 5 ?? null,
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
        $Shop = Shop::where('id', $id)->whereIn('status', [2, 3])->first();
        if (!$Shop) {
            return $this->errorResponse("Không tồn tại Shop nào");
        }
        
        $Shop->visits = $Shop->visits + 1;
        $Shop->save();
        $follow_count = Follow_to_shop::where('shop_id', $Shop->id)->count();
        $limit = $request->limit ?? 20;
        $tax = Tax::where('id', $Shop->tax_id)->where('status', 2)->get();
        $bannerShop = BannerShop::where('shop_id', $Shop->id)->where('status', 2)->get();
        $VoucherToShop = VoucherToShop::where('shop_id', $Shop->id)->where('status', 2)->get();
        $productsQuery = Product::where('shop_id', $Shop->id)
                                ->where('status', 2);
        $category = [];
        foreach ($productsQuery->get() as $product) {
            $categoryId = $product->category_id;
            if (!in_array($categoryId, array_column($category, 'id'))) {
                $category[] = CategoriesModel::find($categoryId);
            }
        }
        $products = $productsQuery->paginate($limit);
        if (!$Shop) {
            return $this->errorResponse("Không tồn tại Shop nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", [
            'shop' => $Shop,
            'tax' => $tax,
            'banner' => $bannerShop,
            'Vouchers' => $VoucherToShop,
            // 'products' => $products,
            'categories' => $category,
            'follow_count' => $follow_count,
        ]);
    }
    public function showClient(string $id)
    {
        $Shop = Shop::where('id', $id)->whereIn('status', [2,3])->first();
        if (!$Shop) {
            return $this->errorResponse("Không tồn tại Shop nào", [], 404);
        }

        $Shop->visits = $Shop->visits + 1;
        $Shop->save();
        $follow_count = Follow_to_shop::where('shop_id', $Shop->id)->count();
        $limit = $request->limit ?? 20;
        $tax = Tax::where('id', $Shop->tax_id)->where('status', 2)->get();
        $bannerShop = BannerShop::where('shop_id', $Shop->id)->where('status', 2)->get();
        $VoucherToShop = VoucherToShop::where('shop_id', $Shop->id)->where('status', 2)->get();
        $productsQuery = Product::where('shop_id', $Shop->id)
                                ->where('status', 2);
        $category = [];
        foreach ($productsQuery->get() as $product) {
            $categoryId = $product->category_id;
            if ($categoryId && !in_array($categoryId, array_column($category, 'id'))) {
            $categoryModel = CategoriesModel::find($categoryId);
            if ($categoryModel) {
                $category[] = $categoryModel;
            }
            }
        }
        $products = $productsQuery->paginate($limit);
        if (!$Shop) {
            return $this->errorResponse("Không tồn tại Shop nào");
        }
        return $this->successResponse("Lấy dữ liệu thành công", [
            'shop' => $Shop,
            'tax' => $tax,
            'banner' => $bannerShop,
            'Vouchers' => $VoucherToShop,
            // 'products' => $products,
            'categories' => $category,
            'follow_count' => $follow_count,
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
        try {
            $shop = Shop::where('id', $id)->where('status', 2)->first();
            $user = JWTAuth::parseToken()->authenticate();
            $shopLock = Shop::where('id', $id)->first();
            if ($shopLock->status == 3) {
                $shop = Shop::where('id', $id)->where('owner_id', $user->id)->first();
                $products = Product::where('shop_id', $id)->get();
                foreach ($products as $product) {
                    $product->status = 2;
                    $product->save();
                }
            }
            if (!$shop) {
                return $this->errorResponse("Shop không tồn tại");
            }
            if ($request->status == 3) {
                $products = Product::where('shop_id', $id)->get();
                foreach ($products as $product) {
                    $product->status = 2;
                    $product->save();
                }
            }

            $dataInsert = [
                'shop_name' => $request->shop_name ?? $shop->shop_name,
                'pick_up_address' => $request->pick_up_address ?? $shop->pick_up_address,
                'slug' => $request->slug ?? Str::slug($request->shop_name ?? $shop->shop_name, '-'),
                'cccd' => $request->cccd ?? $shop->cccd,
                'status' => $request->status ?? $shop->status,
                'tax_id' => $request->tax_id ?? $shop->tax_id,
                'update_by' => $user->id,
                'updated_at' => now(),
                'province' => $request->province,
                'province_id' => $request->province_id,
                'district' => $request->district,
                'district_id' => $request->district_id,
                'ward' => $request->ward,
                'ward_id' => $request->ward_id,
                'image' => $request->image ?? $shop->image,
            ];
            try {
                $shop->update($dataInsert);
                return $this->successResponse("Cập nhật thông tin Shop thành công", $shop);
            } catch (\Throwable $th) {
                return $this->errorResponse("Cập nhật thông tin Shop không thành công", $th->getMessage());
            }
        } catch (\Throwable $th) {
            log_debug($th);
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

    public function get_order_to_shop_by_status(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $limit = $request->limit ?? 10;
        $order_status = $request->order_status ?? 0;
       

        $query = OrdersModel::query();
        if ($request->order_status) {
            $query->where('order_status', $order_status);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
            $q->where('to_name', 'LIKE', "%{$search}%")
              ->orWhere('id', 'LIKE', "%{$search}%");
            });
        }
        if (request()->has('sort')) {
            $sort = request()->input('sort');
            switch ($sort) {
            case 'price':
                $query->orderBy('total_amount', 'asc');
                break;
            case '-price':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'updated_at':
                $query->orderBy('updated_at', 'asc');
                break;
            case '-updated_at':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'name':
                $query->orderBy('to_name', 'asc');
                break;
            case '-name':
                $query->orderBy('to_name', 'desc');
                break;
            default:
                break;
            }
        }

        $orders = $query->with('orderDetails', 'payment')->where('shop_id', $shop->id)->where('order_status', $order_status)->orderBy('updated_at', 'desc')->paginate($limit);



        foreach ($orders as $order) {
            foreach ($order->orderDetails as $orderDetail) {
                if ($orderDetail->variant != null) {
                    $variant = $orderDetail->variant;
                } else {
                    $product = $orderDetail->product;
                }
            }
        }
        foreach ($orders as $key => $order) {
            foreach ($order->orderDetails as $orderDetail) {
                if ($orderDetail->variant) {
                    $orderDetail['product'] = $orderDetail->variant->product;
                    unset($orderDetail->variant['product']);
                }
            }
        }
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
        $limit = $request->input('limit', 10); 
        $limit = is_numeric($limit) && $limit > 0 ? (int)$limit : 10;
    
        $query = Product::where('shop_id', $shop->id);
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('sku', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        if (request()->has('sort')) {
            $sort = request()->input('sort');
            switch ($sort) {
            case 'price':
                $query->orderBy(DB::raw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED ) ELSE CAST(show_price AS UNSIGNED) END'), 'asc');                
                break;
            case '-price':
                $query->orderBy(DB::raw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED ) ELSE CAST(show_price AS UNSIGNED) END'), 'desc');                
                break;
            case 'updated_at':
                $query->orderBy('updated_at', 'asc');
                break;
            case '-updated_at':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'quantity':
                $query->orderBy(DB::raw('CASE WHEN quantity > 0 THEN quantity ELSE (SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) END'), 'asc');
                break;
            case '-quantity':
                $query->orderBy(DB::raw('CASE WHEN quantity > 0 THEN quantity ELSE (SELECT SUM(stock) FROM product_variants WHERE product_variants.product_id = products.id) END'), 'desc');
                break;
                    
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case '-name':
                $query->orderBy('name', 'desc');
                break;
            case 'sold_count':
                $query->orderBy('sold_count', 'asc');
                break;
            case '-sold_count':
                $query->orderBy('sold_count', 'desc');
                break;
            default:
                break;
            }
        }
    
        if ($request->has('status')) {
            $status = $request->status;
    
            if ($status == 1) {
                $query->where('status', '!=', 5);
            } else {
                $query->where('status', $status);
            }
        }
    
        $product = $query->paginate($limit);
        if ($request->has('status')) {
            $product->appends(['status' => $request->status]);
        }
    
        $product->load('variants', 'attributes');
    
        return response()->json([
            'status' => true,
            'message' => 'Lấy sản phẩm thành công',
            'data' => $product,
        ], 200);
    }
    public function calculatePercentage ($ids){
        $total = $ids->count();
        $counts = $ids->countBy();
        $percentages = $counts->map(function ($count) use ($total) {
            return round(($count / $total) * 100, 2);
        });
        return $percentages;
    }

    public function get_dashboard_shop(string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        }
        $orders_wait_confirm = OrdersModel::where('shop_id', $shop->id)->where('order_status', 0)->count();
        $orders_confirmed = OrdersModel::where('shop_id', $shop->id)->where('order_status', 1)->count();
        $orders_prepare = OrdersModel::where('shop_id', $shop->id)->where('order_status', 2)->count();
        $orders_packed = OrdersModel::where('shop_id', $shop->id)->where('order_status', 3)->count();
        $orders_handed_over = OrdersModel::where('shop_id', $shop->id)->where('order_status', 4)->count();
        $orders_shipping = OrdersModel::where('shop_id', $shop->id)->where('order_status', 5)->count();
        $orders_delivery_failed = OrdersModel::where('shop_id', $shop->id)->where('order_status', 6)->count();
        $orders_delivered = OrdersModel::where('shop_id', $shop->id)->where('order_status', 7)->count();
        $orders_refund = OrdersModel::where('shop_id', $shop->id)->where('order_status', 9)->count();
        $orders_complete = OrdersModel::where('shop_id', $shop->id)->where('order_status', 8)->count();
        $orders_canceled = OrdersModel::where('shop_id', $shop->id)->where('order_status', 10)->count();
        $totalOrder = OrdersModel::where('shop_id', $shop->id)->count();
        $totalProduct = Product::where('shop_id', $shop->id)->count();
        $totalRevenue = OrdersModel::where('shop_id', $shop->id)->sum('net_amount');
        $totalFollow = Follow_to_shop::where('shop_id', $shop->id)->count();
        $totalView = $shop->visits;
        $totalRating = $shop->rating;
        
        return $this->successResponse("Lấy dữ liệu thành công", [
            'total_order' => $totalOrder,
            'total_product' => $totalProduct,
            'total_revenue' => $totalRevenue,
            'total_follow' => $totalFollow,
            'total_view' => $totalView,
            'total_rating' => $totalRating,
            'orders_wait_confirm' => $orders_wait_confirm,
            'orders_confirmed' => $orders_confirmed,
            'orders_prepare' => $orders_prepare,
            'orders_packed' => $orders_packed,
            'orders_handed_over' => $orders_handed_over,
            'orders_shipping' => $orders_shipping,
            'orders_delivery_failed' => $orders_delivery_failed,
            'orders_delivered' => $orders_delivered,
            'orders_complete' => $orders_complete,
            'orders_refund' => $orders_refund,
            'orders_canceled' => $orders_canceled,
        ]);
    }

    

    public function get_analyst_rank_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        } 
        if ($request->has('sort')) {
            $sort = $request->input('sort');
            switch ($sort) {
            case 'orders':
                $orders = OrdersModel::where('shop_id', $shop->id)->orderBy('net_amount', 'desc')->select('id', 'net_amount')->get();
                return $this->successResponse("Lấy dữ liệu thành công", $orders ?? []);
            break;
            case 'products':
                $products = Product::where('shop_id', $shop->id)->orderBy('sold_count', 'desc')->select('name', 'sold_count')->get();
                return $this->successResponse("Lấy dữ liệu thành công", $products ?? []);
            break;
            case 'views':
                $products = Product::where('shop_id', $shop->id)->orderBy('view_count', 'desc')->select('name', 'view_count')->get();
                return $this->successResponse("Lấy dữ liệu thành công", $products ?? []);
            break;
            default:
            break;
            }
        }       
            

    }

    public function get_analyst_cate_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        } 
        $products = Product::where('shop_id', $shop->id)->select('category_id', 'sold_count')->get();
        $categories = CategoriesModel::whereIn('id', $products->pluck('category_id'))->get();

        $soldCounts = $products->groupBy('category_id')->map(function ($group) {
            return [
                'category_id' => $group->first()->category_id,
                'sold_count' => $group->sum('sold_count')
            ];
        })->values()->toArray();
        $soldCounts = collect($soldCounts)->sortByDesc('sold_count')->values()->all();
        return $this->successResponse("Lấy dữ liệu thành công", $soldCounts ?? []);
    }

    public function get_analyst_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        } 
        $query = OrdersModel::where('shop_id', $shop->id);
        if ($request->order_status || $request->order_status == 0) {
            $query->where('order_status', $request->order_status);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('time')) {
            $time = $request->input('time');
            switch ($time) {
                case '1':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case '2':
                    $query->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()]);
                    break;
                case '3':
                    $query->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()]);
                    break;
                case '4':
                    $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()]);
                    break;
                default:
                    break;
            }
        }
        $orders = $query->get();
        $total_orders = $orders->count();
        $total_revenue_orders = $orders->sum('net_amount');
        $average_revenue_per_order = $orders->avg('net_amount');
        return $this->successResponse("Lấy dữ liệu thành công", [
            'revenue' => [
                'labelEN' => 'revenue',
                'labelVN' => 'Doanh số',
                'value' => $total_revenue_orders,
                'isPrice' => true
            ],
            'orders' => [
                'labelEN' => 'orders',
                'labelVN' => 'Tổng Đơn Hàng',
                'value' => $total_orders,
                'isPrice' => false
            ],
            'average_revenue_per_order' => [
                'labelEN' => 'average_revenue_per_order',
                'labelVN' => 'Trung Bình Doanh Số Mỗi Đơn Hàng',
                'value' => round($average_revenue_per_order, 2),
                'isPrice' => true
            ],
        ]);
    }

    public function get_analyst_chart_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse("Shop không tồn tại");
        } 
        $query = OrdersModel::where('shop_id', $shop->id);
        if ($request->order_status || $request->order_status == 0) {
            $query->where('order_status', $request->order_status);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('time')) {
            $time = $request->input('time');
            switch ($time) {
            case '1':
                $orders = $query->whereDate('created_at', Carbon::today())->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('H:00');
                });
                $allHours = [];
                for ($i = 0; $i < 24; $i++) {
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $allHours[$hour] = [
                    'dateKey' => $hour,
                    'revenue' => 0,
                    'orders' => 0,
                    'average_revenue_per_order' => 0
                ];
                }
                $sum = 0;
                foreach ($orders as $key => $group) {
                $revenue = $group->sum('net_amount');
                $sum += $group->sum('net_amount');
                $orderCount = $group->count();
                $averageRevenuePerOrder = $orderCount > 0 ? $revenue / $orderCount : 0;
                $allHours[$key] = [
                    'dateKey' => $key,
                    'revenue' => $revenue,
                    'orders' => $orderCount,
                    'average_revenue_per_order' => $averageRevenuePerOrder,
                ];
                }

                $data = array_values($allHours);
                return response()->json(['data' => $data]);
                break;
               
            case '2':
                $orders = $query->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('d-m-Y');
                });
                $allDays = [];
                for ($i = 0; $i < 7; $i++) {
                $day = Carbon::now()->subDays($i)->format('d-m-Y');
                $allDays[$day] = [
                    'dateKey' => $day,
                    'revenue' => 0,
                    'orders' => 0,
                    'average_revenue_per_order' => 0
                ];
                }
                $sum = 0;
                foreach ($orders as $key => $group) {
                $revenue = $group->sum('net_amount');
                $sum += $group->sum('net_amount');
                $orderCount = $group->count();
                $averageRevenuePerOrder = $orderCount > 0 ? $revenue / $orderCount : 0;
                $allDays[$key] = [
                    'dateKey' => $key,
                    'revenue' => $revenue,
                    'orders' => $orderCount,
                    'average_revenue_per_order' => $averageRevenuePerOrder,
                ];
                }

                $data = array_reverse(array_values($allDays));
                return response()->json(['data' => $data]);
                break;
            case '3':
                $orders = $query->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('d-m-Y');
                });
                $allDays = [];
                for ($i = 0; $i < 30; $i++) {
                $day = Carbon::now()->subDays($i)->format('d-m-Y');
                $allDays[$day] = [
                    'dateKey' => $day,
                    'revenue' => 0,
                    'orders' => 0,
                    'average_revenue_per_order' => 0
                ];
                }
                $sum = 0;
                foreach ($orders as $key => $group) {
                $revenue = $group->sum('net_amount');
                $sum += $group->sum('net_amount');
                $orderCount = $group->count();
                $averageRevenuePerOrder = $orderCount > 0 ? $revenue / $orderCount : 0;
                $allDays[$key] = [
                    'dateKey' => $key,
                    'revenue' => $revenue,
                    'orders' => $orderCount,
                    'average_revenue_per_order' => $averageRevenuePerOrder,
                ];
                }

                $data = array_reverse(array_values($allDays));
                return response()->json(['data' => $data]);
                break;
            case '4':
                $orders = $query->whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()])->get()->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m-Y');
                });
                $allMonths = [];
                for ($i = 0; $i < 12; $i++) {
                $month = Carbon::now()->subMonths($i)->format('m-Y');
                $allMonths[$month] = [
                    'dateKey' => $month,
                    'revenue' => 0,
                    'orders' => 0,
                    'average_revenue_per_order' => 0
                ];
                }
                $sum = 0;
                foreach ($orders as $key => $group) {
                $revenue = $group->sum('net_amount');
                $sum += $group->sum('net_amount');
                $orderCount = $group->count();
                $averageRevenuePerOrder = $orderCount > 0 ? $revenue / $orderCount : 0;
                $allMonths[$key] = [
                    'dateKey' => $key,
                    'revenue' => $revenue,
                    'orders' => $orderCount,
                    'average_revenue_per_order' => $averageRevenuePerOrder,
                ];
                }

                $data = array_reverse(array_values($allMonths));
                return response()->json(['data' => $data]);
                break;
            default:
                $orders = collect();
                break;
            }
        }
        return $this->successResponse("Lấy dữ liệu thành công", []);
    }

    public function get_voucher_to_shop(Request $request, string $id)
    {
        $shop = Shop::find($id);
        if (!$shop) {
            return $this->errorResponse('Shop không tồn tại', null, 404);
        }

        $perPage = $request->limit ?? 10; // Number of items per page
        $voucher_to_shop = VoucherToShop::where('shop_id', $shop->id)
            ->where('status', 2)
            ->paginate($perPage);

        return $this->successResponse('Lấy voucher thành công', [
            $voucher_to_shop,
        ]);
    }

    public function VoucherToShop(Request $request, $shop_id)
    {
        $shop = Shop::where('id', $shop_id)->select('id', 'image')->first();
        $dataInsert = [
            'title' => $request->title,
            'description' => $request->description,
            'image' => $request->image ?? $shop->image,
            'quantity' => $request->quantity,
            'limitValue' => $request->limitValue,
            'code' => $request->code,
            'shop_id' => $shop_id,
            'status' => 2,
            'ratio' => is_numeric($request->ratio) ? $request->ratio / 100 : null,
            'price' => $request->ratio ? null : ($request->price ?? null),
            'min' => $request->min ?? null,
            'type' => $request->type ?? 1,
        ];
        $VoucherToShop = VoucherToShop::create($dataInsert);
        return $this->successResponse("Tạo Voucher thành công", $VoucherToShop);
    }


    public function UpdateVoucherToShop(Request $request, $voucher_id)
    {
        $VoucherToShop = VoucherToShop::where('id', $voucher_id)->first();
        $dataInsert = [
            'title' => $request->title ?? $VoucherToShop->title,
            'description' => $request->description ?? $VoucherToShop->description,
            'image' => $request->image ?? $VoucherToShop->image,
            'quantity' => $request->quantity ?? $VoucherToShop->quantity,
            'limitValue' => $request->limitValue ?? $VoucherToShop->limitValue,
            'code' => $request->code ?? $VoucherToShop->code,
            'shop_id' => $VoucherToShop->shop_id,
            'status' => $request->status ?? 1,
            'ratio' => $request->ratio ?? null,
            'price' => $request->ratio ? null : ($request->price ?? null),
        ];
        $VoucherToShop = VoucherToShop::create($dataInsert);
        return $this->successResponse("cập nhật Voucher thành công", $VoucherToShop);
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

    public function bestSellingProducts(Request $request, string $id)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        // $shopId = $request->shop_id;

        $bestSellingProducts = Product::where('shop_id', $id)
            // ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('sold_count', 'desc')
            ->take(10)  // Get top 10 best-selling products
            ->get(['id', 'name', 'show_price', 'sold_count','image'])->take(5);

        return $this->successResponse('Lấy báo cáo sản phẩm bán chạy thành công', [
            'best_selling_products' => $bestSellingProducts,
        ]);
    }

    public function TopUserBuy(Request $request, string $id)
    {
        $bestUser = OrdersModel::where('shop_id', $id)
            ->select('user_id', DB::raw('COUNT(*) as total_orders'))
            ->groupBy('user_id')
            ->orderBy('total_orders', 'desc')
            ->take(5)
            ->with('user:id,fullname')
            ->get();

        return $this->successResponse('Lấy báo cáo sản phẩm bán chạy thành công', $bestUser);
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

    public function wallet(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->select('id','shop_name', 'wallet' ,'account_number', 'bank_name' , 'owner_bank')->first();
        $history = history_get_cash_shops::where('shop_id', $shop->id)->select('id', 'cash', 'date')->paginate(10);
        if (!$shop) {
            return $this->errorResponse('Shop không tồn tại', 404);
        }
        $data = [
            'shop' => $shop,
            'history' => $history,
        ];
        return $this->successResponse('Lấy thông tin ví thành công', $data);
    }

    public function history_get_cash(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->select('id', 'wallet')->first();
        if (!$shop) {
            return $this->errorResponse('Shop không tồn tại', 404);
        }
        $startDate = Carbon::now()->subWeek()->format('Y-m-d');
        $startDateMonth = Carbon::now()->subMonth()->format('Y-m-d');
        $startDate = Carbon::now()->subWeek()->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        $totalWeek = (int) history_get_cash_shops::where('shop_id', $shop->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('cash');

        $totalMonth = (int) history_get_cash_shops::where('shop_id', $shop->id)
            ->whereBetween('date', [$startDateMonth, $endDate])
            ->sum('cash');

        $totalCash = (int) history_get_cash_shops::where('shop_id', $shop->id)
            ->sum('cash');
        $history = [
            'total_week' => $totalWeek,
            'total_month' => $totalMonth,
            'total_cash' => $totalCash,
            'not_paid_yet' => $shop->wallet ?? 0,
        ];
        return $this->successResponse('Lịch sử rút tiền', $history);
    }

    public function number_of_withdrawals(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->select('id')->first();
        if (!$shop) {
            return $this->errorResponse('Shop không tồn tại', 404);
        }
        $history = history_get_cash_shops::where('shop_id', $shop->id)->get();
        return $this->successResponse('Lịch sử các lần rút tiền', $history);
    }

    public function filterShops(Request $request)
    {
        $limit = $request->limit ?? 20;
        $query = Shop::query();
           
            if ($request->has('created_at')) {
                $query->orderby('created_at', 'desc');
            }
            if ($request->has('view_count')) {
                $query->orderby('visits', 'desc');
            }
            $shops = $query->paginate($limit);

        if ($shops->isEmpty()) {
            return response()->json([
                'message' => 'Không có sản phẩm nào'
            ], 404);
        }
    
        return response()->json($shops);
    }

    public function getShopByCategory(Request $request)
    {
        $limit = $request->limit ?? 20;
        $query = CategoriesModel::query();
        $queryPro = Product::query();
        if ($request->has('category_id')) {
            $query->where('id', $request->category_id)->where('status', 2);
        }
        $categories = $query->get();
        $result = $categories->map(function ($category) {
            $nestedCategories = CategoriesModel::where('parent_id', $category->id)
            ->get(['id', 'title', 'slug'])
            ->map(function ($nestedCategory) {
                return [
                'id' => $nestedCategory->id,
                'title' => $nestedCategory->title,
                'slug' => $nestedCategory->slug,
                ];
            });
            return [
            'id' => $category->id,
            'name' => $category->title,
            'slug' => $category->slug,
            'nest' => $nestedCategories,
            ];
        });

        if ($result->isEmpty()) {
            return response()->json([
            'message' => 'Không có danh mục nào'
            ], 404);
        }

        return response()->json($result->first());
    }


    public function shop_request_get_cash(Request $request)
    {
    //    $user = $request->user;
    //    $user = UsersModel::where('email', $user)->select('id')->first();
    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Token không đúng",
    //         ], 401);
    //     }
        $shop = Shop::where('id', $request->shop_id)->first();
        // if (!$user) {
        //     return redirect()->back()->with('error', 'Vui lòng đăng nhập');
        // }
        // if ($shop->wallet < $request->get_cash) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Số dư trong ví không đủ",
        //     ], 401);
        // }
        // $cash = $shop->wallet - $request->get_cash;
        
        
        history_get_cash_shops::create([
            'shop_id' => $shop->id ?? null,
            'user_id' =>  $user ?? null,
            'cash' => $shop->wallet ?? null,
            'date' => Carbon::now() ?? null,
            'account_number' => $shop->account_number ?? null,
            'bank_name' => $shop->bank_name ?? null,
            'owner_bank' => $shop->owner_bank ?? null,
        ]);

        $shop->update([
            'wallet' => 0,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Yêu cầu rút tiền thành công",
        ], 200);
        // return redirect()->back()->with('success', 'Yêu cầu rút tiền thành công');
    }


    public function get_categories_for_shop(Request $request, string $id)
    {
        $shop = Shop::where('id', $id)->select('id')->first();
        $products = Product::where('shop_id', $shop->id)->select('id', 'category_id')->get();
        $categories = CategoriesModel::whereIn('id', $products->pluck('category_id'))->select('id', 'title', 'slug')->get();
        return $this->successResponse('Lấy danh sách danh mục thành công', $categories);
    }

    public function restore(Request $request, string $id)
    {
        $shop = Shop::where('id', $id)->first();
        if (!$shop) {
            return $this->errorResponse('shop không tồn tại', 404);
        }
        $shop->status = 2;
        $shop->save();
        return $this->successResponse('Khôi phục shop thành công', $shop);
    }   
}
