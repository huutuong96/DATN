<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'created_at',
        'updated_at',
        'user_id',
        'shop_id',
        'role',
    ];
}