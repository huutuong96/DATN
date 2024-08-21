<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressModel extends Model
{
    use HasFactory;

    // Tên bảng tương ứng với model này
    protected $table = 'address'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'address',
        'type',
        'default',
        'status',
        'create_by',
        'update_by',
    ];

    // Các trường không được gán hàng loạt
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];



    /**
     * Các phương thức và quan hệ với các model khác có thể được định nghĩa ở đây
     */
}