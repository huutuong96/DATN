<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_get_cash_shops extends Model
{
    use HasFactory;
    protected $table = 'history_get_cash_shops'; // Tên bảng trong cơ sở dữ liệu
    protected $fillable = [
        'id',
        'shop_id',
        'user_id',
        'cash',
        'verify_code',
        'date',
        'account_number',
        'bank_name',
        'owner_bank',
    ];
    public $timestamps = false;
}
