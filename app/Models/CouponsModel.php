<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponsModel extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'status',
        'coupon_percentage',
        'condition',
        'create_by',
        'update_by'
    ];


    /**
     * Các trường sẽ được tự động chuyển đổi sang kiểu dữ liệu tương ứng.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
