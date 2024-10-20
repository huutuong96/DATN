<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_fee_details extends Model
{
    use HasFactory;

    protected $table = 'order_fee_details'; // Khai báo tên bảng

    protected $fillable = [
        'order_id',
        'platform_fee_id',
        'amount'
    ];

}
