<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherToShop extends Model
{
    use HasFactory;
    protected $table = 'voucher_to_shops';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'title',
        'description',
        'image',
        'quantity',
        'limitValue',
        'ratio',
        'code',
        'shop_id',
        'status',
        'create_by',
        'update_by',
        'created_at',
        'updated_at',
        'min',
    ];
}
