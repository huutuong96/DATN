<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_at',
        'updated_at',
        'title',
        'content',
        'create_by',
        'update_by',
    ];

}
