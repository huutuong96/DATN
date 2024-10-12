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
use App\Models\Attribute;
use App\Models\categoryattribute;
use Carbon\Carbon;
use App\Services\ImageUploadService;
use App\Jobs\UploadImageJob;
use App\Jobs\UploadImagesJob;
use App\Jobs\UpdateStockAllVariant;
use App\Jobs\UpdatePriceAllVariant;
use App\Jobs\UpdateImageAllVariant;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = Product::where('status', 1)
        // ->with(['images', 'colors'])  // Eager load images
        // ->paginate(20);
        // if ($products->isEmpty()) {
        //     return response()->json(
        //         [
        //             'status' => true,
        //             'message' => "Không tồn tại sản phẩm nào",
        //         ]
        //     );
        // }
        // return response()->json(
        //     [
        //         'status' => true,
        //         'message' => "Lấy dữ liệu thành công",
        //         'data' => $products,
        //     ]
        // );
        $products = Product::get();
        // dd($products);
        return $products;
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
                // dd($categoryattribute);
        // HÀM NÀY LÀ HÀM QUY ĐỊNH ATTRIBUTE THEO CATEGORY //

        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();
            DB::beginTransaction();

            $mainImageUrl = null;
            $product = Product::create([
                'name' => $request->name,
                'sku' => $request->sku ?? $this->generateSKU(),
                'slug' => $request->slug ?? Str::slug($request->name),
                'description' => $request->description,
                'infomation' => $request->infomation,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'image' => $mainImageUrl ?? null,
                'quantity' => $request->quantity,
                'create_by' => $user->id,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'shop_id' => $request->shop_id,
                'height' => $request->height,
                'length' => $request->length,
                'weight' => $request->weight,
                'width' => $request->width,
            ]);

            Image::where("product_id", null)->update(
                ['product_id' => $product->id]
            );

            $attributes = json_decode($request['attribute'], true);

            foreach ($attributes as $key => $attribute) {
                if (isset($attributes) && is_array($attributes) && !empty($attributes)) {
                    $attributeData = $attribute;
                    $attributeValue = $this->storeProductAttribute($attributeData, $product);
                }
            }

            $variants = json_decode($request['variants'], true);

            foreach ($variants as $variant) {
                if (isset($variants) && is_array($variants) && !empty($variants)) {
                    $variantData = $variant;
                    $this->storeProductVariant($variantData, $product, $attributeValue);
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

    private function storeProductAttribute($attributeData, $product)
    {
        $attribute = Attribute::create([
            'product_id' => $product->id,
            'name' => $attributeData['name'],
            'display_name' => strtoupper($attributeData['name']),
            'type' => $attributeData['type'],
        ]);
        $attributeValue = attributevalue::create([
            'attribute_id' => $attribute->id,
            'value' => $attributeData['value'],
        ]);
        return $attributeValue;
    }

    private function storeProductVariant($variantData, $product, $attributeValue)
    {
        $cloudinary = new Cloudinary();

        $variant = $product->variants()->create([
            'product_id' => $product->id,
            'sku' => $variantData['sku'] ?? $this->generateSKU() . '-' . $attributeValue->value,
            'stock' => $variantData['stock'] ?? $product->quantity,
            'price' => $variantData['price'] ?? $product->price,
            'images' => $variantData['images'] ?? $product->image,
        ]);

        $variantAttribute = variantattribute::create([
            'variant_id' => $variant->id,
            'product_id' => $product->id,
            'shop_id' => $product->shop_id,
            'attribute_id' => $attributeValue->attribute_id,
            'value_id' => $attributeValue->id,
        ]);

        // foreach ($variantData['attributes'] as $attributeId => $valueData) {
        //     $attribute = Attribute::findOrFail($attributeId);
        //     $value = AttributeValue::firstOrCreate([
        //         'attribute_id' => $attribute->id,
        //         'value' => $valueData['value'],
        //     ]);
        //     $variant->attributes()->attach($attribute->id, ['value_id' => $value->id, 'shop_id' => $product->shop_id, 'product_id' => $product->id]);
        // }
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
                    // dd($attribute['id']);
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

        // Giải mã trường images cho mỗi biến thể
        foreach ($variant as $v) {
            $v->images = json_decode($v->images); // Giả sử $v->images chứa chuỗi JSON
        }

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

    public function updateStockOneVariant(Request $request, $id)
    {
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại biến thể nào",
            ], 404);
        }
        $variant->update([
            'stock' => $request->stock ?? $variant->stock,
        ]);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật biến thể thành công",
            'data' => $variant,
        ], 200);
    }

    public function updateStockAllVariant(Request $request)
    {
        // $variantArray = [462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482];

        // $variants = product_variants::whereIn('id', $request->variant_ids)
        //     ->update(['stock' => $request->stock]);

        updateStockAllVariant::dispatch($request->variant_ids, $request->stock);

        return response()->json([
            'status' => true,
            'message' => "Cập nhật biến thể thành công",
        ], 200);
    }

    public function updatePriceOneVariant(Request $request, $id)
    {
        $variant = product_variants::find($id);
        if (!$variant) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại biến thể nào",
            ], 404);
        }
        $variant->update([
            'price' => $request->price ?? $variant->price,
        ]);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật biến thể thành công",
            'data' => $variant,
        ], 200);
    }

    public function updatePriceAllVariant(Request $request)
    {
        // $variantArray = [462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482];
        // $variants = product_variants::whereIn('id', $request->variant_ids)
        //     ->update(['price' => $request->price]);
        updatePriceAllVariant::dispatch($request->variant_ids, $request->price);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật biến thể thành công",
        ], 200);
    }

    public function updateImageOneVariant(Request $request, $id)
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
            'images' => isset($imageData) ? json_encode($imageData) : $variant->images,
        ]);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật ảnh biến thể thành công",
            'data' => $variant,
        ], 200);
    }

    public function updateImageAllVariant(Request $request)
    {
        $request->variant_ids = [697,698,699];
        UpdateImageAllVariant::dispatch($request->images, $request->variant_ids);
        return response()->json([
            'status' => true,
            'message' => "Cập nhật ảnh biến thể thành công",
            // 'data' => $updatedVariants,
        ], 200);
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

    public function update(Request $request, string $id)
    // ProductRequest
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
            'height' => $request->height ?? $product->height,
            'length' => $request->length ?? $product->length,
            'weight' => $request->weight ?? $product->weight,
            'width' => $request->width ?? $product->width,
        ];
        try {
            $product->update($dataInsert);
            // Image::where("product_id", $product->id)->delete();
            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $image) {
            //         $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
            //         $imageUrl = $uploadedImage['secure_url'];
            //         Image::create([
            //             'product_id' => $product->id,
            //             'url' => $imageUrl,
            //             'status' => 1,
            //         ]);
            //     }
            //     $imageUploadService = new ImageUploadService($cloudinary);
            //     $imageUploadService->uploadImages($request->file('images'), $product->id);
            // }

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

    public function upload(Request $request){
        $imageUrls = [];
        $cloudinary = new Cloudinary();
        foreach ($request->file('images') as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageUrl = $uploadedImage['secure_url'];
                    Image::create([
                        'product_id' => null,
                        'url' => $imageUrl,
                        'status' => 1,
                    ]);
                    $imageUrls[] = $imageUrl;
                }
        return response()->json([
            'status' => true,
            'message' => "Upload ảnh thành công",
            'product' => $imageUrls,
        ], 200);
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

    public function search(Request $request)
    {
        $products = Product::search($request->all())
            ->with(['images', 'category', 'brand', 'shop', 'variants.attributes.values']) // Eager load related data
            ->paginate(15); // Paginate results, 15 items per page

        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại sản phẩm nào",
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $products,
        ]);
    }

    public function approve_product(Request $request, $id){
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'status' => false,
                'message' => "Không tồn tại sản phẩm nào",
            ], 404);
        }
        $product->status = 1;
        $product->save();
        return response()->json([
            'status' => true,
            'message' => "Duyệt sản phẩm thành công",
        ], 200);
    }
}
