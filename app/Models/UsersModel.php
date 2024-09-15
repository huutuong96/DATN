<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
