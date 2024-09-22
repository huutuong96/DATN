<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products'; // Thay đổi tên bảng nếu cần

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
        'color_id',
        'sku',
    ];


    // Thêm mối quan hệ images vào đây
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    // Thêm mối quan hệ colors
    public function colors()
    {
        return $this->hasMany(ColorsModel::class);
    }
    public function variants()
    {
        return $this->hasMany(product_variants::class);
    }
    public function attributes()
    {
        return $this->hasManyThrough(
            Attribute::class,
            product_variants::class,
            'product_id', // Khóa ngoại trên bảng product_variants
            'id', // Khóa chính trên bảng attributes
            'id', // Khóa chính trên bảng products
            'attribute_id' // Khóa ngoại trên bảng variantattribute
        );
    }
    public function prices()
    {
        return $this->hasMany(product_prices::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(price_histories::class);
    }

    public function currentPrice()
    {
        return $this->hasOne(product_prices::class)
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->where('is_active', 1)
            ->orderBy('start_date', 'desc');
    }
}

