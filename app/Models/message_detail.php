<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'mes_id',
        'content',
        'send_by',
        'status',
    ];

}
