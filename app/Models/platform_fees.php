<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class platform_fees extends Model
{
    use HasFactory;

    protected $table = 'platform_fees'; // Khai báo tên bảng

    protected $fillable = [
        'name',
        'rate',
        'description',
    ];
}
