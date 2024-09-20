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
        'rate',
        'status',
        'parent_id',
        'product_id',
        'level',
        'user_id'
    ];
    public function parent()
    {
        return $this->hasMany(CommentsModel::class, 'parent_id'); 
    }

}
