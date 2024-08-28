<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnModel extends Model
{
    use HasFactory;
    protected $table = 'learns'; // Khai báo tên bảng

    protected $fillable = [
        'title',
        'content',
        'status',
        'category_id',
        'create_by',
        'update_by',
    ];
}

