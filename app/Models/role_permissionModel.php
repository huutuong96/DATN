<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role_permissionModel extends Model
{
    use HasFactory;
    protected $table = 'role_premissions'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'role_id',
        'premission_id',
        'status',
        'created_at',
        'update_by',
    ];
}

