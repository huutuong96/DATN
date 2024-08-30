<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        if($products->isEmpty()){
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại products nào",
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $products,
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
    public function store(ProductRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $rqt->file('image');
            $cloudinary = new Cloudinary();
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageUrl = $uploadedImage['secure_url'];
        } else {
            $imageUrl = null;
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
            // 'create_by' => $request->create_by,
            // 'update_by' => $request->update_by,
            // 'create_at' => $request->create_at,
            // 'update_at' => $request->update_at,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
        ];
        try {
            $products = Product::create($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "sản phẩm Đã được lưu",
                'Products' => $products,
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

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại product nào",
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $product,
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

        if ($rqt->hasFile('image')) {
            $image = $rqt->file('image');
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
            // 'create_by' => $request->create_by,
            // 'update_by' => $request->update_by,
            // 'create_at' => $request->create_at,
            // 'update_at' => $request->update_at,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'color_id' => $request->color_id,
        ];
        try {
            $products = Product::update($dataInsert);
            $dataDone = [
                'status' => true,
                'message' => "sản phẩm Đã được cập nhật",
                'Products' => $products,
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

            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'Xóa product thành công',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Xóa product không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
}
