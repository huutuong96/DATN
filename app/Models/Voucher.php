<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'create_by',
        'update_by',
        'created_at',
        'updated_at',
        'user_id',
        'code',
        'shop_id',
        'max',
        'min',
        'ratio',
        'title',
        'description',
    ];
}
