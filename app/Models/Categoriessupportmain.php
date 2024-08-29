<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoriessupportmain extends Model
{
    use HasFactory;
    protected $table = 'categories_support_main'; // Khai báo tên bảng

    protected $fillable = [
        'content',
        'status',
        'index',
        'create_by',
        'update_by',
    ];
}
