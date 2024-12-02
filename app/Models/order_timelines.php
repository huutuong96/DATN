<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_timelines extends Model
{
    use HasFactory;

    protected $table = 'order_timelines'; // Khai báo tên bảng

    protected $fillable = [
        'id',
        'title',
        'created_at',
        'updated_at',
        'order_id',
    ];
}
