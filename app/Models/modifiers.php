<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class modifiers extends Model
{
    use HasFactory;
    protected $table = 'modifiers'; // Khai báo tên bảng

    protected $fillable = [
        'id',
        'title',
        'descriptions',
        'products',
        'users',
        'shops',
        'from',
        'to',
        'view',
        'banner',
        'disscount',
        'sale_price',
        'rate',
        'product_apply',
        'user_apply',
        'shop_apply',
        'status',
    ];
}
