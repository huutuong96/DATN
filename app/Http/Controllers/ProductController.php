<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\PDF;
use App\Exports\AdminExport;
use App\Exports\ImageExport;
use App\Exports\ManagerExport;
use App\Exports\OrderDetailExport;
use App\Exports\OrderExport;
use App\Exports\ProductsExport;
use App\Exports\SellerExport;
use App\Exports\ShopExport;
use App\Exports\Transaction_history;
use App\Exports\UserExport;
use App\Models\CategoriesModel;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\ProductRequest;
use App\Imports\imagesProductImport;
use App\Imports\MultiSheetImport;
use App\Imports\ProductExport;
use App\Imports\ProductImport;
use App\Imports\ProductVariantImport;
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
use App\Imports\UsersImport;
use App\Models\Event;
use App\Models\Follow_to_shop;
use App\Models\OrderDetailsModel;
use App\Models\OrdersModel;
use App\Services\RecommendationService;
use Maatwebsite\Excel\Facades\Excel;
use Phpml\FeatureExtraction\CountVectorizer;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use App\Models\CommentsModel;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkShip')->only('store');
        $this->middleware('web'); // Ensure session middleware is applied
    }

    public function index(Request $request)
    {
        $status = 2;
        if ($request->status) {
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
    }

    public function getProductToSlug(Request $request, $slug)
    {

        if (empty($slug)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại'
            ], 400);
        }
        $products = Product::where('slug', $slug)->where('status', 2)->with(['images', 'shop' => function ($query) {
            $query->select('id', 'shop_name', 'slug', 'image', 'province', 'created_at', 'contact_number', 'status')->withCount('products as countProduct')
                ->with(['products' => function ($queryPro) {
                    $queryPro->select('id', 'name', 'slug', 'show_price', 'image', 'quantity', 'sold_count', 'view_count', 'shop_id', 'status', 'created_at', 'updated_at')
                        ->where('status', 2)
                        ->orderBy('sold_count', 'desc')
                        ->limit(5);
                }]);
        }])->get();
        $authorization = $request->header('Authorization');
        if ($authorization) {
            $user = JWTAuth::parseToken()->authenticate();
            Follow_to_shop::where('shop_id', $products[0]->shop->id)->where('user_id', $user->id)->first() ? $products[0]->shop->is_follow = true : $products[0]->shop->is_follow = false;
        }

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'không tìm thấy sản phẩm nào'
            ], 404);
        }
        $data = $products->first();
        $data->countRanting = CommentsModel::where('rate', '!=', null)->where('product_id', $data->id)->get()->count();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $tax_category = tax_category::where('category_id', $request->category_id)->first();
        $taxes = Tax::find($tax_category->tax_id);
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $cloudinary = new Cloudinary();
            DB::beginTransaction();
            $mainImageUrl = null;
            $checkSlug = Product::where("slug", $request->slug ?? Str::slug($request->name))->first();
            if ($checkSlug) {
                $slug = $checkSlug->slug;
                $slug .= '-' . rand(1000, 9999);
            } else {
                $slug = $request->slug ?? Str::slug($request->name);
            }
            $taxAmount = $request->price * $taxes->rate;
            $dataInsert = [
                'name' => $request->name,
                'sku' => $request->sku ?? $this->generateSKU(),
                'slug' => $slug,
                'description' => $request->description,
                'infomation' => json_encode($request->infomation),
                'price' => $request->price + $taxAmount ?? null,
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
            if ($request->variant != null) {
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
                    $taxAmount = $variant['price'] * $taxes->rate;
                    $product_variantsData = [
                        'product_id' => $product->id,
                        'id_fe' => $variant['id'] ?? null,
                        'sku' => $variant['sku'] ?? $this->generateSKU(),
                        'stock' => $variant['stock'] ?? $request->stock,
                        'price' => $variant['price'] + $taxAmount ?? $product->price,
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
                if ($highest_price == $lowest_price) {
                    $product->update([
                        'show_price' => $highest_price,
                    ]);
                }
                if ($highest_price != $lowest_price) {
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
            log_debug($th->getMessage());
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
        $request->variant_ids = [697, 698, 699];
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
        $checkSlug = Product::where("slug", $request->slug ?? Str::slug($request->name))->first();
        if ($checkSlug) {
            $slug = $checkSlug->slug;
            $slug .= '-' . rand(1000, 9999);
        } else {
            $slug = $request->slug ?? Str::slug($request->name);
        }
        $dataInsert = [
            'name' => $request->name ?? $product->name,
            'slug' => $slug,
            'sku' => $request->sku ?? $product->sku,
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
            log_debug($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Cập nhật không thành công",
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request)
    {
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
                    'message' => 'Product không tồn tại',
                ], 404);
            }
            $product->update(['status' => 5]);

            return response()->json([
                'status' => true,
                'message' => 'Xóa sản phẩm thành công',
            ]);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Xóa sản phẩm không thành công",
                'error' => $th->getMessage(),
            ]);
        }
    }
    public function destroyArray(Request $request)
    {
        try {
            foreach ($request->arrayID as $id) {
                $product = Product::find($id);
                if (!$product) {
                    return response()->json([
                        'status' => false,
                        'product_id' => $id,
                        'message' => 'Product id: ' . $id . ' không tồn tại',
                    ], 404);
                }
                $product->update(['status' => 5]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Xóa sản phẩm thành công',
            ]);
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
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
        $limit = $request->limit ?? 20;
        $query = Product::query();
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween(DB::raw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END'), [$request->min_price, $request->max_price]);
        }
        if ($request->has('category_id')) {
            $categoryIds = CategoriesModel::where('parent_id', $request->category_id)
                ->orWhere('id', $request->category_id)
                ->pluck('id')
                ->toArray();
            $query->whereIn('category_id', $categoryIds);
        }
        if ($request->has('updated_at')) {
            $query->orderby('updated_at', 'desc');
        }
        if ($request->has('sold_count')) {
            $query->orderby('sold_count', 'desc');
        }
        if ($request->has('view_count')) {
            $query->orderby('view_count', 'desc');
        }
        if ($request->has('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        if ($request->sort == 'price') {
            $query->orderByRaw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END ASC');
        }
        if ($request->sort == '-price') {
            $query->orderByRaw('CASE WHEN show_price LIKE "% - %" THEN CAST(SUBSTRING_INDEX(show_price, " - ", 1) AS UNSIGNED) ELSE CAST(show_price AS UNSIGNED) END DESC');
        }
        if ($request->has('min_rate') && $request->has('max_rate')) {
            $query->whereHas('comments', function ($q) use ($request) {
                $q->havingRaw('AVG(rate) BETWEEN ? AND ?', [$request->min_rate, $request->max_rate])
                    ->whereNotNull('rate');
            });
        }
        $products = $query->where('status', 2)->paginate($limit);
        foreach ($products as $product) {
            $product->rateAvg = rateAvg($product->id);
        }
        return response()->json($products);
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
        Image::where('product_id', $product->id)->delete();
        foreach ($request->images as $image) {
            $imageModel = Image::create([
                'product_id' => $product->id,
                'url' => $image ?? null,
                'status' => 1,
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();

        if ($request->name != $product->name) {
            $slug = Str::slug($request->name);
        } else {
            $slug = $product->slug;
        }

        $dataInsert = [
            'product_id' => $product->id,
            'name' => $request->name ?? $product->name,
            'sku' => $request->sku ?? null,
            'slug' => $slug,
            'description' => $request->description ?? $product->description,
            'infomation' => $request->infomation ?? $product->infomation,
            'price' => $request->variantMode ? 0 : $request->price, // nếu có biến thể thì nó = 0
            'sale_price' => $request->sale_price ?? $product->sale_price,
            'image' => $request->images[0],
            'quantity' => $request->stock ?? $product->quantity,
            'parent_id' => $request->parent_id ?? $product->parent_id,
            'update_by' => $user->id,
            'category_id' => $request->category ?? $product->category_id,
            'shop_id' => $request->shop_id ?? $product->shop_id,
            'height' => $request->height ?? $product->height,
            'length' => $request->length ?? $product->length,
            'weight' => $request->weight ?? $product->weight,
            'width' => $request->width ?? $product->width,
            'status' =>  $product->status,
            'created_at' => $product->created_at,
            'show_price' => $request->show_price ?? $product->show_price,
            'brand' => $request->brand ?? $product->brand,
            'json_variants' => $product->json_variants,
            'admin_note' => $request->admin_note ?? $product->admin_note,
            'is_delete' => $request->is_delete ?? $product->is_delete,
            'updated_at' => now(),

            'update_version' => $product->update_version + 1,
            'change_of' => $request->variantMode === true ? json_encode($request->variant) : json_encode(0),
        ];

        try {
            DB::table('update_product')->insert($dataInsert);

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
            log_debug($th->getMessage());
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


    public function approveProduct(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $tab = $request->tab;
        $tabchill = $request->tabchill;
        // dd($tabchill);
        $product = Product::find($id ?? $request->id);
        $shop = Shop::where('id', $product->shop_id)->select('owner_id')->first();
        if (!$shop) {
            return back()->with('error', 'Cửa hàng không còn hoạt động hoặc bị xóa.');
        }
        // dd($shop);
        if ($product) {
            $product->status = 2;
            $product->save();
            $user_id = $product->shop->owner_id;
            $notificationRequest = new Request([
                'type' => 'main',
                'user_id' => $user_id,
                'title' => 'Sản phẩm đã phê duyệt',
                'image' => "https://res.cloudinary.com/dg5xvqt5i/image/upload/v1734184099/Pngtree_check_done_approve_vector_13446153_xeqzc5.png",
                'description' => ' sản phẩm' . $product->name . ' của bạn đã được phê duyệt bởi nhân viên ' . $user->fullname,
                'shop_id' => $product->shop_id
            ]);
            $notificationController = new NotificationController();
            $notificationController->store($notificationRequest);
            if ($request->search) {
                return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab, 'search' => $request->search]);
            }
            return redirect()->route('product_all', [
                'token' => auth()->user()->refesh_token,
                'tab' => $tab,
                'tabchill' => $tabchill,
            ])->with('message', 'Sản phẩm đã được duyệt.');
        }
        return redirect()->route('product_all', ['tab' => $tab, 'tabchill' => $tabchill])->with('error', 'Sản phẩm không tìm thấy.');
    }

    public function approve_product(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm');
        }
        $product->status = 2;
        $product->save();
        $user_id = $product->shop->owner_id;
        $notificationRequest = new Request([
            'type' => 'main',
            'user_id' => $user_id,
            'title' => 'Sản phẩm đã bị từ chối',
            'description' => ' sản phẩm' . $product->name . ' của bạn đã bị từ chối với lý do' . $product->admin_note,
            'shop_id' => $product->shop_id
        ]);
        $notificationController = new NotificationController();
        $notificationController->store($notificationRequest);
        return redirect()->back()->with('success', 'Duyệt sản phẩm thành công');
    }
    public function rejectProduct(Request $request, $id)
    {
        $tab = $request->tab;
        $product = Product::find($id);
        if ($product) {
            $product->status = 0;
            $product->save();
            $user_id = $product->shop->owner_id;
            $notificationRequest = new Request([
                'type' => 'main',
                'user_id' => $user_id,
                'title' => 'Sản phẩm đã bị từ chối',
                'image' => "https://res.cloudinary.com/dg5xvqt5i/image/upload/v1734185014/Pngtree_error-cross_6020171_gngo5g.png",
                'description' => ' sản phẩm' . $product->name . ' của bạn đã bị từ chối',
                'shop_id' => $product->shop_id
            ]);
            $notificationController = new NotificationController();
            $notificationController->store($notificationRequest);
            if ($request->search) {
                return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab, 'search' => $request->search]);
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
            $user_id = $product->shop->owner_id;
            $notificationRequest = new Request([
                'type' => 'main',
                'user_id' => $user_id,
                'title' => 'Sản phẩm đã bị từ chối',
                'description' => ' sản phẩm' . $product->name . ' của bạn đã bị từ chối với lý do' . $product->admin_note,
                'shop_id' => $product->shop_id
            ]);
            $notificationController = new NotificationController();
            $notificationController->store($notificationRequest);
            if ($request->search) {
                return redirect()->route('admin_search_get', ['token' => auth()->user()->refesh_token, 'tab' => $request->tab, 'search' => $request->search]);
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
        $allUpdateProducts = update_product::orderBy("updated_at", "desc")->get();
        $allUpdateProducts = update_product::orderBy("updated_at", "desc")
            ->get()
            ->groupBy("product_id")
            ->map(function ($group) {
                return $group->first(); // Lấy bản ghi mới nhất trong từng nhóm
            });

        return view('products.list_product', compact(
            'allProductsCount',
            'pendingProductsCount',
            'activeProductsCount',
            'rejectedProductsCount',
            'violatingProductsCount',
            'mergedProducts',
            'newProductsCount',
            'allUpdateProductsCount',
            'allUpdateProducts',
            'pendingProducts',
            'activeProducts',
            'rejectedProducts',
            'violatingProducts',
            'tab'
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

    //    public function importProducts(Request $request){
    //         $user = JWTAuth::parseToken()->authenticate();
    //         // try {
    //             Excel::import(new ProductImport($user, $request), $request->file('product_import'));
    //             if ($request->hasFile('variant_import')) {
    //                 Excel::import(new ProductVariantImport, $request->file('variant_import'));
    //             }
    //             return 'Import thành công';
    //         // } catch (\Throwable $th) {
    //         //     return 'Import thất bại: ' . $th->getMessage();
    //         // }
    //    }

    public function exportdata(Request $request)
    {
        try {
            if ($request->data == 'products') {
                return Excel::download(new ProductsExport($request), 'vnshop-products.xlsx');
            }
            if ($request->data == 'users') {
                return Excel::download(new UserExport($request), 'vnshop-customer.xlsx');
            }
            if ($request->data == 'orders') {
                return Excel::download(new OrderExport($request), 'vnshop-orders.xlsx');
            }
            if ($request->data == 'transaction_history') {
                return Excel::download(new Transaction_history($request), 'vnshop-transaction_history.xlsx');
            }
            if ($request->data == 'order_details') {
                return Excel::download(new OrderDetailExport($request), 'vnshop-order_details.xlsx');
            }
            if ($request->data == 'bills') {
                $orders = OrdersModel::with(['orderDetails.variant.product', 'shop', 'payment', 'timeline']) // Eager load 'product' qua 'orderDetails'
                    ->where('id', $request->order_id)
                    ->get();
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
                            $orderDetail['product']  = $orderDetail->variant->product;
                            unset($orderDetail->variant['product']);
                        }
                    }
                }
                //    return $order;
                $pdf = PDF::loadView('bill.bill_template', compact('orders'))
                    ->setPaper('a4')
                    ->setOptions(['defaultFont' => 'DejaVuSans']);
                return $pdf->download('vnshop-bills.pdf');
            }
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
            return 'export thất bại: ' . $th->getMessage();
        }
    }


    public function recommendProducts(Request $request)
    {
        // try {
        //     try {
        //         $user = JWTAuth::parseToken()->authenticate();
        //     } catch (\Exception $e) {
        //         $user = null;
        //     }
        //     if ($user) {
        //         $allProducts = Product::pluck('id')->toArray();
        //         $userOrders = OrdersModel::where('user_id', $user->id)->pluck('id')->toArray();
        //         $userPurchasedProducts = OrderDetailsModel::whereIn('order_id', $userOrders)->pluck('product_id')->unique()->toArray();

        //         $trainingData = [];
        //         $labels = [];
        //         $orders = OrdersModel::where('user_id', $user->id)->with('orderDetails')->get();
        //         foreach ($orders as $order) {
        //             $products = $order->orderDetails->pluck('product_id')->toArray();
        //             $vector = array_map(fn($id) => in_array($id, $products) ? 1 : 0, $allProducts);
        //             $trainingData[] = $vector;
        //             $labels = array_merge($labels, $products);
        //         }

        //         $userVector = array_map(fn($id) => in_array($id, $userPurchasedProducts) ? 1 : 0, $allProducts);
        //         $service = new RecommendationService();
        //         $recommendation = $service->recommendTopN([$userVector], $trainingData, $labels, 10);
        //         if (count($recommendation) < 1) {
        //             $recommendedProducts = Product::inRandomOrder()
        //                 ->where('status', 2)
        //                 ->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count', 'category_id')
        //                 ->limit(5)
        //                 ->get();
        //             return response()->json(
        //                 [
        //                     'status' => true,
        //                     'message' => "Lấy dữ liệu thành công",
        //                     'data' => $recommendedProducts,
        //                 ]
        //             );
        //         }

        //         $productsGetCategory = Product::whereIn('id', $userPurchasedProducts)->pluck('category_id')->toArray();
        //         $categories = CategoriesModel::whereIn('id', $productsGetCategory)->pluck('id')->toArray();

        //         $recommendedProducts = Product::whereIn('id', $recommendation)
        //             ->where('status', 2)
        //             ->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count', 'category_id')
        //             ->limit(5)
        //             ->get();

        //         $categoryProducts = Product::whereIn('category_id', $categories)
        //             ->where('status', 2)
        //             ->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count', 'category_id')
        //             ->limit(5)
        //             ->get();
        //         $products = $recommendedProducts->merge($categoryProducts);
        //     } else {
        //         $products = Product::inRandomOrder()->where('status', 2)->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count')->limit(5)->get();
        //     }
        //     foreach ($products as $product) {
        //         $product->rateAvg = rateAvg($product->id);
        //     }
        //     return response()->json(
        //         [
        //             'status' => true,
        //             'message' => "Lấy dữ liệu thành công",
        //             'data' => $products,
        //         ]
        //     );
        // } catch (\Throwable $th) {
        //     log_debug($th->getMessage());
        //     return response()->json([
        //         'status' => false,
        //         'message' => "Lấy dữ liệu không thành công",
        //         'error' => $th->getMessage(),
        //     ]);
        // }


        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                $user = null;
            }
          
            if ($user) {
                
                $userOrders = OrdersModel::where('user_id', $user->id)->pluck('id')->toArray();
                $userPurchasedProducts = OrderDetailsModel::whereIn('order_id', $userOrders)
                ->pluck('product_id')->unique()->toArray();

                $allProducts = Product::pluck('id')->toArray();
                $trainingData = [];
                $labels = [];
                $orders = OrdersModel::where('user_id', $user->id)->with('orderDetails')->get();
                foreach ($orders as $order) {
                $products = $order->orderDetails->pluck('product_id')->toArray();
                $vector = array_map(fn($id) => in_array($id, $products) ? 1 : 0, $allProducts);
                $trainingData[] = $vector;
                $labels = array_merge($labels, $products);
                }

                $userVector = array_map(fn($id) => in_array($id, $userPurchasedProducts) ? 1 : 0, $allProducts);
                $service = new RecommendationService();
                $recommendation = $service->recommendTopN([$userVector], $trainingData, $labels, 30);

                $recommendedProducts = Product::whereIn('id', $recommendation)
                ->where('status', 2)
                ->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count', 'category_id')
                ->get();
                $categories = $recommendedProducts->pluck('category_id')->unique();
                $categoryProducts = Product::whereIn('category_id', $categories)
                ->whereNotIn('id', $recommendation)
                ->where('status', 2)
                ->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count', 'category_id')
                ->limit(10)
                ->get();
                 $products = $recommendedProducts->merge($categoryProducts);

                return response()->json([
                    'status' => true,
                    'message' => 'Lấy dữ liệu thành công.',
                    'data' => $products,
                ]);
            } else {
                $products = Product::inRandomOrder()->where('status', 2)->select('id', 'name', 'slug', 'show_price', 'image', 'view_count', 'sold_count')->limit(10)->get();
                return response()->json([
                    'status' => true,
                    'message' => 'Lấy dữ liệu thành công.',
                    'data' => $products,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
            'status' => false,
            'message' => 'Đã xảy ra lỗi.',
            'error' => $th->getMessage(),
            ]);
        }
    }
}
