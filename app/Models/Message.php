<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
        'created_at',
        'updated_at',
        'user_id',
        'shop_id',
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function shops()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id');
    }
}
