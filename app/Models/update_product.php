<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class update_product extends Model
{
    use HasFactory;
    protected $table = 'update_product';
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
        'height',
        'length',
        'weight',
        'width',
        'version',
        'show_price',
        'status',
        'is_delete',
        'admin_note',
    ];
}
