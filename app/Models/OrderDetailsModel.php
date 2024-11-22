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
        'variant_id',
        'quantity',
        'create_at',
        'update_at',
        'height',
        'length',
        'weight',
        'width',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    // Trong model OrderDetail

    public function variant()
    {
        return $this->belongsTo(product_variants::class, 'variant_id');
    }
}
