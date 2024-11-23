<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recipes extends Model
{
    use HasFactory;
    protected $table = 'recipes'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'id',
        'is_active',
        'code',
        'title',
        'description',
        'type',
        'json',
    ];
    public $timestamps = false;
}
