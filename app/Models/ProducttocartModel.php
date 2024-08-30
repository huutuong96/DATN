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
        'quantity',
        'status',
        'cart_id',
        'product_id',
        'create_by',
        'update_by',
    ];
}
