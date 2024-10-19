<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_to_usersModel extends Model
{
    use HasFactory;

    protected $table = 'cart_to_users';

    protected $fillable = [
        'status',
        'user_id'
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
    public function users()
    {
        return $this->belongsTo(UsersModel::class, 'user_id');
    }
}
