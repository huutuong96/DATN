<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerShop extends Model
{
    use HasFactory;

    protected $table = 'banner_shops'; // Thay đổi tên bảng nếu cần
    
    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'shop_id',
        'title',
        'content',
        'URL',
        'status',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
    ];
}
