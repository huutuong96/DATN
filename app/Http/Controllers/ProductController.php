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
    public function index(Request $request)
    {
        $status = 1;
        if($request->status){
            $status = $request->status;
        }
        $products = Product::where('status', $status)
        ->with(['images', 'colors'])  // Eager load images
        ->paginate(20);
        $products->appends(['status' => $status]); // Append status to pagination links
        if($request->status == 0){
            $products = Product::
            with(['images', 'colors'])  // Eager load images
            ->paginate(20);
            $products->appends(['status' => 0]); // Append status to pagination links
        }



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

    public function store(Request $request)
    {
        // return $request->all();
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();
            DB::beginTransaction();
            $mainImageUrl = null;
            $dataInsert = [
                'name' => $request->name,
                'sku' => $request->sku ?? $this->generateSKU(),
                'slug' => $request->slug ?? Str::slug($request->name),
                'description' => $request->description,
                'infomation' => json_encode($request->infomation),
                'price' => $request->price,
                'sale_price' => $request->sale_price ?? null,
                'image' => $request->thumbnail ?? null,
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
            ];
            $product = Product::create($dataInsert);
            foreach ($request->images as $image) {
                $imageModel = Image::create([
                    'product_id' => $product->id,
                    'url' => $image ?? null,
                    'status' => 1,
                ]);
            }
            // return $request->variant;
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
                            'value' => $value,
                        ];
                        $attributeValue = attributevalue::create($attributeValueData);
                    }
                }
                foreach ($request->variant['variantProducts'] as $variant) {
                    $product_variantsData = [
                        'product_id' => $product->id,
                        'sku' => $variant['sku'] ?? $this->generateSKU(),
                        'stock' => $variant['inStock'] ?? $request->inStock,
                        'price' => $variant['price'] ?? $product->price,
                        'images' => $variant['image'] ?? $product->image,
                    ];
                    $product_variants = product_variants::create($product_variantsData);

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
    public function handleUpdateProduct(Request $request, string $id)
    // ProductRequest
    {

        try {
            if($request-> action == "ok"){
                $newDT = DB::table("update_product")->where("product_id", $id)->first();
                $ollDT = DB::table("products")->where("id", $id)->first();
                $ollData = (array) $ollDT;
                $newData = (array) $newDT;
                DB::table('products_old')->insert($ollData);
                $change_of = $data = json_decode($newData["change_of"], true);
                unset($newData["change_of"]);
                $newData["created_at"] = $newData["updated_at"];
                $newData["id"] = $newData["product_id"];
                unset($newData["product_id"]);
                DB::table('products')->where('id', $id)->update($newData);
                DB::table('update_product')->where('id', $newDT->id)->delete();
                if($request->change  != null){
                    if($request->change === "all"){
                        // lấy tất cả biến thể xong cập nhật
                        $variants = product_variants::where('product_id', $id)->get();
                        foreach ($variants as $key => $variant) {
                            $variant->update([
                                'stock' => $request->stock ?? $variant->stock,
                                'price' => $request->price ?? $variant->price,
                                // 'images' => isset($imageData) ? json_encode($imageData) : $variant->images, phần này cân xu lý them
                            ]);
                        };
                        $output["variants"] = $variants;
                    }else{
                        $variants = product_variants::whereIn('id', $request->change)->get();
                        foreach ($variants as $key => $variant) {
                            $variant->update([
                                'stock' => $request->stock ?? $variant->stock,
                                'price' => $request->price ?? $variant->price,
                                // 'images' => isset($imageData) ? json_encode($imageData) : $variant->images, phần này cân xu lý them
                            ]);
                        };
                        $output["variants"] = $variants;
                    }

                }

                $notificationData = [
                    'type' => 'main',
                    'title' => 'Chỉnh sửa sản phẩm thành công',
                    'description' => 'Sản phẩm của bạn đã được chỉnh sửa thành công',
                    'user_id' => $newData["shop_id"],
                ];
                $notificationController = new NotificationController();
                $notification = $notificationController->store(new Request($notificationData));

            }else{
                DB::table('update_product')->where('id', $newDT->id)->delete();
                $notificationData = [
                    'type' => 'main',
                    'title' => 'Chỉnh sửa sản phẩm không được chấp nhận',
                    'description' => 'Sản phẩm của bạn đã không được chấp nhận thay đổi dữ liệu',
                    'user_id' => $newData["shop_id"],
                ];
                $notificationController = new NotificationController();
$notification = $notificationController->store(new Request($notificationData));
            }
            //---------------------------------
            return response()->json([
                'status' => true,
                'message' => "cập nhật thành công",
                'product' => "ok",
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "Cập nhật thất bại",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

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

    public function variantattribute(Request $request, $id)
    {
        $variantattribute = variantattribute::where('product_id', $id)->get();
        foreach ($variantattribute as $va) {
            $va->variant_id = intval($va->variant_id);
            $va->attribute_id = intval($va->attribute_id);
            $va->value_id = intval($va->value_id);
            $va->shop_id = intval($va->shop_id);
            $va->product_id = intval($va->product_id);
        }
        return response()->json([
            'status' => true,
            'message' => "Lấy dữ liệu thành công",
            'data' => $variantattribute,
        ]);
    }

}
