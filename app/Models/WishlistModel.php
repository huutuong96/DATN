<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistModel extends Model
{
    use HasFactory;
    protected $table = 'wishlists'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'status',
        'user_id',
        'product_id',
        'create_at',
        'update_at',
    ];
    /**
     * Các trường sẽ được tự động chuyển đổi sang kiểu dữ liệu tương ứng.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

}
