<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class voucher_to_main extends Model
{
    use HasFactory;
    protected $table = 'voucher_to_main';

    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'title',
        'description',
        'image',
        'quantity',
        'limitValue',
        'ratio',
        'code',
        'status',
        'create_by',
        'update_by',
        'created_at',
        'updated_at',
    ];


}
