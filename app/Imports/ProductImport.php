<?php

namespace App\Imports;

use App\Models\CategoriesModel;
use App\Models\Product;
use Brick\Math\BigInteger;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    
        // $category_id = BigInteger::of($row[16]);
        $created_at = $row[13];
        // $updated_at = $row[14];
        if (!strtotime($created_at)) {
            $created_at = Carbon::parse($created_at)->format('Y-m-d H:i:s');
            // $updated_at = Carbon::parse($updated_at)->format('Y-m-d H:i:s');
        }
        $category_id = (int)$row[14];
        $shop_id = (int)$row[15];
        $product = new Product([
            'name' => $row[0] ?? null,
            'slug' => $row[1] ?? Str::slug($row[0]),
            'sku' => $row[2] ?? null,
            'description' => $row[3] ?? null,
            'infomation' => $row[4] ?? null,
            'price' => (int)$row[5] ?? null,
            'show_price' => $row[6] ?? null,
            'image' => $row[7] ?? null,
            'quantity' => (int)$row[8] ?? 0,
            'sold_count' => (int)$row[9] ?? 0,
            'view_count' => (int)$row[10] ?? 0,
            'create_by' => (int)$row[11] ?? null,
            'update_by' => (int)$row[12] ?? null,
            'created_at' => $created_at ?? Carbon::now(),
            // 'updated_at' => $updated_at ?? Carbon::now(),
            'category_id' => $category_id ?? null,
            'shop_id' => $shop_id ?? null,
            'status' => $row[16] ?? null,
            'height' => (int)$row[17] ?? null,
            'length' => (int)$row[18] ?? null,
            'weight' => (int)$row[19] ?? null,
            'width' => (int)$row[20] ?? null,
            'update_version' => (int)$row[21] ?? null,
            'is_delete' => (int)$row[22] ?? null,
         ]);
         return $product;
    }



}
