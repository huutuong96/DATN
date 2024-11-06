<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;



class UsersModel extends Authenticatable implements JWTSubject {

    use HasFactory;

    protected $table = 'users'; // Thay đổi tên bảng nếu cần

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'fullname',
        'password',
        'phone',
        'email',
        'description',
        'point',
        'genre',
        'datebirth',
        'avatar',
        'refesh_token',
        'login_at',
        'rank_id',
        'role_id',
        'status',
        'verify_code',
    ];

    protected $hidden = [
        'password',
        'refesh_token',
        'created_at',
        'updated_at',
        'update_version',
    ];

     public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Define the address relationship
    public function address()
    {
        return $this->hasMany(AddressModel::class, 'user_id');
    }

    public function rank()
    {
        return $this->belongsTo(RanksModel::class, 'rank_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(OrdersModel::class, 'user_id');
    }

    public function shop_manager()
    {
        return $this->hasMany(Shop_manager::class, 'user_id');
    }

    public function cart_to_user()
    {
        return $this->belongsTo(RolesModel::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(RolesModel::class, 'role_id');
    }
    
}
