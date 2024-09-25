<?php

namespace App\Models;
use App\Models\OrderDetailsModel;
use App\Models\RefundModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItemModel extends Model
{
    use HasFactory;

    protected $table = 'refund_items'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'refund_id',       // Khóa ngoại đến bảng refunds
        'order_detail_id', // Khóa ngoại đến bảng order_details
        'quantity',        // Số lượng sản phẩm hoàn tiền
        'refund_amount',   // Số tiền hoàn cho sản phẩm
    ];

    // Định nghĩa quan hệ với bảng Refund
    public function refund()
    {
        return $this->belongsTo(RefundModel::class);
    }

    // Định nghĩa quan hệ với bảng OrderDetail
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetailsModel::class);
    }
}
