<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $images = Image::all();

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
            $mainImageUrl = $uploadedImage['secure_url']; // Ảnh chính
        } else {
            $mainImageUrl = null;
        }
        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $mainImageUrl, // Lưu ảnh chính vào bảng product
            'quantity' => $request->quantity,
            'parent_id' => $request->parent_id,
            'create_by' => $user->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'shop_id' => $request->shop_id,
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
        $product = Product::find($id);
        $images = Image::where('product_id', $product->id)->get();

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
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        // dd($product);
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
            $mainImageUrl = $uploadedImage['secure_url']; // Ảnh chính
        }  else {
            $mainImageUrl = $product->image;
        }
      
        $dataupdate = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $mainImageUrl, // Lưu ảnh chính vào bảng product
            'quantity' => $request->quantity,
            'parent_id' => $request->parent_id,
            'create_by' => $user->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'shop_id' => $request->shop_id,
        ];
          dd($dataupdate);

        try {
            $product ->update($dataupdate);

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
