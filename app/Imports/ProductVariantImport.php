<?php

namespace App\Imports;
use App\Models\Attribute;
use App\Models\attributevalue;
use App\Models\Product;
use App\Models\product_variants;
use Carbon\Carbon;
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
        // $product = Product::where('sku', $row[1])->first();
    
        $string = str_replace(['[', ']'], '', $row[5]);
        $attributes = explode(', ', $string);
        dd($attributes);
        foreach ($attributes as $attri) {
            dd($attri);
        }
        $attribute = Attribute::create([
            'name' => $row[5],
            'display_name' => strtoupper($row[5]),
        ]);
        dd($attribute);





        $attributevalue = new attributevalue([
            'value' => $row[6],
            'attribute_id' => $attribute->id,
            'image' => $row[7],
        ]);

        $variants = new product_variants([
            'product_id' => $product->id,
            'name' => $row[0],
            'sku' => $row[1],
            'stock' => $row[2],
            'price' => $row[3],
            'images' => $row[4],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'attribute_id' => $attribute->id,
            'value_id' =>  $attributevalue->id,
            'id_fe' => Str::random(10).'-'.$product->id.'-'.$row[1],
         ]);

        $json = [
                    "variantItems" => [
                        [
                            "name" => $attribute->name,
                            "values" => [
                                [
                                    "id" =>  Str::random(10).'-'.$product->id.'-'.$row[1],
                                    "image" => $attributevalue->image,
                                    "value" => $attributevalue->value,
                                ]
                            ]
                        ]
                    ],
                    "variantProducts" => [
                        [
                            "id" => $variants->id_fe,
                            "sku" => $variants->sku,
                            "image" => $variants->images,
                            "price" => $variants->price,
                            "stock" => $variants->stock,
                            "variants" => [
                                [
                                    "id" => Str::random(10).'-'.$product->id.'-'.$row[1],
                                    "value" => $attributevalue->value,
                                    "attribute" => $attribute->name,
                                ]
                            ]
                        ]
                    ]
                ];

        $product->json_variants = json_encode($json);
        $product->price = 0;
        $product->save();

        return $variants;
    }
}
