<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetailsModel extends Model
{
    use HasFactory;
    protected $table = 'order_details'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'subtotal',
        'status',
        'order_id',
        'product_id',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
    ];
}
