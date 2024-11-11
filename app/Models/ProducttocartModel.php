<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProducttocartModel extends Model
{
    use HasFactory;
    protected $table = 'product_to_carts'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'status',
        'created_at',
        'updated_at',
        'cart_id',
        'shop_id',
        'shop_name',
        'shop_slug',
        'product_id',
        'product_name',
        'product_slug',
        'product_price',
        'product_image',
        'variant_id',
        'variant_name',
        'variant_price',
        'variant_image',
        'quantity',
        'ship_code',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function variant()
    {
        return $this->belongsTo(product_variants::class, 'variant_id', 'id');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }


}
