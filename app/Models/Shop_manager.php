<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_manager extends Model
{
    use HasFactory;

    protected $table = 'shop_managers';

    protected $fillable = [
        'status',
        'user_id',
        'shop_id',
        'role',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsTo(UsersModel::class, 'user_id');
    }
}
