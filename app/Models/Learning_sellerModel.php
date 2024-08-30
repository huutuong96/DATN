<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learning_sellerModel extends Model
{
    use HasFactory;

    protected $table = 'learning_seller'; // Khai báo tên bảng

    protected $fillable = [
        'status',
        'learn_id',
        'shop_id',
        'create_by',
        'update_by',
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
