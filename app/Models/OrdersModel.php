<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'payment_id',
        'ship_id',
        'voucher_id',
        'user_id',
        'shop_id',
        'status',
        'net_amount',
        'delivery_address',
        'create_at',
        'update_at'
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
