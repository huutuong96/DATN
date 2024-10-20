<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremissionsModel extends Model
{
    use HasFactory;
    protected $table = 'premissions'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'premissionName',
        'status',
        'create_by',
        'update_by',
    ];
}
