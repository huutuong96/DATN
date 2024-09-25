<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersModel extends Model
{
    use HasFactory;

    protected $table = 'orders';

    // Define the relationship with OrderDetailsModel
   

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
    public function orderDetails()
    {
        return $this->hasMany(OrderDetailsModel::class, 'order_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Giả sử user_id là khóa ngoại
    }

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
