<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RanksModel extends Model
{
    use HasFactory;

    protected $table = 'ranks'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'title',
        'description',
        'status',
        'create_by',
        'update_by',
    ];
}