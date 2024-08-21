<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    // Tên bảng tương ứng với model này
    protected $table = 'banner_mains'; // Thay đổi để connect tới table

    protected $fillable = [
        'title',
        'content',
        'URL',
        'status',
        'index',
        'create_by',
        'update_by',
        'create_at',
        'update_at',
    ];
}
