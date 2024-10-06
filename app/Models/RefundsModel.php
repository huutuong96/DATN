<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundsModel extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = [
        'order_item_id',
        'user_id',
        'shop_id',
        'reason',
        'refund_amount',
        'approved_by',
        'approved_at',
        'status'
    ];

    public function orderItem() {
        return $this->belongsTo(OrderDetailsModel::class, 'order_item_id');
    }
    
}
