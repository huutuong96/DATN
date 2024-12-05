<?php

namespace App\Imports;
use App\Models\Attribute;
use App\Models\attributevalue;
use App\Models\Product;
use App\Models\product_variants;
use App\Models\variantattribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class ProductVariantImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        // DB::beginTransaction(); 
        if ($row[0] === null) {
            return "sku is required";
        }
        $product = Product::where('sku', $row[0])->first();

        $attributeArray = [];
        $attributevalueArray = [];

        $attribute1 = Attribute::create([
            'name' => $row[6],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $attribute2 = Attribute::create([
            'name' => $row[8],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $attributevalue1 = new attributevalue([
            'value' => $row[7],
            'attribute_id' => $attribute1->id,
        ]);

        $attributevalue2 = new attributevalue([
            'value' => $row[9],
            'attribute_id' => $attribute2->id,
        ]);

        
        $attributeArray[] = $attribute1;
        $attributeArray[] = $attribute2;
        $attributevalueArray[] = $attributevalue1;
        $attributevalueArray[] = $attributevalue2;

        $variants = product_variants::create([
            'product_id' => $product->id,
            'name' => str_replace(' ', '', $row[1]),
            'sku' => str_replace(' ', '', $row[2]),
            'stock' => (int)str_replace(' ', '', $row[3]),
            'price' => (int)str_replace(' ', '', $row[4]),
            'images' => str_replace(' ', '', $row[5]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'id_fe' => Str::random(10).'-'.$product->id.'-'.$row[1],
        ]);

        variantattribute::create([
            'variant_id' => $variants->id,
            'attribute_id' => $attribute1->id,
            'value_id' => $attributevalue1->id,
            'shop_id' => $product->shop_id,
            'product_id' => $product->id,
        ]); 

        variantattribute::create([
            'variant_id' => $variants->id,
            'attribute_id' => $attribute2->id,
            'value_id' => $attributevalue2->id,
            'shop_id' => $product->shop_id,
            'product_id' => $product->id,
        ]); 

        $variantItems = [];
        foreach ($attributeArray as $attribute) {
            $values = [];
            foreach ($attributevalueArray as $attributevalue) {
                $values[] = [
                    "id" => Str::random(10) . '-' . $product->id . '-' . $row[1],
                    "value" => $attributevalue->value,
                ];
            }
            $variantItems[] = [
                "name" => $attribute->name,
                "values" => $values,
            ];
        }

        $variantProducts = [
            [
                "id" => $variants->id_fe,
                "sku" => $variants->sku,
                "image" => $variants->images,
                "price" => $variants->price,
                "stock" => $variants->stock,
                "variants" => [
                    [
                        "id" => Str::random(10) . '-' . $product->id . '-' . $row[1],
                        "value" => $attributevalue->value,
                        "attribute" => $attribute->name,
                    ]
                ]
            ]
        ];

        $json = [
            "variantItems" => $variantItems,
            "variantProducts" => $variantProducts,
        ];

        $product->json_variants = json_encode($json);
        $product->price = 0;
        $product->quantity = 0;
        $product->show_price = $variants->price;
        $product->save();
        // DB::commit();
        return $variants;
    }
}
