<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundModel extends Model
{
    use HasFactory;
    protected $table = 'refunds'; // Khai báo tên bảng

    protected $fillable = [
        'order_id',
        'user_id',
        'shop_id',
        'refund_amount',
        'refund_reason',
        'refund_status',
        'approved_by',
        'approved_at',
    ];

    // Quan hệ với đơn hàng (Order)
    public function order()
    {
        return $this->belongsTo(OrdersModel::class);
    }

    // Quan hệ với người dùng tạo yêu cầu hoàn tiền (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với cửa hàng (Shop)
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Người duyệt yêu cầu hoàn tiền (User)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
