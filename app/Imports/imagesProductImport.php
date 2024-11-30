<?php

namespace App\Imports;

use App\Models\Image;
use App\Models\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class imagesProductImport implements ToModel
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function model(array $row)
    {
        return $this->products;
        $created_at = $row[2];
        $updated_at = $row[3];
        if (!strtotime($created_at)) {
            $created_at = Carbon::createFromFormat('d/m/Y', $created_at)->format('Y-m-d H:i:s');
            $updated_at = Carbon::createFromFormat('d/m/Y', $updated_at)->format('Y-m-d H:i:s');
        }
        
        return new Image([
            'product_id' => $product->id,
            'product_variant_id' => null,
            'url' => $row[0],
            'status' => $row[1],
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ]);
    }
}
