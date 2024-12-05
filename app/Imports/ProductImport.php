<?php

namespace App\Imports;

use App\Models\CategoriesModel;
use App\Models\Product;
use App\Models\product_variants;
use App\Models\Shop;
use App\Models\Tax;
use App\Models\tax_category;
use Brick\Math\BigInteger;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImport implements ToModel
{
    protected $user;
    protected $request;
    public function __construct($user, $request) {
        $this->user = $user;
        $this->request = $request;
    }
    public function model(array $row)
    {
        $proexist = Product::where('sku', $row[6])->first();
        if ($proexist) {
            return $proexist;
        }
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        $category_id = CategoriesModel::where('id', $this->request->category_id)->first()->id;
        $tax_category = tax_category::where('category_id', $category_id)->first();
        $tax = Tax::where('id', $tax_category->tax_id)->first();
        $priceAfterTax = $row[3] + ($row[3] * $tax->rate);
        $shop_id = Shop::where('id', $this->request->shop_id)->first()->id;
        $product = new Product([
            'name' => $row[0],
            'slug' => Str::slug($row[0]),
            'description' => $row[1] ?? null,
            'infomation' => $row[2] ?? null,
            'price' => $priceAfterTax ?? null,
            'image' => $row[4] ?? null,
            'quantity' => $row[5] ?? 0,
            'sold_count' => 0,
            'view_count' => 0,
            'create_by' =>  $this->user->id,
            'update_by' =>  $this->user->id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'category_id' => $category_id,
            'shop_id' => $shop_id,
            'sku' => $row[6],
            'height' => $row[7],
            'length' => $row[8],
            'weight' => $row[9],
            'width' => $row[10],
            'show_price' => $priceAfterTax,
            'status' => 0,
            'json_variants' => null,
         ]);
         return $product;
    }



}
