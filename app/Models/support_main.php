<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class support_main extends Model
{
    use HasFactory;
    protected $table = 'support_main'; // Khai báo tên bảng

    protected $fillable = [
        'content',
        'status',
        'index',
        'create_by',
        'update_by',
        'created_at',
        'updated_at',
        'category_support_id',
    ];
}

