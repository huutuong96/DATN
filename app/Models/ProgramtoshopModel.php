<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramtoshopModel extends Model
{
    use HasFactory;
    protected $table = 'program_to_shops'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'program_id',
        'shop_id',
    ];
}
