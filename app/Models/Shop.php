<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'slug',
        'pick_up_address',
        'image',
        'cccd',
        'status',
        'create_by',
        'update_by',
        'tax_id',
    ];
}
