<?php

namespace App\Http\Controllers;
use App\Models\CategoriesModel;
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
use App\Models\Shop;
use App\Models\Tax;
use App\Models\tax_category;
use App\Models\update_product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = 2;
        if($request->status){
            $status = $request->status;
        }
        $products = Product::where('status', $status)
        ->with(['images', 'colors'])  // Eager load images
        ->paginate(20);
        $products->appends(['status' => $status]); // Append status to pagination links
        // if($request->status == 2){
        //     $products = Product::
        //     with(['images', 'colors'])  // Eager load images
        //     ->paginate(20);
        //     $products->appends(['status' => 2]); // Append status to pagination links
        // }
        if ($products->isEmpty()) {
            return response()->json(
                [
                    'status' => true,
                    'message' => "Không tồn tại sản phẩm nào",
                ]
            );
        }
        // return $products;
        foreach ($products as $product) {
            $product->quantity = intval($product->quantity);
            $product->sold_count = intval($product->sold_count);
            $product->view_count = intval($product->view_count);
            $product->parent_id = intval($product->parent_id);
            $product->create_by = intval($product->create_by);
            $product->update_by = intval($product->update_by);
            $product->category_id = intval($product->category_id);
            $product->brand_id = intval($product->brand_id);
            $product->shop_id = intval($product->shop_id);
            $product->status = intval($product->status);
            $product->height = intval($product->height);
            $product->length = intval($product->length);
            $product->weight = intval($product->weight);
            $product->width = intval($product->width);
            $product->update_version = intval($product->update_version);
            $product->price = intval($product->price);
            $product->sale_price = intval($product->sale_price);
        }

        return response()->json(
            [
                'status' => true,
                'message' => "Lấy dữ liệu thành công",
                'data' => $products,
            ]
        );
        return $products;
    }

    public function getProductToSlug($slug) {
        if (empty($slug)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại'
            ], 400);
        }
        $products = Product::where('slug', $slug)->where('status', 2)->with('images')->get();
        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'không tìm thấy sản phẩm nào'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $products->first()
        ], 200);
    }

    public function store(Request $request)
    {
        // $shopId = $request->shop_id;
        // $shop = Shop::find($shopId);
        // if ($shop->vnp_TmnCode == null) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'CỬA HÀNG CHƯA KHAI BÁO MÃ TÀI KHOẢN NGÂN HÀNG CỦA VNPAY',
        //     ], 400);
        // }
        $tax_category = tax_category::where('category_id', $request->category_id)->first();
        $taxes = Tax::find($tax_category->tax_id);
        $taxAmount = $request->price * $taxes->rate;
        // dd($request->price);
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();
            DB::beginTransaction();
            $mainImageUrl = null;
            $checkSlug = Product::where("slug", $request->slug ?? Str::slug($request->name))->first();
            if($checkSlug){
                $slug = $checkSlug->slug;
                $slug .= '-' . rand(1000, 9999);
            }else{
                $slug = $request->slug ?? Str::slug($request->name);
            }
            $dataInsert = [
                'name' => $request->name,
                'sku' => $request->sku ?? $this->generateSKU(),
                'slug' => $slug,
                'description' => $request->description,
                'infomation' => json_encode($request->infomation),
                'price' => $request->price + $taxAmount,
                'sale_price' => $request->sale_price ?? null,
                'image' => $request->images[0] ?? null,
                'quantity' => $request->stock ?? 0,
                'create_by' => $user->id,
                'category_id' => $request->category_id,
                'brand' => $request->brand ?? null,
                'shop_id' => $request->shop_id,
                'height' => $request->height,
                'length' => $request->length,
                'weight' => $request->weight,
                'width' => $request->width,
                'show_price' => $request->price ?? $request->sale_price,
                'status' => 3,
                'json_variants' => json_encode($request->variant) ?? null,
            ];
            $product = Product::create($dataInsert);
            foreach ($request->images as $image) {
                $imageModel = Image::create([
                    'product_id' => $product->id,
                    'url' => $image ?? null,
                    'status' => 1,
                ]);
            }
            // dd($request->variant['variantItems']);
            if($request->variant != null){
                foreach ($request->variant['variantItems'] as $attribute) {

                    $attributeData = [
                        'name' => $attribute['name'],
                        'display_name' => strtoupper($attribute['name']),
                    ];
                    $attributeId = Attribute::create($attributeData);
                    foreach ($attribute['values'] as $value) {
                        $attributeValueData = [
                            'attribute_id' => $attributeId->id,
                            'value' => $value['value'],
                        ];
                        $attributeValue = attributevalue::create($attributeValueData);
                    }
                }
                // $attributeValue = attributevalue::where()
                foreach ($request->variant['variantProducts'] as $variant) {
                    $product_variantsData = [
                        'product_id' => $product->id,
                        'id_fe' => $variant['id'] ?? null,
                        'sku' => $variant['sku'] ?? $this->generateSKU(),
                        'stock' => $variant['stock'] ?? $request->stock,
                        'price' => $variant['price'] * ($taxes->rate + 1) ?? $product->price,
                        'images' => $variant['image'] ?? $product->image,
                    ];
                    $product_variants = product_variants::create($product_variantsData);
                    $values = [];
                    foreach ($variant['variants'] as $item) {
                        $values[] = $item['value'];
                    }
                    $concatenated_values = implode(', ', $values);
                    $product_variants->update([
                        'name' => $concatenated_values,
                    ]);
                    $variantAttributeData = [
                        'variant_id' => $product_variants->id,
                        'product_id' => $product->id,
                        'shop_id' => $product->shop_id,
                        'attribute_id' => $attributeValue->attribute_id,
                        'value_id' => $attributeValue->id,
                    ];
                    $variantattribute = variantattribute::create($variantAttributeData);
                }

                $product_variants_get_price = product_variants::where('product_id', $product->id)->get();
                $highest_price = $product_variants_get_price->max('price');
                $lowest_price = $product_variants_get_price->min('price');
                if($highest_price == $lowest_price){
                    $product->update([
                        'show_price' => $highest_price,
                    ]);
                }
                if($highest_price != $lowest_price){
                    $product->update([
                        'show_price' => $lowest_price . " - " . $highest_price,
                    ]);
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

    // public function store(Request $request)
    // {
    //     // dd($request->images);
    //     // dd($request->images[0]);
    //     try {
    //         $user = JWTAuth::parseToken()->authenticate();
    //         $cloudinary = new Cloudinary();
    //         DB::beginTransaction();
    //         $mainImageUrl = null;
    //         $dataInsert = [
    //             'name' => $request->name,
    //             'sku' => $request->sku ?? $this->generateSKU(),
    //             'slug' => $request->slug ?? Str::slug($request->name),
    //             'description' => $request->description,
    //             'infomation' => json_encode($request->infomation),
    //             'price' => $request->price,
    //             'sale_price' => $request->sale_price ?? null,
    //             'image' => $request->images[0] ?? null,
    //             'quantity' => $request->stock ?? 0,
    //             'create_by' => $user->id,
    //             'category_id' => $request->category_id,
    //             'brand' => $request->brand ?? null,
    //             'shop_id' => $request->shop_id,
    //             'height' => $request->height,
    //             'length' => $request->length,
    //             'weight' => $request->weight,
    //             'width' => $request->width,
    //             'show_price' => $request->price ?? $request->sale_price,
    //             'status' => 3,
    //         ];
    //         $product = Product::create($dataInsert);
    //         foreach ($request->images as $image) {
    //             $imageModel = Image::create([
    //                 'product_id' => $product->id,
    //                 'url' => $image ?? null,
    //                 'status' => 1,
    //             ]);
    //         }
    //         if($request->variant != null){
    //             foreach ($request->variant['variantItems'] as $attribute) {

    //                 $attributeData = [
    //                     'name' => $attribute['name'],
    //                     'display_name' => strtoupper($attribute['name']),
    //                 ];
    //                 $attributeId = Attribute::create($attributeData);
                    
    //                 foreach ($attribute['values'] as $value) {
    //                     $attributeValueData = [
    //                         'attribute_id' => $attributeId->id,
    //                         'value' => $value['value'],
    //                     ];
    //                     $attributeValue = attributevalue::create($attributeValueData);
    //                     foreach ($request->variant['variantProducts'] as $variant) {
    //                         $product_variantsData = [
    //                             'product_id' => $product->id,
    //                             'sku' => $variant['sku'] ?? $this->generateSKU(),
    //                             'stock' => $variant['stock'] ?? $request->stock,
    //                             'price' => $variant['price'] ?? $product->price,
    //                             'images' => $variant['image'] ?? $product->image,
    //                         ];
    //                         $product_variants = product_variants::create($product_variantsData);
    //                         $values = [];
    //                         foreach ($variant['variants'] as $item) {
    //                             $values[] = $item['value'];
    //                         }
    //                         $concatenated_values = implode(', ', $values);
    //                         $product_variants->update([
    //                             'name' => $concatenated_values,
    //                         ]);
    //                         $variantAttributeData = [
    //                             'variant_id' => $product_variants->id,
    //                             'product_id' => $product->id,
    //                             'shop_id' => $product->shop_id,
    //                             'attribute_id' => $attributeId->id,
    //                             'value_id' => $attributeValue->id,
    //                         ];
    //                         $variantattribute = variantattribute::create($variantAttributeData);
    //                     }
                        
    //                 }
    //             }
    //             $product_variants_get_price = product_variants::where('product_id', $product->id)->get();
    //             $highest_price = $product_variants_get_price->max('price');
    //             $lowest_price = $product_variants_get_price->min('price');
    //             if($highest_price == $lowest_price){
    //                 $product->update([
    //                     'show_price' => $highest_price,
    //                 ]);
    //             }
    //             if($highest_price != $lowest_price){
    //                 $product->update([
    //                     'show_price' => $lowest_price . " - " . $highest_price,
    //                 ]);
    //             }
    //         }
    //         DB::commit();
    //         return response()->json([
    //             'status' => true,
    //             'message' => "Sản phẩm đã được lưu",
    //             'product' => $product->load('images', 'variants'),
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Thêm product không thành công",
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

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
        $product->view_count += 1;
        $product->save();

            $product->quantity = intval($product->quantity);
            $product->sold_count = intval($product->sold_count);
            $product->view_count = intval($product->view_count);
            $product->parent_id = intval($product->parent_id);
            $product->create_by = intval($product->create_by);
            $product->update_by = intval($product->update_by);
            $product->category_id = intval($product->category_id);
            $product->brand_id = intval($product->brand_id);
            $product->shop_id = intval($product->shop_id);
            $product->status = intval($product->status);
            $product->height = intval($product->height);
            $product->length = intval($product->length);
            $product->weight = intval($product->weight);
            $product->width = intval($product->width);
            $product->update_version = intval($product->update_version);
            $product->price = intval($product->price);
            $product->sale_price = intval($product->sale_price);

        foreach ($product->variants as $variant) {
            $variant->product_id = intval($variant->product_id);
            $variant->stock = intval($variant->stock);
            $variant->price = intval($variant->price);
            $variant->is_deleted = intval($variant->is_deleted);
            $variant->deleted_by = intval($variant->deleted_by);
        }

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
            'created_at' => $product->created_at,
            'updated_at' => now(),
            'update_version' => $product->update_version + 1,
            'change_of' => json_encode($request->change_of ?? [])
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
        // dd($request->file('images'));
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
            'images' => $imageUrls,
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
    public function filterProducts(Request $request)
    {
        $query = Product::query();
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $products = $query->paginate(100
    );

        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'Không có sản phẩm nào'
            ], 404);
        }
    
        return response()->json($products);
    }
    

    public function approve_product(Request $request, $id){
        $product = Product::find($id);
        if(!$product){
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm');
        }
        $product->status = 2;
        $product->save();
        return redirect()->back()->with('success', 'Duyệt sản phẩm thành công');
    }
    public function updateProduct(Request $request, string $id)
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
            $mainImageUrl = $uploadedImage['secure_url']; 
        } else {
            $mainImageUrl = $product->image;
        }

        $dataInsert = [
            'product_id' => $product->id,
            'name' => $request->name ?? $product->name,
            'sku' => $product->sku,
            'slug' => $request->filled('slug') ?? $request->slug,
            'description' => $request->description ?? $product->description,
            'infomation' => $request->infomation ?? $product->infomation ,
            'price' => $request->variantMode ? 0 : $request->price, // nếu có biến thể thì nó = 0
            'sale_price' => $request->sale_price ?? $product->sale_price,
            'image' => $mainImageUrl,
            'quantity' => $request->quantity ?? $product->quantity,
            'parent_id' => $request->parent_id ?? $product->parent_id,
            'update_by' => $user->id,
            'category_id' => $request->category_id ?? $product->category_id,
            'shop_id' => $request->shop_id ?? $product->shop_id,
            'height' => $request->height ?? $product->height,
            'length' => $request->length ?? $product->length,
            'weight' => $request->weight ?? $product->weight,
            'width' => $request->width ?? $product->width,
            'created_at' => $product->created_at,
            'show_price' => $request->show_price ?? $product->show_price,
            'brand' => $request->brand ?? $product->brand,
            'json_variants' => $product->json_variants ,
            'admin_note' => $request->admin_note ?? $product->admin_note,
            'is_delete' => $request->is_delete ?? $product->is_delete,
            'updated_at' => now(),
            
            'update_version' => $product->update_version + 1,
            'change_of' => $request->variantMode === true ? json_encode($request->variant) : json_encode(0) ,
        ];
        
        try {
            DB::table('update_product')->insert($dataInsert);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $uploadedImage = $cloudinary->uploadApi()->upload($image->getRealPath());
                    $imageUrl = $uploadedImage['secure_url'];

                    Image::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'status' => 0,
                    ]);
                }
            }
            return response()->json([
                'status' => true,
                'message' => "Yêu cầu của bạn đã được gửi vui lòng chờ xét duyệt"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Yêu cầu Cập nhật không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    
    // public function handleUpdateProduct(Request $request, string $id)
    // // ProductRequest
    // {
    //     $tab = $request->tab;
    //     try {
    //         if($request-> action == 1){
    //             $newDT = DB::table("update_product")->orderBy("updated_at", "desc")->where("product_id", $id)->first();
    //             $ollDT = DB::table("products")->where("id", $id)->first();
    //             $ollData = (array) $ollDT;
    //             $newData = (array) $newDT;
    //             DB::table('products_old')->insert($ollData);
    //             $change_of = $data = json_decode($newData["change_of"], true);
    //             unset($newData["change_of"]);
    //             $newData["created_at"] = $newData["updated_at"];
    //             $newData["id"] = $newData["product_id"];
    //             unset($newData["product_id"]);
    //             DB::table('products')->where('id', $id)->update($newData);
    //             DB::table('update_product')->where('product_id', $newDT->product_id)->delete();
               
    //             if(json_decode($newDT->change_of)  != 0){
    //                 foreach(json_decode($newDT->change_of) as $data){
    //                     // dd($variant);
    //                     $variant = product_variants::find($data->id);
    //                     if (!$variant) {
    //                         return response()->json([
    //                             'status' => false,
    //                             'message' => "Không tồn tại biến thể nào",
    //                         ], 404);
    //                     }

    //                     // $imageData = $this->storeImageVariant($request->images, $variant);

    //                     $variant->update([
    //                         'sku' => $data->sku,
    //                         'stock' => $data->stock ,
    //                         'price' => $data->price ,
    //                         'images' => $data->images,
    //                     ]);
    //                 }
                   

    //             }
                
    //             $product = Product::find($id);
    //             $product_variants_get_price = product_variants::where('product_id', $product->id)->get();
    //             $highest_price = $product_variants_get_price->max('price');
    //             $lowest_price = $product_variants_get_price->min('price');
                
    //             if($highest_price == $lowest_price){
    //                 $product->update([
    //                     'show_price' => $highest_price,
    //                 ]);
    //             }
    //             if($highest_price != $lowest_price){
    //                 $product->update([
    //                     'show_price' => $lowest_price . " - " . $highest_price,
    //                 ]);
    //             }

    //         }else{
    //             DB::table('update_product')->where('product_id', $newDT->product_id)->delete();
    //             $notificationData = [
    //                 'type' => 'main',
    //                 'title' => 'Chỉnh sửa sản phẩm không được chấp nhận',
    //                 'description' => 'Sản phẩm của bạn đã không được chấp nhận thay đổi dữ liệu',
    //                 'user_id' => $newData["shop_id"],
    //             ];
    //             // $notificationController = new NotificationController();
    //             // $notification = $notificationController->store(new Request($notificationData));
    //         }
    //         //---------------------------------
    //         return redirect()->route('product_all', [
    //             'token' => auth()->user()->refesh_token,
    //             'tab' => $tab
    //         ])->with('message', 'Đã cập nhật sản phẩm.');

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Cập nhật thất bại",
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }

    public function updateFastProduct(Request $request, string $id)
    // ProductRequest
    {

        $ollDT = DB::table("products")->where("id", $id)->first();
        $ollData = (array) $ollDT;
        DB::table('products_old')->insert($ollData);
        $user = JWTAuth::parseToken()->authenticate();
        $change_of = $request->change_of;

        $dataInsert = [
            'price' => $request->price ?? $ollData["price"],
            'sale_price' => $request->sale_price ?? $ollData["sale_price"],
            'quantity' => $request->quantity ?? $ollData["quantity"],
            'update_by' => $user->id,
            'brand_id' => $request->brand_id ?? $ollData["brand_id"],
            'height' => $request->height ?? $ollData["height"],
            'length' => $request->length ?? $ollData["length"],
            'weight' => $request->weight ?? $ollData["weight"],
            'width' => $request->width ?? $ollData["width"],
            'created_at' => now(),
            'updated_at' => now(),
            'update_version' => $ollData["update_version"] + 1,
        ];
        try {
            DB::table('products')->update($dataInsert);

            return response()->json([
                'status' => true,
                'message' => "cập nhật san phẩm thành công"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    // public function variantattribute(Request $request, $shop_id, $id)
    // {
    //     $variantattribute = variantattribute::where('product_id', $id)->where('shop_id', $shop_id)->with("variant")->get();
    //     foreach ($variantattribute as $value) {
    //         $value->attribute_id = intval($value->attribute_id);
    //         $value->value_id = intval($value->value_id);
    //         $value->product_id = intval($value->product_id);
    //         $value->shop_id = intval($value->shop_id);
    //     }
    //     $attributevalue = [];
    //     $Attribute = [];
    //     $addedAttributeIds = [];
    //     $addedattributevalueIds = [];
    //     // return $variantattribute;
    //     foreach ($variantattribute as $vaAttribute) {
    //         $attribute = Attribute::where('id', $vaAttribute->attribute_id)->get();
    //         if (!in_array($vaAttribute->attribute_id, $addedAttributeIds)) {
    //             $Attribute[] = $attribute;
    //             $addedAttributeIds[] = $vaAttribute->attribute_id;
    //         }
    //         if (!in_array($vaAttribute->value_id, $addedattributevalueIds)) {
    //             $attributevalue[] = attributevalue::where('id', $vaAttribute->value_id)->get();
    //             $addedattributevalueIds[] = $vaAttribute->value_id;
    //         }
    //     }
    //     $variant = product_variants::where('product_id', $id)->get();
    //     foreach ($variant as $value) {
    //         $value->product_id = intval($value->product_id);
    //         $value->stock = intval($value->stock);
    //         $value->price = intval($value->price);
    //         $value->is_deleted = intval($value->is_deleted);
    //         $value->deleted_by = intval($value->deleted_by);
    //     }
        
    //     $data = [];
    //     $data['attribute'] = $Attribute;
    //     $data['value'] = $attributevalue;
    //     $data['variant'] = $variant;
    //     // $data['variantattribute'] = $variantattribute;
    //     return response()->json([
    //         'status' => true,
    //         'message' => "Lấy dữ liệu thành công",
    //         'data' => $data,
    //     ]);
    // }


    public function variantattribute(Request $request, $shop_id, $id)
    {
        // $product = Product::where('id', $id)->where('shop_id', $shop_id)->first();
        $product = Product::where('id', $id)->where('shop_id', $shop_id)->first();
        // dd($product);
        $product_variants = product_variants::where('product_id', $product->id)->get();
        $data = [];
        // $data['product'] = $product;
        $data['product_variants'] = $product_variants;
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'variants' => $data,
            'json' => json_decode($product->json_variants),
        ]);
    }

    

    // public function productWaitingApproval(Request $request)
    // {

    //     $tab = $request->input('tab', 1);
    
    //     $products = Product::where('status', 0)
    //         ->with(['images', 'variants']) 
    //         ->orderBy('created_at', 'ASC') 
    //         ->paginate(20);
    
    //     return view('products.list_product', [
    //         'products' => $products,
    //         'tab' => $tab 
    //     ]);
    // }
    

    public function approveProduct( Request $request, $id)
    {  
        $tab = $request->tab;
        $tabchill = $request->tabchill;
        // dd($tabchill);
        $product = Product::find($id ?? $request->id);
        if ($product) {
            $product->status = 2;
            $product->save();
            if($request->search){
                return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab,'search'=>$request->search]);
            }
            return redirect()->route('product_all', [
                'token' => auth()->user()->refesh_token,
                'tab' => $tab,
                'tabchill' => $tabchill,
            ])->with('message', 'Sản phẩm đã được duyệt.');
        }
        return redirect()->route('product_all', ['tab' => $tab,'tabchill'=>$tabchill])->with('error', 'Sản phẩm không tìm thấy.');
    }
    
    public function rejectProduct(Request $request,$id)
    {
        $tab = $request->tab;
        $product = Product::find($id);
        if ($product) {
            $product->status = 0;
            $product->save();
            if($request->search){
                return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab,'search'=>$request->search]);
            }
            return redirect()->route('product_all', [
                'token' => auth()->user()->refesh_token,
                'tab' => $tab
            ])->with('message', 'Sản phẩm đã không được duyệt');
        }
    
        return redirect()->route('product_all', ['tab' => $tab])->with('error', 'Sản phẩm không tìm thấy.');
    }
    public function reportProduct(Request $request, $id)
{
    $tab = $request->query('tab');
    $token = $request->query('token'); // Lấy token từ URL
    $reason = $request->input('reason');
    $product = Product::find($id);

    if ($product) {
        $product->status = 4;
        $product->admin_note = $reason;
        $product->save();
        if($request->search){
            return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab,'search'=>$request->search]);
        }
        return redirect()->route('product_all', [
            'token' => $token,
            'tab' => $tab
        ])->with('message', 'Sản phẩm đã bị đánh dấu là vi phạm');
    }

    return redirect()->route('product_all', ['tab' => $tab])->with('error', 'Sản phẩm không tìm thấy.');
}

    

    
public function ProductAll(Request $request)
{
    $tab = $request->input('tab', 1); 

    $allProductsCount = Product::count(); 
    $newProductsCount = Product::where('status', 3)->count(); 
    $activeProductsCount = Product::where('status', 2)->count(); 
    $rejectedProductsCount = Product::where('status', 0)->count(); 
    $violatingProductsCount = Product::where('status', 4)->count(); 
    $allUpdateProductsCount = update_product::all()->count();
    $pendingProductsCount = $allUpdateProductsCount + $newProductsCount;
    
    $allProducts = Product::all(); 
    $allUpdateProducts = update_product::all();
    $mergedProducts = $allProducts->merge($allUpdateProducts);
    $pendingProducts = Product::where('status', 3)
    ->with(['images', 'variants'])->get();

    $activeProducts = Product::where('status', 2)
        ->with(['images', 'variants'])->get();

    $rejectedProducts = Product::where('status', 0)
        ->with(['images', 'variants'])->get();

    $violatingProducts = Product::where('status', 4)
        ->with(['images', 'variants'])->get();

    $allProducts = Product::with(['images', 'variants'])->get();

    // $allUpdateProducts = update_product::with(['variants'])->get();
// $allUpdateProducts = update_product::orderBy("updated_at", "desc")->get();
    $allUpdateProducts = update_product::orderBy("updated_at", "desc")
    ->get()
    ->groupBy("product_id")
    ->map(function ($group) {
        return $group->first(); // Lấy bản ghi mới nhất trong từng nhóm
    });

    return view('products.list_product', compact(
        'allProductsCount', 'pendingProductsCount', 'activeProductsCount', 
        'rejectedProductsCount', 'violatingProductsCount', 'mergedProducts','newProductsCount','allUpdateProductsCount','allUpdateProducts',
        'pendingProducts', 'activeProducts', 'rejectedProducts',
        'violatingProducts', 'tab'
    ));
}

    public function showproduct($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }


    public function showReportForm(Request $request, $id)
    {
        $token = $request->query('token'); // Lấy token từ URL
        $tab = $request->query('tab');
        $product = Product::find($id);
    
        if (!$product) {
            return redirect()->route('product_all', ['tab' => $tab])->with('error', 'Sản phẩm không tìm thấy.');
        }
    
        return view('products.report_form', compact('product', 'token', 'tab'));
    }
      












// public function generate_Variants(Request $request)
// {
//     // Begin a database transaction to ensure data integrity
//     DB::beginTransaction();
//     $variantItems = $request->variant['variantItems'];
//     try {
//         // Create the main product if it doesn't exist
//         $product = Product::create([
//             'name' => $request['name'],
//             'slug' => $request['slug'] ?? Str::slug($request['name']),
//             'category_id' => $request['category_id'],
//             'description' => $request['description'] ?? '',
//             'shop_id' => $request['shop_id'],
//             'weight' => $request['weight'] ?? null,
//             'width' => $request['width'] ?? null,
//             'length' => $request['length'] ?? null,
//             'height' => $request['height'] ?? null,
//             'sku' => $request['sku'] ?? $this->generateSKU(),
//             'stock' => $request['stock'] ?? 0,
//             'price' => $request['price'] ?? null,
//             'images' => $request['images'] ?? [],
//         ]);

//         $productVariants = [];
//         $variantCombinations = $this->getVariantCombinations($variantItems);

//         foreach ($variantCombinations as $combination) {
//             // Generate SKU and default values for each variant
//             $sku = $request['sku'] ?? $this->generateSKU();
//             $stock = $request['stock'] ?? 0;
//             $price = $request['price'] ?? null;
//             $image = $combination['image'] ?? $request['images'][0] ?? null;
            
//             // Create product variant
//             $productVariantData = [
//                 'product_id' => $product->id,
//                 'sku' => $sku,
//                 'stock' => $stock,
//                 'price' => $price,
//                 'images' => $image,
//             ];
//             $productVariant = product_variants::create($productVariantData);
//             $variantName = implode(', ', array_column($combination, 'value'));
//             $productVariant->update(['name' => $variantName]);
//             $productVariants[] = $productVariant;

//             // Insert attribute values for the variant
//             foreach ($combination as $item) {
//                 $attributeId = Attribute::firstOrCreate(['name' => $item['name']], [
//                     'display_name' => strtoupper($item['name']),
//                 ])->id;
                
//                 $attributeValue = AttributeValue::firstOrCreate([
//                     'attribute_id' => $attributeId,
//                     'value' => $item['value'],
//                 ]);

//                 VariantAttribute::create([
//                     'variant_id' => $productVariant->id,
//                     'product_id' => $product->id,
//                     'shop_id' => $product->shop_id,
//                     'attribute_id' => $attributeId,
//                     'value_id' => $attributeValue->id,
//                 ]);
//             }
//         }

//         // Update the product's show price range
//         $highestPrice = collect($productVariants)->max('price');
//         $lowestPrice = collect($productVariants)->min('price');
//         $showPrice = $highestPrice == $lowestPrice ? $highestPrice : "$lowestPrice - $highestPrice";
//         $product->update(['show_price' => $showPrice]);

//         DB::commit();
        
//         return response()->json([
//             'status' => true,
//             'message' => "Product and variants generated successfully.",
//             'product' => $product->load('variants'),
//         ], 200);
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         return response()->json([
//             'status' => false,
//             'message' => "Failed to generate product and variants.",
//             'error' => $th->getMessage(),
//         ], 500);
//     }
// }

// // Helper function to get all possible combinations of attributes and values
// private function getVariantCombinations($variantItems)
// {
//     $combinations = [[]];
//     foreach ($variantItems as $attribute) {
//         $temp = [];
//         foreach ($combinations as $combination) {
//             foreach ($attribute['values'] as $value) {
//                 $temp[] = array_merge($combination, [
//                     [
//                         'name' => $attribute['name'],
//                         'value' => $value['value'],
//                         'image' => $value['image'] ?? null,
//                     ]
//                 ]);
//             }
//         }
//         $combinations = $temp;
//     }
//     return $combinations;
// }


}
