<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class refund_order extends Model
{
    use HasFactory;
    protected $table = 'refund_order';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'shop_id',
        'user_id',
        'status',
        'reason',
        'amount',
        'note_admin',
        'reviewer',
        'approval_date',
        'type',
    ];

    public $timestamps = false;

    // Relationships
    public function order()
    {
        return $this->belongsTo(OrdersModel::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function user()
    {
        return $this->belongsTo(UsersModel::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(UsersModel::class, 'reviewer');
    }
}
