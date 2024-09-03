<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;
use Cloudinary\Cloudinary;
use App\Models\Image;
use App\Models\ColorsModel;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Cache::remember('all_products', 60 * 60, function () {
            return Product::all();
        });
        $images = Cache::remember('all_images', 60 * 60, function () {
            return Images::all();
        });

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
        // dd($request->file('image'));

        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => null,
            'quantity' => $request->quantity,
            'parent_id' => $request->parent_id,
            'create_by' => auth()->user()->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'shop_id' => $request->shop_id,
        ];

        $product = Product::create($dataInsert);
        if ($request->hasFile('image')) {
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
            $dataImage = [
                'product_id' => $product->id,
                'url' => $imageUrl,
                'status' => 1,
            ];
            Image::create($dataImage);
        }
        return response()->json([
            'status' => true,
            'message' => "Thêm sản phẩm thành công",
            'data' => $product,
            'images' => $dataImage,
        ]);
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = $product->URL;
        }

        $dataInsert = [
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'infomation' => $request->infomation,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $imageUrl,
            'quantity' => $request->quantity,
            'sold_count' => $request->sold_count,
            'view_count' => $request->view_count,
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
        ];
        try {
            $product->update($dataInsert);
            Cache::forget('all_products');
            Cache::forget('product_' . $id);
            if ($request->hasFile('image')) {
                Cache::forget('images_' . $product->id);
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

            $product->update(['status' => 101]);
            Cache::forget('all_products');
            Cache::forget('product_' . $id);

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
