<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cloudinary\Cloudinary;
use App\Models\ColorsModel;
use App\Models\variantattribute;
use App\Models\attributevalue;
use App\Models\product_variants;
use App\Models\attributes;
use App\Models\Attribute;
use App\Models\categoryattribute;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::where('status', 1)
        ->with(['images', 'colors'])  // Eager load images
        ->paginate(20);
        if ($products->isEmpty()) {
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
            ]
        );
    }

    public function store(Request $request)
    {

        // HÀM NÀY LÀ HÀM QUY ĐỊNH ATTRIBUTE THEO CATEGORY //
            // TRONG HÀM NÀY CHỨA CÁC THUỘC TÍNH DO SÀN QUY ĐỊNH
            // KHÔNG NHẤT THIẾT PHẢI CÓ CHO SÀN THƯƠNG MẠI ĐIỆN TỬ
                // $categoryattribute = categoryattribute::where('category_id', $request->category_id)->get();
                // $attributes_id = [];
                //     foreach ($categoryattribute as $attribute) {
                //         $attributes_id[] = $attribute->attribute_id;
                //     }
                // // dd($attributes_id);
        // HÀM NÀY LÀ HÀM QUY ĐỊNH ATTRIBUTE THEO CATEGORY //

        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();

            DB::beginTransaction();

            $mainImageUrl = null;
            if ($request->hasFile('image')) {
                $uploadedImage = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
                $mainImageUrl = $uploadedImage['secure_url'];
            }



            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku ?? $this->generateSKU(), // Thêm phương thức để tạo SKU
                'slug' => $request->slug ?? Str::slug($request->name),
                'description' => $request->description,
                'infomation' => $request->infomation,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'image' => $mainImageUrl,
                'quantity' => $request->quantity,
                'create_by' => $user->id,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'shop_id' => $request->shop_id,
            ]);

            if ($request->hasFile('images')) {
                $imageData = [];
                foreach ($request->file('images') as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageData[] = [
                        'product_id' => $product->id,
                        'url' => $uploadedImage['secure_url'],
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                Image::insert($imageData);
            }

            // Kiểm tra xem có thuộc tính nào được chọn không

            if (isset($request['attributes']) && is_array($request['attributes']) && !empty($request['attributes'])) {
                // Generate all possible variants
                $variants = $this->generateVariants($request['attributes']);
                // Create variants
                foreach ($variants as $variant) {
                    $variantData = [
                        'product_id' => $product->id,
                        'sku' => $product->sku . '-' . implode('-', array_column($variant, 'value')),
                        'price' => $request->base_price,
                        'stock' => 0, // Default stock, you might want to adjust this
                        'attributes' => $variant
                    ];

                    $this->storeProductVariant($variantData, $product);

                }

            }

            // Xử lý các biến thể và hình ảnh của chúng
            if (isset($request['variants']) && is_array($request['variants'])) {
                foreach ($request['variants'] as $variantData) {
                    $this->storeProductVariant($variantData, $product);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Sản phẩm đã được lưu",
                'product' => $product->load('images', 'variants'),
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "Thêm product không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function storeProductVariant($variantData, $product)
    {
        $cloudinary = new Cloudinary();

        $variant = $product->variants()->create([
            'sku' => $variantData['sku'],
            'stock' => $variantData['stock'],
            'price' => $variantData['price'] ?? $product->price,
        ]);

        foreach ($variantData['attributes'] as $attributeId => $valueData) {
            $attribute = Attribute::findOrFail($attributeId);
            $value = AttributeValue::firstOrCreate([
                'attribute_id' => $attribute->id,
                'value' => $valueData['value'],
            ]);
            $variant->attributes()->attach($attribute->id, ['value_id' => $value->id]);
        }
        if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
            $uploadedImage = $cloudinary->uploadApi()->upload($variantData['image']->getRealPath());
            Image::create([
                'product_variant_id' => $variant->id,
                'url' => $uploadedImage['secure_url'],
                'status' => 1,
            ]);
        }
        return $variant;
    }

    private function generateSKU()
    {
        // Implement your SKU generation logic here
        return 'SKU-' . uniqid();
    }

    public function generateVariants($attributes)
    {
        // dd($attributes);
        if (empty($attributes)) {
            return [[]]; // Trả về một mảng chứa một mảng rỗng nếu không có thuộc tính
        }
        // dd($attributes);
        $result = [[]];
        foreach ($attributes as $attribute) {
            if (!isset($attribute['values']) || !is_array($attribute['values'])) {
                continue; // Bỏ qua thuộc tính này nếu không có giá trị hợp lệ
            }
            $append = [];
            foreach ($result as $product) {
                foreach ($attribute['values'] as $item) {
                    dd($attribute['id']);
                    $newProduct = $product;
                    $newProduct[$attribute['id']] = $item;
                    $append[] = $newProduct;
                }
            }
            $result = $append;
        }
        return $result;
    }

    public function getVariant($id)
    {
        $variant = product_variants::where('product_id', $id)->get();
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $variant,
        ]);
    }

    private function storeImageVariant($images, $variant)
    {

        $imageURL = [];
        $cloudinary = new Cloudinary();
        foreach ($images as $image) {
            $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            $imageURL[] = [
                'url' => $uploadedImage['secure_url'],
            ];
        }
        return $imageURL;
    }

    public function updateVariant(Request $request, $id)
    {
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại biến thể nào",
            ], 404);
        }
        if ($request->images) {
            $imageData = $this->storeImageVariant($request->images, $variant);
        }
        $variant->update([
            'stock' => $request->stock ?? $variant->stock,
            'price' => $request->price ?? $variant->price,
            'images' => isset($imageData) ? json_encode($imageData) : $variant->images,
        ]);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật biến thể thành công",
            'data' => $variant,
        ], 200);
    }

    public function removeVariant($id)
    {
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại biến thể nào",
            ], 404);
        }
        $variant->delete();
        return response()->json([
            'status' => true,
            'message' => "Xóa biến thể thành công",
        ], 200);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['images', 'variants'])->find($id);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại sản phẩm nào",
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $product,
        ]);
    }

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
            $mainImageUrl = $uploadedImage['secure_url']; // Ảnh chính
        } else {
            $mainImageUrl = $product->image;
        }

        $dataInsert = [
            'name' => $request->name ?? $product->name,
            'slug' => $request->filled('slug') ? $request->slug : Str::slug($request->name ?? $product->name),
            'description' => $request->description ?? $product->description,
            'infomation' => $request->infomation ?? $product->infomation,
            'price' => $request->price ?? $product->price,
            'sale_price' => $request->sale_price ?? $product->sale_price,
            'image' => $mainImageUrl,
            'quantity' => $request->quantity ?? $product->quantity,
            'parent_id' => $request->parent_id ?? $product->parent_id,
            'update_by' => $user->id,
            'category_id' => $request->category_id ?? $product->category_id,
            'brand_id' => $request->brand_id ?? $product->brand_id,
            'shop_id' => $request->shop_id ?? $product->shop_id,
        ];

        try {
            $product->update($dataInsert);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageUrl = $uploadedImage['secure_url'];

                    Image::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'status' => 1,
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => "Sản phẩm đã được cập nhật",
                'product' => $product,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật không thành công",
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