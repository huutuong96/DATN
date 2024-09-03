<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    // protected $table = 'products'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'name',
        'slug',
        'description',
        'infomation',
        'price',
        'sale_price',
        'image',
        'quantity',
        'sold_count',
        'view_count',
        'parent_id',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
        'category_id',
        'brand_id',
        'shop_id',
    ];

}
