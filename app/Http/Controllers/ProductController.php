<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use Cloudinary\Cloudinary;
use App\Models\ColorsModel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        foreach ($products as $key => $product) {
            $images = Image::find($product->id);
            $product["images"] = $images;
        }
        // $products = Cache::remember('all_products', 60 * 60, function () {
        //     return Product::all();
        // });
        // $images = Cache::remember('all_images', 60 * 60, function () {
        //     return Images::all();
        // });

        if($products->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại sản phẩm nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $products,
                'images' => $images,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $cloudinary = new Cloudinary();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = null;
        }


        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $imageUrl,
            'quantity' => $request->quantity,
            'parent_id' => $request->parent_id,
            'create_by' => $user->id,
            'create_by' => $request->shop_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
        ];

        try {
            $product = Product::create($dataInsert);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageUrl = $uploadedImage['secure_url'];

                    // Lưu URL vào bảng image
                    Image::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'status' => 1,
                    ]);
                }
            }

            $dataDone = [
                'status' => true,
                'message' => "Sản phẩm đã được lưu",
                'product' => $product,
            ];

            return response()->json($dataDone, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Thêm product không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Cache::remember('product_' . $id, 60 * 60, function () use ($id) {
            return Product::find($id);
        });
        $images = Cache::remember('images_' . $product->id, 60 * 60, function () use ($product) {
            return Image::where('product_id', $product->id)->get();
        });

        $images = Image::find($product->id);
        $product["images"] = $images;
        
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại sản phẩm nào",
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $product,
            'images' => $images,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => "Sản phẩm không tồn tại",
            ], 404);
        }


        $user = JWTAuth::parseToken()->authenticate();
        $cloudinary = new Cloudinary();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = $product->image;
        }

        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $imageUrl,
            'quantity' => $request->quantity,
            'parent_id' => $request->parent_id,
            'create_by' => $user->id,
            'create_by' => $request->shop_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
        ];

        try {
            $product ->update($dataInsert);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                Image::where("product_id", $product->id)->delete();
                foreach ($images as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageUrl = $uploadedImage['secure_url'];

                    // Lưu URL vào bảng image
                    Image::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'status' => 1,
                    ]);
                }
            }

            $dataDone = [
                'status' => true,
                'message' => "sản phẩm Đã được cập nhật",
                'Products' => $product,
            ];
            return response()->json($dataDone, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "cập nhật không thành công",
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
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'product không tồn tại',
                ], 404);
            }

            Image::where("product_id", $product->id)->delete();
            // $product->delete();

            // $product->update(['status' => 101]);
            // Cache::forget('all_products');
            // Cache::forget('product_' . $id);

            return response()->json([
                'status' => true,
                'message' => 'Xóa sản phẩm thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa sản phẩm không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
