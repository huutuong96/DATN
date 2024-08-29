<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProducttoshopModel extends Model
{
    use HasFactory;
    protected $table = 'product_to_shops'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'url_share',
        'status',
        'product_id',
        'shop_id',
        'create_by',
        'update_by',
    ];
}
