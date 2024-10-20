<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipsModel extends Model
{
    use HasFactory;
    protected $table = 'ships'; // Khai báo tên bảng

    /**
     * Các trường có thể được gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'fees',
        'shop_id',
        'ship_company_id',
        'distance'
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
