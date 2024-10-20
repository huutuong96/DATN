<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentsModel extends Model
{
    use HasFactory;
    
    protected $table = 'comments'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'title',
        'content',
        'images',
        'rate',
        'status',
        'level',
        'parent_id',
        'product_id',
        'level',
        'user_id',

    ];
    protected $casts = [
        'images' => 'array', // Tự động convert JSON thành mảng khi lấy từ DB
    ];
    public function parent()
    {
        return $this->hasMany(CommentsModel::class, 'parent_id'); 
    }
}
