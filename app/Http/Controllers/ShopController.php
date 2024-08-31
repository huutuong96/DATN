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
use Illuminate\Support\Str;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Shops = Shop::all();
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

            // NẾU TẠO SHOP THÀNH CÔNG THÌ TẠO KHÓA HỌC CHO SHOP

                //code tạo khóa học cho shop

            // NẾU TẠO SHOP THÀNH CÔNG THÌ TẠO KHÓA HỌC CHO SHOP

            return response()->json([
                'status' => true,
                'message' => "Thêm Shop thành công",
                'data' => $Shop,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm Shop không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function category_shop_store(Request $rqt,string $id, string $category_main_id)
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
                'message' => "Category không tồn tại",
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

    public function product_to_shop_store(Request $rqt, string $id)
    {
        $shop = Shop::find($id);
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
        $colorInsert = [
            'product_id' => $product->id,
            'title' => $rqt->title,
            'index' => $rqt->index,
            'status' => $rqt->status,
            'create_by' => auth()->user()->id,
            'update_by' => auth()->user()->id,
        ];
        $color = ColorModel::create($colorInsert);
        if(!$color){
            return response()->json([
                'status' => false,
                'message' => "Color không tồn tại",
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => "Thêm Product thành công",
            'data' => $product,
        ], 201);
    }


    public function show_shop_members(string $id)
    {
        $members = Shop_manager::where('shop_id', $id)->with('user')->get();

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
        $Shops = Shop::find($id);

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
                    'message' => "Không tồn tại thành viên trong hóp này",
                ]
            );
        }
        $member->update([
            'role' => $rqt->role,
        ]);
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
        $shop = Shop::find($id);

        if (!$shop) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "shop không tồn tại",
                ],
                404
            );
        }
        $image = $rqt->file('image');
        $cloudinary = new Cloudinary();
        // $uploudinary = $cloudinary->uploadApi()->upload($image->getRealPath());

        // $user = JWTAuth::parseToken()->authenticate();
        // lấy địa chỉ của usẻr để thêm vào dòn 59 ?? $user->address_id



        $dataInsert = [
            'shop_name' => $rqt->shop_name,
            'pick_up_address' => $rqt->pick_up_address ,
            'slug' => $rqt->slug ?? Str::slug($rqt->shop_name, '-'),
            'image' => $uploadedImage['secure_url'] ?? "",// thêm ?? để tranh lỗi khi test băng post man
            'cccd' => $rqt->cccd,
            'status' => $rqt->status ,
            'tax_id' => $rqt->tax_id,
            'created_at' => $rqt->created_at ?? $shop->created_at, // Đặt giá trị mặc định nếu không có trong yêu cầu
        ];
        try {
            $shop->update($dataInsert);

            return response()->json(
                [
                    'status' => true,
                    'message' => "cập nhật thông tin Shop thành công",
                    'data' => $shop,
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "cập nhật thông tin Shop không thành công",
                    'error' => $th->getMessage(),
                ]
            );
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

            // Xóa bản ghi
            $shop->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa shop thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa shop không thành công",
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
                    'message' => 'thành viên không tồn tại',
                ], 404);
            }

            // Xóa bản ghi
            $member->delete();

             return response()->json([
                    'status' => true,
                    'message' => 'Xóa thành viên thành công',
                ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => "xóa thành viên không thành công",
                    'error' => $th->getMessage(),
                ]
            );
        }
    }
}
