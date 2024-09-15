<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_tax_details extends Model
{
    use HasFactory;

    protected $table = 'order_tax_details'; // Khai báo tên bảng

    protected $fillable = [
        'order_id',
        'tax_id',
        'amount',

    ];
}
